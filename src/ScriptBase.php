<?php

namespace DavinBao\PhpGit;


class ScriptBase{

    protected static $envPath = '/.env';

    public static function replace($oldString,$newString){
        $filePath = dirname(app_path()).self::$envPath;
        if(file_exists($filePath)){
            file_put_contents($filePath, str_replace($oldString, $newString, file_get_contents($filePath)));
        }
    }
}