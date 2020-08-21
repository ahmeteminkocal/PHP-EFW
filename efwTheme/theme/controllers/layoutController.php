<?php


class layoutController
{
    static $cache;
    static function getThemeLayout()
    {
        \efwEngine\database::init();
        \efwEngine\database::setPdo(["db" =>  DB_PREFIX."general"]);
        if (isset(self::$cache["themeLayout"])) return self::$cache["themeLayout"];
        if (\efwEngine\app\user::checkLogin()) {
            if (!isset(self::$cache["themeLayout"])) {
                $user = \efwEngine\app\user::getCurrUserID();
                $exec = \efwEngine\database::select(DB_PREFIX."users_theme", ["layout"])->where(["userID"], $user)->exec();
                if(isset($exec[0]))
                $layout = $exec[0][0];
                else{
                    $layout = "";
                }
                self::$cache["themeLayout"] = $layout;
                return $layout;
            }


        }
    }
    static function getFooterStyle(){

        return 'footer-static';
    }
    static function getNavbarStyle(){
        return 'navbar-floating';

    }

    static function bodyStyle(){
        echo self::getThemeLayout()." ".self::getFooterStyle()." ".self::getNavbarStyle();
    }
    static function setThemeLayout($layoutType){
        if(!\efwEngine\app\user::checkLogin()) die();

        $expected = ["semi-dark-layout", "dark-layout", ""];
        if(in_array($layoutType, $expected)){
                $check = \efwEngine\database::select(DB_PREFIX."users_theme")->where(["userID"], \efwEngine\app\user::getCurrUserID())->exec();
                if(isset($check[0])){
                    \efwEngine\database::update(DB_PREFIX."users_theme", "layout", $layoutType)->where(["userID"], \efwEngine\app\user::getCurrUserID())->exec();
                    return true;
                }else{
                    \efwEngine\database::insert(DB_PREFIX."users_theme", ["userID", "layout"], \efwEngine\app\user::getCurrUserID(), $layoutType)->exec();
                }
        }
    }
    static function setTheme($type, $data){
        switch ($type){
            case "themeLayout":
                return self::setThemeLayout($data);
                break;
        }
    }
    static function setChecked($type, $expected){
        switch ($type){
            case "themeLayout":
                if(self::getThemeLayout() == $expected){
                    echo 'checked';
                }else{
                    if(\efwEngine\app\user::getCurrUserID() == 1){
                        echo self::getThemeLayout();
                        echo $expected;
                    }
                }
                break;
        }
    }
}

?>