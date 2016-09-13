<?php

namespace Tawn33y\Swift\Contracts;

interface ConfigurationInterface
{
    public function get($key, $default = null);
}
