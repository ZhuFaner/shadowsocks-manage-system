# Shadowsocks 翻墙管理系统

## 基本特性

- [x] PHP 技术栈（Laravel + MySQL + Swoole）
- [x] 单服务器多用户管理系统
- [x] 使用网页添加、编辑、删除用户
- [x] 单独统计每个用户每天、每周、每月的流量值及流量使用趋势
- [x] 首页以小时、天、周为维度展示每个账号的流量使用排行，便于找出资源消耗过量者

## 截图展示

![rank image](https://raw.githubusercontent.com/ZhuFaner/shadowsocks-manage-system/master/public/image/rank.png)  

![detail image](https://raw.githubusercontent.com/ZhuFaner/shadowsocks-manage-system/master/public/image/detail.png)  

## 原理剖析

1. ssserver 即 shadowsocks 的服务进程，以 manager 模式启动，这样就可以通过 udp 协议对其进行管理，可以在其运行过程中动态地添加、更新、删除账号，同时 ssserver 会将流量信息发送给通过 udp 协议控制它的某个进程
2. Swoole 进程独立运行，每隔 5 秒查询一次数据库，将所有的端口号-密码组合向 ssserver 添加一遍（确实很 dirty，但是没办法）
3. 网站部分有两个功能：
    1. 添加账号时，直接将数据写入数据库，等待 Swoole 进程每 5 秒一次的读取
    2. 删除账号时，使用 Swoole 的 udp 客户端的同步阻塞模式（简单独立调用，跟 Swoole 进程无关），给 ssserver 发送删除请求。更改密码采用 删除再新增 实现。

## 如何使用

### 一、部署 web 部分

#### 1. 部署网站代码

```bash
git clone https://github.com/ZhuFaner/shadowsocks-manage-system.git
```

更改权限：

```bash
cd shadowsocks-manage-system
sudo chmod -R 777 storage bootstrap/cache
```

#### 2. 配置数据库

修改 .env 文件：

```php
DB_CONNECTION = mysql  
DB_HOST = 127.0.0.1  
DB_PORT = 3306
DB_DATABASE = db_name
DB_USERNAME = name
DB_PASSWORD = your password  
```

#### 3. 进行数据库迁移，将所需的表结构写入数据库

```php
php artisan migrate
```
#### 4. 配置自己的服务器域名
在config/app.php中修改ss_domain为自己的服务器域名

```php
'ss_domain' => 'your domain name',
```

### 二、安装并配置 shadowsocks

#### 1. 安装

① Ubuntu

```bash
sudo apt install python-pip
pip install git+https://github.com/shadowsocks/shadowsocks.git@master
```

② CentOS

```bash
sudo yum install python-setuptools && easy_install pip
pip install git+https://github.com/shadowsocks/shadowsocks.git@master
```

> 详细安装教程: https://github.com/shadowsocks/shadowsocks/blob/master/README.md

#### 2. 配置 shadowsocks

修改 `shadowsocks-manage-system/shadowsocks.json` 中的端口号和密码为你喜欢的值：

```json
{
    "server": "0.0.0.0",
    "port_password": {
        "端口号": "密码"
    },
    "timeout": 300,
    "method": "aes-256-cfb"
}
```

#### 3. 启动 shadowsocks 服务

将下列命令中的 ooxx 修改为真实的路径，执行一次：

```bash
ssserver --manager-address 0.0.0.0:6001 -c ooxx/shadowsocks-manage-system/shadowsocks.json -d restart
```
### 三、安装并配置 Swoole

#### 1. 安装swoole

先安装 pecl。

CentOS 系统：

```bash
sudo yum install php-pear pecl
```

Ubuntu 系统：

```bash
sudo apt install php-pear php-dev
```

然后使用 pecl 安装 swoole：

```php
pecl install swoole
```


#### 2. 在 php.ini 中添加 swoole 扩展

```php
extension = swoole.so
```
	
#### 3. 修改配置文件

修改 `shadowsocks-manage-system/swoole_config.json`：

```json
{
	"service_port": 6001,
	"address":"127.0.0.1",
	"dsn":"mysql:host=localhost;dbname=shadow_manage",
	"db_user": "root",
	"db_password": "",
	"interval_time": 5000
}
```

#### 4. 启动 Swoole 进程

```bash
php SwooleTask.php
```

#### 5. 维护 swoole 进程【可选】

Swoole 进程需要一直存在于内存中，以源源不断地接收来自 ssserver 进程的流量信息，同时承担着每 5 秒一次的账号更新操作。推荐使用 Supervisor 来维护 Swoole 进程，具体可以自行搜索，使用起来还是挺简单的。

<hr>

> 遇到问题请提交 issue~

