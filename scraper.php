<?php

require 'vendor/autoload.php';
require 'vendor/scraper/Logger.php';
require 'vendor/scraper/Proxy.php';
require 'vendor/scraper/UserAgent.php';
require 'vendor/scraper/Crawler.php';
require 'vendor/scraper/Cache.php';
require 'vendor/scraper/URL.php';




$s_options=[
    'debug' => true,
    'max_sim_downloads' => 10,
    'max_documents_per_session' => 20,
    'cache_dir' => './cache',
    'use_proxy' => false,
    'check_proxy' => false,
    'single_ua_per_session' => true,
    'single_proxy_per_session' => true,
    'check_proxy_url' => 'http://gde.ru/check_proxy.php',
    'domain' => 'http://salexy.kz',
    'traverse_domains' => false,
    'base_url' => 'http://salexy.kz',
    'db' => ['name'=>'salexy', 'driver' => 'mongodb', 'connection' => "mongodb://localhost:27017",],
];

$s=new Crawler($s_options);

set_time_limit(0);

//while(true)
{
    $s->run_session();
}


