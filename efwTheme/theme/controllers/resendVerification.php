<?php
if(isset($_POST["email"])){
    if(\efwEngine\app\recaptcha::checkCaptcha()){
        stateController::setState(true, "Gönderildi");
        $userID = \efwEngine\app\user::getUserDataEmail("id", $_POST["email"]);
        $verified = \efwEngine\app\user::getUserData("email_validated", $userID);
        if($verified != 1) {
            $userName = \efwEngine\app\user::getUserData("username", $userID);
            \efwEngine\app\user::createValidationMail($userName, $_POST["email"]);
            stateController::setState(true, "Mail tekrar gönderildi!");
        }else{
            stateController::setState(false, "Mailiniz zaten onaylı.");
        }
    }else{
        stateController::setState(false, "Lütfen robot olmadığınızı doğrulayın.");
    }
}


?>