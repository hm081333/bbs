<?php

namespace App\Console\Commands\Catch;

use App\Models\Intel\IntelProduct;
use App\Models\Intel\IntelProductCategory;
use App\Models\Intel\IntelProductSeries;
use App\Models\Intel\IntelProductSpec;
use App\Utils\Tools;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Promise\Utils;
use GuzzleHttp\Psr7\Response;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class Intel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'catch:intel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '抓取 Intel ARK 产品数据';

    private $job_start_time;
    private $now_time;

    private $base_uri = 'https://ark.intel.com';
    private array $client = [];

    private $curl_runtime_file_paths = [];
    private $product_category_list = [];
    private $product_series_list = [];
    private $product_list = [];
    private $product_specs_list = [];

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->job_start_time = microtime(true);
        $this->now_time = Tools::time();
        $this->build_http_client($this->base_uri, [
            "accept" => "text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7",
            "accept-language" => "zh",
            "cache-control" => "no-cache",
            "pragma" => "no-cache",
            "sec-ch-ua" => "\"Not A(Brand\";v=\"99\", \"Google Chrome\";v=\"121\", \"Chromium\";v=\"121\"",
            "sec-ch-ua-mobile" => "?0",
            "sec-ch-ua-platform" => "\"macOS\"",
            "sec-fetch-dest" => "document",
            "sec-fetch-mode" => "navigate",
            "sec-fetch-site" => "none",
            "sec-fetch-user" => "?1",
            "upgrade-insecure-requests" => "1",
        ]);
        $this->_handle();
        $this->info('完成|' . $this->description . '|耗时：' . Tools::secondToTimeText(microtime(true) - $this->job_start_time));
        return Command::SUCCESS;
    }

    private function _handle()
    {
        ini_set('pcre.backtrack_limit', -1);
        $this->_catch();
    }

    private function _catch()
    {
        $this->getProductCategoryAndSeries();
        $this->getProductList();
        $this->getProductSpec();
    }

    private function getProductCategoryAndSeries()
    {
        $url = $this->getUrlFromString('/content/www/us/en/ark.html');
        $base_url = str_replace(implode('/', array_reverse(explode('_', 'en_us'))), '{language_key}', $url);
        $html = $this->curlGet($url);
        preg_match_all('/<li[^>]*class="[^"]*lang-option[^"]*"[^>]*>[^<]*<a[^>]*data-locale="(.*?)"[^>]*href="(.*?)"[^>]*>(.*?)<\/a>[^<]*<\/li>/', $html, $matches);
        $language_list = [];
        foreach ($matches[1] as $index => $key) {
            $name = $this->removeExtraSpaceAndHtmlTag($matches[3][$index]);
            $item = [
                'key' => $key,
                'name' => $name,
                // 'selected' => $key == $language,
            ];
            $item['url'] = str_replace('{language_key}', implode('/', array_reverse(explode('_', $key))), $base_url);
            $language_list[$key] = $item;
        }
        collect($language_list)->each(fn($info, $language) => $this->_getProductCategoryAndSeries($info['url'], $language));
        // file_put_contents(Tools::backupPath('intel_ark_category.json'), Tools::jsonEncode($this->product_category_list));
        // file_put_contents(Tools::backupPath('intel_ark_series.json'), Tools::jsonEncode($this->product_series_list));
        $this->saveProductCategory();
        $this->saveProductSeries();
    }

    private function _getProductCategoryAndSeries(string $url, string $language)
    {
        $html = $this->curlGet($url);
        //region 正则匹配出内容区域
        preg_match('/<section class="blade".*?\/section>/', $html, $matches);
        if (empty($matches[0])) dd($html);
        $section = $matches[0];
        //endregion
        // 正则匹配出产品分类
        preg_match_all('/<div class="ark-accessible-color col-xs-12 col-sm-6 col-md-4 product-category with-icons"[^>]*data-panel-key=\s*([^\s]+)[^>]*>.*?<span class="name show-icon" role="button">(.*?)<\/span>.*?<\/div>/', $section, $matches);
        $product_category_list = array_combine($matches[1], $matches[2]);
        foreach ($product_category_list as $category_panel_key => $product_category_name) {
            $product_category_name = $this->removeExtraSpaceAndHtmlTag($product_category_name);
            $product_category = [
                'pid' => 0,
                'level' => 0,
                'language' => $language,
                'panel_key' => $category_panel_key,
                'unique_key' => "{$category_panel_key}:{$language}",
                'name' => $product_category_name,
            ];
            $this->product_category_list[$product_category['unique_key']] = $product_category;
            dump("{$language}|主分类：{$product_category_name}");

            // 正则匹配出旗下子产品分类
            preg_match('/<div[^>]*class="product-categories product-categories-2"[^>]*data-parent-panel-key="' . $product_category['panel_key'] . '"[^>]*>[^<]*<div class="row category-row">([^<]*<div[^>]*data-wap_ref="category\|subcategory"[^>]*>.*?<\/div>[^<]*)+<\/div>[^<]*<\/div>/', $section, $matches);
            $product_subcategory_html = $matches[0];
            // 正则匹配出旗下所有子产品分类
            preg_match_all('/<div[^>]*data-panel-key="(.*?)"[^>]*>[^<]*<span class="name ark-accessible-color">([^<]*)<\/span>[^<]*<\/div>/', $product_subcategory_html, $matches);
            foreach (array_combine($matches[1], $matches[2]) as $subcategory_panel_key => $product_subcategory_name) {
                $product_subcategory_name = $this->removeExtraSpaceAndHtmlTag($product_subcategory_name);
                $product_subcategory = [
                    'parent_unique_key' => $product_category['unique_key'],
                    'pid' => null,
                    'level' => 1,
                    'language' => $language,
                    'panel_key' => $subcategory_panel_key,
                    'unique_key' => "{$subcategory_panel_key}:{$language}",
                    'name' => $product_subcategory_name,
                ];
                $this->product_category_list[$product_subcategory['unique_key']] = $product_subcategory;
                dump("{$language}|子分类：{$product_subcategory_name}");

                // 正则匹配出分类下产品系列
                preg_match('/<div[^>]*class="products processors"[^>]*data-parent-panel-key="' . $product_subcategory['panel_key'] . '"[^>]*>[^<]*<div class="product-row">([^<]*<div[^>]*class="product[^>]*>.*?<\/div>[^<]*)+<\/div>[^<]*<\/div>/', $section, $matches);
                $product_html = $matches[0];
                // 正则匹配出分类下所有产品系列
                preg_match_all('/<a class="ark-accessible-color" href="(.*?)">(.*?)<\/a>/', $product_html, $matches);
                foreach (array_combine($matches[2], $matches[1]) as $product_series_name => $product_series_path) {
                    $product_series_name = $this->removeExtraSpaceAndHtmlTag($product_series_name);
                    preg_match('/\/(\d+)\//', $product_series_path, $matches);
                    $product_series_ark_series_id = $this->removeExtraSpaceAndHtmlTag($matches[1]);
                    $product_series = [
                        'category_unique_key' => $product_subcategory['unique_key'],
                        'category_panel_key' => $product_subcategory['panel_key'],
                        'language' => $language,
                        'unique_key' => "{$product_series_ark_series_id}:{$language}",
                        'ark_series_id' => $product_series_ark_series_id,
                        'name' => $product_series_name,
                        'path' => $product_series_path,
                        'url' => $this->getUrlFromString($product_series_path),
                    ];
                    $this->product_series_list[$product_series['unique_key']] = $product_series;
                    dump("{$language}|产品系列：{$product_series_name}");
                }
            }
        }
    }

    private function saveProductCategory()
    {
        $product_category_list = collect($this->product_category_list);
        $product_category_list
            ->whereNull('parent_unique_key')
            ->chunk(500)
            ->each(function (Collection $product_category_list_chunk) {
                IntelProductCategory::upsert($product_category_list_chunk
                    ->map(function ($product_category) {
                        $product_category['created_at'] = $this->now_time;
                        $product_category['updated_at'] = $this->now_time;
                        unset($product_category['parent_unique_key']);
                        return $product_category;
                    })
                    ->toArray(), ['unique_key']);
                /*IntelProductCategory::insert($product_category_list_chunk
                    ->whereNotIn('unique_key', IntelProductCategory::whereIn('unique_key', $product_category_list_chunk->pluck('unique_key'))
                        ->select('unique_key')
                        ->pluck('unique_key'))
                    ->map(function ($product_category) {
                        $product_category['created_at'] = $this->now_time;
                        $product_category['updated_at'] = $this->now_time;
                        unset($product_category['parent_unique_key']);
                        return $product_category;
                    })
                    ->toArray());*/
                $this->info('产品分类保存处理完成' . $product_category_list_chunk->count() . '条');
                unset($product_category_list_chunk);
            });

        $top_product_category_ids = IntelProductCategory::where('pid', 0)
            ->select(['id', 'unique_key'])
            ->pluck('id', 'unique_key');

        $product_category_list
            ->whereNotNull('parent_unique_key')
            ->chunk(500)
            ->each(function (Collection $product_category_list_chunk) use ($top_product_category_ids) {
                IntelProductCategory::upsert($product_category_list_chunk
                    ->map(function ($product_category) use ($top_product_category_ids) {
                        $product_category['created_at'] = $this->now_time;
                        $product_category['updated_at'] = $this->now_time;
                        $product_category['pid'] = $top_product_category_ids[$product_category['parent_unique_key']];
                        unset($product_category['parent_unique_key']);
                        return $product_category;
                    })
                    ->toArray(), ['unique_key']);
                /*IntelProductCategory::insert($product_category_list_chunk
                    ->whereNotIn('unique_key', IntelProductCategory::whereIn('unique_key', $product_category_list_chunk->pluck('unique_key'))
                        ->select('unique_key')
                        ->pluck('unique_key'))
                    ->map(function ($product_category) use ($top_product_category_ids) {
                        $product_category['created_at'] = $this->now_time;
                        $product_category['updated_at'] = $this->now_time;
                        $product_category['pid'] = $top_product_category_ids[$product_category['parent_unique_key']];
                        unset($product_category['parent_unique_key']);
                        return $product_category;
                    })
                    ->toArray());*/
                $this->info('产品分类保存处理完成' . $product_category_list_chunk->count() . '条');
                unset($product_category_list_chunk);
            });
        $this->info('产品分类保存成功');
        $this->product_category_list = [];
    }

    private function saveProductSeries()
    {
        collect($this->product_series_list)
            ->chunk(500)
            ->each(function (Collection $product_series_list_chunk) {
                $product_category_ids = IntelProductCategory::whereIn('unique_key', $product_series_list_chunk
                    ->pluck('category_unique_key')
                    ->unique())
                    ->select(['id', 'unique_key'])
                    ->pluck('id', 'unique_key');
                IntelProductSeries::upsert($product_series_list_chunk
                    ->map(function ($product_series) use ($product_category_ids) {
                        $product_series['created_at'] = $this->now_time;
                        $product_series['updated_at'] = $this->now_time;
                        $product_series['category_id'] = $product_category_ids[$product_series['category_unique_key']];
                        unset($product_series['category_unique_key']);
                        return $product_series;
                    })
                    ->toArray(), ['unique_key']);
                /*IntelProductSeries::insert($product_series_list_chunk
                    ->whereNotIn('unique_key', IntelProductSeries::whereIn('unique_key', $product_series_list_chunk->pluck('unique_key'))
                        ->select('unique_key')
                        ->pluck('unique_key'))
                    ->map(function ($product_series) use ($product_category_ids) {
                        $product_series['created_at'] = $this->now_time;
                        $product_series['updated_at'] = $this->now_time;
                        $product_series['category_id'] = $product_category_ids[$product_series['category_unique_key']];
                        unset($product_series['category_unique_key']);
                        return $product_series;
                    })
                    ->toArray());*/
                $this->info('产品系列保存处理完成' . $product_series_list_chunk->count() . '条');
                unset($product_series_list_chunk);
            });
        $this->info('产品系列保存成功');
        $this->product_series_list = [];
    }

    private function getProductList()
    {
        IntelProductSeries::chunk(500, function (Collection $product_series_list) {
            $product_series_list = $product_series_list->filter(function (IntelProductSeries $product_series) {
                [$ark_series_id, $language] = explode(':', $product_series['unique_key']);
                $content = $this->getCurlRuntimeContent($product_series['url']);
                if (!empty($content)) {
                    $this->_getProductList($product_series, $language, $content);
                    return false;
                }
                return true;
            });

            $product_series_list = $product_series_list->pluck([], 'unique_key');

            $retryTimes = 5;
            $product_series_urls = $product_series_list->pluck('url', 'unique_key');
            while ($product_series_urls->isNotEmpty() && $retryTimes > 0) {
                $product_series_urls->chunk(10)->each(function (Collection $urls) use (&$product_series_urls, $product_series_list) {
                    $responses = $this->multi_http_get($this->base_uri, $urls);
                    unset($urls);
                    $this->info('本批并发请求成功数量：' . $responses['fulfilled']->count());
                    // 追加失败重试集合（找出不在本次成功相应集合中的差集，为未成功响应集合）
                    $product_series_urls = $product_series_urls->diffKeys($responses['fulfilled']);
                    // dump($product_series_urls);
                    // 处理响应
                    $responses['fulfilled']->each(function (Response $response, $product_series_unique_key) use ($product_series_list) {
                        $content = $this->parseResponse($response);
                        unset($response);
                        if (!empty($content)) {
                            $product_series = $product_series_list[$product_series_unique_key];
                            $this->saveCurlRuntimeContent($product_series['url'], $content);
                            [$ark_series_id, $language] = explode(':', $product_series['unique_key']);
                            $this->_getProductList($product_series, $language, $content);
                        } else {
                            dd($content);
                        }
                    });
                    unset($responses);
                });
                $retryTimes--;
                sleep(1);
            }

            $this->saveProduct();
        });
        // file_put_contents(Tools::backupPath('intel_ark_product.json'), Tools::jsonEncode($this->product_list));
    }

    private function _getProductList(IntelProductSeries $product_series, string $language, string $product_list_html)
    {
        $product_list_html = $this->removeHtmlWarp($product_list_html);
        //region 正则匹配出内容区域
        preg_match('/<table[^>]*id="product-table"[^>]*>.*?<tbody[^>]*>(.*?)<\/tbody><\/table>/', $product_list_html, $matches);
        $product_list_table_tbody = $matches[1];
        //endregion
        // 正则匹配出产品下所有规格详情链接
        preg_match_all('/<tr.*?data-product-id="(.*?)".*?<a href="(.*?)">(.*?)<\/a>.*?<\/tr>/', $product_list_table_tbody, $matches);
        foreach ($matches[1] as $index => $ark_product_id) {
            $ark_product_id = $this->removeExtraSpaceAndHtmlTag($ark_product_id);
            $product_name = $this->removeExtraSpaceAndHtmlTag($matches[3][$index]);
            $product_path = $this->removeExtraSpaceAndHtmlTag($matches[2][$index]);
            $product = [
                'language' => $language,
                'unique_key' => "{$ark_product_id}:{$language}",
                'category_id' => $product_series['category_id'],
                'series_id' => $product_series['id'],
                'ark_product_id' => $ark_product_id,
                'ark_series_id' => $product_series['ark_series_id'],
                'name' => $product_name,
                'path' => $product_path,
                'url' => $this->getUrlFromString($product_path),
            ];
            $this->product_list[$product['unique_key']] = $product;
            dump("{$language}|产品：{$product_name}");
        }
    }

    private function saveProduct()
    {
        collect($this->product_list)
            ->chunk(500)
            ->each(function (Collection $product_list_chunk) {
                IntelProduct::upsert($product_list_chunk
                    ->map(function ($product) {
                        $product['created_at'] = $this->now_time;
                        $product['updated_at'] = $this->now_time;
                        return $product;
                    })
                    ->toArray(), ['unique_key']);
                /*IntelProduct::insert($product_list_chunk
                    ->whereNotIn('unique_key', IntelProduct::whereIn('unique_key', $product_list_chunk->pluck('unique_key'))
                        ->select('unique_key')
                        ->pluck('unique_key'))
                    ->map(function ($product) {
                        $product['created_at'] = $this->now_time;
                        $product['updated_at'] = $this->now_time;
                        return $product;
                    })
                ->toArray());*/
                $this->info('产品保存处理完成' . $product_list_chunk->count() . '条');
            });
        $this->info('产品保存成功');
        $this->product_list = [];
    }

    private function getProductSpec()
    {
        IntelProduct::chunk(500, function (Collection $product_list) {
            $product_list = $product_list->filter(function (IntelProduct $product) {
                [$ark_product_id, $language] = explode(':', $product['unique_key']);
                $curl_runtime = $this->getCurlRuntimeInfo($product['url']);
                if (!empty($curl_runtime['content'])) {
                    $this->_getProductSpec($product, $language, $curl_runtime['content']);
                    return false;
                }
                return true;
            });

            $product_list = $product_list->pluck([], 'unique_key');

            $product_urls = $product_list->pluck('url', 'unique_key');
            $retryTimes = 5;
            while ($product_urls->isNotEmpty() && $retryTimes > 0) {
                $product_urls->chunk(10)->each(function (Collection $urls) use (&$product_urls, $product_list) {
                    $responses = $this->multi_http_get($this->base_uri, $urls);
                    unset($urls);
                    $this->info('本批并发请求成功数量：' . $responses['fulfilled']->count());
                    // 追加失败重试集合（找出不在本次成功相应集合中的差集，为未成功响应集合）
                    $product_urls = $product_urls->diffKeys($responses['fulfilled']);
                    // dump($product_urls);
                    // 处理响应
                    $responses['fulfilled']->each(function (Response $response, $product_unique_key) use ($product_list) {
                        $content = $this->parseResponse($response);
                        unset($response);
                        if (!empty($content)) {
                            $product = $product_list[$product_unique_key];
                            $this->saveCurlRuntimeContent($product['url'], $content);
                            [$ark_product_id, $language] = explode(':', $product['unique_key']);
                            $this->_getProductSpec($product, $language, $content);
                        } else {
                            dd($content);
                        }
                    });
                    unset($responses);
                });
                $retryTimes--;
                sleep(1);
            }

            $this->saveProductSpec();

        });
        // file_put_contents(Tools::backupPath('intel_ark_product_specs.json'), Tools::jsonEncode($this->product_specs_list));
        // dd($this->product_specs_list);
    }

    private function _getProductSpec($product, $language, $content)
    {
        $content = $this->removeHtmlWarp($content);
        $content = preg_replace('/<div[^>]*class="[^"]*arkSignInBanner[^"]*"[^>]*>.*?<\/section><\/div>/', '', $content);
        //region 正则匹配出内容区域
        preg_match('/<div[^>]*class="specs-section active"[^>]*>(<section[^>]*>.*?<\/section>)+<\/div>/', $content, $matches);
        if (empty($matches[0])) {
            $this->unlinkCurlRuntime($product['url']);
            $this->error($product['name']);
            return false;
            dd($content);
        }
        // $this->info($product['name']);
        $specs_section = $matches[0];
        //endregion
        preg_match_all('/<section[^>]*>(.*?)<\/section>/', $specs_section, $matches);
        $specifications_html = $matches[1];
        foreach ($specifications_html as $specification_tab_index => $specification_html) {
            preg_match('/<div[^>]*class="[^"]*subhead[^"]*"[^>]*>.*?<h2 class="h2">(.*?)<\/h2>.*?<\/div>/', $specification_html, $matches);
            // 规格分类名称
            $specification_tab_title = $this->removeExtraSpaceAndHtmlTag($matches[1]);
            dump($specification_tab_title);
            preg_match_all('/<li.*?<span[^>]*class="[^"]*label[^"]*"[^>]*>(.*?)<\/span><span[^>]*class="[^"]*value[^"]*"[^>]*data-key="(.*?)"[^>]*>(.*?)<\/span>.*?\/li>/', $specification_html, $matches);
            foreach ($matches[0] as $index => $key) {
                $key = $this->removeExtraSpaceAndHtmlTag($matches[2][$index]);
                $label = $this->removeExtraSpaceAndHtmlTag($matches[1][$index]);
                $value = $this->removeExtraSpaceAndHtmlTag($matches[3][$index]);
                $product_specs_item = [
                    'language' => $language,
                    'unique_key' => "{$product['ark_product_id']}:{$language}:{$key}",
                    'category_id' => $product['category_id'],
                    'series_id' => $product['series_id'],
                    'product_id' => $product['id'],
                    'ark_series_id' => $product['ark_series_id'],
                    'ark_product_id' => $product['ark_product_id'],
                    'tab_index' => $specification_tab_index,
                    'tab_title' => $specification_tab_title,
                    'key' => $key,
                    'label' => $label,
                    'label_tips_rich_text' => null,
                    'value' => $value,
                    'value_url' => null,
                ];
                if (preg_match('/<a[^>]*href="(.*?)".*?<\/a>/', $matches[3][$index], $a_matches)) $product_specs_item['value_url'] = $this->getUrlFromString($this->removeExtraSpaceAndHtmlTag($a_matches[1]));
                unset($a_matches);
                preg_match('/data-modal="(.*?)"/', $matches[1][$index], $tips_model_id_matches);
                if ($tips_model_id = $tips_model_id_matches[1] ?? '') {
                    preg_match('/<div[^>]*class="[^"]*support-modal[^"]*"[^>]*id="' . $tips_model_id . '"[^>]*>.*?<h2[^>]*>(.*?)<\/h2>.*?<p[^>]*>(.*?)<\/p>.*?<\/div>/', $content, $tips_model_matches);
                    $product_specs_item['label_tips_rich_text'] = htmlspecialchars($tips_model_matches[2]);
                    unset($tips_model_matches);
                }
                unset($tips_model_id_matches);

                $this->product_specs_list[$product_specs_item['unique_key']] = $product_specs_item;
                // dump("{$language}|产品：{$product['name']}，规格：{$label}");
            }
        }
        dump("{$language}|规格产品：{$product['name']}");
    }

    private function saveProductSpec()
    {
        collect($this->product_specs_list)
            ->chunk(500)
            ->each(function (Collection $product_specs_list_chunk) {
                IntelProductSpec::upsert($product_specs_list_chunk
                    ->map(function ($product_spec) {
                        $product_spec['created_at'] = $this->now_time;
                        $product_spec['updated_at'] = $this->now_time;
                        return $product_spec;
                    })
                    ->toArray(), ['unique_key']);
                /*IntelProductSpec::insert($product_specs_list_chunk
                    ->whereNotIn('unique_key', IntelProductSpec::whereIn('unique_key', $product_specs_list_chunk->pluck('unique_key'))
                        ->select('unique_key')
                        ->pluck('unique_key'))
                    ->map(function ($product_spec) {
                        $product_spec['created_at'] = $this->now_time;
                        $product_spec['updated_at'] = $this->now_time;
                        return $product_spec;
                    })
                ->toArray());*/
                $this->info('产品规格保存处理完成' . $product_specs_list_chunk->count() . '条');
            });
        // dump($product['ark_product_id']);
        $this->info('产品规格保存成功');
        $this->product_specs_list = [];
    }

    private function curlGet(string $url): string
    {
        $content = $this->getCurlRuntimeContent($url);
        if (empty($content)) {
            $this->unlinkCurlRuntime($url);
            $content = $this->parseResponse($this->single_http_get($this->base_uri, $url));
            $this->saveCurlRuntimeContent($url, $content);
        }
        return $this->removeHtmlWarp($content);
    }

    private function parseResponse(Response $response): string
    {
        $content = $response->getBody()->getContents();
        if (empty($content)) return '';
        $content = Tools::compress_html($content);
        return $content;
    }

    private function getCurlRuntimeInfo(string $url): array
    {
        return [
            'url' => $url,
            'file_path' => $this->getCurlRuntimeFilePath($url),
            'content' => $this->getCurlRuntimeContent($url),
        ];
    }

    private function getCurlRuntimeContent(string $url): ?string
    {
        $file_path = $this->getCurlRuntimeFilePath($url);
        $content = null;
        if (file_exists($file_path)) {
            $content = file_get_contents($file_path);
            if (empty($content)) {
                $content = null;
                @unlink($file_path);
            }
        }
        return $content;
    }

    private function saveCurlRuntimeContent(string $url, string $content): bool
    {
        if (empty($content)) return false;
        return !!file_put_contents($this->getCurlRuntimeFilePath($url), $content);
    }

    private function unlinkCurlRuntime(string $url): bool
    {
        return @unlink($this->getCurlRuntimeFilePath($url));
    }

    /**
     * 获取请求缓存路径
     *
     * @param string $url
     *
     * @return string
     */
    private function getCurlRuntimeFilePath(string $url): string
    {
        $key = urlencode($url);
        if (!isset($this->curl_runtime_file_paths[$key])) {
            $url_path_info = pathinfo(preg_replace('/((https|http|ssftp|rtsp|mms)?:\/\/)/', '', $url));
            $this->curl_runtime_file_paths[$key] = Tools::runtimePath($url_path_info['dirname'], true) . '/' . $url_path_info['basename'];
        }
        return $this->curl_runtime_file_paths[$key];

    }

    private function getUrlFromString(string $string): string
    {
        if (!preg_match('/((https|http|ssftp|rtsp|mms)?:\/\/)/', $string)) $string = rtrim($this->base_uri, '/') . '/' . ltrim($string, '/');
        return $string;
    }

    private function getPathFromString(string $string): string
    {
        return preg_replace('/((https|http|ssftp|rtsp|mms)?:\/\/[^\/]+)/', '', $string);
    }

    private function removeHtmlWarp($string): string
    {
        return preg_replace('/<br\/?>/', '', $string);
    }

    private function removeExtraSpaceAndHtmlTag($string): string
    {
        return trim(strip_tags(preg_replace("/[\s]+/", " ", htmlspecialchars_decode($string))));
    }

    /**
     * 构建GuzzleHttp客户端
     *
     * @param string $base_uri
     *
     * @return Client
     */
    private function build_http_client(string $base_uri, array $headers = [])
    {
        $base_uri = rtrim($base_uri, '/');
        $base_uri_key = urlencode($base_uri);
        if (!isset($this->client[$base_uri_key])) $this->client[$base_uri_key] = new Client([
            'base_uri' => $base_uri . '/',
            'connect_timeout' => 2,
            'timeout' => 5,
            'verify' => false,
            'headers' => $headers,
        ]);
        return $this->client[$base_uri_key];
    }

    /**
     * 单个http get请求
     *
     * @param string $base_uri   接口url
     * @param string $path       接口
     * @param array  $headers    请求头
     * @param int    $retryTimes 重试次数
     *
     * @return Response|ResponseInterface
     * @throws GuzzleException
     */
    private function single_http_get(string $base_uri, string $path, array $headers = [], int $retryTimes = 5): Response|ResponseInterface
    {
        $retryTimes = max($retryTimes, 0);
        // $url = Tools::urlRebuild($base_uri . '/' . ltrim($path, '/'), $params);
        // $path = ltrim(str_replace($base_uri, '', $url), '/');
        do {
            try {
                $response = $this->build_http_client($base_uri, $headers)->get($path);
                if ($response->getStatusCode() == 200) return $response;
            } catch (Throwable $e) {
                $this->error($e->getMessage());
            }
            $retryTimes--;
        } while ($retryTimes >= 0);
        return new Response;
    }

    /**
     * 多个http并发 get请求
     *
     * @param string           $base_uri   接口url
     * @param array|Collection $paths      接口
     * @param array            $headers    请求头
     * @param int              $retryTimes 重试次数
     *
     * @return array
     */
    private function multi_http_get(string $base_uri, array|Collection $paths, array $headers = [], int $retryTimes = 5): array
    {
        $successfully = collect();
        $retryTimes = max($retryTimes, 0);
        if (is_array($paths)) $paths = collect($paths);
        if ($paths->isNotEmpty()) {
            do {
                $promises = $paths->map(fn($path) => $this->build_http_client($base_uri, $headers)->getAsync($path));
                $this->info('本次并发请求数量：' . $promises->count());
                // 等待全部请求返回,如果其中一个请求失败会抛出异常
                // $responses = \GuzzleHttp\Promise\Utils::unwrap($promises);
                // 等待全部请求返回,允许某些请求失败
                $responses = Utils::settle($promises->toArray())->wait();
                $responses = collect($responses);
                unset($promises);
                // 成功列表
                $fulfilled = $responses->where('state', 'fulfilled');
                // 失败列表
                $rejected = $responses->where('state', 'rejected');
                unset($responses);
                // $this->info('本次并发请求成功响应数量：' . $fulfilled->count());
                $this->info('本次并发请求失败响应数量：' . $rejected->count());
                $rejected->each(fn($value) => $this->error($value['reason']->getMessage()));
                unset($rejected);
                // 追加成功列表
                $successfully = $successfully->merge($fulfilled->mapWithKeys(fn($item, $key) => [$key => $item['value']]));
                // 匹配成功列表不存在的合集，返回未成功请求的path
                $paths = $paths->diffKeys($fulfilled);
                // 重试次数减一
                $retryTimes--;
            } while ($paths->isNotEmpty() && $retryTimes >= 0);
        }
        return [
            // 成功响应集合
            'fulfilled' => $successfully,
            // 失败请求集合
            'rejected' => $paths,
        ];
    }

}
