<?php


namespace efwEngine;
define("ROLE_PERM_SUPERADMIN", 0);
define("ROLE_PERM_GROUP_PROVIDER", 2);
define("ROLE_PERM_PANEL", 40);
define("ROLE_PERM_DEL_USER", 41);
define("ROLE_PERM_MODIFY_USER", 42);
define("ROLE_PERM_VERIFY_USER", 43);
define("ROLE_PERM_BAN_USER", 44);
define("ROLE_PERM_DEL_GLOBAL_POST", 45);

define("ROLE_PERM_PAUSE_WEBSITE", 50);
define("ROLE_PERM_SEND_EMAIL", 51);
define("ROLE_PERM_MODIFY_SETTINGS", 52);
define("ROLE_PERM_MODIFY_STMP", 53);
define("ROLE_PERM_MODIFY_DISCORD", 54);
define("ROLE_PERM_DEL_GROUP", 55);

define("ROLE_PERM_PROV_SEESELF_STATISTICS", 56);
define("ROLE_PERM_PROV_SEESELF_TRANSACTIONS", 57);
define("ROLE_PERM_PROV_REQS_PAYMENT", 58);

define("ROLE_PERM_ARTIFICIAL_ALLOW", 1);

define("ROLE_DESCRIPTRATIONS", [
    ROLE_PERM_PANEL => ["name" => "ROLE_PERM_PANEL", "descriptration" => "Panel görüntüleme yetkisi."],
    ROLE_PERM_MODIFY_USER => ["name" => "ROLE_PERM_MODIFY_USER", "descriptration" => "Kullanıcı düzenleme yetkisi."],
    ROLE_PERM_BAN_USER => ["name" => "ROLE_PERM_BAN_USER", "descriptration" => "Kullanıcı banlama yetkisi"],
    ROLE_PERM_PAUSE_WEBSITE => ["name" => "ROLE_PERM_PAUSE_WEBSITE", "descriptration" => "Site duraklatma yetkisi"]
]);
use efwEngine\app\user;

class roles
{

    static function getUserRole($userID = "")
    {
        if ($userID == "") $userID = user::getCurrUserID();

        return database::select(DB_PREFIX . "users", ["role"])->where(["id"], $userID)->exec(true);
    }
    static function getRoles(){
        return database::select(DB_PREFIX."roles")->exec();
    }
    static function getUserStyle($userID)
    {
        if ($userID == "") $userID = user::getCurrUserID();

        $role = self::getUserRole($userID);
        return self::getInfo($role, "style");
    }

    static function getInfo($roleID, $info)
    {
        return database::select(DB_PREFIX . "roles", [$info])->where(["id"], $roleID)->exec()[0][0];
    }

    static function reqPerm($permID, $userID = "")
    {
        if(!user::checkLogin()) return false;

        if($permID == ROLE_PERM_ARTIFICIAL_ALLOW) return true;
        $role = self::getUserRole($userID);

        database::setQuery("SELECT permissions FROM ".DB_PREFIX."roles WHERE id=?");
        database::setBind([$role]);
        $roleInfo = database::exec()[0];
        $json_decode = json_decode($roleInfo[0], true);
        return in_array($permID, $json_decode) || in_array(0, $json_decode);
    }
}