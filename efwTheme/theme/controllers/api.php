<?php
header("Access-Control-Allow-Origin: *");


class api
{
    static $key = "";
    static $commands = [];
    static function checkKey($key){
        return (bool)\efwEngine\database::system()::select(DB_PREFIX."api")->where(["apiKey"], $key)->exec(true);
    }
    static function result($data)
    {

        return json_encode($data);
    }

    static function runCommand($command, $data)
    {
        $commandRaw = explode(" ", $command);
        if (isset(self::$commands[$commandRaw[0]])) {
            $commands = self::$commands;
            return self::$commands[$commandRaw[0]]($data);
        } else {
            return [false, "noCommand"];
        }
    }

    static function regCommand($command, callable $function)
    {
        self::$commands[$command] = $function;
    }

    static function getCommandData($command)
    {

        return json_decode($_POST["data"], true);

    }


}
?>