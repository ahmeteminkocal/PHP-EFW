<?php


namespace efwEngine\app;


use efwEngine\database;
use efwTheme\engine;

class tempkeys
{
    static function newKey($keyName, $key = false, $valMinutes = 3, $len = 300){
        if(!$key)
        $key = engine::generateRandomString(300);
        database::massData();
        database::setQuery("INSERT INTO ".DB_PREFIX."tempKeys (tempKey, data, validUntil) VALUES(?, ?, DATE_ADD(current_timestamp, INTERVAL ? MINUTE))");
        database::setBind([$keyName, $key, $valMinutes])->exec();
        return $key;
    }
    static function checkAndDelKey($keyName, $reqVal = false){
        database::massData();
        $exec = database::select(DB_PREFIX . "tempKeys")->where(["tempKey"], $keyName)->exec(2);
        if(isset($exec[0])){
            if(!$reqVal) {
                database::massData();

                database::delete(DB_PREFIX . "tempKeys")->where(["tempKey"], $keyName)->exec();
            return true;
            }
            else if($exec["data"] == $reqVal){
                database::massData();

                database::delete(DB_PREFIX."tempKeys")->where(["tempKey"], $keyName)->exec();
                return true;
            }else {
                return false;
            }
        }
        return false;
    }
}