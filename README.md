## PHP Git Client For Laravel

This project uses the Laravel 5.1 framework. Actually this is starter Laravel 5.1 project. It has add git repository, switch branch, checkout / delete branch .etc

1. [Bootstrap 3](http://getbootstrap.com/) - can be found in ```Resources/```
2. [jQuery 1.10.1](https://jquery.com/) - can be found in ```public/js/jquery.js```

# Installation
1. add code in composer.json

```
"davin-bao/php-git": "1.0-dev"
```

2. Add this to your service provider in app.php:

```
DavinBao\PhpGit\PhpGitServiceProvider::class,
```

3. Copy the package config to your local config with the publish command:

```
php artisan vendor:publish --provider="DavinBao\PhpGit\PhpGitServiceProvider"
```
4. SET umask 022 to umask 000 in /etc/profile

## Configuration

config file is in app/config/phpgit.php

5. default, you can visit the url

http://your_domain/_tool/git

## 添加的新功能

 分支切换前
 1.运行指定目录下php文件删除配置文件的修改
 2.恢复当前分支对数据库修改的SQL执行
 分支切换后
 1.运行指定目录下php文件修改配置文件
 2.执行对当前分支数据库修改的SQL

## 配置参数

1.关于gitphp项目配置的添加

 'path'=>'sql文件的存放路径',

 'install_command' => [

  env('PHP_GIT_COMMAND', '（当前php文件的所在路径）php artisan patch:db --uninstall=true'),

  env('PHP_SCRIPT_COMMAND', '（当前php文件的所在路径）php artisan patch:script --uninstall=true')

  ],

 'uninstall_command' => [

  env('PHP_GIT_COMMAND', '（当前php文件的所在路径）php artisan patch:db --uninstall=false'),

  env('PHP_SCRIPT_COMMAND', '（当前php文件的所在路径）php artisan patch:script --uninstall=false'),

   ]

   提示：必须是.env文件里没有配置 PHP_GIT_COMMAND 和 PHP_SCRIPT_COMMAND 没有配置这两个参数才可生效

2.在 sql文件的存放路径 创建文件（BranchName 分支名）

  创建3个文件：

  BranchName.php   修改配置文件参数含有 install，uninstall 两个方法

  BranchName-uninstall.sql  切换分支前执行的安装sql语句

  BranchName-install.sql  切换分支后执行的卸载的sql语句