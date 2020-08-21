<?php


if(\efwEngine\app\user::checkLogin() == false){
    echo "Giriş Yap";
\efwEngine\app\system::redirect("/account/login/");
}else{
}


?>