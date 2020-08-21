<?php


namespace efwEngine;

use efwEngine\app\user;
use efwTheme\engine;

include "interfaces/cdnInterface.php";
class cdn implements cdnInterface
{

    //TESTUPDT
    public static $cdnBaseDIR = "";
        public static $cdnURLs = [];
        static function getURL(){
            $str = self::$cdnURLs[engine::getCurrentDomain()];
            return $str;

        }
        static function deleteFileURL($link){
            $dir = str_replace(self::getURL(), "", $link);
            $fileDir = self::$cdnBaseDIR . $dir;
            if(file_exists($fileDir)) {
                unlink($fileDir);
            }else{
                echo "dosya yok ";
            }
        }
        static function uploadGetURL($fileDir, $fileName, $fileType, $logText = ""){
            $currUserID = user::getCurrUserID();
            switch ($fileType){
                case "imagePost":
                    $destination = "userContent/images/posts/" . $currUserID . "/image/" . engine::generateRandomString(20) . "." . pathinfo($fileName, PATHINFO_EXTENSION);

                    $folder = self::$cdnBaseDIR . "userContent/images/posts/" . $currUserID . "/image/";
                    if(!file_exists($folder)) {
                        if (!mkdir($concurrentDirectory = $folder, 0777, true) && !is_dir($concurrentDirectory)) {
                            throw new \RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
                        }
                    }
                    break;
                case "videoPost":

                    $destination = "userContent/images/posts/" . $currUserID . "/video/" . engine::generateRandomString(20) . "." . pathinfo($fileName, PATHINFO_EXTENSION);

                    if (!mkdir($concurrentDirectory = self::$cdnBaseDIR . "userContent/images/posts/" . $currUserID . "/video/", 0777, true) && !is_dir($concurrentDirectory)) {
                        throw new \RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
                    }

                    break;
                case "profileImage":
                    $profileImageDir = "userContent/images/profileImages/" . $currUserID . "/";
                    $extension  = pathinfo($fileName, PATHINFO_EXTENSION);
                    $acceptedExtensions = ["jpg", "png", "gif", "jpeg"];
                    if(in_array($extension, $acceptedExtensions)) {
                        $destination = $profileImageDir . engine::generateRandomString() . "." . $extension;
                        $folder = self::$cdnBaseDIR . $profileImageDir;

                        if (!file_exists($folder))
                            if (!mkdir($folder, 0777, true) && !is_dir($profileImageDir)) {
                                throw new \RuntimeException(sprintf('Directory "%s" was not created', $destination));
                            }
                    }else{
                        return [false, "extension"];
                    }
                    break;
                default:
                    return false;
                    break;
            }
            move_uploaded_file($fileDir, self::$cdnBaseDIR . $destination);
            return self::$cdnURLs[engine::getCurrentDomain()]. $destination;
        }
}