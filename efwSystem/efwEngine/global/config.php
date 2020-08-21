<?php


namespace efwEngine;


use efwTheme\engine;

class config
{
    protected static $config = [];
    static function set($conf, $data){
        self::$config[$conf] = $data;
    }
    static function get($conf){
        return self::$config[$conf];
    }
    static function regSite($siteAddress, $siteName, $routes, $cdnBasedir, $cdnURL = "/", $logo = "", $wssServer = "", $wssServerKey = "", $ecryptionKey = ""){
    self::$config["sites"][$siteAddress] = ["address" => $siteAddress, "name" => $siteName, "routes" => $routes, "cdnURL" => $cdnURL, "logo" => $logo, "wssSerever" => $wssServer, "wssServerKey" => $wssServerKey, "cdnBasedir" => $cdnBasedir,
     "encryptionKey" => $ecryptionKey];
        engine::registerSite($siteAddress, $cdnURL, $siteName, count(self::$config["sites"]));

    }
    static function getSites(){
        return self::$config["sites"];
    }
    static function getCurrentSiteConfig(){

               return self::$config["sites"][engine::getCurrentDomain(false)]["routes"];


    }
    static function setDB($server, $user, $pass, $prefix = DB_PREFIX.""){
        self::$config["db"]["server"] = $server;
        self::$config["db"]["user"] = $user;
        self::$config["db"]["pass"] = $pass;
        self::$config["db"]["prefix"] = $prefix;
    }
    static function getDB(){
        return new class extends config{
            static function getUser(){
                return parent::$config["db"]["user"];
            }
            static function getPass(){
                return parent::$config["db"]["pass"];
            }
            static function getServer(){
                return parent::$config["db"]["server"];
            }
            static function getPrefix(){
                return parent::$config["db"]["prefix"];
            }
        };
    }
    static function setMail($server, $user, $pass, $sender, $name, $port, $type = 'ssl', $replyTo = "", $replyToName = ""){
        self::$config["mail"]["server"] = $server;
        self::$config["mail"]["user"] = $user;
        self::$config["mail"]["pass"] = $pass;
        self::$config["mail"]["name"] = $name;
        self::$config["mail"]["sender"] = $sender;
        self::$config["mail"]["port"] = $port;
        self::$config["mail"]["type"] = $type;
        self::$config["mail"]["replyTo"] = $type;
        self::$config["mail"]["replyToName"] = $type;
    }
    static function setMailDebug($mode){
        self::$config["mail"]["debug"] = $mode;
    }
    static function getMailServer(){
        return self::$config["mail"]["server"];
    }
    static function getMailUser(){
        return self::$config["mail"]["user"];

    }
    static function getMailReply(){
        return self::$config["mail"]["replyTo"];

    }
    static function getMailReplyName(){
        return self::$config["mail"]["replyToName"];

    }
    static function getMailPass(){
        return self::$config["mail"]["pass"];

    }
    static function getMailName(){
        return self::$config["mail"]["name"];

    }
    static function getMailSender(){
        return self::$config["mail"]["sender"];

    }
    static function getMailPort(){
        return self::$config["mail"]["port"];
    }
    static function getMailType(){
        return self::$config["mail"]["type"];
    }
    static function getMailDebug(){
        return self::$config["mail"]["debug"] ?? 0;
    }
}