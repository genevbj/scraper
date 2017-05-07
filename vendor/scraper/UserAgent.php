<?php



class UserAgent
{

    private $user_agents;
    private $currnet_user_agent;
    private $logger;

    function __construct()
    {

	$this->logger = new Logger();
	$this->user_agents=file('./crawlers.list');
	
	$this->current_user_agent=!empty($this->user_agents)?$this->user_agents[array_rand($this->user_agents)]:'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; .NET CLR 1.0.3705; .NET CLR 1.1.4322; Media Center PC 4.0)'; 

    }

    function get_ua()
    {
	$this->logger->log("Selected UA is ".$this->current_user_agent);
        return $this->current_user_agent;
    }



}


