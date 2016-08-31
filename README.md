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