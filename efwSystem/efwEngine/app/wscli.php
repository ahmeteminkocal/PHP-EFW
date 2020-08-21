<?php


namespace efwEngine\app;

use efwEngine\config;
use WSSC\Exceptions\BadOpcodeException;
use WSSC\Exceptions\BadUriException;
use WSSC\Exceptions\ConnectionException;
use WSSC\WebSocketClient;
use \WSSC\Components\ClientConfig;
use efwTheme\engine;

class wscli
{
    private static $initOK = false;
    private static WebSocketClient $websocket;
    private static $key ="";
    private static $channels = [];

    static function getChannels(){
    return self::$channels;
    }
    static function addChannel($channel, $key = null){
        self::$channels[$channel]["key"] = $key;
    }
    static function initChannels(){
        self::addChannel("/");
    }
    static function genChannelKey($channel){

    }

    static function init()
    {
        if(self::$initOK === false){
            try {
                $config = new ClientConfig();
                $config->setContextOptions(['ssl' => ['verify_peer' => false, 'verify_peer_name' => false]]);
                $client = new WebSocketClient('wss://'.config::getCurrentSiteConfig()["wssServer"], $config);
                $client->send(config::getCurrentSiteConfig()["wssServerKey"]);
                $resp = json_decode($client->receive(), true);
                if($resp["mid"] == "login" && $resp["status"] == "info"){
                    self::$initOK = true;
                    self::$websocket = $client;
                    return true;
                }else{
                    error_log("WSS kullanıcı girişi yapılamadı.", "ERROR");
                    return false;
                }
            } catch (BadUriException $e) {
            } catch (ConnectionException $e) {
            }

        }else{
            return true;
        }
    }
    static function genAdmKey(){
        $key = engine::generateRandomString(10);
        self::sendCommand("regTempKey", ["key" => $key, "ip" => $_SERVER["HTTP_CF_CONNECTING_IP"]]);
        return $key;
    }
    static function runJSonUser($uid, $js){
        self::ssuBroadcast($uid, ["command" => "runjsCommand", "script" => $js]);
    }
    static function sendCommand($command, $data = "", $waitForReply = true)
    {

            $send = ["command" => $command, "data" => $data];
            if (!self::init()) {
            }else {
                $cli = self::$websocket;
                $cli->send(json_encode($send));
                try {
                    if($waitForReply)
                        return $cli->receive();
                } catch (BadOpcodeException $e) {
                } catch (BadUriException $e) {
                } catch (ConnectionException $e) {
                } catch (\Exception $e) {
                }

            }

    }
    static function isOnline($uid){
     return self::sendCommand("isOnline", $uid);
    }
    static function getOnlineData(){
        return self::sendCommand("getonlineData");
    }
    static function broadcast($data, $channel = ""){
        if($channel == "")
        $data = ["arguments" => $data];
        else
            $data = ["channel" => $channel, "arguments" => $data];
        return self::sendCommand("broadcast", $data, false);
    }
    static function ssuBroadcast($user, $data, ...$extraHeaders){
        $data = ["user" => $user, "arguments" => $data, ...$extraHeaders];
        return self::sendCommand("ssuBroadcast", $data, false);
    }

}