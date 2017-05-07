<?php

include('dom/simple_html_dom.php');


function echolog($var)
{
    echo time().':'.$var."\n";
}


function get_url($url, $referrer) { 

        $headers[] = 'Accept: image/gif, image/x-bitmap, image/jpeg, image/pjpeg,text/html,application/xhtml+xml'; 
        $headers[] = 'Connection: Keep-Alive'; 
        $headers[] = 'Content-type: application/x-www-form-urlencoded;charset=UTF-8'; 
        $useragent = get_ua();


        $process = curl_init($url); 
        curl_setopt($process, CURLOPT_HTTPHEADER, $headers); 
        curl_setopt($process, CURLOPT_HEADER, 0); 
        curl_setopt($process, CURLOPT_USERAGENT, $useragent);
        curl_setopt($process, CURLOPT_REFERER, $referrer);
        curl_setopt($process, CURLOPT_TIMEOUT, 30); 
        curl_setopt($process, CURLOPT_RETURNTRANSFER, 1); 
        curl_setopt($process, CURLOPT_FOLLOWLOCATION, 1); 
	curl_setopt($process, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($process, CURLOPT_COOKIESESSION, TRUE);
	curl_setopt($process, CURLOPT_FOLLOWLOCATION, TRUE);
	curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);

//	$GLOBALS['USE_PROXY'] && curl_setopt($process, CURLOPT_PROXY, get_proxy());    // Set CURLOPT_PROXY with proxy in $proxy variable

        $return = curl_exec($process); 
        curl_close($process); 

        return $return; 
    } 

function get_ua()
{
    if(!isset($GLOBALS['scraper_ua']))
	init_ua();

    return $GLOBALS['scraper_ua'];
}

function init_ua()
{

    $ua_list=file('./crawlers.list');
    $GLOBALS['scraper_ua']=!empty($ua_list)?$ua_list[array_rand($ua_list)]:'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; .NET CLR 1.0.3705; .NET CLR 1.1.4322; Media Center PC 4.0)'; 

}

function get_proxy()
{
    if(!isset($_GLOBALS['scraper_proxy']))
	init_ua();

    return $_GLOBALS['scraper_proxy'];
}

function init_proxy()
{

    $proxies = array(); 

    $proxies[] = '189.1.164.190:3128';
    $proxies[] = '134.213.29.202:4444';
    $proxies[] = '177.66.81.83:8080';
    $proxies[] = '191.96.50.197:8080';
    if (isset($proxies)) {  // If the $proxies array contains items, then
        $proxy = $proxies[array_rand($proxies)];    // Select a random proxy from the array and assign to $proxy variable
    }

    $GLOBALS['scraper_proxy']=$proxy;
    
}

$GLOBALS['DEBUG']=true;
$GLOBALS['USE_PROXY']=false;


init_ua();
init_proxy();

echolog($GLOBALS['scraper_ua']);
echolog($GLOBALS['scraper_proxy']);

$base_url = 'http://yandex.ru';
$url = 'http://salexy.kz/';




print_r(get_url($url,$base_url));


#$html = str_get_html($str);
#echo $html->find('div div div', 0)->innertext . '<br>'; // result: "ok"

