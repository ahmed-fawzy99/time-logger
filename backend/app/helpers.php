<?php

if (! function_exists('get_env_port')) {
    function get_env_port($with_colon = true): string
    {
        if (env('OCTANE_PORT')) {
            return ($with_colon ? ':' : '').env('OCTANE_PORT');
        }
        if (env('SERVER_PORT')) {
            return ($with_colon ? ':' : '').env('SERVER_PORT');
        }

        return '';
    }
}
