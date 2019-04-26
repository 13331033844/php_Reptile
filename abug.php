<?php
// 定义参数
	function out($arr)
	{
		echo '<pre>';
		print_r($arr);
		echo '</pre>';
		exit;
	}
	require('phpQuery/phpQuery.php');
	set_time_limit(0);
	error_reporting(E_ALL);

	ob_end_clean();
ob_implicit_flush(1);

			


	$link='http://www.bitcoin86.com';

		
	$memcache = memcache_pconnect('127.0.0.1', 11211);
	$memcache->flush(1);
	// $temp=listpath('/bitcoin/list_17_1.html');
	// 	out($temp);


	$dirpath=dirpath($link);
	$listarr=array();

	$ch = curl_init();
	foreach ($dirpath as $key => $value) {
		$temp=listpath($value,$value);
		

		$listarr=array_merge($listarr,$temp);
		$listarr=array_unique($listarr);

		while (count($listarr)>1) {

			$url=array_shift($listarr);
			// echo $url.'<br>';
			$temp=listpath($url,$value);


			$listarr=array_merge($listarr,$temp);
			$listarr=array_unique($listarr);

			$arr=A($url,$memcache);
			foreach ($arr as $k => $v) {
				$memcache->set($v,1);//存入mem
				$l=base64_encode($v);
				curl_setopt($ch, CURLOPT_URL, "http://localhost/bbug.php?url=".$l);
				curl_setopt($ch, CURLOPT_HEADER, 0);
				curl_exec($ch);
			}
		}
	}
	curl_close($ch);

function listpath($lin,$lin1=''){
	$ar=array();
	$GLOBALS['memcache']->set($lin,1);//存入mem
	phpQuery::newDocumentFILE($GLOBALS['link'].$lin);//phpquery主函数FILE
	$ar=array();
	foreach (pq('a') as $key => $value) {//所有a链接正则
		$url=pq('a:eq('.$key.')')->attr('href');
		$pattern='/^list[\s\S]*.html$/';
		if(preg_match($pattern,$url)){
			if ($GLOBALS['memcache']->get($lin1.$url)!=1) {
				$ar[]=$lin1.$url;
			}
		}
	}
	
	
	$ar=array_unique($ar);
	return $ar;
}


function dirpath($lin){
	phpQuery::newDocumentFILE($lin);//phpquery主函数FILE
	$ar=array();
	foreach (pq('a') as $key => $value) {//所有a链接正则
		$url=pq('a:eq('.$key.')')->attr('href');
		$pattern='/^(?!.*php)\/[\s\S]*\/$/';
		if(preg_match($pattern,$url)){
				$ar[]=$url;
		}
	}
	$ar=array_unique($ar);
	return $ar;
}


function A($lin,$memcache = '')
{

	phpQuery::newDocumentFILE($GLOBALS['link'].$lin);//phpquery主函数FILE
	$ar=array();
	foreach (pq('a') as $key => $value) {//所有a链接正则
		$url=pq('a:eq('.$key.')')->attr('href');
		$pattern='/^\/[\s\S]*\/[0-9]*.html$/';
		if(preg_match($pattern,$url)){
			if ($memcache->get($GLOBALS['link'].$url)!=1) {
				$ar[]=$GLOBALS['link'].$url;
			}
		}
	}
	$ar=array_unique($ar);
	return $ar;
}
?>
