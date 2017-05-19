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

    'ssh_private_key' => '/root/.ssh/id_rsa',

    /*
     | git repositories root path
     */
    'repo_list' => [
        'laravel51'=>'/Users/davin/Sites/laravel51/'
    ],

    /*
      | git script path
      */

    'path'=>'/database/patchs/',

    /*
     | php git will run this command before checkout the branch
     */
    'install_command' => [
        env('PHP_GIT_COMMAND', '/xmisp/server/php7/bin/php artisan patch:db -i'),
        env('PHP_SCRIPT_COMMAND', '/xmisp/server/php7/bin/php artisan patch:script -i'),
    ],

    /*
     | php git will run this command after checkout the branch
     */
    'uninstall_command' => [
        env('PHP_GIT_COMMAND', '/xmisp/server/php7/bin/php artisan patch:db -u'),
        env('PHP_SCRIPT_COMMAND', '/xmisp/server/php7/bin/php artisan patch:script -u'),
    ],
);