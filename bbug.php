<?php
// 定义参数
	require('phpQuery/phpQuery.php');
	set_time_limit(20);
	error_reporting(E_ALL);
	$con = @mysqli_connect("localhost","root","");//开数据库
	mysqli_select_db($con,"bitc");
	mysqli_query($con,"SET NAMES UTF8");//重点3
	$l=$_GET['url'];
	$link=base64_decode($l);
	$sql='select * from tb_news where link="'.$link.'"';
	$r = mysqli_query($con,$sql);
	
	if($r->num_rows>0){
		echo '(exist)'.$link.'<br>';
	}else{
		// $memcache = memcache_pconnect('127.0.0.1', 11211);
		// $memcache->set($l,1);//存入mem
		$c=file_get_contents($link);
		phpQuery::newDocumentHTML($c);//phpquery主函数HTMl
		$title=pq('.article-title')->text();
		if ($title) {
			$date=pq('.article-meta > span:first')->text();
			$type=pq('.article-meta > span > a')->text();
			$content=pq('.article-content')->text();
			$sql="INSERT INTO tb_news (title,`date`,content,type,link,`time`) VALUES ('".$title."','".$date."','".$content."','".$type."','".$link."','".time()."')";
			$result = mysqli_query($con,$sql);
			echo '(ok)'.$link.'<br>';
		}else{
			echo '(none)'.$link.'<br>';
		}
	}
	mysqli_close($con);

?>
