<?php


namespace eminEngine;


class wscommands extends wscli
{
    public static $userID = "";
    static function changeUserDivText($divID, $text, $user = ""){
        if($user == "") $user = self::$userID;
        $command = <<<JS
        $("#$divID").text("$text");
        
        JS;
        wscli::runJSonUser($user, $command);


    }



}