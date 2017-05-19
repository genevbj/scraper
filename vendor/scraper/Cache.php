<?php

function __var_dump_ret($mixed = null) {
  ob_start();
  var_dump($mixed);
  $content = ob_get_contents();
  ob_end_clean();
  return $content;
}

class Cache 
{

    private $options;
    private $cache_dir;
    private $cache_hier;
    private $logger;

    function __construct($options)
    {

	$this->options = $options;
	$this->logger = new Logger();

	$this->cache_dir = $options['cache_dir'] ?? './cache';


	if (!file_exists($this->cache_dir) && !mkdir($this->cache_dir, 0777, true)) 
	{
	    $this->logger->log("Cannot create cache directory ".$this->cache_dir, Logger::ERROR);
	    die();
	}

	$this->cache_dir = realpath($this->cache_dir);

	$this->logger->log("Set cache directory ".$this->cache_dir, Logger::DEBUG);

#TODO 
# Check writeable cache_dir

    }


    function save($url,$html,$meta='')
    {
	file_put_contents($this->get_ondisk_name($url),$html);
	if(!empty($meta))
	{
	    file_put_contents($this->get_ondisk_name($url).'.meta',__var_dump_ret($meta));
	    file_put_contents($this->get_ondisk_name($url).'.meta-json',json_encode($meta));
	}
    }

    function get_hash($url)
    {
	return false;
    }
    
    function get($url)
    {
    }

    function get_ondisk_name($url)
    {
        $result=$this->cache_dir.'/'.sha1($url);
	return $result;

    }

    function is_in_cache($url)
    {
        $result=realpath($this->get_ondisk_name($url));
	return $result;

    }

    
}



