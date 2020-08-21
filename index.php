<?php
use efwEngine\database;
ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);
define("SYS_DEBUG"
    , true);
include __DIR__."/efwSystem/efwEngine/global/startup.php";
\efwEngine\startup::init();


\efwTheme\router::addressExecutor( \efwTheme\router::getAddress());




