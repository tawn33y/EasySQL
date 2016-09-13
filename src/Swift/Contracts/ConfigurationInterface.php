<?php

namespace Tawn33y\Swift\Contracts;

interface ConfigurationInterface
{
    /**
     * Get the configuration value from the store.
     *
     * @param      $key
     * @param null $default
     *
     * @return mixed|null
     */
    public function get($key, $default = null);
}
