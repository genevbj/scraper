<?php
require 'vendor/autoload.php'; // include Composer's autoloader

$client = new MongoDB\Client("mongodb://localhost:27017");
$collection = $client->olx_uz->map;
$collection_leaf = $client->olx_uz->ads;


switch($argv[1])
{

    case "find-url" : 
	
	$q=['url' => new \MongoDB\BSON\Regex($argv[2], '')];
	
	$result = $collection->find( $q, []  );

	echo "Found tree nodes\n";

	foreach ($result as $entry) {
	    echo $entry['_id'], ': ', $entry['url'], ': ', $entry['retry_count'],":", (time()-$entry['update_time']), "\n";
	}

	break;

    case "delete-url" : 


	$q=['url' => new \MongoDB\BSON\Regex($argv[2], '')];
	
	$result = $collection->find( $q, ['limit' => 1000]   );

	echo "Found tree nodes\n";

	foreach ($result as $entry) {
	    echo $entry['_id'], ': ', $entry['url'], ': ', $entry['retry_count'],":", (time()-$entry['update_time']), "\n";
	}
	echo "Going to delete\n";
	sleep(4);

	$result = $collection->deleteMany( $q, []   );

	break;


} 

die;

echo "Tree nodes\n";

//$q=[ '$and' => [ [ 'retry_count' => [ '$lte' => 3 ]  ] , [ 'update_time' => [ '$lt' =>  (time()-600)  ]  ] ] ];

$q=[];


$result = $collection->find( $q, ['limit' => 1000]   );


foreach ($result as $entry) {
    echo $entry['_id'], ': ', $entry['url'], ': ', $entry['retry_count'],":", (time()-$entry['update_time']), "\n";
}

/*
$result = $collection_leaf->find( [] );

foreach ($result as $entry) {
    echo $entry['_id'], ': ', $entry['parent'], ': ', $entry['url'], ': ', $entry['retry_count'],":", (time()-$entry['update_time']), "\n";
}
*/
/*
echo "\nAds\n";

$result = $collection_leaf->find( [] );
//$result = $collection_leaf->find( ['url' => 'http://astana.salexy.kz/c/samohodnyy_mulcher_ferri_tskf_dtf_5220267.html'] );

foreach ($result as $entry) {
    echo $entry['_id'], ': ', $entry['url'], ': ', $entry['up_count'],":", (time()-$entry['update_time']), "\n";
}
*/

?>
