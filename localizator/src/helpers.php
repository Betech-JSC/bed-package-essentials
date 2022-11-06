<?php

if (!function_exists('lang_path')) {
    /**
     * @param string $path
     * @return string
     */
    function lang_path($path = '', $prefix = 'Frontend/')
    {
        return resource_path($prefix . 'lang' . ($path !== '' ? DIRECTORY_SEPARATOR . $path : ''));
    }
}
