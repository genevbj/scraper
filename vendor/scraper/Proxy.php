<?php


class Proxy 
{

    private $proxies_list;
    private $current_proxy;
    private $logger;

    function __construct()
    {

	$this->logger = new Logger();

	$this->proxies_list = array(); 
	$this->proxies_list[] = '189.1.164.190:3128';
	$this->proxies_list[] = '134.213.29.202:4444';
	$this->proxies_list[] = '177.66.81.83:8080';
	$this->proxies_list[] = '191.96.50.197:8080';

        $this->current_proxy = $this->proxies_list[array_rand($this->proxies_list)];    // Select a random proxy from the array and assign to $proxy variable
    }

    function get_proxy()
    {
	$this->logger->log("Selected proxy is ".$this->current_proxy);
	return $this->current_proxy;
    }
}



