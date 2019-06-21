<?php

namespace App\Cache;

interface CacheInterface {
    public function get (string $key);
    public function set (string $key, $value, int $ttl = 3600);
}