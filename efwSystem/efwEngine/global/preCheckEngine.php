<?php


namespace efwEngine;


use efwEngine\app\system;
use efwEngine\app\user;
use efwTheme\router;

class preCheckEngine
{
    static function checkRoute($checkData){
        $state = true;
        foreach ($checkData as $filter => $val){
            switch ($filter){
                case "permissions":
                    $state = roles::reqPerm($val);
                    break;
                case "login":
                    $state = user::checkLogin();
                    if($val == "forward" && $state == false) system::redirect("/account/login/");
                    break;
            }
        }
        return $state;
    }
}