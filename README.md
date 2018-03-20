# php-flask
 一个模仿 Flask 的 PHP 微型框架

## 思路
PHP 是一个多入口的大型程序，具有不太好维护的特点。而且基于`.php`文件后缀访问，容易给黑客造成上传文件形成攻击的漏洞

目前公司实践下来发现了 `PHP + Nginx` 的配置维护起来比较困难，主要是 Ngnix 的灵活性不够，需要编辑规则文件（规则不容易被普通程序员维护）

这里就模仿 Flask 实现一个单一 Webapp 入口的 微型框架，目的是简化 Nginx 配置，实现单一入口管理，过滤防止攻击

## 图谱思路
![原来的配置](php-flask-2.jpg)

![本框架达成的配置](php-flask-1.jpg)

## 简化后的 Nginx配置

```
server {
    listen 80;
    root /var/www/html;
    server_name example.com;
    
    access_log  syslog:server=xxx.xxx.xxx.xxx:xxx,facility=yourservercofig,tag=nginx,severity=info json;


    location = /path/to/your/folder/index.php { --> index.php 统一入口
        include snippets/fastcgi-php.conf;
        fastcgi_pass 127.0.0.1:9000;
    }

    location ~ /path/to/your/folder/(.+){ --> 简化了，全部去到 index.php 统一入口
        rewrite ^/path/to/your/folder/(.+)$ /path/to/your/folder/index.php?r=$1&$args;
    }

    location / {
        return 404;
    }
}
```