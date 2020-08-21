<?php
$state = null;
if(isset($_POST["isim"])){

    if($_POST["password"] == $_POST["passwordVerify"]) {
        stateController::$state = \efwEngine\app\user::register($_POST["username"], $_POST["username"], $_POST["isim"], $_POST["soyisim"], $_POST["email"], $_POST["password"], true);
    }
    else{
        stateController::$state  = [false, "passValidationError", "Şifreler Uyuşmuyor"];
    }
}
?>