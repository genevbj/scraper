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
    private $collection;
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


//	$this->cache = new Cache(['cache_dir' => $this->options['cache_dir']] ?? '' );

//	$this->conn = new MongoDB\Client("mongodb://localhost:27017");
//	$this->db = $this->conn->demo;
//	$this->collection = $this->db->map;

    }



    function update_map($link=null)
    {

	$link = $link ?? $this->options['base_url'];

	$this->logger->log("Memory usage (".__METHOD__.", $link , start): ".number_format((memory_get_usage()/1024/1024), 2, '.', ' ')."Mb", Logger::DEBUG);
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
	    $this->update_map($links[$li]);
	}    
	$this->logger->log("Memory usage (".__METHOD__.", ".$url->get_link().", after traverse): ".number_format((memory_get_usage()/1024/1024), 2, '.', ' ')."Mb", Logger::DEBUG);
    }


}



/*




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

*/