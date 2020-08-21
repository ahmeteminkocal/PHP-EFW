<?php
echo "çalışıyor";
ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);
include "../efwEngine/vendor/autoload.php";
error_reporting(E_ALL);
$bot = \seregazhuk\PinterestBot\Factories\PinterestBot::create();
var_dump($bot);
// Login
$bot->auth->login('', '');

// Get lists of your boards
$boards = $bot->boards->forUser('');
var_dump($boards);
// Create a pin
//$bot->pins->create('http://exmaple.com/image.jpg', $boards[0]['id'], 'Pin description');

?>