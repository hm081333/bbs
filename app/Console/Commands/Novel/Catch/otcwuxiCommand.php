<?php

namespace App\Console\Commands\Novel\Catch;

use App\Exceptions\Server\InternalServerErrorException;
use App\Models\Novel\Novel;
use App\Models\Novel\NovelChapter;
use App\Utils\GuzzleHttp;
use App\Utils\Tools;
use GuzzleHttp\Psr7\Response;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class otcwuxiCommand extends Command
{
    // https://www.otcwuxi.com/
    protected $signature = 'novel:catch:otcwuxi
    {--url= : 小说链接}
    {--path= : 小说目录}
    {--main : 抓取主要信息}
    {--chapter : 抓取章节}';

    protected $description = '小说抓取-锡海小说网';
    private $job_start_time;
    private $base_uri = 'https://www.otcwuxi.com/';
    /**
     * @var GuzzleHttp
     */
    private $http;

    public function handle()
    {
        $this->job_start_time = microtime(true);
        $this->http = new GuzzleHttp($this->base_uri, [
            'accept' => 'text/html',
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3626.121 Safari/537.36',
        ]);
        if (!empty($this->option('url')) || !empty($this->option('path'))) {
            $path = empty($this->option('path')) ? str_replace($this->base_uri, '/', $this->option('url')) : $this->option('path');
            $novel = $this->_catchOnce($path);
            $this->_getChapters($novel);
        } else {
            $this->_catchAll();
        }
        $this->info('完成|' . $this->description . '|耗时：' . Tools::secondToTimeText(microtime(true) - $this->job_start_time));
        // 调起写入命令行
        return Command::SUCCESS;
    }

    private function _catchAll()
    {
        $this->comment('获取所有小说-开始');
        try {
            // 获取所有小说路径
            $novel_paths = $all_novel_paths = $this->_batchCatchNovelPath();
            $retryTimes = 5;
            while ($novel_paths->isNotEmpty() && $retryTimes > 0) {
                $novel_paths->chunk(50)->each(function (Collection $paths) use (&$all_novel_paths, &$novel_paths) {
                    $novels = Novel::whereIn('source_detail_url', $paths->map(fn($path) => $this->getUrlFromString($path)))->get();
                    $responses = $this->http->multiRequest('get', $paths);
                    $this->info('本批并发请求成功数量：' . $responses['fulfilled']->count());
                    // 移除初始集合中 本次成功的集合（找出不在本次成功相应集合中的差集，为未成功响应集合）
                    $novel_paths = $novel_paths->diffKeys($responses['fulfilled']);
                    // 处理响应
                    $responses['fulfilled']->each(function (Response $response, $index) use (&$all_novel_paths, &$novel_paths, $paths, $novels) {
                        $html = $this->parseResponse($response);
                        //region 获取可能出现的新小说，部分小说不存在列表中
                        $new_novel_paths = $this->pregNovelFromResponse($response)->diff($all_novel_paths);
                        if ($new_novel_paths->isNotEmpty()) {
                            $all_novel_paths = $all_novel_paths->merge($new_novel_paths);
                            $novel_paths = $novel_paths->merge($new_novel_paths);
                        }
                        //endregion
                        unset($response);
                        $path = $paths[$index];
                        if (!empty($html)) {
                            $novel = $novels->where('source_detail_url', $this->getUrlFromString($path))->first() ?? new Novel([
                                'source_detail_url' => $this->getUrlFromString($path),
                                'source_url' => $this->base_uri,
                                'source_detail_path' => $path,
                            ]);
                            $novel = $this->_getMainInfo($html, $novel);
                            $novel = $this->_getMenu($html, $novel);
                        } else {
                            dd($path, $html);
                        }
                        unset($html, $path);
                    });
                    unset($responses);
                    sleep(1);
                });
                $retryTimes--;
                sleep(1);
            }
            unset($novel_paths, $all_novel_paths, $retryTimes);
        } catch (\Throwable $e) {
            $this->error('获取所有小说-失败');
            $this->error($e->getMessage());
        }
        $this->comment('获取所有小说-结束');
    }

    private function _batchCatchNovelPath(): Collection
    {
        $this->comment('获取所有小说路径-开始');
        $novel_paths = collect();
        $novel_list_urls = collect([$this->base_uri]);
        for ($class = 1; $class <= 7; $class++) {
            for ($page = 1; $page <= 5; $page++) {
                $novel_list_urls->push("https://www.otcwuxi.com/class{$class}/p{$page}.html");
            }
        }
        foreach ([
                     'allvisit',// 总排行榜
                     'monthvisit',// 月排行榜
                     'weekvisit',// 周排行榜
                     'dayvisit',// 日排行榜
                     'finish',// 完本
                 ] as $rank) {
            for ($page = 1; $page <= 5; $page++) {
                $novel_list_urls->push("https://www.otcwuxi.com/{$rank}/p{$page}.html");
            }
        }
        try {
            $retryTimes = 5;
            while ($novel_list_urls->isNotEmpty() && $retryTimes > 0) {
                $novel_list_urls->chunk(50)->each(function (Collection $urls) use (&$novel_list_urls, &$novel_paths) {
                    $responses = $this->http->multiRequest('get', $urls);
                    unset($urls);
                    $this->info('本批并发请求成功数量：' . $responses['fulfilled']->count());
                    // 移除初始集合中 本次成功的集合（找出不在本次成功相应集合中的差集，为未成功响应集合）
                    $novel_list_urls = $novel_list_urls->diffKeys($responses['fulfilled']);
                    // 处理响应
                    $responses['fulfilled']->each(function (Response $response) use (&$novel_paths) {
                        $novel_paths = $novel_paths->merge($this->pregNovelFromResponse($response));
                    });
                    unset($responses);
                    sleep(1);
                });
                $retryTimes--;
                sleep(1);
            }
            unset($novel_list_urls);
        } catch (\Throwable $e) {
            $this->error('获取所有小说路径-失败');
            $this->error($e->getMessage());
        }
        $this->comment('获取所有小说路径-结束');
        // 集合去重并返回
        return $novel_paths->unique()->values();
    }

    private function pregNovelFromResponse(Response $response): Collection
    {
        $novel_paths = collect();
        try {
            $html = $this->parseResponse($response);
            unset($response);
            preg_match_all('/\/chapter\/[^\/]+\//', $html, $matches);
            unset($html);
            $novel_paths = $novel_paths->merge($matches[0] ?? [])->unique()->values();
        } catch (\Throwable $e) {
            $this->error('小说列表解析异常');
            $this->error($e->getMessage());
        }
        return $novel_paths;
    }

    private function _catchOnce($path): Novel
    {
        $this->comment('获取小说主要信息-开始');
        /* @var $novel Novel */
        $novel = Novel::firstOrNew([
            'source_detail_url' => $this->getUrlFromString($path),
        ], [
            'source_url' => $this->base_uri,
            'source_detail_path' => $path,
        ]);
        try {
            $response = $this->http->singleRequest('get', $novel['source_detail_url']);
            $html = $this->parseResponse($response);
            unset($response);

            $novel = $this->_getMainInfo($html, $novel);
            $novel = $this->_getMenu($html, $novel);
        } catch (\Throwable $e) {
            $this->error('获取小说主要信息-失败');
            $this->error($e->getMessage());
        }
        $this->comment('获取小说主要信息-结束');
        return $novel;
    }

    private function _getMainInfo(string $html, Novel $novel)
    {
        $this->comment('获取小说主要信息-开始');
        try {
            preg_match('/<div[^>]*id="maininfo"[^>]*>\s*<div[^>]*id="info"[^>]*>\s*<h1>([^<]*)<\/h1>\s*<p>作者:([^<]*)<\/p>\s*<p>类别:([^<]*)<\/p>\s*<p>最后更新:([^<]*)<\/p>\s*<p>最新:<a[^>]*>([\s\S]*)<\/a>\s*<\/p>\s*<\/div>\s*<div[^>]*id="intro"[^>]*>\s*([^<]*)<\/div>\s*<\/div>/', $html, $matches);
            if (empty($matches)) throw new InternalServerErrorException('小说主要信息解析异常');
            $novel['title'] = $matches[1];
            $novel['author'] = $matches[2];
            $novel['category'] = $matches[3];
            $novel['last_updated_time'] = strtotime($matches[4]);
            // $novel['latest_chapter'] = $matches[5];
            $novel['intro'] = $matches[6];
            $this->comment($novel['title']);
            $novel->save();
        } catch (\Throwable $e) {
            $this->error('获取小说主要信息-失败');
            $this->error($e->getMessage());
        }
        $this->comment('获取小说主要信息-结束');
        return $novel;
    }

    private function _getMenu(string $html, Novel $novel)
    {
        $this->comment('获取小说目录-开始');
        try {
            preg_match_all('/<dd[^>]*><a[^>]*href="([^"]+)"[^>]*title="([^"]+)">([^<]*)<\/a><\/dd>/', $html, $matches);
            if (empty($matches)) throw new InternalServerErrorException('小说主要信息解析异常');
            collect(array_combine($matches[2], $matches[1]))->chunk(500)->each(function (Collection $title_paths) use ($novel) {
                $exists_novel_chapter_source_urls = NovelChapter::where('novel_id', $novel->id)->pluck('source_url')->toArray();
                $chapters = [];
                $title_paths->each(function ($path, $title) use ($novel, $exists_novel_chapter_source_urls, &$chapters) {
                    $source_url = $this->getUrlFromString($path);
                    if (!in_array($source_url, $exists_novel_chapter_source_urls)) {
                        $chapter = [
                            'novel_id' => $novel->id,
                            'title' => $title,
                            'source_path' => $path,
                            'source_url' => $source_url,
                        ];
                        $this->comment($novel['title'] . ' - ' . $chapter['title']);
                        $chapters[] = $chapter;
                        unset($chapter);
                    }
                    unset($source_url);
                });
                unset($exists_novel_chapter_source_urls);
                if (!empty($chapters)) $novel->chapters()->createMany($chapters);
                unset($chapters);
            });
        } catch (\Throwable $e) {
            $this->error('获取小说目录-失败');
        }
        $this->comment('获取小说目录-结束');
        return $novel;
    }

    private function _getChapters(Novel $novel = null)
    {
        $this->comment('获取小说章节内容-开始');
        try {
            $chapters = NovelChapter::where('novel_id', $novel->id)
                ->whereNull('content')
                ->select(['id', 'source_url'])
                ->get();
            $retryTimes = 5;
            $chapter_urls = $chapters->pluck('source_url', 'id');
            while ($chapter_urls->isNotEmpty() && $retryTimes > 0) {
                $chapter_urls->chunk(50)->each(function (Collection $urls) use (&$chapter_urls, $chapters) {
                    $responses = $this->http->multiRequest('get', $urls);
                    unset($urls);
                    $this->info('本批并发请求成功数量：' . $responses['fulfilled']->count());
                    // 移除初始集合中 本次成功的集合（找出不在本次成功相应集合中的差集，为未成功响应集合）
                    $chapter_urls = $chapter_urls->diffKeys($responses['fulfilled']);
                    // dump($chapter_urls);
                    // 处理响应
                    $responses['fulfilled']->each(function (Response $response, $novel_chapter_id) use ($chapters) {
                        $html = $this->parseResponse($response);
                        unset($response);
                        if (!empty($html)) {
                            preg_match('/<div[^>]*id="content"[^>]*><!--go-->\s*(.*?)\s*<\/div>\s*<div/', $html, $matches);
                            if (!empty($matches[1])) NovelChapter::where('id', $novel_chapter_id)->update([
                                'content' => htmlspecialchars($matches[1]),
                            ]);
                        } else {
                            dd($html);
                        }
                    });
                    unset($responses);
                    sleep(1);
                });
                $retryTimes--;
                sleep(1);
            }
        } catch (\Throwable $e) {
            $this->error('获取小说章节内容-失败');
            $this->error($e->getMessage());
        }
        $this->comment('获取小说章节内容-结束');
        return true;
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

    private function parseResponse(Response $response): string
    {
        $contentType = $response->getHeaderLine('content-type');
        preg_match('/charset=([^;]+);?/', $contentType, $matches);
        $charset = strtolower($matches[1] ?? '');
        if (empty($charset)) throw new InternalServerErrorException('抓取响应异常');
        if ($charset == 'gb2312') $charset = 'gbk';
        $content = $response->getBody()->getContents();
        if ($charset !== 'utf-8') $content = mb_convert_encoding($content, 'utf-8', $charset);
        // $content = mb_convert_kana($content, 'rnaskhc', 'utf-8');
        $content = mb_convert_kana($content, 'rnas', 'utf-8');
        // file_put_contents(Tools::runtimePath('otcwuxi.html'), $content);
        // $content = file_get_contents(Tools::runtimePath('otcwuxi.html'));
        // HTML实体转换 例如：&nbsp;转空格
        return str_replace(['&nbsp;'], [''], $content);
    }


}
