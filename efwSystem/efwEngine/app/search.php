<?php


namespace efwEngine\app;


use efwEngine\database;

class search
{
    static function searchUsers($data){
        database::init();
        database::setQuery("SELECT id FROM ".user::$defUserTable." WHERE name LIKE CONCAT('%',?, '%') ");
        database::setBind([$data]);
        $exec = database::exec();
        $return = [];
        foreach ($exec as $no => $user){
            $return[$no]["name"] = user::getUserData("name", $user["id"]);
            $return[$no]["url"] = "/account/profile/".$user["id"];
            $return[$no]["icon"] = "feather icon-home";
        }


        return $return;
    }
    static function searchPosts(){

    }

}