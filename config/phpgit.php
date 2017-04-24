<?php

return array(

    /*
     |--------------------------------------------------------------------------
     | Php Git Settings
     |--------------------------------------------------------------------------
     |
     | phpGit is enabled by default, when debug is set to true in app.php.
     | You can override the value by setting enable to true or false instead of null.
     |
     */
    'enabled' => env('APP_DEBUG', false),

    /*
     | if route_prefix = '_tool', then the url is http://<your_domain>/_tool/git
     */
    'route_prefix' => '_tool',

    'git_path' => '/usr/bin/git',

    /*
     | git repositories root path
     */
    'repo_list' => [
        'laravel51'=>'/Users/davin/Sites/laravel51/'
    ],

    /*
     | php git will run this command when checkout the branch
     */
    'command' => [
//        'composer dump-autoload',
//        'php artisan migrate --seed',
//        'php artisan acl:update',
        'php artisan patch:script',
    ]
);