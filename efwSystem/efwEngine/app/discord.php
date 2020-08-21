<?php


namespace efwEngine\app;


use efwEngine\database;
use RestCord\DiscordClient;
define("DISCORD_MESSAGE_TYPE_RECEIVED", 1);
define("DISCORD_MESSAGE_TYPE_SEND", 0);
define("DISCORD_RECEIVER_TYPE_CHANNEL", 0);
define("DISCORD_RECEIVER_TYPE_MEMBER", 1);
class discord
{

    static function regUser($discordID, $username, $discriminator, $locale, $avatar, $public_flags, $token, $userID = ""){
        if($userID == "") $userID = user::getCurrUserID();
        database::setPdo(["db" => DB_PREFIX."general"]);
        database::insert(DB_PREFIX."users_discord", ["userID", "discordID", "username", "discriminator", "locale", "avatar", "public_flags", "token"], $userID, $discordID, $username, $discriminator, $locale, $avatar, $public_flags, $token)->exec();

    }
    static function user_add($discord_id, $token, $nick = null, $roles = null) {
        $discord = new DiscordClient(['token' => '']); // Token is required
        $guild =   $discord->guild->getGuild(['guild.id' => 0]);
        $response = $discord->client->guild->addGuildMember([
            'guild.id' => 0,
            'user.id' => (int)$discord_id,
            'access_token' => (string)$token,
            'nick' => (string)$nick,
            'roles' => $roles
        ]);
        return $response;
    }
    static function isConnected($userID = ""){
        if($userID == "") $userID = user::getCurrUserID();
        database::setPdo(["db" =>  DB_PREFIX."general"]);
        return isset(database::select(DB_PREFIX."users_discord", ["id"])->where(["userID"], $userID)->exec()[0][0]);
    }
    static function getUserData($userID = ""){
        if($userID == "") $userID = user::getCurrUserID();
        database::setPdo(["db" =>  DB_PREFIX."general"]);

        return database::select(DB_PREFIX."users_discord")->where(["userID"], $userID)->exec()[0];
    }
    static function sendMessage($receiverType, $receiver, $message, $sendDate = null){
        database::massData()::insert(DB_PREFIX."discord_messages", ["type", "receiverType", "receiver", "message"], $receiverType, $receiverType, $receiver, $message)->exec();
    }
    static function checkMessages($receiverType){
        database::massData();
        $lastID = database::setQuery("SELECT MAX(id) FROM ".DB_PREFIX."discord_messages WHERE receiverType=? AND state = 0")->setBind([$receiverType])->exec(true);
        $messages["data"] = database::massData()::setQuery("SELECT * FROM ".DB_PREFIX."discord_messages WHERE receiverType=? AND state = 0 and id <= ?")->setBind([$receiverType, $lastID])->exec();
        $messages["lastID"] = $lastID;
        database::massData()::update(DB_PREFIX."discord_messages", "state", true)->where(["textSql" => "id <= ? AND state = ? AND receiverType = ?", "paramNo" => 3], $lastID, 0, $receiverType)->exec();
        return $messages;
    }
    static function regApi(\api $api){
        if(!$api::checkKey($_POST["key"] ?? $_GET["key"])) die("KEY ERROR");

       $api::regCommand("checkMessages", function ($data){
           $type = $_POST["type"] ?? $_GET["type"];
            $messages = self::checkMessages($type)["data"];
            $return = [];
            foreach ($messages as $no=> $message){
                $return[] = ["rec" => $message["receiver"], "message" => $message["message"]];
            }
            if(is_null($return)) return false;

            return $return;
       });
    }
}