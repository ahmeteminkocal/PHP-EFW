<?php


namespace efwTheme;


use efwEngine\app\featureSet;
use efwEngine\app\system;
use efwEngine\app\user;
use efwEngine\cache;
use efwEngine\cdn;

class engine
{

    static function isMobile(){
        $useragent=$_SERVER['HTTP_USER_AGENT'];

        return preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4));
    }
    static function replacements()
    {
        $key = "replacements";
        if(!cache::exists($key)) {
            //TODO optimize et
            $data = [
                "cdnAddress" => cdn::getURL()
            ];
            foreach (self::links() as $link => $val) {
                $data[$link] = $val;
            }
            cache::add($key, $data);
            return $data;
        }
        return cache::get($key);
    }
    static function getCurrentDomain($replaceWWW = true){
        if($replaceWWW)
        return str_replace("www.", "", $_SERVER['HTTP_HOST']);
        return $_SERVER['HTTP_HOST'];

    }
    static $siteNames = [];
    static function registerSite($host, $cdn, $name, $id){
        cdn::$cdnURLs[$host] = $cdn;
        self::$siteNames[$host] = $name;

    }
    static function getActiveSiteName(){
        return self::$siteNames[self::getCurrentDomain()];
    }
    static function buildPage($address)
    {
        $address = ltrim($address, "/");
        if($address == "") $address = 0;
        $adres = router::getAddresses()[$address];
        $cont = true;
        $adresToBeExecuted = $adres;
        $extraScripts = "";

        while(is_array($adresToBeExecuted)){
            if(isset($adresToBeExecuted["controller"])){
                self::workerExecutor("controller", $adresToBeExecuted["controller"]);
            }
            if(isset($adresToBeExecuted["engine"])){
                self::workerExecutor("engine", $adresToBeExecuted["engine"]);
            }
            if(isset($adresToBeExecuted["module"])){
                self::workerExecutor("module", $adresToBeExecuted["module"]);
            }
            if(isset($adresToBeExecuted["parameters"])){
                if(is_array($adresToBeExecuted["parameters"]))
                $extraScripts =  router::parameterExecutor(...$adresToBeExecuted["parameters"]);
                else
                    $extraScripts =  router::parameterExecutor($adresToBeExecuted["parameters"]);

            }
            $adresToBeExecuted = self::addressNormaliser($adresToBeExecuted);
        }
        if(!file_exists($adresToBeExecuted)){
            system::redirect("/404");
        }
        $file = file_get_contents($adresToBeExecuted);
        self::workerEngine($file);
        self::workerEngine($file);

        $search = array(
            '/\>[^\S ]+/s',     // strip whitespaces after tags, except space
            '/[^\S ]+\</s',     // strip whitespaces before tags, except space
            '/(\s)+/s',         // shorten multiple whitespace sequences
            '/<!--(.|\s)*?-->/' // Remove HTML comments
        );

        $replace = array(
            '>',
            '<',
            '\\1',
            ''
        );
        $file = preg_replace($search, $replace, $file);
        $addressRobotiser = router::addressRobotiser($adresToBeExecuted);
        $fileContent =  "<!-- $adresToBeExecuted -->".$extraScripts . $file;
        if(!file_exists("dist/")) mkdir("dist");
        file_put_contents("dist/" . $addressRobotiser . ".php", $fileContent);

        cache::add($addressRobotiser, "ok");
    }
    static function addressNormaliser($adresToBeExecuted){

        $is_array = is_array($adresToBeExecuted);
        if($is_array === false){
                return router::getAddresses()[$adresToBeExecuted];
            }else{
                return $adresToBeExecuted[0];
            }
    }
    static function workerEngine(&$fileData){
        foreach (self::replacements() as $text => $val) {
            $fileData = str_replace("{{" . $text . "}}", $val, $fileData);

        }
        preg_match_all('/\{\{(.*?)\}\}/i', $fileData, $regs, PREG_PATTERN_ORDER);
        for ($i = 0; $i < count($regs[1]); $i++) {
            $found =  $regs[1][$i];
            $worker = explode(":",$found);
            $data = trim(explode(":",$found)[1]);
            $worker = explode(":",$found)[0];
            $toBeReplaced = '{{'.$found.'}}';
            $fileData = str_replace($toBeReplaced, self::workerExecutor($worker, $data), $fileData);


        }
    }
    static function workerExecutor($worker, $data){
        switch ($worker){
            case "engine":
                self::runEngine($data);
                break;
            case "module":
                return self::getModule($data);
                break;
            case "controller":
                return self::getController($data);
                break;
            default:
                return "";
                break;
        }
    }
    static function runEngine($engine){

            include("theme/engines/".$engine.".php");

    }
    static function runPage($address, $addressData = "")
    {
        if($addressData != ""){


        }else {
            include "dist/" . $address . ".php";
            die;
        }
    }

    static function send404()
    {
        system::redirect("/404/");
    }
    static function getModule($module){
        return file_get_contents("theme/modules/".$module.".php");
    }
    static function getController($controller){
        $data = "";

        if(is_array($controller)){
            foreach ($controller as $control){
            $data .= file_get_contents("theme/controllers/".$control.".php");
            }
        }else{
            return file_get_contents("theme/controllers/".$controller.".php");

        }
        return $data;
    }
    static function addController($controller){
        include "theme/controllers/".$controller.".php";
    }
    static function generateRandomString($length = 10) {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    static function execController($controller){
        if(is_array($controller)){
            foreach ($controller as $exec)
            include "theme/controllers/".$exec.".php";
        }else
        include "theme/controllers/".$controller.".php";
    }
    static function links(){
        $linkler = array();
        $linkler["loginLink"] = "/account/login/";
        //$linkler["registerLink"] = "/account/register/";
        $linkler["logoutLink"] = "/xadmin/account/logout/";
   //     $linkler["userProfileLink"] = "/account/profile/";
        return $linkler;
    }
}