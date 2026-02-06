<?php

if (!function_exists('setting')) {
    /**
     * Obtener un valor de configuración desde la base de datos
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function setting(string $key, $default = null)
    {
        return \App\Models\Setting::get($key, $default);
    }
}
