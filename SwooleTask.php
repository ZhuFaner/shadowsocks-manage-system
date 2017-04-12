<?php

        $file_path = dirname(__FILE__).'/swoole_config.json';
        $json_string = file_get_contents($file_path);
        $config = json_decode($json_string);
        $service_port = $config->service_port;
        $address = $config->address;
        $dsn = $config->dsn; //构造数据源，mysql是数据类型，localhost是主机地址，shadow_manage是数据库名称
        $db_user = $config->db_user; //数据库用户名
        $db_password = $config->db_password; //登录数据库的密码
        $interval_time = $config->interval_time; //向SSServer添加端口号的间隔时间

        $client = new swoole_client(SWOOLE_SOCK_UDP, SWOOLE_SOCK_ASYNC);
        $client->on("connect", function(swoole_client $cli) use($dsn,$db_user,$db_password) {
	        $db = new PDO($dsn,$db_user,$db_password);
	        $sql = 'select * from members';
	        $query = $db->query($sql);
	        $query->setfetchmode(pdo::FETCH_ASSOC); //设置数组关联方式
	        $result = $query->fetchAll();
	        $db = null;
	        // mysql_close($con);
	        if(!empty($result)){
	                foreach($result as $array){
	                    $attr = array(
	                        'server_port' => (int)$array['port'],
	                        'password' => $array['password']
	                    );
	                    $jsonAttr = 'add:'.json_encode($attr);
	                    $cli->send($jsonAttr);
	                }
	            }

        });
        $client->on("receive", function(swoole_client $cli, $data){
            echo "Receive: $data\n";
            // stat: {"8001":11370}
            sleep(1);
        });
        $client->on("error", function(swoole_client $cli){
            echo "error\n";
        });
        $client->on("close", function(swoole_client $cli){
            echo "Connection close\n";
        });
        $client->connect($address, $service_port); 

        //每50秒遍历一遍数据库，把所有端口都添加到ssserver中,从第50秒开始
        swoole_timer_tick($interval_time,function() use($service_port,$address,$dsn,$db_user,$db_password){
	        $client = new swoole_client(SWOOLE_SOCK_UDP, SWOOLE_SOCK_ASYNC);
	        $client->on("connect", function(swoole_client $cli) use($dsn,$db_user,$db_password){
		        $db = new PDO($dsn,$db_user,$db_password);
		        $sql = 'select * from members';
		        $query = $db->query($sql);
		        $query->setfetchmode(pdo::FETCH_ASSOC); //设置数组关联方式
		        $result = $query->fetchAll();
		        $db = null;
		        // mysql_close($con);
		        if(!empty($result)){
		                foreach($result as $array){
		                    $attr = array(
		                        'server_port' => (int)$array['port'],
		                        'password' => $array['password']
		                    );
		                    $jsonAttr = 'add:'.json_encode($attr);
		                    $cli->send($jsonAttr);
		                }
		            }

		        
	        });
	        $client->on("receive", function(swoole_client $cli, $data)  use($dsn,$db_user,$db_password){
	            echo "Receive: $data\n";
	            updateData($data,$dsn,$db_user,$db_password);
	            sleep(1);
	        });
	        $client->on("error", function(swoole_client $cli){
	            echo "error\n";
	        });
	        $client->on("close", function(swoole_client $cli){
	            echo "Connection close\n";
	        });
	        $client->connect($address, $service_port); 
		});
        
        /**
        * 更新端口号的数据流量
        */
		function updateData($json,$dsn,$db_user,$db_password){
			$stat = 'stat: ';
			if(!strstr($json, $stat)){
				return;
			}
			$json = str_replace($stat, '', $json);
			$db = new PDO($dsn,$db_user,$db_password);
			$array = json_decode($json);
			if(empty($array)){
				return;
			}
			foreach ($array as $key => $value) {
				try{
					//向数据库中记录一条流量
					date_default_timezone_set('PRC');
					$currentTime = "'".date('Y-m-d H:i:s',time())."'";
					$values = $key.','.time().','.$currentTime.','.$value;
					$sqlFlow = 'insert into flows(port,time,date_time,flow) values('.$values.')';
					$db->exec($sqlFlow);
					echo $sqlFlow;

					//向数据库中记录总流量
					$sql = 'select * from members where port='.$key;
					$query = $db->query($sql);
		        	$query->setfetchmode(pdo::FETCH_ASSOC); //设置数组关联方式
		        	$result = $query->fetchAll();
		        	if(!empty($result)){
		        		foreach($result as $data){
		        			echo "数据流量是：$value";
		        			$flow = $data['flow']+$value;
		        			$update = 'update members set flow = '
		        						.$flow.' where port = '.$data['port'];
		        			$db->exec($update);
		        			// echo "\n查询的结果是".$value['port'];
		        		}
		        	}
				}catch(PDOException $e){

				}
				
			};
			$db = null;
		}
