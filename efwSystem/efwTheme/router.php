<?php


namespace efwTheme;


use efwEngine\app\system;
use efwEngine\app\user;
use efwEngine\cache;
use efwEngine\config;
use TRegx\SafeRegex\Exception\CompileSafeRegexException;
use TRegx\SafeRegex\Exception\RuntimeSafeRegexException;
use TRegx\SafeRegex\Exception\SafeRegexException;
use TRegx\SafeRegex\Exception\SuspectedReturnSafeRegexException;
use TRegx\SafeRegex\preg;
use efwEngine\database;
use efwEngine\preCheckEngine;
use function Couchbase\defaultDecoder;

class router
{
    static $addresses = [];
    static $parameters = [];

    static function getAddresses()
    {

        return self::$addresses;
    }

    static function autoForwardWWW()
    {

    }

    static function activeSetter($curPage)
    {
        $strpos = strpos($curPage, self::getAddress());
        if ($curPage === self::getAddress()) {
            return "active";

        } else {
            return false;
        }

    }

    static $subAddresses = [
        "userProfile" => ["fotolar"]
    ];

    static function getSubAddresses()
    {

    }

    static $defThemeFolder = "theme/pages/";
    static function checkBan(){
        $ip = user::getIP();
        database::setPdo(["db" => DB_PREFIX."system"]);
        $x = database::select("ipbans", "ip")->where(["ip"], $ip)->exec(true);
        if($x) die();
    }
    static function setAddresses()
    {
      //  self::checkBan();
        self::$addresses = config::getCurrentSiteConfig()["routes"];

    }
    private static $_routeParams = [];
    static function setRouteParameter($param){
    self::$_routeParams = $param;
    }
    static function getRouteParameters(){
        return self::$_routeParams;
    }
    static function addAddress($route, $mode, $data = "")
    {
        switch ($mode) {
            case "controller":
                self::$addresses[$route] = [$mode => $data];
                break;
            default:
                die("Router Add Address Undefined mode: $route");
                break;
        }
    }

    static function getAddress()
    {


        return $_SERVER['REQUEST_URI'];
    }

    static function parameterExecutor(...$parameters)
    {
        foreach ($parameters as $parameter) {
            switch ($parameter) {
                case "indexRedirect":
                    include "efwSystem/efwEngine/app/parameterFiles/indexRedirect.php";
                    break;
                case "loginOnly":
                    return file_get_contents("efwSystem/efwEngine/app/parameterFiles/loginOnly.php");
                    break;
                default:
                    echo "noParam";
                    break;
            }

        }
    }

    static function addressRobotiser($address)
    {
        return md5(json_encode($address));
    }

    static function addressExecutor($address)
    {
        $address = explode("?", $address)[0];
        $checkPageExists = self::checkPageExists($address);
        $address = $checkPageExists;

        if ($checkPageExists || $checkPageExists === 0) {
            $addressNormaliser = engine::addressNormaliser($address);
            if (!is_array($addressNormaliser)) {
                $fileName = explode(".", $addressNormaliser);
                $fileExt = end($fileName);
                $fileOrj = pathinfo($addressNormaliser, PATHINFO_FILENAME);
                switch ($fileExt) {
                    case "js":
                        if (file_exists($addressNormaliser)) {
                            header('Content-Type: application/javascript');
                            header('X-Accel-Redirect: /efwTheme/' . $addressNormaliser);
                            header('Content-Disposition: filename="' . $fileOrj . '"');
                            exit();
                        } else {
                            engine::send404();

                        }
                        die;
                        break;
                    case "json":
                        if (file_exists($addressNormaliser)) {
                            header('Content-Type: application/json');
                            header('X-Accel-Redirect: /efwTheme/' . $addressNormaliser);
                            header('Content-Disposition: filename="' . $fileOrj . '"');
                            exit();
                        } else {
                            engine::send404();

                        }
                        die;
                        break;
                    case "gz":
                        if (file_exists($addressNormaliser)) {
                            header('Content-Type: application/gzip');
                            header('X-Accel-Redirect: /efwTheme/' . $addressNormaliser);
                            header('Content-Disposition: filename="' . $fileOrj . '"');
                            exit();
                        } else {
                            engine::send404();

                        }
                        die;
                        break;
                    case "xml":
                        if (file_exists($addressNormaliser)) {
                            header('Content-Type: application/xml');
                            header('X-Accel-Redirect: /efwTheme/' . $addressNormaliser);
                            header('Content-Disposition: filename="' . $fileOrj . '"');
                            exit();
                        } else {
                            engine::send404();

                        }
                        die;
                        break;
                    case "txt":

                        if (file_exists($addressNormaliser)) {
                            header('X-Accel-Redirect: /efwTheme/' . $addressNormaliser);
                            header("Content-Type: text/plain");
                            header('Content-Disposition: filename="' . $fileOrj . '"');

                        } else {
                            engine::send404();
                        }
                        die;
                        break;

                }
            }


            if (is_array($addressNormaliser)) {
                if(isset($addressNormaliser["preCheck"])){
                   $state = preCheckEngine::checkRoute($addressNormaliser["preCheck"]);
                    if(!$state) die("CHKER");
                }
                if(isset($addressNormaliser["ob_start"])){
                    if($addressNormaliser["ob_start"]){
                        ob_start();
                    }
                }
                if(isset($addressNormaliser["routeParams"])){

                   router::setRouteParameter($addressNormaliser["routeParams"]);
                }
                if (isset($addressNormaliser[0])) {
                    $addressNormaliser = $addressNormaliser[0];

                } else {
                    if (isset($addressNormaliser["controller"])) {
                        engine::execController($addressNormaliser["controller"]);
                    }
                }
            }
            if (!is_array($addressNormaliser)) {
                $MD5page = self::addressRobotiser($addressNormaliser);
                if (self::checkBuild($MD5page)) {

                    engine::runPage($MD5page);
                } else {
                    engine::buildPage($address);
                    engine::runPage($MD5page);

                }
            }

        } else {

            engine::send404();
        }

    }

    /*
     * sayfa var mı diye kontrol et, varsa işleme koy
     */
    static function checkPageExists($page)
    {
        $keyX = "routeForURL-$page";
        if (cache::exists($keyX)) {
            $params = cache::get($keyX . "-params");
            foreach ($params as $no => $param) {
                self::setParameters($no, $param);
            }
            return cache::get($keyX);
        } //devre dışı
        else {

            $addresses = self::getAddresses();

            $page = ltrim($page, "/");
            $page = urldecode($page);
            foreach ($addresses as $address => $val) {
                if ($page != "") {
                    $strpos = strpos($page, (string)$address);
                    if ($strpos === 0) {

                        return $address;
                    }

                } else {
                    return "anasayfa";
                }
                if ($page === $address) {
                    return $page;
                }
                $orig = $address;
                $address = str_replace("/", "\/", $address);
                try {
                    preg::match_all('/^' . $address . '/', $page, $matches);
                } catch (CompileSafeRegexException $e) {
                } catch (RuntimeSafeRegexException $e) {
                } catch (SuspectedReturnSafeRegexException $e) {
                } catch (SafeRegexException $e) {
                }
                if (isset($matches[0][0])) {

                    if (isset($matches[1])) {

                        $params = explode("/", $matches[1][0]);
                        cache::add($keyX . "-params", $params);
                        foreach ($params as $no => $param) {
                            self::setParameters($no, $param);
                        }
                    }
                    cache::add($keyX, $orig);
                    return $orig;
                }


            }
            return false;
        }
    }

    static function setParameters($parameter, $val)
    {
        self::$parameters[$parameter] = $val;
    }

    static function getParameter($param)
    {
        self::$parameters[$param];
    }

    static function getParameters()
    {
        return self::$parameters;
    }

    static function checkBuild($MD5page)
    {
        if (cache::exists($MD5page)) {
            if (file_exists('dist/' . $MD5page . '.php')) {
                return true;
            }
        }

        return false;
    }
}