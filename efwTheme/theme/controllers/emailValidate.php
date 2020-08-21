<?php

use efwEngine\app\onesignal;
use efwEngine\app\user;

$params = \efwTheme\router::getParameters();

$user = $params[0];
$key = $params[1];

function hatali()
{
    echo "<h1>Anahtar geçersiz</h1>";
}

if (\efwEngine\app\user::userExists($user)) {
    $owner = \efwEngine\app\user::getUserID($user);
    $syskey = \efwEngine\app\system::getMeta("activation", $owner);
    if ($syskey == $key) {
        $o = new onesignal();
        $o->sendUsers([1, 63], "Hesap doğrulama", "$user (" . user::getUserRealNameAndSurname($owner) . ") hesabını doğruladı.");
        \efwEngine\app\system::delMeta("activation", intval($owner));
        \efwEngine\app\user::updateUser("email_validated", true, $owner);
        \efwEngine\app\user::loginProcedure($user);
        header("Location: /");
    } else {
        hatali();
    }
} else {
    hatali();

}
?>