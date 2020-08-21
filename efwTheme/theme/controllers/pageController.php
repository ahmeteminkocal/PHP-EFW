<?php


class pageController
{
        static $info = [];
        static function getMassData($pageID){
            $key = "pageControllerData-$pageID";
            self::$info =  \efwEngine\cache::get($key);
        }
        static function checkMassData($pageID){
            $key = "pageControllerData-$pageID";
            return \efwEngine\cache::exists($key);
        }
        static function setMassData($pageID){
            $key = "pageControllerData-$pageID";
            \efwEngine\cache::add($key, self::$info);
        }
        static function setInfo($info, $val){
            self::$info[$info] = $val;
        }
        static function getInfo($info){

            return self::$info[$info] ?? null;
        }
}

?>