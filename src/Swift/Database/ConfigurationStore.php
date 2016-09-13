<?php

namespace Tawn33y\Swift\Database;

use Tawn33y\Swift\Contracts\ConfigurationInterface;

class ConfigurationStore implements ConfigurationInterface
{

    protected $configuration;

    /**
     * ConfigurationStore constructor.
     *
     * @param array $configuration
     */
    public function __construct($configuration = [])
    {
        $this->configuration = $configuration;
    }

    /**
     * Get the configuration value from the store.
     *
     * @param      $key
     * @param null $default
     *
     * @return mixed|null
     */
    public function get($key, $default = null)
    {
        return isset($this->configuration[$key]) ? $this->configuration[$key] : $default;
    }
}
