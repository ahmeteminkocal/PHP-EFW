<?php

namespace efwEngine\app;

use efwEngine\database;
use seregazhuk\PinterestBot\Api\Providers\Comments;

define("NOTIFICATION_TYPE_INFO", 0);
define("NOTIFICATION_TYPE_ALERT", 1);
define("NOTIFICATION_TYPE_WARNING", 2);
define("NOTIFICATION_TYPE_SUCCESS", 3);
define("NOTIFICATION_TYPE_ERROR", 4);
define("NOTIFICATION_TYPE_PRIMARY", 5);

define("NOTIFICATION_STATE_UNREAD", 0);
define("NOTIFICATION_STATE_READ", 1);
define("NOTIFICATION_STATE_OPEN", 2);

class notifications
{
    static $defNotifTable = DB_PREFIX . "notifications";
    static $defNotifDoneTable = DB_PREFIX . "notifications_donelist";
    static $defNotifMetaTable = DB_PREFIX . "notifications_meta";

    static function db(): database
    {
        database::setPdo(DB_CONFIGS["massdata"]);
        return new database();
    }

    static function setReadState($userid, $state = true, $notifNo = "all")
    {
        self::db()::update(self::$defNotifTable, "readState", $state)->where(["user"], $userid)->exec();
        return true;
    }

    static function contextFinder($data)
    {
        switch ($data["context"]) {

            case "notice":
                $postID = $data["itemID"];
                $isExits = posts::db()::select(notifications::$defNotifMetaTable, ["COUNT(id), notifID"])->where(["context", "itemID", "userID"], $data["context"], $postID, $data["user"])->exec()[0];
                if ($isExits[0]) {
                    return ["theme" => "notice", "data" => ["times" => ($isExits[0] + 1)], "notifID" => $isExits["notifID"]];
                } else {
                    return ["theme" => "notice", "data" => ["message" => $data["message"]]];
                }
                break;
        }
    }

    static function notificationParser($data, $affector = null, $hint = null)
    {/*
        $contexts = [
            "addCommentPost" => ["init" => "addComment", "next" => "addCommentB", "rare" => "addComment"],
            "addLikePost" => ["init"=> "addLike", "next" => "addLikeB", "rare" => "addLike"],
            "friendShipReq" => [],
            "newMessage" => []
        ];
        */
        $notifThemes = [
            "notice" => ["header" => "Duyuru", "body" => "{{message}}"],
        ];
        $contextTheme = self::contextFinder($data);
        $theme = $notifThemes[$contextTheme["theme"]];
        $themeData = $contextTheme["data"];
        $body = $theme["body"];
        preg_match_all('/\{\{(.*?)\}\}/i', $body, $regs, PREG_PATTERN_ORDER);
        for ($i = 0; $i < count($regs[1]); $i++) {
            $found = $regs[1][$i];
            $toBeReplaced = '{{' . $found . '}}';
            $body = str_replace($toBeReplaced, $themeData[$found], $body);
        }
        if ($contextTheme["notifID"] == null) return ["body" => $body, "header" => $theme["header"]]; else {
            self::addNotificationMeta($contextTheme["notifID"], $data["itemID"], $data["user"], $data["context"], $affector, $hint);

            self::updateNotification("text", $body, $contextTheme["notifID"]);
            self::updateNotification("readState", 0, $contextTheme["notifID"]);

            return true;
        }

    }

    static function updateNotification($info, $data, $notifID)
    {
        self::db()::update(self::$defNotifTable, $info, $data)->where(["id"], $notifID)->exec();
    }

    static function getUnreadNotifCount($userid)
    {
        return self::db()::select(self::$defNotifTable, ["COUNT(id)"])->where(["user", "readState"], $userid, 0)->exec()[0][0];
    }

    static function findNotification($user, $type, $link)
    {
        return self::db()::select(self::$defNotifTable, ["id"])->where(["sender", "type", "link"], $user, $type, $link)->exec()[0][0];
    }

    static function addNotification($userid, $notificationType, $link = "", $contextData = [], $affector = null, $hint = null)
    {
        $notificationParser = notifications::notificationParser($contextData, $affector, $hint);
        if ($notificationParser !== true) {
            if (!self::checkDoneList($contextData["itemID"], $contextData["context"], $affector)) {

                self::db()::insert(self::$defNotifTable, ["user", "type", "header", "text", "link"], $userid, $notificationType, $notificationParser["header"], $notificationParser["body"], $link)->exec();
                $o = new onesignal();
                $o->sendUsers([$userid], $notificationParser["header"], $notificationParser["body"]);
                if (isset($contextData["itemID"])) {
                    self::addNotificationMeta(database::getLastInsertID(), $contextData["itemID"], $userid, $contextData["context"], $affector, $hint);
                    self::addDoneList($contextData["itemID"], $contextData["context"], $affector);
                }
            }
        }
    }

    static function addNotificationMeta($notifID, $itemID, $userID, $context, $affector = null, $hint = null)
    {
        if ($hint === null) $hint = "";
        self::db()::insert(self::$defNotifMetaTable, ["notifID", "itemID", "userID", "context", "affector", "hint"], $notifID, $itemID, $userID, $context, $affector, $hint)->exec();
    }

    static function delNotificationMeta($itemID, $userID, $context, $affector = null, $hint = "")
    {
        self::db()::delete(self::$defNotifMetaTable)->where(["itemID", "userID", "context", "affector", "hint"], $itemID, $userID, $context, $affector, $hint)->exec();
    }

    static function addDoneList($itemID, $context, $affector)
    {
        self::db()::insert(self::$defNotifDoneTable, ["itemID", "context", "affector"], $itemID, $context, $affector)->exec();
    }

    static function delIfNotDone($itemID, $context, $notifID, $userID)
    {
        if (!self::db()::select(self::$defNotifDoneTable, ["id"])->where(["itemID", "context", "userID"], $itemID, $context, $userID)) {
            self::delNotification($notifID);
        }
    }

    static function checkDoneList($itemID, $context, $affector)
    {
        if ($affector == "") return false;
        return isset(self::db()::select(self::$defNotifDoneTable)->where(["itemID", "context", "affector"], $itemID, $context, $affector)->exec()[0]);
    }

    static function getNotifications($userid, $count = 5, $type = "")
    {
        return self::db()::select(self::$defNotifTable)->where(["user"], $userid)->orderBy("readState ASC, id", "DESC")->limit($count)->exec();
    }

    static function getTimeNotifications($userid, $time)
    {

    }

    static function delNotification($notifID)
    {
        self::db()::delete(self::$defNotifTable)->where(["id"], $notifID)->exec();
    }

    static function massNotification($notificationType, $message)
    {

    }

    static function getNotificationInfo($notifID, $data)
    {

    }

    static function setNotificationInfo($notifID, $data, $val)
    {

    }

}