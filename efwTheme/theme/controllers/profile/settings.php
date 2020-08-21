<?php
$data = ["name", "surname", "email", "username"];
foreach ($data as $key => $datum){
    $data[$key] = htmlspecialchars($datum);
}
if(isset($_GET["delPimg"])){
    if(isset($_SERVER["HTTP_REFERER"])) {
        $var = \efwTheme\router::getAddress();
        if ($var === "/account/profile/settings/") {
            \efwEngine\app\user::changeProfileImage("");
            \efwEngine\app\system::redirect("/account/profile/settings/");
        }
    }
}

foreach ($data as $dataToSet){
    if(isset($_POST[$dataToSet])){
        \efwEngine\app\user::updateUser($dataToSet, $_POST[$dataToSet]);
    }
}
if(isset($_FILES["profileImage"])){
   $state =  \efwEngine\app\user::changeProfileImage($_FILES);
    if(!$state[0]){
        switch ($state[1]){
            case "extension":
                pageController::setInfo("errMessage", "Yalnızca PNG, GIF, JPG ve JPEG formatı desteklenmektedir.");
                break;
        }
    }
}
$data2 = ["website", "about", "phone"];
foreach ($data as $dataToSet){
    if(isset($_POST[$dataToSet])){
        \efwEngine\app\user::updateUser($dataToSet, $_POST[$dataToSet]);
    }
}
pageController::setInfo("name", \efwEngine\app\user::getUserData("name"));
pageController::setInfo("surname", \efwEngine\app\user::getUserData("surname"));
pageController::setInfo("email", \efwEngine\app\user::getUserData("email"));
pageController::setInfo("username", \efwEngine\app\user::getUserData("username"));
pageController::setInfo("email_validated", \efwEngine\app\user::getUserData("email_validated"));
pageController::setInfo("profileImage", \efwEngine\app\user::getUserProfilePicture());
$profileData = \efwEngine\app\user::getProfileDatum(\efwEngine\app\user::getCurrUserID());
pageController::setInfo("website", $profileData["website"]);
pageController::setInfo("about", $profileData["about"]);
pageController::setInfo("phone", $profileData["phone"]);

if(isset($_POST["password"])){

    if($_POST["password"] == $_POST["con-password"]){
        if(\efwEngine\app\user::verifyPassword($_POST["old-password"], \efwEngine\app\user::getUserData("password"))) {
            \efwEngine\app\user::updateUser("password", \efwEngine\app\user::passwordEncrypt($_POST["password"]));


        }
    }
}

?>