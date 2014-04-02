withbitcoin
===========

Source code for http://withbitcoin.co.nz.

## Installation

Uses [https://getcomposer.org/](Composer) dependency manager, PHP 5.3+.

```
git clone https://github.com/soundasleep/withbitcoin.git
cd withbitcoin
cp config.php.sample config.php && vim config.php
composer install
```

And then point your Apache/etc to the `web` directory:

```
Alias "/withbitcoin" "/var/www/withbitcoin/web"
<Directory "/var/www/withbitcoin/web">
   Options Indexes FollowSymLinks
   DirectoryIndex index.html index.php default.html default.php
   AllowOverride All
   Allow from All   
</Directory>
```
