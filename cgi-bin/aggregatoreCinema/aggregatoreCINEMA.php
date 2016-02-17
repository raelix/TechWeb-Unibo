<?php //Gabriele Cigna
require_once('../funzioni.php');
				
//Header della risposta
$headers = ('Content-Type: application/xml'); 

//Prendo i parametri dall'url
$URL_key = strtolower($_GET["key"]);
$URL_comp = strtolower($_GET["comp"]);
$URL_value = strtolower($_GET["value"]);

//Elimino eventuali spazi vuoti prima e dopo il valore dei parametri
//(es. 'value=     test ' sarà uguale a 'value=test')
$URL_key = trim($URL_key);
$URL_comp = trim($URL_comp);
$URL_value = trim($URL_value);

//Database xml
$filePath = "./CINEMA.xml";

//Gestione degli errori
if($_SERVER['REQUEST_METHOD']!="GET")//Verifico che il metodo sia GET
{
	sendHTTPError($Errore[405], "Il metodo utilizzato non e' di tipo GET");
}
if ($URL_key and $URL_comp and $URL_value)//Se sono presenti tutti i parametri...
{
	//...ne verifico la correttezza semantica(funzione definita nel file funzioni.php)
	if(correttezzaConfronti($comparazioniValide,$URL_key,$URL_comp) == FALSE)
	{
		sendHTTPError($Errore[406], "Comparazione al di fuori delle specifiche");
	}
}
else if (!$URL_key and !$URL_comp and !$URL_value)//Se non sono presenti i 3 parametri
{
	$XPath_query = '/locations/location';//restituisco il database intero come da protocollo
}
else//se 1 o 2 parametri non sono presenti...
{
   sendHTTPError($Errore[406], "Parametro non settato");
}

//Genero la query Xpath
if (!$URL_key and !$URL_comp and !$URL_value)
{
   $XPath_query = '/locations/location';
}
else if ($URL_comp == 'contains')
{	
	if ($URL_key == 'id') $URL_key = '@'.$URL_key;
   $upper = "ABCDEFGHIJKLMNOPQRSTUVWXYZÆØÅ"; 
   $lower = "abcdefghijklmnopqrstuvwxyzæøå"; 
   $XPath_query = '/locations/location[contains(translate('.$URL_key.',"'.$upper.'","'.$lower.'"),"'.$URL_value.'")]';
}
else if ($URL_comp == 'ncontains')
{
   $upper = "ABCDEFGHIJKLMNOPQRSTUVWXYZÆØÅ"; 
   $lower = "abcdefghijklmnopqrstuvwxyzæøå"; 
   $XPath_query = '/locations/location[not(contains(translate('.$URL_key.',"'.$upper.'","'.$lower.'"),"'.$URL_value.'"))]';
}
else//se il comp non è contains/ncontains
{
   switch ($URL_comp)
   {  //traduco il comp nel relativo simbolo matematico
      case 'lt' : $URL_comp = '<';  break;
      case 'le' : $URL_comp = '<='; break;
      case 'eq' : $URL_comp = '=';  break;
      case 'ne' : $URL_comp = '!='; break;
      case 'gt' : $URL_comp = '>='; break;
      case 'ge' : $URL_comp = '>';  break;     	
   }
   
   if ($URL_key == 'lat' or $URL_key == 'long' or $URL_key == 'id') $URL_key = '@'.$URL_key;
   $upper = "ABCDEFGHIJKLMNOPQRSTUVWXYZÆØÅ"; 
   $lower = "abcdefghijklmnopqrstuvwxyzæøå";
   $XPath_query = '/locations/location[translate('.$URL_key.',"'.$upper.'","'.$lower.'") '.$URL_comp.' "'.$URL_value.'"]'; 
}

//Creo un nuovo documento DOM
$DOM_doc = DOMDocument::load($filePath);
$XPath_doc = new DOMXPath($DOM_doc);
//Salvo il risultato della query xpath
$locationList = $XPath_doc->query($XPath_query);
$locationsEl = $DOM_doc->documentElement;
//Trovo e salvo i Metadati
$metadata = $locationsEl->getElementsByTagName("metadata")->item(0);
$DOM_doc->removeChild($locationsEl);
//Creo una locations vuota
$emptyLocations = $DOM_doc->createElement("locations","");
$DOM_doc->appendChild($emptyLocations);
$newLocations = $DOM_doc->getElementsByTagName("locations")->item(0);
//Aggiungo i metadati
$newLocations->appendChild($metadata);

//Aggiungo tutte le location del risultato
foreach ($locationList as $location) 
{
	$newLocations->appendChild($location);
}
//Salvo il documento XML
$body = $DOM_doc->saveXML($DOM_doc);	
	
//Mando la risposta
sendHTTPResult($headers, $body);
?>
