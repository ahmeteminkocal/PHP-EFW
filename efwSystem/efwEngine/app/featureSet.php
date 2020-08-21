<?php


namespace efwEngine\app;


class featureSet
{
    private static $siteFeatures = [];
    static function autoRegister(){

    }
    static function checkFunction($site, $functionName){
        return isset(self::$siteFeatures[$site][$functionName]);
    }
    static function regFunction($site, $functionName)
    {
        self::$siteFeatures[$site][$functionName] = true;
    }
}