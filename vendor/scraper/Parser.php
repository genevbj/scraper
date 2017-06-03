<?php

require 'vendor/autoload.php';
use Symfony\Component\DomCrawler\Crawler;


class Parser
{

    protected $options;
    protected $logger;
    protected $xpathes;

    function __construct($options)
    {

	$this->options = $options;
	$this->xpathes = $options['xpath_struct'];
	$this->logger = new Logger();


    }


    function parse($html)
    {
	$ret = [] ;
	$crawler = new Crawler($html);
	foreach($this->xpathes as $key=>$xpath)
	{

	    $c=$crawler->filterXPath($xpath);
	    foreach ($c as $domElement) 
	    {
		$ret[$key][]=$domElement;
	    }
	}
	return $ret;

    }


    
}



