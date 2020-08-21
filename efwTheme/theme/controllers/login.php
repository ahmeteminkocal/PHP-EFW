<?php

ob_start();

if(isset($_POST["username"])) {
    stateController::$state = \efwEngine\app\user::login($_POST["username"], $_POST["password"]);
    if(stateController::$state[0]) {
        if($_POST["beniHatirla"] === "on"){
                setcookie("session", \efwEngine\app\user::getSessionID(),  time()+(3600*60*60), "/", \efwTheme\engine::getCurrentDomain(false));
        }else{
            setcookie("session", "", null,  "/", \efwTheme\engine::getCurrentDomain(false));
        }
        \efwEngine\app\system::redirect("/");
    }
}

ob_end_flush();
?>