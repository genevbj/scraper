<?php

class URL_Tree extends URL
{

    private $collection_tree;
    private $children_tree;

    function __construct($link, $options, &$leaf_instance, &$tree_instance)
    {

	parent::__construct($link, $options, $leaf_instance);

	$this->collection_tree = $tree_instance;

    }

    function update($curl_res,$set_counter) 
    {
	if($curl_res['errno'] === 0)
	{
	    $this->html = $curl_res['content'];

	    $this->up_count++;
	    $this->retry_count=$set_counter;
	    $this->cache->save($this->link,$this->html,$curl_res['meta']);
	    
	    $result = $this->collection_tree->FindOneAndUpdate(
		array("url" => $this->link),
		array('$set' => [ 'url' => $this->link, 'urlhash' => sha1($this->link), 'rank' => 0, 'status' => 200, 'retrieve_time' => time(), 'update_time' => time(), 'title' => 'NOTITLE-FIXME', 'ondisk' => $this->cache->get_ondisk_name($this->link), 'parsed' => true, 'retrieved' => true, 'retry_count' => $this->retry_count, 'up_count' => $this->up_count]),
		array("upsert" => true, 'sort' => ['retry_count' => 1] )
	    );
	    

	    $this->update_children();
	}
    }

    function update_children()
    {

	    $this->find_children('html');


	    // Upsert tree changes
	    for($li=0;$li<count($this->children_tree);$li++)
	    {

		$this->logger->log($this->children_tree[$li]);
		try
		{
		    $result = $this->collection_tree->FindOneAndUpdate(
			array("url" => $this->children_tree[$li]),
			array('$set' => [ 'url' => $this->children_tree[$li], 'parent' => $this->link, 'urlhash' => sha1($this->children_tree[$li]), 'rank' => 0, 'status' => 200, 'update_time' => time(), 'title' => 'NOTITLE-FIXME', 'ondisk' => $this->cache->get_ondisk_name($this->children_tree[$li]), 'retry_count' => $this->retry_count, 'up_count' => $this->up_count ]),
			array("upsert" => true)
		    );
		}
		catch(Exception $e)
		{
		    $this->logger->log("Duplicated: ".$this->children_tree[$li]);
		}

	    }

	    // Delete obsolete nodes
	    $q=[ '$and' => [ [ 'up_count' => [ '$lt' => $this->up_count ]  ] , [ 'parent' => [ '$eq' => $this->link ] ] ] ];

	    $result = $this->collection_tree->DeleteMany($q);

	    // Upsert leaf changes
	    for($li=0;$li<count($this->children);$li++)
	    {

		$this->logger->log($this->children[$li]);
		try
		{
		    $result = $this->collection->FindOneAndUpdate(
			array("url" => $this->children[$li]),
			array('$set' => [ 'url' => $this->children[$li], 'parent' => $this->link, 'urlhash' => sha1($this->children[$li]), 'rank' => 0, 'status' => 200, 'update_time' => time(), 'title' => 'NOTITLE-FIXME', 'ondisk' => $this->cache->get_ondisk_name($this->children[$li]), 'up_count' => $this->up_count ]),
			array("upsert" => true)
		    );
		}
		catch(Exception $e)
		{
		    $this->logger->log("Duplicated: ".$this->children[$li]);
		}

	    }

	    // Delete obsolete nodes

#	    $q=[ '$and' => [ [ 'up_count' => [ '$lt' => $this->up_count ]  ] , [ 'parent' => [ '$eq' => $this->link ] ] ] ];

	    $result = $this->collection->DeleteMany($q);


	    unset($result);

    }


    function find_children($where = 'db')
    {

	switch($where)
	{
	    case 'html':
		$this->parse_html();
		break;
	
	    case 'db':
	    default:
		$this->logger->log("Retrieve (".$this->link.") children from DB younger than " . $this->retry_count);
		$this->logger->log("Memory usage (".__METHOD__.", ".$this->link.", before): ".number_format((memory_get_usage()/1024/1024), 2, '.', ' ')."Mb", Logger::DEBUG);
		
		$a = $this->collection_tree->find(['parent' => $this->link, 'retry_count' => ['$lte' => $this->retry_count]], ['url', 'update_time', 'retrieved', 'retrieve_time','parsed'])->toArray();
		foreach($a as $li => $lv)
		{

		    $this->logger->log($lv['url'] . ":" . $lv['retry_count'] . "(<" . $this->retry_count . ")");
		    $this->children_tree[]=$lv['url'];
		}
		$this->logger->log("Memory usage (".__METHOD__.", ".$this->link.", after): ".number_format((memory_get_usage()/1024/1024), 2, '.', ' ')."Mb", Logger::DEBUG);
		unset($a);
		break;
	    
	}
    }

    function parse_html()
    {


	$this->logger->log("Parse links from ".$this->link);
	$this->logger->log("Memory usage (".__METHOD__.", ".$this->link.", before): ".number_format((memory_get_usage()/1024/1024), 2, '.', ' ')."Mb", Logger::DEBUG);

	$this->children_tree = [];
	$this->children = [];

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
		$this->logger->log("Found ($link)");

		if (strpos($link, 'http') === false && strpos($link, '/') === 0) 
		{
            	    $link = $this->base_url . $link;
		    $this->logger->log("Convert to ($link)");
		}
        	// check for a same directory reference
        	else 
		    if (strpos($link, 'http') === false && strpos($link, '/') === false) 
		    {
			if (strpos($link, 'www.') !== false) continue;
			$link = $this->base_url . '/' . $link;
			$this->logger->log("Convert to ($link)");
		    }
		    // dont index email addresses
		    else 
			if (strpos($link, 'mailto:') !== false) 
			{
			    $ignore_url = true;
			    $this->logger->log("Ignore email link");
			    continue;
			}
			// skip link if it isnt on the same domain
			else 
//			    if ( (strpos($link, '.'.$this->domain) === false) || (strpos($link, '/'.$this->domain) === false) )
			    if ( (strpos($link, '//'.$this->domain) === false) && (strpos($link, '//www.'.$this->domain) === false ) )
			    {
//				if ($this['options']['traverse_inner_domains'] && strpos($link, $this->domain) === false) 
//				{
				    $ignore_url = true;
				    $this->logger->log("Ignore outside link");
//				    continue;
//				Ъ
//				if ($this['options']['traverse_outer_domains'] && strpos($link, $this->domain) === false) 
//				{
			    }
		
		// skip ignored
		foreach($this->ignore_ext as $ext)
		{
		    if (strpos($link, '.'.$ext) !== false) 
		    {
			$this->logger->log("Ignore by extention \n");
			$ignore_url = true;
		    }
		}

		foreach($this->ignore_url as $iu)
		{
		    if (strpos($link, $this->domain.$iu) !== false) 
		    {
			$this->logger->log("Ignore by uri \n");
			$ignore_url = true;
		    }
		}

		if(!$ignore_url)
		{
		    foreach($this->leaf_ext as $ext)
		    {
			if (strpos($link, '.'.$ext) !== false) 
			{
			    $this->logger->log("Found leaf page ($link)\n");
			    $this->children[]= ($link!='/') ?  rtrim($link, '/') : $link;
			    $ignore_url = true;
			}
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
		    $this->children_tree[]= ($link!='/') ?  rtrim($link, '/') : $link;
		}
	    }

	    // garbage collect
	    unset($urlMatches);
	}

	unset($this->html);

	$this->logger->log("Memory usage (".__METHOD__.", ".$this->link.", after): ".number_format((memory_get_usage()/1024/1024), 2, '.', ' ')."Mb", Logger::DEBUG);
	return array($this->children_tree,$this->children);
    }

    function get_children()
    {
	return $this->children;
    }

    function get_children_tree()
    {
	return $this->children_tree;
    }

/*
    function retrieve() 
    { 

	$this->logger->log("Loading ".$this->link);
	$this->logger->log("Memory usage (".__METHOD__.", ".$this->link.", before): ".number_format((memory_get_usage()/1024/1024), 2, '.', ' ')."Mb", Logger::DEBUG);

        $this->create_context();

        $this->html = curl_exec($this->curl_context); 
        curl_close($this->curl_context); 

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

*/



}


