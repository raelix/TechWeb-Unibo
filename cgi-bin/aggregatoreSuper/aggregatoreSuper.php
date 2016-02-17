<?php //Margherita Donnici
require_once('../funzioni.php');

//Header della risposta
$headers = ('Content-type: application/json');

// Leggo il database JSON in una stringa $filejson
$filejson =file_get_contents("supermarketBO2011.json");  

// Prendo la stringa codificata in JSON e la converto in una variabile PHP; il valore "true" indica che la voglio convertire in un array associativo
$jsondecoded =json_decode($filejson, true); 


//Prelevo key, comp e value passati allo script via parametri URL, convertendo i primi due in minuscole per evitare problemi di case sensitive
$key = strtolower($_GET["key"]); 
$conf = strtolower($_GET["comp"]);
$value = ($_GET["value"]);
// Uso la funzione trim per rimuovere eventuali whitespace
$key = trim($key);
$conf = trim($conf);
$value = trim($value);

//Gestione degli errori

if($_SERVER['REQUEST_METHOD']!="GET") // Se il metodo di richiesta non è quello da protocollo ( GET ), restituisco l'errore corrispondente
{
	sendHTTPError($Errore[405], "Il metodo utilizzato non e' di tipo GET");
}

if ($key and $conf and $value) // Se sono presenti tutti e tre i parametri richiesti
{
	if(correttezzaConfronti($comparazioniValide,$key,$conf) == FALSE) // Verifico che i confronti rispettano le specifiche del protocollo
	{
		sendHTTPError($Errore[406], "Comparazione al di fuori delle specifiche");
	}
}
// Se non è stato specificato nessun parametro, assegno alla variabile del risultato restitituito il file decodificato precedentemente

else if (!$key and !$conf and !$value) 
{
	$loc= $jsondecoded;
}
else // Altrimenti se manca uno o due dei parametri restituisco l'error corrispondente
{
   sendHTTPError($Errore[406], "Parametro non settato"); 
}

// Converto i valori comp e value rispettivamente in maiuscole e minuscole (come sono stati scritti nel codice) per evitare problemi di case sensitive
$conf = strtoupper($_GET["comp"]);
$value = strtolower($_GET["value"]);
// Uso la funzione trim per rimuovere eventuali whitespace
$key = trim($key);
$conf = trim($conf);
$value = trim($value);

// Se non è stato specificato nessun parametro, assegno alla variabile del risultato restitituito il file decodificato precedentemente
if (!$key and !$conf and !$value) {
	$loc= $jsondecoded;
}
else // Procedo nelle comparazioni caso per caso(l'array finale con il risultato sarà $s[])
{
 // Con il primo foreach entro nel campo "locations" dell'array jsondecoded, che è un array di location,  e assegno i nomi dei campi (in questo caso gli id) alla variabile jkey e il loro contenuto alla variabile $jvalue
	foreach($jsondecoded["locations"] as $jkey => $jvalue) 
	{
		if($key == "id") // Se il parametro key corrisponde a id
		{
			switch ( $conf ) // controllo qual'è il confronto richiesto
			{
// Se è il confronto EQ, comparo le due stringhe (l'id passato come value e quello correntemente assegnato alla variabile jkey con la funzione strcasecmp che confronta le stringhe senza fare distinzione tra maiuscole e minuscole e restituisce 0 se sono uguali. In caso risulta che siano uguali, assegno all'array $s la location ( inserendo $jkey (quindi l'id) come key e $jvalue (valore dell'array $jsondecoded corrispondente a quel campo) come valore.
				case 'EQ' : 
					if(strcasecmp($jkey,$value)==0)  $s[$jkey]=$jvalue;break;
// Se è il confronto NE, eseguo lo stesso procedimento per EQ, ma inserendo la location in $s quando strcasecmp restituisce un risultato diverso da 0 (e quindi non sono uguali)
				case 'NE' : 
					if(strcasecmp($jkey,$value)!=0)  $s[$jkey]=$jvalue;break;
// Se è il confronto CONTAINS, uso la funzione stristr che è ugualmente caseinsensitive e resitituisce FALSE se non trova nessuna occorrenza della seconda stringa passata come parametro nella prima stringa . Se la funzione restituisce un qualunque valore diverso da FALSE inserisco la location in $s
				case 'CONTAINS' : 
					if( stristr($jkey,$value)) $s[$jkey]=$jvalue;break;
			}
			
		}elseif($key== "category") // Se il parametro key corrisponde a category
		{
                       $jivalue= $jvalue[$key]; // Assegno alla variabile jivalue il valore del campo category ; $jivalue contiene quindi l'array "category"
// Eseguo un secondo foreach, assegnando a $jiivalue il contenuto dei campi dell'array category
				foreach($jivalue as  $jiivalue) {

				switch ($conf) // Controllo qual'è il confronto richiesto
				{
				case 'EQ' : // Se il confronto è EQ faccio lo stesso procedimento spiegato precedentemente
					if(strcasecmp($jiivalue,$value)==0)  $s[$jkey]=$jvalue;break;
				case 'NE' :  // Se il confronto è NE faccio lo stesso procedimento spiegato precedentemente
					if(strcasecmp($jiivalue,$value)!=0)  $s[$jkey]=$jvalue;break;
				case 'CONTAINS' : // Se il confronto è CONTAINS faccio lo stesso procedimento spiegato precedentemente
					if( stristr($jiivalue,$value)) $s[$jkey]=$jvalue;break;
// e il confronto è NCONTAINS faccio lo stesso procedimento del contains ma negato; se stristr restituisce false, inserisco la funzione
				case 'NCONTAINS' : 
					if( !stristr($jiivalue,$value)) $s[$jkey]=$jvalue;break;	
				}
			
		}}

//Eseguo un terzo foreach per effettuare il resto dei confronti, assegnando a $jikey le key dei vari campi di ogni location ( name, lat, long...) e a jivalue il loro contenuto
		else{ foreach($jvalue as $jikey => $jivalue) 
				switch ( $conf ) // Controllo qual'è il confronto richiesto
			{

				case 'EQ' : 
// Una volta trovato il confronto, trovo zquale key si sta cercando comparandola con il valore di jkey e poi proseguo con il confronto tra il valore cercato e quello di jikey
					switch($key) {  
						case 'name' : 
							if ( strcasecmp($jikey,$key)==0) { if (strcasecmp($jivalue,$value)==0)  $s[$jkey]=$jvalue;}break;
						case 'lat' :
							if ( strcasecmp($jikey,$key)==0) { if (strcasecmp($jivalue,$value)==0)  $s[$jkey]=$jvalue;}break;
						case 'long' :
							if ( strcasecmp($jikey,$key)==0) { if (strcasecmp($jivalue,$value)==0)  $s[$jkey]=$jvalue;}break;
						
} break;

				case 'NE' :
					switch($key) {
						case 'name' :
							if ( strcasecmp($jikey,$key)==0) { if (strcasecmp($jivalue,$value)!=0)  $s[$jkey]=$jvalue;}break;
						case 'lat' :
							if ( strcasecmp($jikey,$key)==0) { if (strcasecmp($jivalue,$value)!=0)  $s[$jkey]=$jvalue;}break;
						case 'long' :
							if ( strcasecmp($jikey,$key)==0) { if (strcasecmp($jivalue,$value)!=0)  $s[$jkey]=$jvalue;}break;
						case 'category' :
							if ( strcasecmp($jikey,$key)==0) { if (strcasecmp($jivalue,$value)!=0)  $s[$jkey]=$jvalue;}break;
	}break;			
				case 'CONTAINS' :
					switch($key) {
						case 'name' :
							if ( strcasecmp($jikey,$key)==0) { if( stristr($jivalue, $value))  $s[$jkey]=$jvalue;}break;
						case 'address' :
							if ( strcasecmp($jikey,$key)==0) { if( stristr($jivalue,$value))  $s[$jkey]=$jvalue;}break;
						case 'opening' :
							if ( strcasecmp($jikey,$key)==0) { if( stristr($jivalue,$value))  $s[$jkey]=$jvalue;}break;
						case 'closing' :
							if ( strcasecmp($jikey,$key)==0) { if(stristr($jivalue,$value))  $s[$jkey]=$jvalue;}break;
		} break;
				case 'NCONTAINS' :
					switch($key) {
						case 'name' :
							if ( strcasecmp($jikey,$key)==0) { if( !stristr($jivalue, $value))  $s[$jkey]=$jvalue;}break;
						case 'address' :
							if ( strcasecmp($jikey,$key)==0) { if( !stristr($jivalue,$value))  $s[$jkey]=$jvalue;}break;
						case 'opening' :
							if ( strcasecmp($jikey,$key)==0) { if( !stristr($jivalue,$value))  $s[$jkey]=$jvalue;}break;
						case 'closing' :
							if ( strcasecmp($jikey,$key)==0) { if( !stristr($jivalue,$value))  $s[$jkey]=$jvalue;}break;
		} break;
				case 'GT' :
					switch($key) {
					
						case 'lat' :
							if ( strcasecmp($jikey,$key)==0) { if ($jivalue > $value)  $s[$jkey]=$jvalue;}break;
						case 'long' :
							if ( strcasecmp($jikey,$key)==0) { if ($jivalue > $value)  $s[$jkey]=$jvalue;}break;
} break;

				case 'LT' :
					switch($key) {
					
						case 'lat' :
							if ( strcasecmp($jikey,$key)==0) { if ($jivalue < $value)  $s[$jkey]=$jvalue;}break;
						case 'long' :
							if ( strcasecmp($jikey,$key)==0) { if ($jivalue < $value)  $s[$jkey]=$jvalue;}break;
} break;


				case 'GE' :
					switch($key) {
					
						case 'lat' :
							if ( strcasecmp($jikey,$key)==0) { if ($jivalue >= $value)  $s[$jkey]=$jvalue;}break;
						case 'long' :
							if ( strcasecmp($jikey,$key)==0) { if ($jivalue >= $value)  $s[$jkey]=$jvalue;}break;
} break;

				case 'LE' :
					switch($key) {
					
						case 'lat' :
							if ( strcasecmp($jikey,$key)==0) { if ($jivalue <= $value)  $s[$jkey]=$jvalue;}break;
						case 'long' :
							if ( strcasecmp($jikey,$key)==0) { if ($jivalue <= $value)  $s[$jkey]=$jvalue;}break;
} break;



break; }}}}
  
//Creo l'array associativo con i risultati rispettando il formato del file originale
$loc=array("locations"=>$s, "metadata"=>array());
//Popolo l'array 'metadata'
$loc["metadata"] = $jsondecoded["metadata"];}

//Converto l'array in json con la json_encode che restituisce la rappresentazione json di $loc
$body = json_encode($loc);		

//Mando il risultato con i  header
sendHTTPResult($headers, $body);
?>
