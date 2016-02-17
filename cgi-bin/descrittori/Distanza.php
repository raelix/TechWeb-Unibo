<?php //Margherita Donnici
require_once('../funzioni.php');

//Prendo i valori dall'url ed elimino eventuali spazi vuoi
$lat1 = $_GET["lat1"];
$long1 = $_GET["long1"];
$lat2 = $_GET["lat2"];
$long2 = $_GET["long2"];
$lat1 = trim($lat1);
$lat2 = trim($lat2);
$long1 = trim($long1);
$long2 = trim($long2);

//Gestione degli errori
if(!$lat1 or !$lat2 or !$long1 or !$long2)
{
	sendHTTPError($Errore[406], 'Uno o piu parametri assenti');
}
if(is_numeric($lat1)==FALSE or is_numeric($lat2)==FALSE or is_numeric($long1)==FALSE or is_numeric($long2)==FALSE)
{
	sendHTTPError($Errore[406], 'Uno o piu parametri non sono numeri');
}
//Verifico che lat e long siano rispettino gli standard reali
if((90 >= $lat1) and ($lat1 >= (-90)) and (90 >= $lat2) and ($lat2 >= (-90)) and (180 >= $long1) and ($long1 >= (-180)) and (180 >= $long2) and ($long2 >= (-180)))
{	
	//Calcolo la distanza
	$diff_long = $long1 - $long2;
	$dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($diff_long));
	$dist = acos($dist);
	$dist = rad2deg($dist);
	$miles = $dist * 60 * 1.1515;
	$distanzainkm = $miles * 1.609344;
	$distanzainm = $distanzainkm*1000;
	//Mando il risultato
	sendHTTPResult("Content-type: text/plain",$distanzainm);
	//echo $distanzainm;
}
else sendHTTPError($Errore[406], 'Valori al di fuori del range massimo (-90/90 per lat e -180/180 per long)');


?>
