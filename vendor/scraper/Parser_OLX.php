<?php

require 'vendor/autoload.php';
use Symfony\Component\DomCrawler\Crawler;


class Parser_OLX extends Parser
{
    protected $serve;


    function parse($html)
    {
	return $this->filter( parent::parse($html));

    }

    function filter($xp)
    {
	$ret = [];
	foreach($xp as $k=>$v)
	{
	    foreach($v as $vi => $vv)
	    {

//		echo $vi."\n";
//		var_dump($vv);
		switch($k)
		{
		    case '+SCRIPTS' :

			$e=['region_id',
			    'subregion_id',
			    'city_id',
			    'cat_path',
			    'adID',
			    'regionName',
			    'subregionName',
			    'category_id',
			    'categoryName',
			    'categoryCode',
			    'root_category_id',
			    'rootCategoryName',
			    'rootCategoryCode',
			    ];
			foreach($e as $el) 
			{
//			    echo $el,"\n";
			    if(preg_match("/var ".$el."=[\"']([^\r\t\n]+)[\"'];/is",trim($vv->nodeValue), $s))
			    {
//				print_r($s);
//				die;
				$ret['+extra'][$el]=$s[1];
			    }
			}
			break;
		    case 'ID' :

			if($vi == 0 )
			{
			    $pattern = '/.+: (.+)/i';
			    $ret[$k][0]= preg_replace($pattern,"\\1",trim($vv->nodeValue), -1 );
			}
			break;

		    case '@PHONE_TOKEN' :

			if($vi == 0 )
			{
			    $pattern = '/.*\'(.+)\'.*/i';
			    $ret[$k][0]= preg_replace($pattern,"\\1",trim($vv->nodeValue), -1 );
			}

			break;

		    case 'TITLE' :
		    case 'DESCRIPTION' :
		    case 'CONTACT' :
			if($vi == 0 )
			    $ret[$k][0] = trim($vv->nodeValue, " \t\n\r\0\x0B" );
			break;
		    case 'DATE' :
			if($vi == 0 )
			{
			    $pattern = '/.* ((\\d+) (.+) (\\d+)),.*/i';
			    $ret[$k][0]= preg_replace_callback($pattern,
								function ( $matches) 
								{
								    $a['января']	='01'; 
								    $a['февраля']	='02'; 
								    $a['марта']		='03'; 
								    $a['апреля']	='04'; 
								    $a['мая']		='05'; 
								    $a['июня']		='06'; 
								    $a['июля']		='07'; 
								    $a['августа']	='08'; 
								    $a['сентября']	='09'; 
								    $a['октября']	='10'; 
								    $a['ноября']	='11'; 
								    $a['декабря']	='12'; 
								    return $matches[4]."-".$a[$matches[3]]."-".($matches[2]);
								},
					    			trim($vv->nodeValue), -1 );
			}
			break;
		    default:
			$ret[$k][$vi] = $vv->nodeValue;
		}
	    }
	}

	return $ret;
    }

    
}



