<?php
//天知道字符集有多复杂，纠结点位下三处
// header("Content-type: text/html; charset=utf-8");//重点1
require('phpQuery/phpQuery.php');
error_reporting(E_ALL);
$con = mysql_connect("localhost","root","000000");//开数据库
mysql_select_db("163", $con);
mysql_query("SET NAMES UTF8");//重点3
if (isset($_GET['a'])) {//get方式寻找爬虫下一个路径点
	$t=0;
	$l=$_GET['a'];
}else{
	$l=base64_encode('https://www.163.com');
	$t=1;
}
	$link=base64_decode($l);
// echo $link;
// exit;

if($t==0){//在不是主页的时候存数据库
	$c=file_get_contents($link);
	$c=mb_convert_encoding($c,'UTF-8','GB2312');//重点2转码
	phpQuery::newDocumentHTML($c);//phpquery主函数HTMl
	$title=pq('#epContentLeft > h1')->text();

	$time=pq('.post_time_source')->html();
	$time=strtotime(substr($time,strpos($time,'201'),19));//17/19时间数字前面有看不见的空白
	
	$content=pq('#endText')->html();
	$writer=pq('#ne_article_source')->text();
	if ($title) {
		$sql="INSERT INTO tb_news (title,`time`,content,writer,link) VALUES ('".$title."','".$time."','".htmlspecialchars(mysql_real_escape_string($content))."','".$writer."','".$l."')";
		$result = mysql_query($sql);
	}
}

	phpQuery::newDocumentFILE($link);//phpquery主函数FILE
	$arr=array();
	foreach (pq('a') as $key => $value) {//所有a链接正则
		$url=pq('a:eq('.$key.')')->attr('href');
		$brr[]=$url;
		$pattern = '/^http[s]?:\/\/[a-z]+\.163\.com\/[0-9]{2}[\s\S]*\.html$/';
		if(preg_match($pattern,$url)){
			$arr[]=$url;
		}
	}
		
	if (!empty($arr)) {
		$result='';
		$i=1;
		foreach ($arr as $key => $value) {//对所有符合条件的节点进行随机爬取
			$temp=$arr[array_rand($arr,1)];

			if ($i!=2) {
				$sql='select id from tb_news where link="'.base64_encode($temp).'"';

				$r = mysql_query($sql);
				$row = mysql_fetch_array($r);
				if(!$row){
					if(!strstr($temp,'renjian')){

						if ($link!=$temp && $i==1) {

							$result=$temp;
							$i=2;

						}
					}
					// mysql_close($con);
				}else{
					// mysql_close($con);
					header("Refresh: 1;url=http://www.test.com/test.php");
					exit;
				}
			}
		}
	}else{
		header("Refresh: 1;url=http://www.test.com/test.php");
		exit;
	}

	$rand=rand(0,10000000);//防重复 好像没用
header("Refresh: 1;url=http://www.test.com/test.php?V=".$rand."&a=".base64_encode($result));
echo $rand;

?>