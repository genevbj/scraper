<?php

class Crawler
{

    private $headers;
    private $ua;
    private $proxy;
    private $options;
    private $cache;
    private $conn;
    private $db;
    private $map;
    private $ads;
    private $logger;
    private $recursion_level;

    function __construct($options)
    {
	$this->logger = new Logger('echo',Logger::DEBUG);
	$this->recursion_level = 0;

	$this->options = $options;

        $this->headers[] = 'Accept: image/gif, image/x-bitmap, image/jpeg, image/pjpeg,text/html,application/xhtml+xml'; 
        $this->headers[] = 'Connection: Keep-Alive'; 
        $this->headers[] = 'Content-type: application/x-www-form-urlencoded;charset=UTF-8'; 
        $this->ua = (new UserAgent())->get_ua();
        $this->proxy = $this->options['use_proxy'] ? (new Proxy())->get_proxy() : '';


	$this->options['headers'] = $this->headers;
	$this->options['proxy'] = $this->proxy;
	$this->options['ua'] = $this->ua;

	$this->obsolescence_time = $this->options['obsolescence'] ?? 6000;
	$this->per_session_pause = $this->options['sleep_between'] ?? 10;
	$this->sleep_for_nothing = $this->options['waiting'] ?? 180;
	$this->max_sim_downloads = $this->options['max_sim_downloads'] ?? 10;
//	$this->cache = new Cache(['cache_dir' => $this->options['cache_dir']] ?? '' );

	$this->conn = new MongoDB\Client("mongodb://localhost:27017");
	$this->db = $this->conn->demo;
	$this->map = $this->db->map;
	$this->ads = $this->db->ads;

    }


    function run_session()
    {
    
	$this->logger->log("Memory usage (".__METHOD__.",  start): ".number_format((memory_get_usage()/1024/1024), 2, '.', ' ')."Mb", Logger::DEBUG);

	// Get max counter 

	$a = $this->map->find([], [ 'sort' => [ 'retry_count' => -1 ], 'limit' => 1  ])->toArray();

	$max_counter = $a[0]['retry_count'] ?? 1;

	$this->logger->log("Run new session");
	$this->logger->log("Got maximum reached downloads counter: ".$max_counter);
	$this->logger->log("Got maximum simultaneous downloads: ".$this->max_sim_downloads);

	// Get max resources to parse

// Stage 1: Download tree

	$ot = time() - $this->obsolescence_time;
	$q=[ '$and' => [ [ 'retry_count' => [ '$lt' => $max_counter ]  ] , [ 'update_time' => [ '$lt' => $ot ]  ] ] ];


	$this->logger->log("Select " . $this->max_sim_downloads . " tree nodes not modified within " . $this->obsolescence_time . "s to find structure changes");
	$a = $this->map->find($q, ['limit' => $this->max_sim_downloads] )->toArray();

	$ffound=count($a);


	if(!$ffound)
	{

	    // Let's get more tolerant
	    $ot = time() - $this->obsolescence_time;
	    $q=[ '$and' => [ [ 'retry_count' => [ '$lte' => $max_counter ]  ] , [ 'update_time' => [ '$lt' => $ot ]  ] ] ];
	    $this->logger->log("Select " . $this->max_sim_downloads . " documents not modified within " . $this->obsolescence_time . "s to find structure changes");
	    $a = $this->map->find($q, ['limit' => $this->max_sim_downloads] )->toArray();

	    $ffound=count($a);

	}



	if(!$ffound)
	{
	    $this->logger->log("Found nothing" );
	    $this->logger->log("Sleep for ".($this->sleep_for_nothing)."s" );
	    sleep($this->sleep_for_nothing);
	}
	else
	{

	    $max_counter++;
	    $this->logger->log("Found " . $ffound . " documents"); 

	    // GC
	    unset ($this->urls);
	    unset ($this->ch);
	    

		

	    $this->logger->log("Memory usage (".__METHOD__.",  before downloading): ".number_format((memory_get_usage()/1024/1024), 2, '.', ' ')."Mb", Logger::DEBUG);
	    $this->logger->log("Start simultaneous downloading");

	    $this->url_master = curl_multi_init();
	    for($li = 0; $li < $ffound ; $li++)
	    {


//		$proxy = $proxies[ array_rand( $proxies, 1 ) ];
//		$results[$url]  = array(
//                                    'url'=>$url,
//                                    'proxy'=>$proxy,
//                                    'meta'=>'',
//                                    );

		$this->urls[$li] = new URL($a[$li]['url'], $this->options); 
		$this->ch[$li] =  $this->urls[$li]->create_context();

		curl_multi_add_handle($this->url_master, $this->ch[$li]);
	    }

	    // The curl event loop: process the data of the channels.
	    $running = null;
	    $ready = 0;
	    do 
	    {
		$exec_status = curl_multi_exec($this->url_master,$running);
		$ready=curl_multi_select($this->url_master, 1);

		if($ready > -1)
		{
		    while($info = curl_multi_info_read($this->url_master))
		    {
		        $res = curl_getinfo($info['handle']);
			$url = $res['url'];
//			$this->logger->log("META INFO FOR URL: '{$url}'",Logger::DEBUG);
//			$this->logger->log(var_export($res,true),Logger::DEBUG);
			$this->logger->log("{$url} HTTP code:".$res['http_code'],Logger::DEBUG);
		    }
		}
	    } while( $exec_status == CURLM_CALL_MULTI_PERFORM || $running > 0);

	    $this->logger->log("Simultaneous downloading is finished");

	    // Get the accumulated content for each of the channels, and cleanup the curl stuff:
	    for($li = 0; $li < $ffound ; $li++)
	    {
		$results[$li]['content'] = '';
		$results[$li]['meta'] = '';
		$results[$li]['error'] = '';
		$results[$li]['errno'] = curl_errno($this->ch[$li]);
		if( $results[$li]['errno'] === 0 )
		{
		    $results[$li]['content'] = curl_multi_getcontent($this->ch[$li]);
		    $results[$li]['meta'] = curl_getinfo($this->ch[$li]);
		}
		else
		{
		    $results[$li]['error'] = curl_error($this->ch[$li]);
		}
		$this->urls[$li]->update($results[$li], $max_counter);
		curl_multi_remove_handle($this->url_master, $this->ch[$li]);
	    }
	    curl_multi_close($this->url_master);



	}


// Stage 2: Download ads pages

	$ot = time() - $this->obsolescence_time;
	$q=[ '$and' => [ [ 'retry_count' => [ '$lt' => $max_counter ]  ] , [ 'update_time' => [ '$lt' => $ot ]  ] ] ];


	$this->logger->log("Select " . $this->max_sim_downloads . " documents not modified within " . $this->obsolescence_time . "s to find structure changes");
	$a = $this->ads->find($q, ['limit' => $this->max_sim_downloads] )->toArray();

	$ffound=count($a);


	if(!$ffound)
	{
	    $this->logger->log("Found nothing" );
	    $this->logger->log("Sleep for ".($this->sleep_for_nothing)."s" );
	    sleep($this->sleep_for_nothing);
	}
	else
	{
	

	    //$max_counter++;

	    $this->logger->log("Found " . $ffound . " ads"); 

	    unset ($this->urls);
	    unset ($this->ch);
	    

		

	    $this->logger->log("Memory usage (".__METHOD__.",  before downloading): ".number_format((memory_get_usage()/1024/1024), 2, '.', ' ')."Mb", Logger::DEBUG);
	    $this->logger->log("Start simultaneous downloading");

	    $this->url_master = curl_multi_init();
	    for($li = 0; $li < $ffound ; $li++)
	    {


//		$proxy = $proxies[ array_rand( $proxies, 1 ) ];
//		$results[$url]  = array(
//                                    'url'=>$url,
//                                    'proxy'=>$proxy,
//                                    'meta'=>'',
//                                    );

		$this->urls[$li] = new URL($a[$li]['url'], $this->options); 
		$this->ch[$li] =  $this->urls[$li]->create_context();

		curl_multi_add_handle($this->url_master, $this->ch[$li]);
	    }

	    // The curl event loop: process the data of the channels.
	    $running = null;
	    $ready = 0;
	    do 
	    {
		$exec_status = curl_multi_exec($this->url_master,$running);
		$ready=curl_multi_select($this->url_master, 1);

		if($ready > -1)
		{
		    while($info = curl_multi_info_read($this->url_master))
		    {
		        $res = curl_getinfo($info['handle']);
			$url = $res['url'];
//			$this->logger->log("META INFO FOR URL: '{$url}'",Logger::DEBUG);
//			$this->logger->log(var_export($res,true),Logger::DEBUG);
			$this->logger->log("{$url} HTTP code:".$res['http_code'],Logger::DEBUG);
		    }
		}
	    } while( $exec_status == CURLM_CALL_MULTI_PERFORM || $running > 0);

	    $this->logger->log("Simultaneous downloading is finished");

	    // Get the accumulated content for each of the channels, and cleanup the curl stuff:
	    for($li = 0; $li < $ffound ; $li++)
	    {
		$results[$li]['content'] = '';
		$results[$li]['meta'] = '';
		$results[$li]['error'] = '';
		$results[$li]['errno'] = curl_errno($this->ch[$li]);
		if( $results[$li]['errno'] === 0 )
		{
		    $results[$li]['content'] = curl_multi_getcontent($this->ch[$li]);
		    $results[$li]['meta'] = curl_getinfo($this->ch[$li]);
		}
		else
		{
		    $results[$li]['error'] = curl_error($this->ch[$li]);
		}
		$this->urls[$li]->update($results[$li], $max_counter);
		curl_multi_remove_handle($this->url_master, $this->ch[$li]);
	    }
	    curl_multi_close($this->url_master);

	}

	$this->logger->log("Session ended");
	$this->logger->log("Memory usage (".__METHOD__.", end): ".number_format((memory_get_usage()/1024/1024), 2, '.', ' ')."Mb", Logger::DEBUG);

    }



    function sleep()
    {
	$this->logger->log("Sleep for a ".$this->per_session_pause."s");
    
	sleep($this->per_session_pause);
    }

    function update_map($link=null)
    {

	$link = $link ?? $this->options['base_url'];

	$this->logger->log("Memory usage (".__METHOD__.", $link , end): ".number_format((memory_get_usage()/1024/1024), 2, '.', ' ')."Mb", Logger::DEBUG);
	$url=new URL($link,$this->options);

	if($url->is_in_db())
	{
	    if($url->is_actual())
	    {
		if($url->is_in_cache())
		{
		    $this->logger->log("Check passed. Next children.");
		    $this->traverse_url($url);
		    return;
		}
	    }
	}

        $url->retrieve();
	$this->traverse_url($url);
	unset($url);	

	

    }


    function traverse_url($url)
    {
	$this->logger->log("Memory usage (".__METHOD__.", ".$url->get_link().", before traverse): ".number_format((memory_get_usage()/1024/1024), 2, '.', ' ')."Mb", Logger::DEBUG);
	$links=$url->get_children();
	$this->recursion_level++;
	for($li=0;$li<count($links);$li++)
	{
//	    $this->update_map($links[$li]);
	}    
	$this->logger->log("Memory usage (".__METHOD__.", ".$url->get_link().", after traverse): ".number_format((memory_get_usage()/1024/1024), 2, '.', ' ')."Mb", Logger::DEBUG);
    }


}



