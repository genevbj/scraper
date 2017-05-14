<?php

class URL
{

    private $headers;
    private $ua;
    private $proxy;
    private $options;
    private $cache;
    private $conn;
    private $db;
    private $collection;
    private $logger;
    private $html;
    private $link;
    private $obsolescence_time;
    private $is_in_db;
    private $is_in_cache;
    private $is_actual;
    private $domain;
    private $children;
    private $ignore_ext;
    private $leaf_ext;


    function __construct($link,$options)
    {
	$this->logger = new Logger(Logger::DEBUG);

	$this->logger->log("Check $link:");
	$this->logger->log("Memory usage (".__METHOD__.", ".$link."): ".number_format((memory_get_usage()/1024/1024), 2, '.', ' ')."Mb", Logger::DEBUG);

	$this->ignore_ext=['css', 'js', 'ico'];
	$this->ignore_url=['/subscribe', '/favorite', '/login', '/post', '/p'];
	$this->leaf_ext=['html'];
	$this->link = $link;
	$this->options = $options;
	$this->obsolescence_time = $this->options['obsolescence'] ?? 36000;
	$this->domain = $this->options['domain'];

        $this->headers = $options['headers'];
        $this->proxy = $options['proxy'];
        $this->ua = $options['ua'];
	$this->cache = new Cache(['cache_dir' => $this->options['cache_dir']] ?? '' );

	$this->conn = new MongoDB\Client("mongodb://localhost:27017");
	$this->db = $this->conn->demo;
	$this->collection = $this->db->map;
	
	$this->collection->createIndex(['url' => 1], ['unique' => true]);


	$a = $this->collection->find(['url' => $this->link], ['url', 'update_time', 'retrieved', 'retrieve_time','parsed'])->toArray();

	$this->is_in_db = count($a) > 0;
	$this->retry_count = $a[count($a)-1]['retry_count'] ?? 0;
	$this->logger->log("Age: ".$this->retry_count);

	if($this->is_in_db)
	{
	    $this->dup = count($a);
//	    echo time()-($a[count($a)-1]['update_time'] ?? 0);
	    $this->is_actual = (time() - ($a[count($a)-1]['update_time'] ?? 0)) < $this->obsolescence_time;
	    $this->is_in_cache=$this->cache->is_in_cache($link);
	}
	else
	{
	    $this->is_actual = false;
	    $this->is_in_cache = false;
	
	}

	$this->logger->log("DB - ".($this->is_in_db?"OK":"FAIL"));
	$this->logger->log("Actual - ".($this->is_actual?"OK":"FAIL"));
	$this->logger->log("On disk - ".($this->is_in_cache?"OK":"FAIL"));

	$this->find_children('db');

	$this->logger->log("Memory usage (".__METHOD__.", ".$link.", after construct): ".number_format((memory_get_usage()/1024/1024), 2, '.', ' ')."Mb", Logger::DEBUG);
    }


    function retrieve() 
    { 

	$this->logger->log("Loading ".$this->link);
	$this->logger->log("Memory usage (".__METHOD__.", ".$this->link.", before): ".number_format((memory_get_usage()/1024/1024), 2, '.', ' ')."Mb", Logger::DEBUG);

        $process = curl_init($this->link); 
        curl_setopt($process, CURLOPT_HTTPHEADER, $this->headers); 
        curl_setopt($process, CURLOPT_HEADER, 0); 
        curl_setopt($process, CURLOPT_USERAGENT, $this->ua);
//        curl_setopt($process, CURLOPT_REFERER, $referrer);
        curl_setopt($process, CURLOPT_TIMEOUT, 30); 
        curl_setopt($process, CURLOPT_RETURNTRANSFER, 1); 
        curl_setopt($process, CURLOPT_FOLLOWLOCATION, 1); 
	curl_setopt($process, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($process, CURLOPT_COOKIESESSION, TRUE);
	curl_setopt($process, CURLOPT_FOLLOWLOCATION, TRUE);
	curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);

        $this->options['use_proxy'] && curl_setopt($process, CURLOPT_PROXY, $this->proxy);

        $this->html = curl_exec($process); 
        curl_close($process); 

	$this->cache->save($this->link,$this->html);

	$this->find_children('html');
	

//	$result = $this->collection->insertOne( [ 'url' => $this->link, 'urlhash' => sha1($this->link), 'rank' => 0, 'status' => 200, 'retrieve_time' => time(), 'update_time' => time(), 'title' => 'NOTITLE-FIXME', 'ondisk' => $this->cache->get_ondisk_name($this->link), 'parsed' => true, 'retrieved' => true ] );

	$this->logger->log("Update url: " . $this->link . " Set retry = " . ($this->retry_count+1));
    
	    $result = $this->collection->FindOneAndUpdate(
		array("url" => $this->link),
		array('$set' => [ 'url' => $this->link, 'urlhash' => sha1($this->link), 'rank' => 0, 'status' => 200, 'retrieve_time' => time(), 'update_time' => time(), 'title' => 'NOTITLE-FIXME', 'ondisk' => $this->cache->get_ondisk_name($this->link), 'parsed' => true, 'retrieved' => true, 'retry_count' => $this->retry_count+1 ]),
		array("upsert" => true, 'sort' => ['retry_count' => 1] )
	    );
	
	    
	    for($li=0;$li<count($this->children);$li++)
	    {

		$this->logger->log($this->children[$li]);
		try
		{
		    $result = $this->collection->FindOneAndUpdate(
			array("url" => $this->children[$li], 'retry_count' => [ '$lt' => ($this->retry_count+1) ] ),
			array('$set' => [ 'url' => $this->children[$li], 'parent' => $this->link, 'urlhash' => sha1($this->children[$li]), 'rank' => 0, 'status' => 200, 'retrieve_time' => time(), 'update_time' => time(), 'title' => 'NOTITLE-FIXME', 'ondisk' => $this->cache->get_ondisk_name($this->children[$li]), 'parsed' => false, 'retrieved' => false, 'retry_count' => $this->retry_count ]),
			array("upsert" => true)
		    );
		    unset($result);
		}
		catch(Exception $e)
		{
		    $this->logger->log("Duplicated: ".$this->children[$li]);
		}

	    }
	$this->logger->log("Memory usage (".__METHOD__.", ".$this->link.", after): ".number_format((memory_get_usage()/1024/1024), 2, '.', ' ')."Mb", Logger::DEBUG);

    } 




    function is_in_db()
    {
	return $this->is_in_db;
    }    

    function is_actual()
    {
	return $this->is_actual;
    }    

    function is_in_cache()
    {
	return $this->is_in_cache;
    }    

    function parse_html()
    {


	$this->logger->log("Parse links from ".$this->link);
	$this->logger->log("Memory usage (".__METHOD__.", ".$this->link.", before): ".number_format((memory_get_usage()/1024/1024), 2, '.', ' ')."Mb", Logger::DEBUG);

	$ch=[];

	if (preg_match_all('/href="([^#"]*)"/i', $this->html, $urlMatches, PREG_PATTERN_ORDER)) 
	{

	    // garbage collect
	    unset($this->html, $urlMatches[0]);

	    foreach ($urlMatches[1] as $k => $link) 
	    {

		$ignore_url = false;

		$link = trim($link);
		if (strlen($link) === 0) 
		    $link = '/';

		// don't allow more than maxDepth forward slashes in the URL
///	if ($this->maxDepth > 0
//                && strpos($link, 'http') === false
//                && substr_count($link, '/') > $this->maxDepth) 
//	{
//		continue;
//	}

		if (strpos($link, 'http') === false && strpos($link, '/') === 0) 
		{
            	    $link = $this->domain . $link;
		}
        	// check for a same directory reference
        	else 
		    if (strpos($link, 'http') === false && strpos($link, '/') === false) 
		    {
			if (strpos($link, 'www.') !== false) continue;
			$link = $this->domain . '/' . $link;
		    }
		    // dont index email addresses
		    else 
			if (strpos($link, 'mailto:') !== false) 
			{
			    $ignore_url = true;
			    continue;
			}
			// skip link if it isnt on the same domain
			else 
			    if (strpos($link, $this->domain) === false) 
			    {
				$ignore_url = true;
				continue;
			    }
		
		// skip ignored
		foreach($this->ignore_ext as $ext)
		{
		    if (strpos($link, '.'.$ext) !== false) 
		    {
			$this->logger->log("Found ($link). Ignore by extention \n";
			$ignore_url = true;
		    }
		}

		foreach($this->ignore_url as $iu)
		{
		    if (strpos($link, $this->domain.$iu) !== false) 
		    {
			$this->logger->log("Found ($link). Ignore by uri \n";
			$ignore_url = true;
		    }
		}

		foreach($this->leaf_ext as $ext)
		{
		    if (strpos($link, '.'.$ext) !== false) 
		    {
			$this->logger->log("Found ($link). Ignore leaf on \n";
			$ignore_url = true;
		    }
		}
	    // only add request if this is the first time in this session
//	    $link_id = $this->checkIfFirstRequest($link);
//	    if ($link_id === true) {
//	    	$this->addRequest($link);
//	    } else {
//		// update the inbound link count
//		$this->updateInboundCount($link_id);
//	    }
		if(!$ignore_url)
		{
		    $ch[]= ($link!='/') ?  rtrim($link, '/') : $link;
		}
	    }

	    // garbage collect
	    unset($urlMatches);
	}

	unset($this->html);

	$this->logger->log("Memory usage (".__METHOD__.", ".$this->link.", after): ".number_format((memory_get_usage()/1024/1024), 2, '.', ' ')."Mb", Logger::DEBUG);
	return $ch;
    }

    function get_children()
    {
	return $this->children;
    }

    function find_children($where = 'db')
    {

	switch($where)
	{
	    case 'html':
		$this->children=$this->parse_html();
		break;
	
	    case 'db':
	    default:
		$this->logger->log("Retrieve (".$this->link.") children from DB younger than " . $this->retry_count);
		$this->logger->log("Memory usage (".__METHOD__.", ".$this->link.", before): ".number_format((memory_get_usage()/1024/1024), 2, '.', ' ')."Mb", Logger::DEBUG);
		
		$a = $this->collection->find(['parent' => $this->link, 'retry_count' => ['$lte' => $this->retry_count]], ['url', 'update_time', 'retrieved', 'retrieve_time','parsed'])->toArray();
		foreach($a as $li => $lv)
		{

		    $this->logger->log($lv['url'] . ":" . $lv['retry_count'] . "(<" . $this->retry_count . ")");
		    $this->children[]=$lv['url'];
		}
		$this->logger->log("Memory usage (".__METHOD__.", ".$this->link.", after): ".number_format((memory_get_usage()/1024/1024), 2, '.', ' ')."Mb", Logger::DEBUG);
		unset($a);
		break;
	    
	}
    }

    function get_link()
    {
	return $this->link;
    }
}


