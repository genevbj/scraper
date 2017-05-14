<?php
require 'vendor/autoload.php'; // include Composer's autoloader

$client = new MongoDB\Client("mongodb://localhost:27017");
$collection = $client->demo->map;

/*
$result = $collection->FindOneAndUpdate(
		array("url" => 'http://salexy.kz/dlya_biznesa/promyshlennost' ),
		array('$set' => [  'retry_count' => 18 ] ),
		array("upsert" => false));

*/

//$result = $collection->drop();
/*
	$result = $collection->FindOneAndUpdate(
	    array("url" => 'http://salexy.kz'),
	    array('$set' => [ 'url' => 'http://salexy.kz', 'urlhash' => sha1('http://salexy.kz'), 'rank' => 0, 'status' => 200, 'retrieve_time' => time(), 'update_time' => time(), 'title' => 'NOTITLE-FIXME', 'ondisk' => sha1('http://salexy.kz'), 'parsed' => true, 'retrieved' => true, 'retry_count' => 3 ]),
	    array("upsert" => true, 'sort' => ['retry_count' => 1] )
	);
	
	
*/

$result = $collection->find( [] );

foreach ($result as $entry) {
    echo $entry['_id'], ': ', $entry['url'], ': ', $entry['retry_count'], "\n";
}

?>
