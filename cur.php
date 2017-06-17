<?php

require 'vendor/autoload.php';
require 'vendor/scraper/Logger.php';
require 'vendor/scraper/Parser.php';
require 'vendor/scraper/Parser_OLX.php';
require 'vendor/scraper/Proxy.php';
require 'vendor/scraper/UserAgent.php';
require 'vendor/scraper/Crawler.php';
require 'vendor/scraper/Cache.php';
require 'vendor/scraper/URL.php';
require 'vendor/scraper/URL_Tree.php';
require 'vendor/scraper/URL_Helper.php';

require 'config.olx.uz.php';

        $headers[] = 'Accept: image/gif, image/x-bitmap, image/jpeg, image/pjpeg,text/html,application/xhtml+xml'; 
        $headers[] = 'Connection: Keep-Alive'; 
        $headers[] = 'Content-type: application/x-www-form-urlencoded;charset=UTF-8'; 
        $ua = (new UserAgent())->get_ua();


	$conn = new MongoDB\Client($s_options['db']['connection']);
	$db = $conn->selectDatabase($s_options['db']['name']);
//	    $this->map = $this->db->selectCollection($options['db']['struct']['tree']);
//	    $this->ads = $this->db->selectCollection($options['db']['struct']['leaf']);

	$proxies = $db->selectCollection($s_options['db']['struct']['proxies']);
        $proxy = new Proxy($s_options,$proxies);
	$s_options['proxy'] = $proxy->get_proxy();


	$s_options['headers'] = $headers;
	$s_options['ua'] = $ua;



	$c = new URL_Helper($s_options);

	echo ("Update SUM -> USD excnange rate");
	
	$c->add_options([CURLOPT_BINARYTRANSFER => false, CURLOPT_COOKIEJAR => 'cookie.txt', CURLOPT_COOKIEFILE => 'cookie.txt',  ]);
	$cu = $c->load($s_options['currency_json_url']); 
	$sum=json_decode($cu)->uzs->rate;
	echo "Rate is $sum";

$PRICE = '150 у.е.';
//$PRICE = '25 000 сум';

	$pattern = '/(.+)у.е./i';
	$r = preg_replace($pattern,"\\1",$PRICE);
	
	if($PRICE == $r) //uzs
	{
	    $pattern = '/(.+)сум/i';
	    $PRICE = str_replace(' ','',preg_replace($pattern,"\\1",$PRICE));
	    
    	}
	else
	{
	    $PRICE = (int)($sum * str_replace(' ','',$r));

	}


echo "\n price is  $PRICE";