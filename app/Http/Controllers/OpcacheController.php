<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Symfony\Component\Finder\Finder;

/**
 * Class OpcacheController.
 */
class OpcacheController extends BaseController
{
    /**
     * Get config values.
     * @return \Illuminate\Http\JsonResponse
     */
    public function config()
    {
        if (function_exists('opcache_get_configuration')) {
            return response()->json(['result' => opcache_get_configuration()]);
        }
    }

    /**
     * Get status info.
     * @return \Illuminate\Http\JsonResponse
     */
    public function status()
    {
        if (function_exists('opcache_get_status')) {
            return response()->json(['result' => opcache_get_status()]);
        }
    }

    /**
     * Reset the OPCache.
     * @return \Illuminate\Http\JsonResponse
     */
    public function reset()
    {
        if (function_exists('opcache_reset')) {
            return response()->json(['result' => opcache_reset()]);
        }
    }

    /**
     * 获取待优化文件集合
     * @return \Illuminate\Support\Collection
     */
    private function getOptimizedFiles()
    {
        return collect(Finder::create()->in(config('opcache.directories'))
            ->name('*.php')
            ->ignoreUnreadableDirs()
            ->notContains('#!/usr/bin/env php')
            ->exclude(config('opcache.exclude'))
            ->files()
            ->followLinks());
    }

    /**
     * Clear the OPCache.
     * @return \Illuminate\Http\JsonResponse
     */
    public function clear()
    {
        if (function_exists('opcache_invalidate')) {
            $invalidated = 0;
            // 获取这些路径中的文件
            $files = $this->getOptimizedFiles();
            // 优化文件
            $files->each(function ($file) use (&$invalidated) {
                try {
                    if (opcache_is_script_cached($file)) {
                        // 废除脚本缓存，刷新脚本缓存
                        // $invalidate = opcache_invalidate($file, true);
                        $invalidate = opcache_invalidate($file);
                        if ($invalidate) $invalidated++;
                    }
                } catch (\Exception $e) {
                }
            });
            return response()->json(['result' => [
                'total_files_count' => $files->count(),
                'invalidated_count' => $invalidated,
            ]]);
        }
    }

    /**
     * Compile.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function compile()
    {
        if (!ini_get('opcache.dups_fix')) {
            return response()->json(['result' => ['message' => 'opcache.dups_fix 必须启用']]);
        }
        if (function_exists('opcache_compile_file')) {
            $compiled = 0;
            // 获取这些路径中的文件
            $files = $this->getOptimizedFiles();
            // 优化文件
            $files->each(function ($file) use (&$compiled) {
                try {
                    if (!opcache_is_script_cached($file)) {
                        $res = opcache_compile_file($file);
                        if ($res) $compiled++;
                    }
                } catch (\Exception $e) {
                }
            });
            return response()->json(['result' => [
                'total_files_count' => $files->count(),
                'compiled_count' => $compiled,
            ]]);
        }
    }
}
