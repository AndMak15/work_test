<?php

namespace App\Cache;

use App\Cache\Drivers\StaticCache;
use App\Cache\Drivers\FileCache;
use App\Cache\Drivers\CacheItemInterface;

/**
 * Class Cache
 */
class Cache implements CacheInterface {
    /**
     * Список используемых хранилищ кеша
     *
     * @var $drivers CacheItemInterface[]
     */
    private $drivers = [];

    /**
     * Cache constructor.
     */
    public function __construct() {
        $this->drivers[] = new StaticCache();
        $this->drivers[] = new FileCache();
    }

    /**
     * Добавить хранилище в кеш.
     *
     * @param CacheItemInterface $driver
     */
    public function addDriver(CacheItemInterface $driver) {
        $this->drivers[] = $driver;
    }

    /**
     * Очистить список хранилищ
     *
     * @throws CacheException
     */
    public function clearDrivers() {
        foreach ($this->drivers as $driver) {
            if ( !$driver->clear() ) {
                throw new CacheException('Driver cache not cleared: ' . get_class($driver));
            }
            unset($driver);
        }
    }

    /**
     * Получить значение из кеша
     *
     * @param string $key
     * @return null
     */
    public function get(string $key)
    {
        foreach ($this->drivers as $driver) {
            $val = $driver->get($key);
            if (!is_null($val)) {
                return $val;
            }
        }
        return null;
    }

    /**
     * Записать значение в кеш
     *
     * @param string $key
     * @param $value
     * @param int $ttl
     * @throws CacheException
     */
    public function set(string $key, $value, int $ttl = 3600) {
        foreach ( $this->drivers as $driver) {
            if (!$driver->set($key, $value, $ttl)) {
                throw new CacheException('Cache not work: ' . get_class($driver));
            }
        }
    }

}

