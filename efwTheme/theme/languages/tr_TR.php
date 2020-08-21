<?php

class language {

    static array $langData = [

      "welcome" => "Hoş Geldin",
      "register" => "Kayıt Ol",
      "login" => "Giriş Yap"

    ];

    static function getLanguageData(){
        return self::$langData;
    }
}