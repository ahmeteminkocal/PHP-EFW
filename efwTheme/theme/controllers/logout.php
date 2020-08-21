<?php


\efwEngine\app\user::logout();
if(!isset($_GET["refer"]))
\efwEngine\app\system::redirect("/account/login/");
else{
    \efwEngine\app\system::redirect($_GET["refer"]);

}

?>