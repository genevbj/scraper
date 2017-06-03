<?php


class Proxy 
{

    private $proxies_list;
    private $current_proxy_id;
    private $logger;
    protected $options;
    protected $storage;

    function __construct($options, &$proxies_table)
    {
	$this->options = $options;
	$this->storage = $proxies_table;

	$this->logger = new Logger();

	$this->proxies_list = array(); 
#	$this->proxies_list[] = '149.56.180.31:8080';
	$this->proxies_list['avail'][] = [ 'host' => '167.114.35.69', 		'port' => '8080', 'type' => 'http', 'enabled' => false, 'checked' => false ];
	$this->proxies_list['avail'][] = [ 'host' => '88.99.208.147', 		'port' => '8080', 'type' => 'http', 'enabled' => false, 'checked' => false ];
	$this->proxies_list['avail'][] = [ 'host' => '190.210.231.188', 	'port' => '3128', 'type' => 'http', 'enabled' => false, 'checked' => false ];
	$this->proxies_list['avail'][] = [ 'host' => '1.28.246.144', 		'port' => '8080', 'type' => 'http', 'enabled' => false, 'checked' => false ];
	$this->proxies_list['avail'][] = [ 'host' => '185.80.149.4', 		'port' => '443', 'type' => 'http', 'enabled' => false, 'checked' => false ];
	$this->proxies_list['noproxy'][] = [ 'host' => '', 	'port' => '', 'type' => 'http', 'enabled' => true, 'checked' => true ];
	$this->proxies_list['enabled'] = [];

        $headers[] = 'Accept: image/gif, image/x-bitmap, image/jpeg, image/pjpeg,text/html,application/xhtml+xml'; 
        $headers[] = 'Connection: Keep-Alive'; 
        $headers[] = 'Content-type: application/x-www-form-urlencoded;charset=UTF-8'; 
        $ua = (new UserAgent())->get_ua();
	$proxy = '';


	$options['headers'] = $headers;
	$options['proxy'] = $proxy;
	$options['ua'] = $ua;
	
	$u=new URL_Helper($options);

	$this->logger->log("Probing proxies");
	foreach($this->proxies_list['avail'] as $pi => $proxy)
	{

	    $this->logger->log("Looking up for ".$proxy['host']);

	    $u->set_proxy($this->proxies_list['avail'][$pi]['host'].':'.$this->proxies_list['avail'][$pi]['port']);
	    $h = $u->load('http://quickpay.kg/headers.php'); 
	    if($h === false)
	    {
		$this->logger->log("FAILED. No connection. Disable ".$proxy['host']);
	    }
	    else
	    {
		var_dump($h);
		if(preg_match('/191.101.158.250/is',$h))
		{
		    $this->logger->log("FAILED. Disable ".$proxy['host']);
		}
		else 
		{
		    $this->logger->log("OK. Enable ".$proxy['host']);
		    $this->proxies_list['enabled'][]=$proxy;
		}
	    }
	} 

        $this->current_proxy_id = (empty($this->proxies_list['enabled'])) ? -1 : array_rand($this->proxies_list['enabled']);    // Select a random proxy from the array and assign to $proxy variable
    }

    function get_proxy()
    {

	if($this->current_proxy_id == -1)
	{
	    $this->logger->log("No proxy is available");
	}
	else
	{
	    $this->logger->log("Selected proxy is ".$this->proxies_list['enabled'][$this->current_proxy_id]['host']);
	}
	return $this->proxies_list['enabled'][$this->current_proxy_id]['host'].':'.$this->proxies_list['enabled'][$this->current_proxy_id]['port'];
    }
}



