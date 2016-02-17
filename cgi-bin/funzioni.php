<?php //Gabriele Cigna

//Array contenente gli Errori da gestire 
$Errore = array(
				403 => 'HTTP/1.1 403 Forbidden',
				404 => 'HTTP/1.1 404 Not Found',
				405 => 'HTTP/1.1 405 Method Not Allowed',
				406 => 'HTTP/1.1 406 Not Acceptable',
				500 => 'HTTP/1.1 500 Internal Server Error',
				501 => 'HTTP/1.1 501 Not implemented'
				);				

//Array contenente le comparazioni valide per ciascuna key
$comparazioniValide = array(
								'id' => array('eq','ne','contains'),
								'name' => array('eq','ne','contains','ncontains'),
								'lat' => array('eq','ne','gt','lt','ge','le'),
								'long' => array('eq','ne','gt','lt','ge','le'),
								'address' => array('contains'),
								'category' => array('eq','ne','contains','ncontains'),
								'opening' => array('contains','ncontains'),
								'closing' => array('contains','ncontains')
								);

//Funzione che prende in input l'arrey delle comparazioni valide, una key e un comp 
//e ne verifica la correttezza semantica come da protocollo
function correttezzaConfronti($array,$key,$comp)
{
	if(array_key_exists($key,$array))
	{	
			for($i=0;$i<count($array[$key]);$i++)
			{
				if($array[$key][$i] == $comp)
				{
					return TRUE;
					break;
				}
			}				  
	}
	else return FALSE;
}	

//Genera una stringa casuale della lunghezza desiderata
function random_string($length) 
{
	$string = "";
	// genera una stringa casuale con lunghezza
	// uguale al multiplo di 32 successivo a $length
	for ($i = 0; $i <= ($length/32); $i++)
		$string .= md5(time()+rand(0,99));
	// indice di partenza limite
	$max_start_index = (32*$i)-$length;
	// seleziona la stringa, utilizzando come indice iniziale
	// un valore tra 0 e $max_start_point
	$random_string = substr($string, rand(0, $max_start_index), $length);
	return $random_string;
}
						
//Funzione che prende in input un codice Errore(es. Errore[406]) e una
//descrizione dell'errore stesso e lo visualizza a video
function sendHTTPError($obj, $text)
{
	header('Content-Type: text/plain');
	$body = $obj.": ".$text;
	echo $body;
	exit();
}

//Funzione che prende in input un header(content-type) e un object
//ed invia la risposta 
function sendHTTPResult($header, $obj)
{
	header($header);
	header("charset=utf-8");
	echo $obj;
	exit();
}

//Funzione che si occupa della comunicazione Descrittore-Aggregatore
function curl_request($url,$accept) 
{
	//Ricevo l'arrey con gli header della risposta
	$arrey_headers = get_headers($url, 1);
	//Salvo il Content-Type della risposta
	$content = $arrey_headers["Content-Type"];
	//Richiesta CURL
	if (!function_exists('curl_init')) 
	{
		die('Sorry cURL is not installed!');
	}    
	$ch = curl_init(); 
	curl_setopt($ch, CURLOPT_URL, $url);
	// Include header in result? (0 = yes, 1 = no)
	curl_setopt($ch, CURLOPT_HEADER, 0); 
	curl_setopt($ch, CURLOPT_HTTPHEADER, array("Accept: text/turtle, text/csv, application/json,  application/xml"));
	// Should cURL return or print out the data? (true = return, false = print)
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
	$output = curl_exec($ch); 
	curl_close($ch);

	//Genero nome random per il file temporaneo
	$nomefiletemp = random_string(5);

	//Creo un file temporaneo dove salvare il risultato
	$ourFileHandle = fopen($nomefiletemp.".txt", 'w+') or die("can't open file");
	fwrite($ourFileHandle, $output);
	fclose($ourFileHandle);
		 	 
	//Leggo il Content-type della risposta e se necessario converto
	switch($content){
		case "application/json": $xml = json2xml($nomefiletemp.".txt"); break;
		case "text/csv" : $xml = csv2xml($nomefiletemp.".txt"); break;		
		case "text/turtle" : $xml = turtle2xml($nomefiletemp.".txt"); break;	
		case "application/xml" : $xml = $output; break;
		//Errore
		case "text/plain" : sendHTTPError($output,'Sorgente errore --> aggregatore'); break;
		default : {unlink($nomefiletemp.".txt"); sendHTTPError('Attenzione','Formato ricevuto non accettato, Sorgente errore --> aggregatore');} break;
		}	 

	//remove temp file
	unlink($nomefiletemp.".txt"); 

	//Genero nome random per il file temporaneo
	$nomefiletemp2 = random_string(5);
	//Creo file temporaneo con la risposta convertita in formato XML
	$ourFileHandle2 = fopen($nomefiletemp2.".xml", 'w+') or die("can't open file");
	fwrite($ourFileHandle2, $xml);
	fclose($ourFileHandle2);

	//Converto nel formato richiesto dal narratore
	switch($accept){
		case "application/json": $result = xml2json($nomefiletemp2.".xml"); break;
		case "text/csv" : $result = xml2csv($nomefiletemp2.".xml"); break;		
		case "text/turtle" : $result = turtle2xml($nomefiletemp2.".xml"); break;	
		case "application/xml" : $result = $xml; break;
		default : {unlink($nomefiletemp2.".xml"); sendHTTPError('Attenzione','Formato richiesto non accettato');} break; 
		}	 

	//remove temp file
	unlink($nomefiletemp2.".xml");	
	
	return $result;			
}

//Funzione che preso in input l'id di un aggregatore ne ricerca l'url nel metacatalogo
function get_aggr_url($aggr_id)
{
	$metacatalogoxml = new DOMDocument();
	//Metacatalogo_stone.xml viene generato grazie ad uno script esterno 
	//per velocizzare il processo di lettura del Metacatalogo online
	$metacatalogoxml->load("../metacatalogo_store.xml");
	$dommeta=simplexml_import_dom($metacatalogoxml);

	$arrayurl = $dommeta->Xpath("/Aggregatori/aggregatore/@id"); 
	$i=0;
	foreach($arrayurl as $url1)
	{
						if( $url1 == $aggr_id )
						{							
							$ccc = $dommeta->Xpath("/Aggregatori/aggregatore/@url");
							$urlaggr = $ccc[$i];		
							break;	
						}	
						$i++;	
	}
	return $urlaggr;	
}

//Funzione per verificare se un URL esiste 
function urlExists($url=NULL)  
{  
   if($url == NULL) return false;  
   
   $ch = curl_init($url);  
   curl_setopt($ch, CURLOPT_TIMEOUT, 5);  
   curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);  
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  
   $data = curl_exec($ch);  
   $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);  
   curl_close($ch);  
   if($httpcode>=200 && $httpcode<300)
   {  
     return true;  
   } 
   else 
   {  
     return false;  
   }  
} 


?>
