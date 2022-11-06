<?php

if (!function_exists('lang_path_with_prefix')) {
    /**
     * @param string $path
     * @return string
     */
    function lang_path_with_prefix($path = '', $prefix = 'Frontend/')
    {
        return resource_path($prefix . 'lang' . ($path !== '' ? DIRECTORY_SEPARATOR . $path : ''));
    }
}
