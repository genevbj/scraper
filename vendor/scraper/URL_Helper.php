<?php

class URL_Helper
{

    protected $headers;
    protected $ua;
    protected $proxy;
    protected $options;
    protected $cache;
    protected $logger;
    protected $is_loaded;
    protected $retry_count;
    protected $curl_error;
    protected $curl_errno;

    protected $html;
    protected $link;
    protected $domain;
    protected $base_url;
    protected $curl_context;
    protected $curlopt;

    function __construct($options)
    {
	$this->logger 	= new Logger(Logger::DEBUG);

	$this->options 	= $options;
	$this->domain 	= $this->options['domain'];
	$this->base_url = $this->options['base_url'];

        $this->headers 	= $options['headers'];
        $this->proxy 	= $options['proxy'];
        $this->ua 	= $options['ua'];
        $this->reuse_curl = $options['reuse_curl'];

	$this->curl_context = false;


	$this->curlopt = [
	    CURLOPT_HEADEROPT		=> CURLHEADER_UNIFIED,
	    CURLOPT_HTTP_VERSION	=> CURL_HTTP_VERSION_1_1,
	    CURLOPT_RETURNTRANSFER => true,
	    CURLOPT_HEADER         => false,
	    CURLOPT_HTTPHEADER     => $this->headers,
	    CURLOPT_FOLLOWLOCATION => true,
	    CURLOPT_ENCODING       => "",
	    CURLOPT_USERAGENT      => $this->ua,
	    CURLOPT_AUTOREFERER    => true,
	    CURLOPT_CONNECTTIMEOUT => 30,
	    CURLOPT_TIMEOUT        => 30,
	    CURLOPT_MAXREDIRS      => 10,
//	    CURLOPT_POST           => 1,
	    CURLOPT_SSL_VERIFYHOST => 0,
	    CURLOPT_SSL_VERIFYPEER => false,
	    CURLOPT_VERBOSE        => 1,
	    CURLOPT_FAILONERROR    => true,
//	    CURLOPT_COOKIESESSION  => true,
//	    CURLOPT_COOKIESESSION  => true,
	]; 

        $this->options['use_proxy'] && $this->curlopt[CURLOPT_PROXY] = $this->proxy;



    }


    function __destruct()
    {	
	unset($this->curl_context);
    }

    function reset()
    {
	$this->is_loaded	= false;
	$this->curl_error	= false;
	$this->curl_errno	= 0;
	$this->retry_count	= 0;
	
    }


    function set_link($link)
    {
	$this->link 	= $link;
	$this->reset();

    }

    function get_link()
    {
	return $this->link;
    }


    function add_options($op)
    {
	$co = $op + $this->curlopt;
	$this->curlopt= $co;

    }

    function set_headers($h)
    {
        $this->headers 	= $h;
	$this->curlopt[CURLOPT_HTTPHEADER]     = $this->headers;
    }

    function set_proxy($p)
    {
        $this->proxy 	= $p;
        $this->options['use_proxy'] && $this->curlopt[CURLOPT_PROXY] = $this->proxy;
    }

    function set_ua($ua)
    {
        $this->ua = $ua;
	$this->curlopt[CURLOPT_USERAGENT]      = $this->ua;
    }

    function &get_context()
    {
	return ($this->retry_count == 0) ?  $this->curl_context : false;
    }


    function &create_context() 
    { 

	// 
	$this->curlopt [ CURLOPT_URL ]  = $this->link;

	$this->logger->log("Memory usage (".__METHOD__.", ".$this->link.", before): ".number_format((memory_get_usage()/1024/1024), 2, '.', ' ')."Mb", Logger::DEBUG);

	if($this->curl_context === false)
	    $this->curl_context = curl_init(); 



        curl_setopt_array($this->curl_context, $this->curlopt); 

	$this->logger->log("Memory usage (".__METHOD__.", ".$this->link.", after): ".number_format((memory_get_usage()/1024/1024), 2, '.', ' ')."Mb", Logger::DEBUG);

	return $this->curl_context;
	
    }



    function load($link = "")
    {

	if($link != "")
	    $this->set_link($link);

	$this->logger->log("Memory usage (".__METHOD__.", ".$this->link.", before): ".number_format((memory_get_usage()/1024/1024), 2, '.', ' ')."Mb", Logger::DEBUG);



	$c=$this->create_context();



	$this->html = curl_exec($this->curl_context);
	$this->curl_error = curl_error($this->curl_context);
	$this->curl_errno = curl_errno($this->curl_context);

	if($this->html !== false)
	{
	    $this->is_loaded = true;
	}

	$this->retry_count++;


	if($this->reuse_curl === false) 
	{
	    curl_close($this->curl_context);
	    $this->curl_context = false;
	}


	$this->logger->log("Memory usage (".__METHOD__.", ".$this->link.", after): ".number_format((memory_get_usage()/1024/1024), 2, '.', ' ')."Mb", Logger::DEBUG);
	return $this->html;
    }
}


