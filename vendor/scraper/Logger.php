<?php

class Logger
{
    private $destination;
    private $precision;

    function __constructor($destination='echo')
    {
	$precision=true;
    }

    function log($message)
    {
	$micro_date = microtime();
	$date_array = explode(" ",$micro_date);
	$date = date("Y-m-d H:i:s",$date_array[1]);
	$d = $this->precision?($date . '.' . $date_array[0].': '):'' ;
	echo $d.$message;

    }

}


