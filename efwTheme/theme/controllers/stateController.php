<?php
class stateController
{
    static $state = null;

    static function getState(){
        $state = self::$state;
        if (!is_null($state))
        {
            echo "<a  ".($state[0] ? "style='color: green'" : "style='color: red'").">".$state[2]."</a>";
        }

    }
    static function setState($stateBool, $message){
        self::$state[0] = $stateBool;
        self::$state[2] = $message;
    }

}
?>