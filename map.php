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
    if(!isset($GLOBALS['scraper_proxy']))
	init_ua();

    return $GLOBALS['scraper_proxy'];
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

function generate_ondisk_name($url)
{
    $result=$GLOBALS['scraper_cache'].'/'.sha1($url);

    return $result;


}

$GLOBALS['DEBUG']=true;
$GLOBALS['USE_PROXY']=false;
$GLOBALS['scraper_cache']='./cache';


init_ua();
init_proxy();

echolog($GLOBALS['scraper_ua']);
echolog($GLOBALS['scraper_proxy']);

$base_url = 'http://yandex.ru';
$url = 'http://salexy.kz/';
$this_domain = 'salexy.kz';


require 'vendor/autoload.php'; // include Composer's autoloader

$client = new MongoDB\Client("mongodb://localhost:27017");
$collection = $client->demo->map;



$html=get_url($url,$base_url);

$fname = generate_ondisk_name($url);
$result = $collection->insertOne( [ 'url' => $url, 'urlhash' => sha1($url), 'rank' => 0, 'status' => 200, 'retrieve__time' => time(), 'update_time' => time(), 'title' => 'NOTITLE-FIXME', 'ondisk' => $fname ] );

file_put_contents($fname,$html);



if (preg_match_all('/href="([^#"]*)"/i', $html, $urlMatches, PREG_PATTERN_ORDER)) 
{

	// garbage collect
	unset($html, $urlMatches[0]);

	// iterate over each link found on the page
	foreach ($urlMatches[1] as $k => $link) {

	    $link = trim($link);
	    if (strlen($link) === 0) $link = '/';

	    // don't allow more than maxDepth forward slashes in the URL
///	if ($this->maxDepth > 0
//                && strpos($link, 'http') === false
//                && substr_count($link, '/') > $this->maxDepth) 
//	{
//		continue;
//	}

	    // check for a relative path starting with a forward slash
	    if (strpos($link, 'http') === false && strpos($link, '/') === 0) 
	    {
		// update the link with the full domain path
                $link = $this_domain . $link;
	    }
            // check for a same directory reference
            else if (strpos($link, 'http') === false && strpos($link, '/') === false) 
	    {
		if (strpos($link, 'www.') !== false) continue;
                $link = $this_domain . '/' . $link;
            }
	    // dont index email addresses
	    else if (strpos($link, 'mailto:') !== false) 
	    {
		continue;
	    }
	    // skip link if it isnt on the same domain
	    else if (strpos($link, $this_domain) === false) {
                        continue;
	    }

	    // only add request if this is the first time in this session
//	    $link_id = $this->checkIfFirstRequest($link);
//	    if ($link_id === true) {
//	    	$this->addRequest($link);
//	    } else {
//		// update the inbound link count
//		$this->updateInboundCount($link_id);
//	    }

	    echo "$link\n";
	}

	// garbage collect
	unset($urlMatches);
}

#$html = str_get_html($str);
#echo $html->find('div div div', 0)->innertext . '<br>'; // result: "ok"

