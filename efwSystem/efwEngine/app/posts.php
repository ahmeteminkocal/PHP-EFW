<?php


namespace efwEngine\app;


use efwEngine\cache;
use efwEngine\cdn;
use efwEngine\database;
use efwTheme\syncEngine;

class posts
{
    static $defPostsTable = DB_PREFIX . "posts";
    static $defPostsMetaTable = DB_PREFIX . "posts_meta";
    static $defHashtagTable = DB_PREFIX . "hashtags";
    static $defPostCommentsTable = DB_PREFIX . "comments";
    static $filePostTypes = [
        "imagePost",
        "videoPost"
    ];
    static $commentDatas = [];
    static function addPost($postText, $address, $postImage = [])
    {
        cache::deleteUserCaches(user::getCurrUserID());
        switch(pathinfo($postImage["name"], PATHINFO_EXTENSION)){
            case "mp4":
                $postType = "videoPost";
                break;
            case "jpg":
                $postType = "imagePost";
                break;
            case "png":
                $postType = "imagePost";
                break;
            case "gif":
                $postType = "imagePost";
                break;
        }
        if (isset($postImage["tmp_name"]) && $postImage["tmp_name"] != "") {
            $link = cdn::uploadGetURL($postImage["tmp_name"], $postImage["name"], $postType);
        } else {
            $postType = "post";
            $link = "";
        }
        database::setPdo(DB_CONFIGS["massdata"]);

        $hashtags = self::getHashtags($postText);
        $postText = htmlspecialchars($postText);
        database::insert(self::$defPostsTable, ["sender", "type", "text", "data", "address", "tags"], user::getCurrUserID(), $postType, $postText, $link, $address, "")->exec();
        $lastInsertId = intval(database::getLastInsertID());

        foreach ($hashtags as $hashtag) {
            self::addToHashtag($hashtag, $lastInsertId);
        }
        \efwEngine\cache::on("newPost", $address);

    }

    static function checkHashtagExistance($hashtag)
    {
        database::setPdo(DB_CONFIGS["massdata"]);

        return isset(database::select(self::$defHashtagTable, ["id"])->where(["tag"], $hashtag)->exec()[0][0]);
    }

    static function addToHashtag($tag, $postid)
    {
        database::setPdo(DB_CONFIGS["massdata"]);

        database::insert(self::$defHashtagTable, ["tag", "postid"], $tag, $postid)->exec();
    }

    static function getPosts($order = "default")
    {

    }

    static function getHashtags($string)
    {
        $hashtags = FALSE;
        preg_match_all("/(#\w+)/u", $string, $matches);
        if ($matches) {
            $hashtagsArray = array_count_values($matches[0]);
            $hashtags = array_keys($hashtagsArray);
        }
        return $hashtags;
    }
    static function getPostsHashtag($tag){
        $tag = "#".$tag;
        database::setPdo(["db" => DB_PREFIX."massdata"]);
        $tags = database::select(DB_PREFIX."hashtags", ["postid"])->where(["tag"], $tag)->exec();
        $return = [];
        foreach ($tags as $tag){
            $return[] = $tag["postid"];
        }
        return $return;
    }
    static function getPostsUser($userid, $type = "", $limit = 1000)
    {
        database::setPdo(DB_CONFIGS["massdata"]);
        $key = $userid."-posts-$type";
        if(cache::exists($key)){
            return cache::get($key);
        }else{
            if($type != "") {
                $data = database::select(self::$defPostsTable)->where(["address", "type"], "profile-" . $userid, $type)->orderBy("postdate", "DESC")->limit($limit)->exec();


            }else
            $data = database::select(self::$defPostsTable)->where(["address"], "profile-" . $userid)->orderBy("postdate", "DESC")->limit($limit)->exec();
            cache::add($key, $data);
        }
        return $data;
    }

    static function getPostsLatest($tags = [], $type = "", $limit = 100)
    {

        database::setPdo(DB_CONFIGS["massdata"]);
        $key = md5(json_encode($tags))."-LatestPosts-$type";
        if(cache::exists($key)){
            return cache::get($key);
        }else{
            if($type != "") {
                $data = database::select(self::$defPostsTable)->where(["type"], $type)->orderBy("postdate", "DESC")->limit($limit)->exec();


            }else {
                if($tags != POSTS_NOTAG){
                    $data = self::getPostsHashtag($tags);
                    return self::getPostsID($data);
                }else
                $data = database::select(self::$defPostsTable)->orderBy("postdate", "DESC")->limit($limit)->exec();
            }
            cache::add($key, $data);
        }
        return $data;
    }
    static function getPostsID($idsArray){
        $array=array_map('intval', $idsArray);
        $array = implode("','",$array);
        database::setPdo(["db" => DB_PREFIX."massdata"]);
        database::setQuery("SELECT * FROM ".DB_PREFIX."posts WHERE id IN ('".$array."')")->orderBy("postdate", "DESC");

        $result = database::exec();
        return $result;
    }
    static function getCommentsOnPost($postid, $limit = 4, $start = 0)
    {
        database::setPdo(DB_CONFIGS["massdata"]);
        $commentDatas = database::select(self::$defPostsMetaTable)->where(["type", "postid"], "comment", $postid)->orderBy("time", "DESC")->exec();
        self::$commentDatas[$postid] = $commentDatas;
        return array_reverse(array_slice(self::$commentDatas[$postid], $start, $limit));
    }
    static function checkPermissions($type, $dataID){
        //TODO yorum eklemeden önce eklemeye izin var mı kontrol et
        switch ($type){
            case "postComment":
                return user::checkLogin();
                break;
            case "delPost":
                database::init();
                database::setPdo(["db" => DB_PREFIX."massdata"]);
                $ownershipPerm = database::select(DB_PREFIX."posts", ["id"])->where(["id", "sender"], $dataID, user::getCurrUserID())->exec();

                return isset($ownershipPerm[0]);
                break;
            case "delCommentPost":
                return user::checkLogin();

                break;
        }
        return false;
    }
    static function getPostData($id){
        database::setPdo(DB_CONFIGS["massdata"]);
        return database::select(self::$defPostsTable)->where(["id"], $id)->exec()[0];

    }
    static function delCommentPost($id){
        if(!self::checkPermissions("delCommentPost", $id)) {
            return false;
        }
        database::setPdo(DB_CONFIGS["massdata"]);
        database::delete(self::$defPostsMetaTable)->where(["id"], $id)->exec();
        \efwEngine\cache::on("delCommentPost", $id);

        return true;
    }
    static function delPost($id){

        if(!self::checkPermissions("delPost", $id)) {
            return false;
        }
        $postInfo = self::getPostData($id);
        if(in_array($postInfo["type"], self::$filePostTypes)){
            cdn::deleteFileURL($postInfo["data"]);
        }
        database::setPdo(DB_CONFIGS["massdata"]);
        database::delete(self::$defPostsTable)->where(["id"], $id)->exec();
        database::setPdo(DB_CONFIGS["massdata"]);
        database::delete(self::$defPostsMetaTable)->where(["postid"], $id)->exec();
        database::setPdo(DB_CONFIGS["massdata"]);
        database::delete(self::$defHashtagTable)->where(["postid"], $id)->exec();
        cache::on("delPost", $id);
        return true;
    }

    static function addCommentOnPost($postid, $comment)
    {
        if($comment == ""){
            return false;
        }
        database::setPdo(DB_CONFIGS["massdata"]);

        $comment = htmlspecialchars($comment);
        if(self::checkPermissions("postComment", $postid))
        database::insert(self::$defPostsMetaTable, ["owner", "type", "postid", "data"], user::getCurrUserID(), "comment", $postid, $comment)->exec();
        \efwEngine\cache::on("addCommentOnPost", $postid);
        $commentID = database::getLastInsertID();

        self::onPostComment($postid, $comment, $commentID);

        return $commentID;

        /*
         * TODO bildirim sistemi içindüzenle
         */
    }
    static function onPostComment($postid, $comment, $commentID){
        syncEngine::onState("addCommentPost", [$postid, user::getCurrUserID(), $comment, $commentID]);
    }
    static function onPostlike($postid)
    {
        /*
         * TODO: bildirim sistemi için düzenle
         */
        syncEngine::onState("addLikePost", [$postid, user::getCurrUserID()]);
    }

    static function onPostUnlike($postid)
    {
        /*
         * TODO: bildirim sistemi için düzenle
         */
        syncEngine::onState("delLikePost", [$postid, user::getCurrUserID()]);
    }

    static function toggleLike($postid)
    {
        if (self::checkLike($postid)) {
            self::onPostUnlike($postid);
            self::delLike($postid);
            return false;
        } else {
            self::onPostlike($postid);
            self::addLike($postid);
            return true;
        }
    }

    static function checkLike($postid)
    {
        if(user::checkLogin()) {
            database::setPdo(DB_CONFIGS["massdata"]);
            return isset(database::select(self::$defPostsMetaTable)->where(["owner", "postid", "type"], user::getCurrUserID(), $postid, "postLike")->exec()[0]);
        }
        }

    static $likerDatas = [];

    static function getLikers($postid)
    {
        if (isset(self::$likerDatas[$postid])) {
            return self::$likerDatas[$postid];
        } else {
            $data = database::select(self::$defPostsMetaTable, ["owner"])->where(["postid", "type"], $postid, "postLike")->exec();
            self::$likerDatas[$postid] = $data;
            return $data;
        }
    }

    static function getLikeCount($postid)
    {
        database::setPdo(DB_CONFIGS["massdata"]);
        if (!isset(self::$likerDatas[$postid])) self::getLikers($postid);
        return count(self::$likerDatas[$postid]);
    }
    static function getCommentCount($postid){
        if(isset(self::$commentDatas[$postid])) {
            return count(self::$commentDatas[$postid]);
        }else{
            self::getCommentsOnPost($postid);
            return count(self::$commentDatas[$postid]);
        }
    }
    static function delLike($postid)
    {
        database::setPdo(DB_CONFIGS["massdata"]);
        database::delete(self::$defPostsMetaTable)->where(["owner", "postid", "type"], user::getCurrUserID(), $postid, "postLike")->exec();
        cache::on("delLike", $postid);
    }
    static function db(): database {
        database::setPdo(DB_CONFIGS["massdata"]);
        return new database();
    }
    static function info($info, $postid){
    return self::db()::select(self::$defPostsTable, [$info])->where(["id"], $postid)->exec()[0][0];
    }
    static function addLike($postid)
    {
        if(user::checkLogin()) {
            database::setPdo(DB_CONFIGS["massdata"]);
            database::insert(self::$defPostsMetaTable, ["owner", "postid", "type"], user::getCurrUserID(), $postid, "postLike")->exec();
            cache::on("addLikePost", $postid);
        }
    }
}