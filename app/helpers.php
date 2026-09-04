<?php

if (!function_exists('formatBytes')) {
    function formatBytes($bytes, $precision = 2)
    {
        if ($bytes <= 0) return '0 B';
        $base = log($bytes, 1024);
        $suffixes = array('B', 'KB', 'MB', 'GB', 'TB');
        return round(pow(1024, $base - floor($base)), $precision) . ' ' . $suffixes[floor($base)];
    }
}

if (!function_exists('uppercase')) {
    function uppercase($string)
    {
        return strtoupper($string ?? '');
    }
}
