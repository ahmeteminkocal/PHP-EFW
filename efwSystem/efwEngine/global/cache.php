<?php


namespace efwEngine;



use efwTheme\engine;
include "interfaces/cacheInterface.php";
class cache implements cacheInterface
{

    public static $expSeconds = 15000;
    private static array $_instance = [];

    public static function getInstance() : cache {
        if(!self::$_instance[0]) {
            self::$_instance[0] = new self();
        }
        return self::$_instance[0];
    }

    static function get($key){
        return apcu_fetch($key);
    }
    static function cache($id, $data){
        if(apcu_exists($id)) return apcu_fetch($id);

        apcu_add($id, $data, self::$expSeconds);
        return $data;
    }
    static function add($key, $data){
        apcu_add($key, $data, self::$expSeconds);
        return $data;
    }
    static function clearCache(){
        apcu_clear_cache();
    }
    static function on($trigger, $item){
        self::clearCache();
    }
    static function exists($id){return apcu_exists($id);}

    static function deleteUserCaches($userID){
        /*
        $todel = [
            "profile-$userID",
            $userID-"-profilePosts"
        ];
        */
    }
}