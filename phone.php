<?php



$r = exec("./phone.sh http://olx.uz/obyavlenie/avto-shiny-legkovye-avto-mobilam-razmere-ot-r12-do-r20-c-dostovkay-IDCKFg.html 1.28.246.144:8080", $ph, $rv);

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