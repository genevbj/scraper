<?php



$r = exec("./phone.sh http://olx.uz/obyavlenie/avto-shiny-legkovye-avto-mobilam-razmere-ot-r12-do-r20-c-dostovkay-IDCKFg.html", $ph, $rv);

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
//    print_r($ph);
    $phone = (json_decode($ph[0]))->phone;
    echo $phone;
}