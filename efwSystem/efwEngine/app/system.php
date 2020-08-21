<?php


namespace efwEngine\app;


use efwEngine\database;

class system
{

    private static $defMetaTable = DB_PREFIX."meta";

    /*
     * Metalar
     */
    static function createMeta($meta, $data, $owner = 0){
        database::setPdo(DB_CONFIGS["system"]);
        database::insert(self::$defMetaTable, ["meta", "data", "owner"], $meta, $data, $owner)->exec();

    }
    static function getMeta($meta, $owner = 0){
        database::setPdo(DB_CONFIGS["system"]);

        $var = database::select(self::$defMetaTable, ["data"])->where(["owner", "meta"], $owner, $meta)->orderBy("id", "desc")->exec()[0][0];
        return $var;
    }
    static function delMeta($meta, $owner = 0){
        database::setPdo(DB_CONFIGS["system"]);
        return database::delete(self::$defMetaTable)->where(["owner", "meta"], $owner, $meta)->exec();
    }

    static function redirect($url){
        header("Location: $url");
        die;
    }
    static function getUserIP(){
        return $_SERVER["HTTP_CF_CONNECTING_IP"] ?? $_SERVER["REMOTE_ADDR"];
    }
}