<?php
use efwEngine\database;
define("SYS_DEBUG", true);
include __DIR__."/efwSystem/efwEngine/global/startup.php";
\efwEngine\startup::init();


\efwTheme\router::addressExecutor( \efwTheme\router::getAddress());




