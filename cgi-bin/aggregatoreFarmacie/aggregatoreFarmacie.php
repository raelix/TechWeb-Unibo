<?php //Gabriele Cigna
require_once('../funzioni.php');
				
$headers = ('Content-Type: application/xml'); 

$URL_key = strtolower($_GET["key"]);
$URL_comp = strtolower($_GET["comp"]);
$URL_value = strtolower($_GET["value"]);

$URL_key = trim($URL_key);
$URL_comp = trim($URL_comp);
$URL_value = trim($URL_value);

$filePath = "./farmacieBO2011.xml";

if($_SERVER['REQUEST_METHOD']!="GET")
{
	sendHTTPError($Errore[405], "Il metodo utilizzato non e' di tipo GET");
}

if ($URL_key and $URL_comp and $URL_value)
{
	if(correttezzaConfronti($comparazioniValide,$URL_key,$URL_comp) == FALSE)
	{
		sendHTTPError($Errore[406], "Comparazione al di fuori delle specifiche");
	}
}
else if (!$URL_key and !$URL_comp and !$URL_value)
{
	$XPath_query = '/locations/location';
}
else
{
   sendHTTPError($Errore[406], "Parametro non settato");
}


if (!$URL_key and !$URL_comp and !$URL_value)
{
	$XPath_query = '/locations/location';
}
else if ($URL_comp == 'contains')
{
	if ($URL_key == 'id') $URL_key = '@'.$URL_key;
	//$URL_value = strtolower($_GET["value"]);
   $upper = "ABCDEFGHIJKLMNOPQRSTUVWXYZÆØÅ"; 
   $lower = "abcdefghijklmnopqrstuvwxyzæøå"; 
   $XPath_query = '/locations/location[contains(translate('.$URL_key.',"'.$upper.'","'.$lower.'"),"'.$URL_value.'")]';
}
else if ($URL_comp == 'ncontains')
{
	//$URL_value = strtolower($_GET["value"]);
   $upper = "ABCDEFGHIJKLMNOPQRSTUVWXYZÆØÅ"; 
   $lower = "abcdefghijklmnopqrstuvwxyzæøå"; 
   $XPath_query = '/locations/location[not(contains(translate('.$URL_key.',"'.$upper.'","'.$lower.'"),"'.$URL_value.'"))]';
}
else 
{
   switch ($URL_comp)
   { 
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

$DOM_doc = DOMDocument::load($filePath);
$XPath_doc = new DOMXPath($DOM_doc);
$locationList = $XPath_doc->query($XPath_query);
$locationsEl = $DOM_doc->documentElement;
$metadata = $locationsEl->getElementsByTagName("metadata")->item(0);
$DOM_doc->removeChild($locationsEl);
$emptyLocations = $DOM_doc->createElement("locations","");
$DOM_doc->appendChild($emptyLocations);
$newLocations = $DOM_doc->getElementsByTagName("locations")->item(0);
$newLocations->appendChild($metadata);

foreach ($locationList as $location) 
{
	$newLocations->appendChild($location);
}
$body = $DOM_doc->saveXML($DOM_doc);	
	
sendHTTPResult($headers, $body);
?>
