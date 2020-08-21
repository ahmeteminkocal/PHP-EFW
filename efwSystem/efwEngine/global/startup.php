<?php


namespace efwEngine;



use efwTheme\engine;
use efwTheme\router;
use efwTheme\syncEngine;

define("POSTS_NOTAG", []);
define("POSTS_ALLTYPE", "");
class startup
{
    static function loadModule(...$modname)
    {
        foreach ($modname as $mod)
            require_once "efwSystem/".str_replace("\\", "/", $mod).".php";
    }
    static function init(){
        session_start();
        include "efwSystem/efwTheme/engine.php";
        include "efwSystem/efwTheme/router.php";
        include "efwSystem/efwEngine/global/cache.php";
        include "efwSystem/efwEngine/global/cdn.php";
        include "efwSystem/efwEngine/global/database.php";
        include "efwSystem/efwEngine/app/user.php";
        include "efwSystem/efwEngine/global/roles.php";
        include "efwSystem/vendor/autoload.php";
        include "setConfig.php";
        router::setAddresses();
        \efwTheme\router::addAddress("account/logout/", "controller", "logout");
        \efwEngine\app\user::init();
        spl_autoload_register("efwEngine\\startup::loadModule");

        \efwEngine\app\featureSet::autoRegister();
        syncEngine::prepare();

        /*
         *
         * Önbelleği geliştirme esnasında devre dışı bırak
         */
    }

}