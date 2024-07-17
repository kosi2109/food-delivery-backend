<?php

if (!function_exists('getFullImageUrl')) {
    function getFullImageUrl($path)
    {
        if (empty($path)) {
            return null;
        }

        $appUrl = rtrim(config('app.url'), '/');
        return $appUrl . '/' . ltrim($path, '/');
    }
}