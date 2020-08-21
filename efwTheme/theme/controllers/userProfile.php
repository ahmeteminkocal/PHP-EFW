<?php
$parameters = \efwTheme\router::getParameters()[0];
if(count(\efwTheme\router::getParameters()) >= 2) {
    \efwTheme\engine::send404();
}
$explode = explode("-", $parameters);
$userID = $explode[0];

if($userID == "") \efwEngine\app\system::redirect("/account/profile/".\efwEngine\app\user::getUserSlug());
$userSlug = \efwEngine\app\user::getUserSlug($userID);
if($parameters != $userSlug) \efwEngine\app\system::redirect("/account/profile/". $userSlug);
$pageID = "profile-$userID";

if(pageController::checkMassData($pageID)){
    pageController::getMassData($pageID);
}else {
    $name = \efwEngine\app\user::getUserRealNameAndSurname($userID, false);
    $namePlain = \efwEngine\app\user::getUserRealNameAndSurname($userID);
    $regdata = explode(".", \efwEngine\app\user::getUserData("regdate", $userID))[0];
    $profileDatum = \efwEngine\app\user::getProfileDatum($userID);
    $userProfilePicture = \efwEngine\app\user::getUserProfilePicture($userID);

    $cover = $profileDatum["coverPicture"];
    $about = $profileDatum["about"];
    /*
     * Önbelleklenecek bilgiler
     */
    pageController::setInfo("about", $about);
    pageController::setInfo("coverPhoto", $cover);
    pageController::setInfo("regdate", $regdata);
    pageController::setInfo("website", $profileDatum["website"]);
    pageController::setInfo("profileImage", $userProfilePicture);
    pageController::setInfo("nameSurname", $name);
    pageController::setInfo("nameSurnamePlain", $namePlain);
    pageController::setMassData($pageID);
    /*
     * bundan sonrası önbelleklenmeyecek
     */
}
pageController::setInfo("userID", $userID);
if(isset($_POST["postText"]) || isset($_FILES["image"])){
    $address = "profile-".$userID;
    \efwEngine\app\posts::addPost($_POST["postText"], $address, $_FILES["image"]);
}
pageController::setInfo("postsData", \efwEngine\app\posts::getPostsUser($userID));
?>