# 基于Laravel的翻墙管理系统
### 简介

#### 翻墙管理系统 **shadowsocks-manage-system** 是一个基于Laravel5的一个vpn用户管理系统，使用Swoole框架与SSServer通信，实现添加用户、编辑用户、统计每天、每周、每月以及总的流量使用情况，还可以根据用户的流量使用情况进行排名，直观地反应出每个用户的流量使用。  
### 展示
![rank image](https://raw.githubusercontent.com/ZhuFaner/shadowsocks-manage-system/master/public/image/rank.png)  

![detail image](https://raw.githubusercontent.com/ZhuFaner/shadowsocks-manage-system/master/public/image/detail.png)  
### 使用  

#### 1. 在你的网站目录下，执行git clone [https://github.com/ZhuFaner/shadowsocks-manage-system.git](]https://github.com/ZhuFaner/shadowsocks-manage-system.git)，更改storage和bootstrap的权限为777
#### 2. 在.env中配置你的数据库连接
    DB_CONNECTION = mysql  
    DB_HOST = 127.0.0.1  
    DB_PORT = 3306
    DB_DATABASE = db_name
    DB_USERNAME = name
    DB_PASSWORD = your password  
#### 3. 进行数据库迁移操作，只需要在根目录下执行
	php artisan migrate  
#### 4. 安装shadowsocks
#### Debian/Ubuntu
    apt-get install python-pip
    pip install git+https://github.com/shadowsocks/shadowsocks.git@master
#### CentOS
	yum install python-setuptools && easy_install pip
	pip install git+https://github.com/shadowsocks/shadowsocks.git@master
##### 详细安装教程请参阅: [https://github.com/shadowsocks/shadowsocks/blob/master/README.md](https://github.com/shadowsocks/shadowsocks/blob/master/README.md)
#### 5. 在swoole_config.json中配置swoole与SSServer通信连接所需的信息
	{  
		"service_port": 6001,  
		"address":"127.0.0.1",
		"dsn":"mysql:host=localhost;dbname=shadow_manage",
		"db_user": "root",
		"db_password": "",
		"interval_time": 5000
	}
#### 6. 安装swoole
##### swoole项目已经收录到PHP官方扩展库，可以通过PHP官方提供的pecl命令，一键下载安装swoole  
	pecl install swoole
如果没有安装pecl，请先安装pecl。如在CentOS上，需要安装php-pear和php-devel（php-pear包含pecl,php5-dev包含phpize,pecl依赖phpize），执行
	yum install php-pear
##### swoole安装参考文档：[https://wiki.swoole.com/wiki/page/7.html](https://wiki.swoole.com/wiki/page/7.html)

#### 7. swolle安装成功后，在php.ini中添加swoole扩展
	extension = swoole.so
	
#### 8. 除了在系统中添加端口号，也可以在配置文件shadowsocks.json中配置需要添加到SSServer的端口号
	 {
	    "server": "0.0.0.0",
	    "port_password": {
	        "端口号": "密码"
	    },
	    "timeout": 300,
	    "method": "aes-256-cfb"
    }
#### 9. SwooleTask.php专门负责与SSServer的通信工作，主要负责两部分工作，一个是查询数据库中添加的端口，向SSServer中添加，每个50秒查询一次数据库，并把端口添加到SSServer.；另一个工作是统计SSServer返回的端口流量并记录到数据库中。执行如下命令：
	· ssserver --manager-address 127.0.0.1:6001 -c /var/www/shadowsocks_manage/shadowsocks.json  -d start  
	· php SwooleTask.php
第一条命令后台启动SSServer，第二条命令运行swoole进程与SSServer进行通信。至此，翻墙管理系统的配置已经完成了。

### 拓展
为了确保与服务器与SSServer的通信不中断，我们需要一个后台守护进程，专门守护我们的Swoole进程，这里我们可以使用Supervisor。这个我就不多说了，感兴趣的朋友可以自己尝试做一下。  
  

---  
>#### 如果部署过程中遇到问题或者发现项目中存在的不足，请及时提出来大家一起交流哈(￣▽￣)"

