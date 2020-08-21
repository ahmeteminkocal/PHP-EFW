<?php

namespace efwEngine;

interface cacheInterface
{
    public static function getInstance(): cache;

    static function get($key);

    static function cache($id, $data);

    static function add($key, $data);

    static function clearCache();

    static function exists($id);
}