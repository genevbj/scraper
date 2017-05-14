<?php

class Logger
{
    const ERROR = 0;
    const WARN = 1;
    const INFO = 2;
    const DEBUG = 3; 

    private $destination;
    private $precision;
    private $level;

    function __construct($destination='echo', $level=Logger::INFO)
    {
	$this->level = $level;
	$precision=true;
    }

    function log($message, $level=Logger::INFO)
    {
	if($level < ($this->level + 1))
	{
	    $micro_date = microtime();
	    $date_array = explode(" ",$micro_date);
	    $date = date("Y-m-d H:i:s",$date_array[1]);
	    $d = $this->precision?($date . '.' . $date_array[0].': '):'' ;
	    echo $d.$message."\n";
	}
    }

}


