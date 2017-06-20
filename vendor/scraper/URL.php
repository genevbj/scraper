<?php

class URL
{

    protected $headers;
    protected $ua;
    protected $proxy;
    protected $options;
    protected $cache;
    protected $conn;
    protected $db;
    protected $collection;
    protected $logger;
    protected $html;
    protected $link;
    protected $obsolescence_time;
    protected $is_in_db;
    protected $is_in_cache;
    protected $is_actual;
    protected $domain;
    protected $base_url;
    protected $children;
    protected $ignore_ext;
    protected $leaf_ext;
    protected $curl_context;
    protected $parser;
    protected $url_helper;
    protected $xml_cache;

    function __construct($link, $options, &$table_instance)
    {
	$this->logger = new Logger(Logger::DEBUG);
	$this->logger->log("Check $link:");
	$this->logger->log("Memory usage (".__METHOD__.", ".$link."): ".number_format((memory_get_usage()/1024/1024), 2, '.', ' ')."Mb", Logger::DEBUG);


	$this->options = $options;
	$this->parser = new Parser_OLX($options);
	$this->url_helper = new URL_Helper($options);
	$this->url_helper->add_options([CURLOPT_COOKIEJAR => 'cookie.txt', CURLOPT_COOKIEFILE => 'cookie.txt',  ]);


	$this->cache = new Cache(['cache_dir' => $this->options['cache_dir']] ?? '' );
	$this->xml_cache = new Cache(['cache_dir' => $this->options['xml_cache_dir']] ?? '' );


	$this->ignore_ext=$options['ignore_ext'];
	$this->ignore_url=$options['ignore_url'];
	$this->leaf_ext=$options['leaf_ext'];

	$this->link = $link;

	$this->obsolescence_time = $this->options['obsolescence'] ?? 36000;
	$this->domain = $this->options['domain'];
	$this->base_url = $this->options['base_url'];


	$this->collection = $table_instance;
	
	$a = $this->collection->find(['url' => $this->link])->toArray();

	$this->is_in_db = count($a) > 0;
	$this->retry_count = $a[0]['retry_count'] ?? 0;
	$this->up_count = $a[0]['up_count'] ?? 0;

	$this->logger->log("Age: ".$this->retry_count);

	if($this->is_in_db)
	{
	    $this->dup = count($a);
	    $this->is_actual = (time() - ($a[count($a)-1]['update_time'] ?? 0)) < $this->obsolescence_time;
	    $this->is_in_cache=$this->cache->is_in_cache($link);
	}
	else
	{
	    $this->is_actual = false;
	    $this->is_in_cache = false;
	
	}

	$this->logger->log("DB - ".($this->is_in_db?"OK":"FAIL"));
	$this->logger->log("Actual - ".($this->is_actual?"OK":"FAIL"));
	$this->logger->log("On disk - ".($this->is_in_cache?"OK":"FAIL"));

	$this->logger->log("Memory usage (".__METHOD__.", ".$link.", after construct): ".number_format((memory_get_usage()/1024/1024), 2, '.', ' ')."Mb", Logger::DEBUG);
    }


    function &get_context()
    {
	return $this->url_helper->get_context();
    }


    function &create_context() 
    { 

	$this->url_helper->set_link($this->link);
	return $this->url_helper->create_context();
	
    }

    function update($curl_res,$set_counter) 
    {
	$this->logger->log("Memory usage (".__METHOD__.", ".$this->link.", before update): ".number_format((memory_get_usage()/1024/1024), 2, '.', ' ')."Mb", Logger::DEBUG);

	if($curl_res['errno'] === 0)
	{
	    $this->html = $curl_res['content'];

	    $this->up_count++;
	    $this->retry_count=$set_counter;
	    $this->cache->save($this->link,$this->html,$curl_res['meta']);
	    $extracted_array = $this->parser->parse($this->html);
	    //	Load and save images
	    $u = new URL_Helper($this->options);

	    if(!empty($extracted_array['+imgb']))
	    {
		$this->logger->log("Load images");
		foreach($extracted_array['+imgb'] as $img_link)
		{
		    $f = basename($img_link);
		    $this->logger->log("Load image $f");
		    $fn = $this->options['img_cache_dir'].'/'.$f;
		    $extracted_array['imgb'][]=$this->options['own_host'].$this->options['img_web_dir'].'/'.$f;
		    if(!file_exists($fn) || filesize($fn) === 0 )
		    {
			$fp = fopen ($fn, 'wb');
			$u->add_options([CURLOPT_BINARYTRANSFER => true, CURLOPT_COOKIEJAR => 'cookie.txt', CURLOPT_COOKIEFILE => 'cookie.txt',  ]);
			$img = $u->load($img_link); 
			fputs($fp,$img,strlen($img));
			fclose($fp);
		    }
		}
		unset($extracted_array['+imgb']);
	    }
	    else
	    {
		$extracted_array['imgb'] = [];

	    }

	    $xml = new XmlWriter();
	    $xml->openMemory();
	    $xml->startDocument('1.0', 'UTF-8');
//	    $xml->setIndentString("\n"); 
	    $xml->setIndent(true); 
	    $xml->startElement('ADS');
	    $xml->startElement('AD');
	    $xml->writeAttribute('id',$extracted_array['ID'][0]);

	    
	    foreach($extracted_array as $k => $field)
	    {

		switch($k)
		{
		    case 'imgb':
			foreach( $field as $img)
			{
			    $xml->startElement('IMAGE_URL');
			    $xml->writeCData($img);
			    $xml->endElement();
			    
			}
			break;
		    case '+extra':
			$xml->startElement('DEBUG');

			$xml->startElement('src');
			$xml->writeCData($this->link);
			$xml->endElement();

			foreach( $field as $e=>$i)
			{
			    $xml->startElement('extra_field');
			    $xml->writeAttribute('name',$e);
			    $xml->writeCData($i);
			    $xml->endElement();
			    
			}
			$xml->endElement();
			
			break;
		    case 'ID':
			$xml->startElement('ID');
			$xml->writeCData($field[0]);
			$xml->endElement();
			break;
		    case '@PHONE_TOKEN':
			
			$r = exec("./phone.sh ".$this->link." ".$this->options['proxy'], $ph, $rv);

			if ($rv !== 0)
			{
			    echo "Error:";
			    foreach ($ph as $ps)
			    {
				echo $ps, "\n";
			    } 

			}
			else
			{
			    $xml->startElement('CONTACT_PHONE');
			    $xml->writeCData((json_decode($ph[0]))->phone);
			    $xml->endElement();
			    ;
			}

			break;
		    case 'PRICE':
			$xml->startElement($k);
			
			$PRICE = $field[0];
			$pattern = '/(.+)у.е./i';
			$r = preg_replace($pattern,"\\1",$PRICE);
	
			if($PRICE == $r) //uzs
			{
			    $pattern = '/(.+)сум/i';
			    $PRICE = str_replace(' ','',preg_replace($pattern,"\\1",$PRICE));
	    
			}
			else
			{
			    $PRICE = (int)($this->options['rates']['usd']['uzs'] * str_replace(' ','',$r));
			}

			$xml->writeCData($PRICE);
			$xml->endElement();
			break;
		    default:
			$xml->startElement($k);
			$xml->writeCData($field[0]);
			$xml->endElement();
		}
		
	    }

	    $xml->endElement();
	    $xml->endElement();
	    
	    $this->xml_cache->save($this->link,$xml->outputMemory(true));
	    unset($xml);	    
	    unset($u);	    
	    //$ADS = $this->options['xml_cache_dir'].'/'.$this->options['xml_name'];

//	    var_dump($extracted_array);
#	    $this->xml_exporter->update($extracted_xml);
//	    die;

	    $result = $this->collection->FindOneAndUpdate(
		array("url" => $this->link),
		array('$set' => [ 'url' => $this->link, 'urlhash' => sha1($this->link), 'rank' => 0, 'status' => 200, 'retrieve_time' => time(), 'update_time' => time(), 'title' => 'NOTITLE-FIXME', 'ondisk' => $this->cache->get_ondisk_name($this->link), 'parsed' => true, 'retrieved' => true, 'retry_count' => $this->retry_count, 'up_count' => $this->up_count, 'extracted' => $extracted_array ]),
		array("upsert" => true, 'sort' => ['retry_count' => 1] )
	    );
	    
	}
	$this->logger->log("Memory usage (".__METHOD__.", ".$this->link.", after update): ".number_format((memory_get_usage()/1024/1024), 2, '.', ' ')."Mb", Logger::DEBUG);
    }


    function get_link()
    {
	return $this->link;
    }

    function is_in_db()
    {
	return $this->is_in_db;
    }    

    function is_actual()
    {
	return $this->is_actual;
    }    

    function is_in_cache()
    {
	return $this->is_in_cache;
    }    

}


