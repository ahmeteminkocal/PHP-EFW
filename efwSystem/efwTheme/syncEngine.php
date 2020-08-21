<?php


namespace efwTheme;


use efwEngine\app\notifications;
use efwEngine\app\posts;
use efwEngine\app\user;

class syncEngine
{
    private static $states = [

    ];

    static function regState($state, $function)
    {

        self::$states[] = $state;
        self::$states[$state][] = $function;

    }

    static function onState($state, $data)
    {
        if (isset(self::$states[$state])) {
            foreach (self::$states[$state] as $function)
                $function($data);
        } else {
            throw new \Exception("Böyle bir aksiyon durumu yok ($state)", 1000001);
        }
    }

    static function prepare()
    {

        self::defaultStates();
    }

    static function defaultStates()
    {
        self::$states["addLikePost"][] = function ($data) {
            $owner = posts::info("sender", $data[0]);

            if ($owner != user::getCurrUserID()) {
                notifications::addNotification($owner, NOTIFICATION_TYPE_INFO, "", ["itemID" => $data[0], "context" => "addLikePost", "user" => $owner, "sender" => $data[1]], $data[1]);
            }

        };
        self::$states["delLikePost"][] = function ($data) {
            $owner = posts::info("sender", $data[0]);
            notifications::delNotificationMeta("", $owner, "addLikePost", user::getCurrUserID());

        };
        self::$states["addCommentPost"][] = function ($data) {
            $owner = posts::info("sender", $data[0]);

            if ($owner != user::getCurrUserID()) {
                notifications::addNotification($owner, NOTIFICATION_TYPE_INFO, "", ["itemID" => $data[0], "context" => "addCommentPost", "user" => $owner, "sender" => $data[1]], $data[1], $data[3]);
            }
            };
        self::$states["delComment"][] = function ($data) {
            $owner = posts::info("sender", $data[0]);
                $liker = user::getUserRealNameAndSurname($data[1]);
                notifications::addNotification($owner, NOTIFICATION_TYPE_INFO, "", []);
        };
    }

}