<?php //Gabriele Cigna
require_once('../funzioni.php');
require_once('../conversioni/CONVERSIONI_NEW.php');
//Prendo i valori dall'url
$strinput = strtolower($_GET['s']);
$strinput = trim($strinput);
$idagg = strtolower($_GET['aggr_id']);
$idagg = trim($idagg);

//Leggo l'accept header del narratore
$arrayHead = apache_request_headers();
$accept = $arrayHead["Accept"];

//Gestione degli errori
if(!$idagg or !$strinput){
	sendHTTPError($Errore[406], 'Parametro/i assente/i');
}

//Richiamo la funzione per ricercare l'url dell'aggregatore
$urlaggr = get_aggr_url($idagg);

//Se non esiste rispondo con un errore
if($urlaggr == NULL){
	sendHTTPError($Errore[404], 'Aggregatore non trovato');
}

//Custom: se nel narratore viene effettuata una ricerca vuota, restituisco tutto il database
if($strinput == '*'){

	$url = $urlaggr;
	$xml_result = curl_request($url,$accept);		
						
	sendHTTPResult("Content-type: ".$accept."; charset=utf-8",$xml_result);
}
else//Richiamo la funzione curl_request all'aggregatore
{
	$url = $urlaggr."/name/contains/".$strinput;
	$xml_result = curl_request($url,$accept);		
						
	//Mando il risultato
	sendHTTPResult("Content-type: ".$accept."; charset=utf-8",$xml_result);
}

?>
