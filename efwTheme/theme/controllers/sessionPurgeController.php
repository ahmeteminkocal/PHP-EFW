<?php

if(isset($_POST["end"])){
    if($_POST["end"] == \efwEngine\app\user::getSessionID()){
        \efwEngine\app\user::activeSessions(\efwTheme\router::getParameters()[0], $_POST["end"]);
        \efwEngine\app\system::redirect("/account/login/");
    }else
    \efwEngine\app\user::activeSessions(\efwTheme\router::getParameters()[0], $_POST["end"]);

}
if(isset($_POST["endall"])){
    if(\efwTheme\router::getParameters()[0] == \efwEngine\app\user::getCurrUserID()){
        \efwEngine\app\user::activeSessions(\efwTheme\router::getParameters()[0], true);

        \efwEngine\app\system::redirect("/account/login/");

    }else
    \efwEngine\app\user::activeSessions(\efwTheme\router::getParameters()[0], true);

}
?>