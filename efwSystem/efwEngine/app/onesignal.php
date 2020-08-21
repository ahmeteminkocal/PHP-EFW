<?php


namespace efwEngine\app;


use efwEngine\database;
define("ONESIGNAL_TABLE", DB_PREFIX."onesignal");
class onesignal
{
    public $ch = "";
    public function connect(){
        curl_setopt($this->ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json; charset=utf-8',
            'Authorization: Basic '
        ));
    }

    function regUser($userID, $playerID){
        //TODO Kişi başı 10 limitleme koy
        //TODO Çağrı limiti koy
        if(!self::checkReg($userID, $playerID)) {
            if(!is_null($playerID)) {
                database::delete(ONESIGNAL_TABLE)->where(["playerID"], $playerID)->exec();
                database::insert(ONESIGNAL_TABLE, ["userID", "playerID"], $userID, $playerID)->exec();
            }
        }
    }
    function getUserPlayerIDs($userID){
        $ids = [];
        $all = database::select(ONESIGNAL_TABLE, ["playerID"])->where(["userID"], $userID)->exec();
        foreach ($all as $id){
            $ids[] = $id[0];
        }
        return $ids;
    }
    function sendUsers($users, $header, $message, $bigPicture = null){
        $IDs = [];
        foreach ($users as $user){
            $IDs = array_merge($IDs,  self::getUserPlayerIDs($user));
        }
        $this->sendToPlayerID($header, $message, $IDs, $bigPicture);
    }
    function checkReg($userID, $playerID){
        $x = database::select(ONESIGNAL_TABLE)->where(["userID", "playerID"], $userID, $playerID)->exec();
        return isset($x[0]);
    }
    function sendToPlayerID($header, $message, array $playerIDs, $bigPicture = null){
        $content      = array(
            "en" => $message,
            "tr" => $message
        );
        $headings = [
            "en" => $header,
            "tr" => $header

        ];
        $fields = array(
            'app_id' => "",
            'include_player_ids' => [...$playerIDs],
            'contents' => $content,
            'headings' => $headings,
            'big_picture' => $bigPicture,

        );
        $fields = json_encode($fields);



        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($this->ch, CURLOPT_HEADER, FALSE);
        curl_setopt($this->ch, CURLOPT_POST, TRUE);
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        $response = curl_exec($this->ch);
        curl_close($this->ch);

        return $response;
    }
    function sendToALL($header, $message) {
        $content      = array(
            "en" => $message,
            "tr" => $message
        );
        $headings = [
          "en" => $header,
            "tr" => $message

        ];
        $fields = array(
            'app_id' => "",
            'included_segments' => array(
                'All'
            ),
            'data' => array(
                "test" => "bir"
            ),
            'contents' => $content,
            'headings' => $headings
        );

        $fields = json_encode($fields);

        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($this->ch, CURLOPT_HEADER, FALSE);
        curl_setopt($this->ch, CURLOPT_POST, TRUE);
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        $response = curl_exec($this->ch);
        curl_close($this->ch);

        return $response;
    }

    public function __construct()
    {
        $this->ch = curl_init();

        $this->connect();
    }
}