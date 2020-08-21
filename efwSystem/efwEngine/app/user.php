<?php


namespace efwEngine\app;

use efwEngine\cache;
use efwEngine\cdn;
use efwEngine\database;
use efwEngine\roles;
use efwEngine\startup;
use efwTheme\engine;
use efwTheme\router;


class user
{
    private static $currUserID;
    public static $defUserTable = DB_PREFIX . "users";
    private static $defUserProfileData = DB_PREFIX . "users_profileData";
    private static $userDataCache = [];

    static function db(): database
    {
        return new database();
    }


    static function sysdb(): database
    {
        database::setPdo(["db" => DB_PREFIX."system"]);
        return new database();
    }

    static function init()
    {
        self::$currUserID = false;

    }

    static function getCurrUserID()
    {
        return self::$currUserID;
    }

    static function getRandomPeople($limit)
    {
        database::init();
        return database::select(self::$defUserTable)->orderBy("RAND()")->limit($limit)->exec();
    }

    static function getUserID($username)
    {
        $exec = database::select(self::$defUserTable, ["id"])->where(["username"], $username)->exec();
        if (isset($exec[0][0])) {
            return database::getResult()[0][0];
        } else {

            return false;
        }
        // TODO: Implement getUserID() method.
    }

    static function checkLogin()
    {
        $sessID = self::getSessionDBdata($_COOKIE["PHPSESSID"] ?? false);
        if (isset($sessID[0]) ) {
            return true;
        } else {

            $session = self::getSessionDBdata(self::getSessionKey());
            if($session){
                @session_start();
                    foreach ($session as $key => $d){
                        $_SESSION[$d["data"]] = $d["val"];
                    }
                header('Location: '.$_SERVER['REQUEST_URI']);
                    die;
            }
            return false;
        }
    }
    static function getSessionKeyData($key){
        $data = self::sysdb()::select("sessionData")->where(["sesskey"], $key)->exec();
        $r = [];

        foreach ($data as $datum){
            $r[$datum["data"]] = $datum["val"];
        }
        return $r;
    }
    static function getSessionDBdata($key, $active = true)
    {
        database::setPdo(["db" => DB_PREFIX."system"]);
        return database::select("sessionData")->where(["sesskey", "active"], $key, $active)->exec();
    }
    static function getUserAllData($userid){
        return self::db()::select(self::$defUserTable)->where(["id"], $userid)->exec()[0];
    }
    static function getUserData($data, $userid = "")
    {
        if ($userid == "") $userid = self::getCurrUserID();
        if (isset(self::$userDataCache[$userid][$data])) {
            return self::$userDataCache[$userid][$data];
        }
        $key = "$userid-data-$data";
        if (!cache::exists($key)) {


            $info = database::select(self::$defUserTable, [$data])->where(["id"], $userid)->exec()[0][0];
            cache::add($key, $info);
            self::$userDataCache[$userid][$data] = $info;
            return $info;
        } else {
            $var = cache::get($key);
            self::$userDataCache[$userid][$data] = $var;

            return $var;
        }
    }

    static function getUserDataEmail($data, $email = "")
    {
            $info = database::select(self::$defUserTable, [$data])->where(["email"], $email)->exec()[0][0];
            return $info;
    }

    static function updateUser($data, $val, $userid = "")
    {
        $userid = self::ifUserIDnotSet($userid);

        database::update(self::$defUserTable, $data, $val)->where(["id"], $userid)->exec();
        \efwEngine\cache::on("updateUser", $userid);
        return true;
        // TODO: Implement updateUser() method.
    }

    static function getUserProfilePicture($userid = "")
    {
        if ($userid == "") $userid = self::getCurrUserID();
        $image = self::getUserData("profileImage", $userid);
        if (is_null($image)) {
            return cdn::getURL() . "app-assets/images/profile/profile.png";
        } else {
            return $image;
        }
    }

    static function getProfileDatum($userid)
    {
        $key = "$userid-data-profileDatum";
        if (!cache::exists($key)) {
            $data = database::select(DB_PREFIX . "users_profileData")->where(["userid"], $userid)->exec()[0] ?? null;
            $about = $data["about"] ?? null;
            $cover = $data["coverPicture"] ?? null;
            $website = $data["website"] ?? null;
            $phone = $data["phone"] ?? null;
            if (is_null($about)) {
                $about = "Hakkında bilgi vermemiş";
            }
            if (is_null($cover)) {
                $cover = "https://".engine::getCurrentDomain(false)."/efwCdn/app-assets/images/backgrounds/177292.jpg";
            }
            if (is_null($website)) {
                $website = null;
            }
            $returnData = ["coverPicture" => $cover, "about" => $about, "website" => $website, "phone" => $phone];
            cache::add($key, $returnData);
            return $returnData;
        } else {
            return cache::get($key);
        }

    }

    static function changeProfileImage($_FILE, $index = "profileImage")
    {

        if ($_FILE == null) {
            user::updateUser("profileImage", null);
        } else {
            $url = cdn::uploadGetURL($_FILE[$index]["tmp_name"], $_FILES[$index]["name"], "profileImage");
            user::updateUser("profileImage", $url);
            return $url;
        }
    }

    static function setProfileData($data, $val, $userid = "")
    {
        if ($userid == "") $userid = self::getCurrUserID();
        self::createBaseData($userid);
        database::update(self::$defUserProfileData, [$data], $val)->where(["userid"], $userid)->exec();
    }

    static function createBaseData($userid)
    {
        if (!self::checkBaseData($userid)) {
            database::insert(self::$defUserProfileData, ["userid"], $userid)->exec();
        }
    }

    static function checkBaseData($userid)
    {
        return isset(database::select(self::$defUserProfileData, ["id"])->where(["userid"], $userid)->exec()[0][0]);
    }

    static function getUserMail(): string
    {
        // TODO: Implement getUserMail() method.
    }

    static function sendMail(): bool
    {
        // TODO: Implement sendMail() method.
    }

    static function verifyEmail()
    {
        // TODO: Implement verifyEmail() method.
    }
    public static function updatePassword($userID, $newPassword){
        self::updateUser("password", self::passwordEncrypt($newPassword), $userID);
    }
    public static function passwordEncrypt($password)
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    public static function verifyPassword($password, $hash, $userID = "")
    {
        //eski sistem kontrolü
        if (password_verify(md5($password), $hash)) {
            $newPass = self::passwordEncrypt($password);
            self::updateUser("password", $newPass, $userID);
            $hash = $newPass;
        }

        return password_verify($password, $hash);
    }

    static function register($username, $nickname, $name, $surname, $email, $password, $passRecap = false, $dontSendRegMail = false)
    {
        if (!$passRecap) {
            if (!recaptcha::checkCaptcha()) {
                return [false, "recaptcha", "Google Recaptcha onaylama hatası"];
            }
        }
        $checkUserExists = self::checkUserExists($username, $email);
        $name = strip_tags($name);
        $surname = strip_tags($surname);
        if ($checkUserExists[0] === true) return [false, "userExists", "E-Posta adresi veya kullanıcı adı zaten mevcut"];
        $password = self::passwordEncrypt($password);        //şifreyi şifrele
        database::insert(self::$defUserTable, ["username", "nickname", "name", "surname", "email", "password"], $username, $nickname, $name, $surname, $email, $password)->exec();
        $userid = database::getLastInsertID();
        if ($dontSendRegMail === false) {
            self::createValidationMail($username, $email);

        }
        else {
            if (!is_null($dontSendRegMail)) {

                self::updateUser("email_validated", true, $userid);
            }
        }
        $o = new onesignal();
        $o->sendUsers([1, 63], "Yeni hesap", "$name $surname firma hesabı oluşturdu.");
        return [true, "ok", "Hesap oluşturuldu! E-Posta adresine onay maili gönderildi!", "userID" => $userid];
        // TODO: Implement register() method.


    }

    static function validateEmail($username, $key)
    {
        startup::loadModule("efwEngine\app\system");

        $keyMeta = system::getMeta("activation", self::getUserID($username));
        if ($key == $keyMeta) {

            return self::updateUser("email_validated", true, self::getUserID($username));
        } else
            return false;
    }

    static function createValidationMail($username, $email)
    {

        $topic = "Asosyal.club Üyelik Mail Onaylama";
        $mail = "Asosyal Club'a hoş geldiniz! Hesabınızı " . self::createValidationLink($username) . " adresinden onaylayabilirsiniz!";
        mail::$receivers = [$email];
        mail::sendMail($topic, $mail);

    }

    static function checkValidation($username)
    {
        return database::select(self::$defUserTable, ["email_validated"])->where(["username"], $username)->exec()[0][0];
    }

    static function slugify($string)
    {
        $string = str_replace(["ü","ö", "ı", "ç", "ğ", "ş", "ī"], ["u","o", "i", "c", "g", "s", "i"], $string);
        return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $string), '-'));
    }

    static function getUserSlug($userID = "")
    {
        if ($userID == "") {
            $userData = self::getUserData("id") . "-" . self::slugify(self::getUserRealNameAndSurname());
            return $userData;
        } else {
            $userData = self::getUserData("id", $userID) . "-" . self::slugify(self::getUserRealNameAndSurname($userID));

            return $userData;
        }
    }

    static function createValidationLink($username)
    {
        startup::loadModule("efwEngine\app\system");

        $key = engine::generateRandomString();
        system::createMeta("activation", $key, self::getUserID($username));
        return "https://".engine::getCurrentDomain(false)."/mod/validate/" . urlencode($username) . "/" . urlencode($key);
    }

    static function getSlug($userid)
    {
        //TODO cache ekle
        if ($userid == "") $userid = user::getSessionData("userid");
        $name = self::getUserData("slug", $userid);
        return $name;
    }

    static function getUserRealNameAndSurname($userid = "", $plain = true, $link = false)
    {
        //TODO cache ekle
        if ($userid == "") $userid = user::getSessionData("userid");

        $name = self::getUserData("name", $userid);
        $surname = self::getUserData("surname", $userid);
        if ($name == "") {
            $name = self::getUserData("username", $userid);
            $surname = "";
        }
        if(!$plain)
        return "<a ".($link ? "href='/account/profile/".user::getUserSlug($userid)."'" : "")." style='".roles::getUserStyle($userid)."'>".htmlspecialchars("$name $surname")."</a>";
        return htmlspecialchars("$name $surname");
    }

    static function checkUserExists($username, $email)
    {
        if (self::getUserID($username)) {
            return [true, "username"];
        }
        if (isset(database::select(self::$defUserTable, ["id"])->where(["email"], $email)->exec()[0])) {
            return [true, "email"];
        }

        return [false];
    }

    static function userExists($username)
    {
        $userID = self::getUserID($username);
        if ($userID !== false) {
            return true;
        }
        return false;
    }

    static function getSessionKey()
    {
        return $_COOKIE["session"] ?? null;
    }

    static function login($username, $password, $abot = "")
    {
        if (!isset(database::select(self::$defUserTable, ["password"])->where(["username"], $username)->exec()[0][0])) {
            if (!isset(database::select(self::$defUserTable, ["password"])->where(["email"], $username)->exec()[0][0])) {
                return [false, "noUser", "Kullanıcı bulunamadı!"];
            } else {
                $result = database::select(self::$defUserTable, ["password", "username"])->where(["email"], $username)->exec()[0];
                $username = $result["username"];
            }
        }
        $hash = database::getResult()[0][0];

        if (self::verifyPassword($password, $hash, user::getUserID($username))) {
            if (self::checkValidation($username)) {
                self::loginProcedure($username);
                discord::sendMessage(DISCORD_RECEIVER_TYPE_CHANNEL, '739904302008762379', $username. " giriş yaptı. IP: ".user::getIP());
                logging::addLog($username." giriş başarılı.", LOG_TYPE_LOGIN, LOG_THEME_SUCCESS);
                return [true, "ok", "Giriş Başarılı!"];


            } else {
                return [false, "emailValidation", "Lütfen E-Posta hesabınıza gönderilen onay mailini kontrol ediniz. Mail ulaşmadıysa bize info@asosyal.club ve discord üzerinden ulaşabilirsiniz!"];
            }
        } else {
            logging::addLog($username." giriş başarısız.", LOG_TYPE_LOGIN, LOG_THEME_DANGER);

            return [false, "paswError", "Hatalı Şifre"];
        }

        // TODO: Implement login() method.
    }

    static function loginProcedure($username)
    {
        session_start();
        self::setSessionData("user", $username);
        self::setSessionData("userid", self::getUserID($username));
        self::setSessionData("loginIp", user::getIP());
        self::setSessionData("agent", $_SERVER['HTTP_USER_AGENT']);
        database::setQuery("UPDATE ".DB_PREFIX."users SET lastlogin = current_timestamp WHERE username = ?");
        database::setBind([$username]);
        database::exec();
    }

    static function logout()
    {
        self::destroySession();
    }

    static function setSessionDB($sess, $key, $val)
    {
        database::setPdo(DB_CONFIGS["system"]);
        database::insert("sessionData", ["sesskey", "data", "val"], $sess, $key, $val)->exec();
    }

    static function getUserStatusText($userID = "")
    {
        return "Çevrimiçi";

    }

    static function getSessionDB($sess, $key)
    {
        if($sess == false) return false;
        database::setPdo(DB_CONFIGS["system"]);
        return database::select("sessionData", ["val"])->where(["sesskey", "data"], $sess, $key)->exec(true);
    }

    static function getSessionID()
    {
        return $_COOKIE["PHPSESSID"];
    }

    static function setSessionData($data, $val)
    {

        self::setSessionDB($_COOKIE["PHPSESSID"], $data, $val);
        $_SESSION[$data] = $val;
    }

    static function getSessionData($data)
    {
        if (isset($_SESSION[$data]))
            return $_SESSION[$data];
        $dbData = user::getSessionDB($_COOKIE["PHPSESSID"] ?? false, $data);
        if($dbData != "") $_SESSION[$data] = $dbData;
        return $dbData;
    }

    static function destroySession()
    {
        database::setPdo(["db" => DB_PREFIX."system"]);
        database::update("sessionData", "active", false)->where(["sessKey"], self::getSessionID())->exec();
        setcookie("session", "", null,  "/", engine::getCurrentDomain(true));
        session_destroy();

    }

    //

    private static function ifUserIDnotSet($userid)
    {
        if ($userid == "") $userid = self::getCurrUserID();
        return $userid;
    }

    public static function getUsers()
    {
        return database::select(self::$defUserTable)->exec();
    }

    public static function getIP()
    {
        return $_SERVER["HTTP_CF_CONNECTING_IP"] ?? $_SERVER["REMOTE_ADDR"] ?? "cli";
    }
    public static function activeSessions($userID, $clear = false){
        $userKeys = self::sysdb()::select("sessionData", ["sesskey", "date"])->where(["data", "val", "active"], "userid", $userID, true)->exec();
        if($clear === true) {


            foreach ($userKeys as $key) {

                self::sysdb()::update("sessionData", "active", false)->where(["sessKey"], $key[0])->exec();
            }


        } else if(is_string($clear)){

            self::sysdb()::update("sessionData", "active", false)->where(["sessKey"], $clear)->exec();


        }
        if(!$clear) return self::sysdb()::select("sessionData", ["sesskey", "date"])->where(["data", "val", "active"], "userid", $userID, true)->exec();

    }
}