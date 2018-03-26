<?php

$file_path = dirname(__FILE__).'/swoole_config.json';
$json_string = file_get_contents($file_path);
$config = json_decode($json_string);
// $service_port = $config->service_port;  //节点监听端口号
// $address = $config->address;  //节点地址
$dsn = $config->dsn; //构造数据源，mysql是数据类型，localhost是主机地址，shadow_manage是数据库名称
$db_user = $config->db_user; //数据库用户名
$db_password = $config->db_password; //登录数据库的密码
$interval_time = $config->interval_time; //向SSServer添加端口号的间隔时间

//全局存储器，以防数据库故障
date_default_timezone_set('PRC');

//发送端口
/**
* 
*/
class G
{
  public static $lastSwooleCli = [];
}
function swooleConnect($node_address, $node_port)
{

  global $dsn,$db_user,$db_password;
  $client = new swoole_client(SWOOLE_SOCK_UDP, SWOOLE_SOCK_ASYNC);
  $client->on("connect", function(swoole_client $cli) use($dsn,$db_user,$db_password){
    array_push(G::$lastSwooleCli, $cli);
    try {
      $db = new PDO($dsn,$db_user,$db_password);
      $sql = 'select * from members';
      $query = $db->query($sql);
      $query->setfetchmode(pdo::FETCH_ASSOC); //设置数组关联方式
      $result = $query->fetchAll();
      $db = null;
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
    } catch (Exception $e) {
      echo date('Y-m-d H:i:s',time()).': ';
      echo "数据库连接失败\n";
    }
  });
  $client->on("receive", function(swoole_client $cli, $data) use($node_address){
      // if ($data == 'ok'){
        // $cli->close();
      // }
    echo date('Y-m-d H:i:s',time()).': ';
    echo "Receive: $data, Address: $node_address\n";
    updateData($node_address, $data);
    // sleep(1);
  });
  $client->on("error", function(swoole_client $cli){
    echo date('Y-m-d H:i:s',time()).': ';
    echo "error\n";
  });
  $client->on("close", function(swoole_client $cli) use($node_address){
    echo date('Y-m-d H:i:s',time()).': ';  
    echo "Close: $node_address Connection\n";
  });

  swoole_async_dns_lookup($node_address, function($host, $ip) use($client, $node_port){
      $client->connect($ip, $node_port); 
  });
}


//每50秒遍历一遍数据库，把所有端口都添加到ssserver中,从第50秒开始
swoole_timer_tick($interval_time,function() use($dsn,$db_user,$db_password){
  if (G::$lastSwooleCli){
    foreach (G::$lastSwooleCli as $cli) {
      $cli->close();
    }
    G::$lastSwooleCli = [];
  }
  try {
    $db = new PDO($dsn, $db_user, $db_password);
    $sql = 'select node_address, node_port from nodes';
    $query = $db->query($sql);
    $query->setfetchmode(pdo::FETCH_ASSOC); //设置数组关联方式
    $result = $query->fetchAll();
    $db = null;
    if ($result){
      foreach ($result as $node) {
        swooleConnect($node['node_address'], $node['node_port']);
      }
    }
  } catch (Exception $e) {
    echo date('Y-m-d H:i:s',time()).': ';
    echo $e;
  }
});


    /**
    * 更新端口号的数据流量
    */
function updateData($node_address, $json){
  global $dsn,$db_user,$db_password;
  
  echo json_decode($json);
	$stat = 'stat: ';
	if(!strstr($json, $stat)){
		return;
	}
	$json = str_replace($stat, '', $json);
  try {
  	$db = new PDO($dsn,$db_user,$db_password);
  } catch (Exception $e) {
    echo "插入数据库连接失败\n$json\n";
    return;
  }

	$array = json_decode($json);
	if(empty($array)){
		return;
	}
	foreach ($array as $key => $value) {
		try{
      date_default_timezone_set('PRC');
      $currentTime = "'".date('Y-m-d H:i:s',time())."'";
			//向数据库中记录一条流量
      $sql = "SELECT * From flows WHERE address = '$node_address' AND port = $key ORDER BY id";
      $query = $db->query($sql);
      $query->setfetchmode(PDO::FETCH_ASSOC);
      $array = $query->fetchAll();
      $result = array_pop($array);
      if ($result){

        // if ($result["flow"] < 20*1024*1024){
        if (time()-$result['time'] < 60*10) {
          $flowResult = $result["flow"]+$value;
          $sql = "update flows set flow = $flowResult, date_time = $currentTime where id = ".$result['id'];
          $db->exec($sql);
        }else{
          $values = "'$node_address'".','.$key.','.time().','.$currentTime.','.$value;
          $sqlFlow = 'insert into flows(address,port,time,date_time,flow) values('.$values.')';
          $db->exec($sqlFlow);
          // echo 'INSERT SQL:'.$sqlFlow.'\n';
        }
      }else{
        $values = "'$node_address'".','.$key.','.time().','.$currentTime.','.$value;
        $sqlFlow = 'insert into flows(address,port,time,date_time,flow) values('.$values.')';
        $db->exec($sqlFlow);
        // echo 'INSERT SQL:'.$sqlFlow.'\n';
      }
			

			//向数据库中记录总流量
			$sql = 'select * from members where port='.$key;
			$query = $db->query($sql);
        	$query->setfetchmode(PDO::FETCH_ASSOC); //设置数组关联方式
        	$result = $query->fetchAll();
        	if(!empty($result)){
        		foreach($result as $data){
        			echo "数据流量是：$value\n";
        			$flow = $data['flow']+$value;
        			$update = 'update members set flow = '
        						.$flow.' where port = '.$data['port'];
        			$db->exec($update);
        			// echo "\n查询的结果是".$value['port'];
        		}
        	}
		}catch(PDOException $e){
      echo "记录流量失败";
		}
		
	};
	$db = null;
}