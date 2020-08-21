<?php

namespace efwEngine;


interface userInterface
{
    static function init();
    static function getUserID($username) : int;
    static function updateUser(): bool;
    static function getUserMail(): string ;
    static function sendMail() : bool;
    static function verifyEmail();
    static function register($username, $nickname, $name, $surname, $email, $password): bool ;
    static function login($username, $password, $abot = "");

}