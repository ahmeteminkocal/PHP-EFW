<?php
use efwEngine\database;

include "efwSystem/efwEngine/global/startup.php";
\efwEngine\startup::init();
if(\efwEngine\app\user::getCurrUserID() == 1) {
    ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);
}
\efwEngine\cli::run();