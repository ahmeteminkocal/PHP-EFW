<?php


use efwEngine\config;

$routes = [
    "x" => ["controller" => "logout"],

    "sa/" => __DIR__."/efwTheme/theme/pages/test.php"
];
if(SYS_DEBUG){
    \efwEngine\cache::clearCache(); //enable during development
    ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);

}else{
    ini_set('display_errors', 0); ini_set('display_startup_errors', 0); error_reporting(0);

}
\efwEngine\config::setMail("smtp.gmail.com", "x@gmail.com", "", "x@gmail.com", "x", "465");
\efwEngine\config::regSite("localhost", "X Site", $routes, "/var/www/efwCdn/","https://www.x.com/xcdn/", "https://x.com/logo.png", "wss.x.com/xcom/", "12341234");
\efwEngine\config::setDB("localhost","test", "1234567");

\efwEngine\cdn::$cdnBaseDIR = config::getCurrentSiteConfig()["cdnBasedir"];