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

 开始切换分支时
 1.运行指定目录下php文件删除配置文件的修改
 2.恢复当前分支对数据库修改的SQL执行

 完成分支切换后
 1.运行指定目录下php文件修改配置文件
 2.执行对当前分支数据库修改的SQL

## 配置参数

1.关于gitphp项目配置的添加

 'path'=>'sql文件的存放路径',

 'install_command' => [

  env('PHP_GIT_COMMAND', '（当前php文件的所在路径）php artisan patch:db -i'),

  env('PHP_SCRIPT_COMMAND', '（当前php文件的所在路径）php artisan patch:script -i')

  ],

 'uninstall_command' => [

  env('PHP_GIT_COMMAND', '（当前php文件的所在路径）php artisan patch:db -u'),

  env('PHP_SCRIPT_COMMAND', '（当前php文件的所在路径）php artisan patch:script -u'),

   ]

   提示：必须是.env文件里没有配置 PHP_GIT_COMMAND 和 PHP_SCRIPT_COMMAND 参数才可生效

2.在 sql文件的存放路径 创建文件（[branch name] 分支名）

  创建6个文件：

  [branch name].php   修改配置文件参数脚本文件 需含有 install，uninstall 两个方法(只影响测试环境)

  [branch name]-uninstall.sql  切换分支前执行的安装sql语句(只影响测试环境)

  [branch name]-install.sql  切换分支后执行的卸载的sql语句(只影响测试环境)

  [branch name]-production.php   修改配置文件参数脚本文件，该文件只用于合并生成只在生产环境生效的参数配置文件 需含有 install，uninstall 两个方法(只影响生产环境)

  [branch name]-production-uninstall.sql 安装的sql语句，该文件只用于合并生成只在生产环境生效的sql语句文件(只影响生产环境)

  [branch name]-production-install.sql 卸载的sql语句，该文件只用于合并生成只在生产环境生效的sql语句文件(只影响生产环境)