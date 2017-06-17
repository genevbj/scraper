<?php

require 'vendor/autoload.php';
require 'vendor/scraper/Logger.php';
require 'vendor/scraper/Parser.php';
require 'vendor/scraper/Parser_OLX.php';
require 'vendor/scraper/Proxy.php';
require 'vendor/scraper/UserAgent.php';
require 'vendor/scraper/Crawler.php';
require 'vendor/scraper/Cache.php';
require 'vendor/scraper/URL.php';
require 'vendor/scraper/URL_Tree.php';
require 'vendor/scraper/URL_Helper.php';

require 'config.olx.uz.php';



$s=new Crawler($s_options);

set_time_limit(0);

while(true)
{
    $s->run_session();
    $s->sleep();
}


