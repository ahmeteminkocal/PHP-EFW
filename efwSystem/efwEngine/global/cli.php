<?php


namespace efwEngine;


use efwEngine\app\notifications;
use efwEngine\app\onesignal;
use efwEngine\app\user;
use efwTheme\engine;

class cli
{

    static $commands = [];

    static function run()
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        self::registerCommands();
        while (true) {
            $line = readline("MONTRACON>>");
            readline_add_history($line);
            self::runCommand($line);
        }
    }

    static function runCommand($command)
    {
        $commandRaw = explode(" ", $command);
        $data = explode(" ", $command);
        if (is_array($data))
            array_shift($data);
        if (isset(self::$commands[$commandRaw[0]]))
            echo self::$commands[$commandRaw[0]]($data) . "\r\n";
        else {
            echo "Komut bulunamadı.\r\n";
        }
    }

    static function regCommand($command,callable $function)
    {
        self::$commands[$command] = $function;
    }

    static function registerCommands()
    {
        self::regCommand("duyuru",function ($data) {
            $users = user::getUsers();

            foreach ($users as $user) {
                notifications::addNotification($user["id"], NOTIFICATION_TYPE_PRIMARY, "", ["context" => "notice", "message" => implode(" ", $data), "sender" => 1]);
            }
        } );
        self::regCommand("push", function ($data){
            $s = new onesignal();
         var_dump(   $s->sendToALL("Deneme", "tek seferlik deneme gönderimi"));
        });
        self::regCommand("pushs", function ($data){
            $data = implode(" ", $data);
            $command = explode("/", $data);
            $userID = $command[0];
            $header = $command[1];
            $message = $command[2];
            $o = new onesignal();
            var_dump($command);
            var_dump( $o->sendToPlayerID($header, $message, [$userID]));
        });
        self::regCommand("sendPush", function ($data){
            $o = new onesignal();
        });
        self::regCommand("uinfo", function ($data){
            $info = user::getUserAllData($data[0]);
            var_dump($info);
            return "";
        });
        self::regCommand("setuinfo", function ($data){
            user::updateUser($data[1], $data[2], $data[0]);
        });
        self::regCommand("changepw", function ($data){
            user::updatePassword($data[0], $data[1]);
        });
        self::regCommand("controller", function ($data){
              engine::addController($data[0]);
        });

    }
}