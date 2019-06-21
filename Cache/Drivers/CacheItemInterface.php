<?php

namespace App\Cache\Drivers;

interface CacheItemInterface {
    /**
     * Возвращает значение кэша
     *
     * @param string $key
     * @return mixed
     */
    public function get (string $key);

    /**
     * Записывает  значение в кэш
     *
     * @param string $key
     * @param $data
     * @param int|null $interval
     * @return bool
     */
    public function set (string $key, $data, int $interval = null);

    /**
     * Удаление кеша
     *
     * @param string $key
     * @return bool
     */
    public function delete(string $key);

    /**
     * Очистка кеша
     *
     * @return bool
     */
    public function clear();
}