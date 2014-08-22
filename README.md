# 欢迎使用COCOPHP框架

@[完全面向对象|易扩展|高效]

**COCOPHP** 是一个完全面向对象的PHP框架。PHP的框架都泛滥了，为什么我还要捣鼓这个框架出来，初衷很简单：我就是想知道，我能不能写一个框架。借这个机会来训练自己的写码能力和对项目架构的把控能力。

这个框架是在我之前的一个项目上改写的，借鉴了Cola, Yaf等各种框架的设计，其中部分应用类代码直接使用我师父的，感谢师傅栽培。

#####[git地址https://github.com/sunnyingit/CocoPHP](https://github.com/sunnyingit/CocoPHP)

### 特点概述

- **功能单一** ：实现了框架最核心的功能。
- **MVC实现**  ：规范的MVC的实现, 代码可读性很好。
- **完美扩展** ：简单到令人发指的自动加载，你可以随心所欲添加扩展类。
- **异常处理** ：你可以自定义各种异常处理的方式。 
- **数据处理** ：毫不吝啬的提供你各种数据处理函数。
- **SQL记录**  ：框架记录了所有执行的SQL的信息，方便你分析SQL执行效率。
----------

### 流程图
![](http://hellosunli-wordpress.stor.sinaapp.com/blog-uploads/framework.png)

> 是不是觉得和[Yaf](http://www.laruence.com/manual/yaf.sequence.html)的流程图很像

----------
#### 虚拟机配置

####xampp
``` python
<VirtualHost *:80>
    ServerName framework.com
    DocumentRoot "Path\to\CocoPHP\Project\Web"
    <Directory "Path\to\CocoPHP\Project\Web">
        Options Indexes FollowSymLinks
        AllowOverride All
        Order allow,deny
        Allow from all
        <IfModule mod_rewrite.c>
          RewriteEngine On
          RewriteCond $1 !^(index\.php|images|robots.txt)
          RewriteCond %{REQUEST_FILENAME} !-f
          RewriteCond %{REQUEST_FILENAME} !-d
          RewriteRule ^(.*)$ /index.php/$1 [L]
        </IfModule>
    </Directory>
</VirtualHost>
```
----------
#### Nginx的Rewrite (nginx.conf)
``` python
      location / {
            root path/to/CocoPHP/Project/Web;
            index index.php;
       
            if (!-e $request_filename){
                rewrite ^/(.*)$ /index.php?$1 last;
                break;
            }
        }
```
----------
#### 工程目录结构
```
+ Project  // 你的项目的命名，你可以随意取名
  | Web
     |- index.php // 项目的入口文件
  |+ Conf
     |- Gloabl.conf.php // 全局配置文件
     |- Router.conf.php // 路由配置文件，你可以通过此文件修改默认路由
     |- Redis.conf.php // redis配置文件
     |- Db.conf.php // 数据库配置文件
  |+ Controllers
     |- Index.php //默认控制器
  |+ Data // 日志数据，文件缓存数据，可以保持在此目录中，当然，你也可以自定义这些数据保存的位置
  |+ Views    
     |+ index   //控制器, 首字母需要小写
        |- index.html //默认视图，首字母需要小写 .html 这个后缀名你也可以自己定义
  |+ Library // 本地类库
  |+ Models  //model目录
```
----------
#### 入口文件
``` php
define('APP_PATH', dirname(__DIR__) . '/');

define('SYS_PATH', dirname(APP_PATH) . '/System/');

require SYS_PATH . 'Core/App.php';

Core_App::getInstance()->run();
```
----------
#### 控制器
在Yaf中, 默认的模块/控制器/动作, 都是以Index命名的, 可通过配置文件修改的.
对于默认模块, 控制器的目录是在project目录下的controllers目录下, Action的命名规则是"名字+Action"
``` php
class Controller_Index extends Controller_Abstract
{
	public function indexAction()
	{
		echo "hello world";
	}
}
```
----------
#### 视图
对于默认模块, 视图文件的路径是在Project目录下的Views目录中以小写的action名的目录中.支持PHP的短标签
``` php
<html>
 <head>
   <title>Hello World</title>
 </head>
 <body>
  <?php echo $content;?>
 </body>
</html>
```
----------


---------
我使用的是markdown编写此份文档。
