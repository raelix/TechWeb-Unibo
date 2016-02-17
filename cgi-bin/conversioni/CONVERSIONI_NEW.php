<?php

function json2xml($file){//Gabriele Cigna

//header("Content-type:application/xml");

require_once('json.php'); //JSON parser
require_once('jpath.php'); // JSONPath evaluator

$i=0;
$w=0;
$doc=new DomDocument();
$filejson =file_get_contents($file);
$root=$doc->appendChild($doc->createElement('locations'));
$jsondecoded=json_decode($filejson);
$dumbo= new RecursiveArrayIterator($jsondecoded);

foreach($dumbo as $key){//trovo gli Id
if($key!=NULL){
foreach($key as $key=>$value){ 
$id[$i]=$key;
$i++;
}}
}

$parser = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
$o = $parser->decode($filejson);
$bum = jsonPath($o, "$..category");
$k=0;
if($bum!=NULL){
foreach($bum as $v){
foreach($v as $s){
$category[$k]=$s;
$k++;}
}
}

$location=jsonPath($o,"$.locations");
foreach($location as $key){//trovo i campi
if($key!=NULL){
foreach($key as $keys=>$vall){
foreach($vall as $ciaa=>$rrr){
if($aaa[0]==$ciaa and $w>0) break;
$aaa[$w]=$ciaa;
$w++; 
}}
}
}

$x=0;
$metadata=jsonPath($o,"$.metadata");
if($metadata!=NULL){
foreach($metadata as $key=>$value){
foreach($value as $v){
$meta[$x]=$v;
$x++;
}}}

$metadata= $root->appendChild($doc->createElement('metadata'));
if($metadata!=NULL){
for ($y=0; $id[$y]=='creator';$y++){
$k=$y;}

$z=0;
for ($k;$k<=((sizeof($id))-1);$k++){
$creator=$metadata->appendChild($doc->createElement($id[$k]));
$creator->appendChild($doc->createTextNode($meta[$z]));
$z++;
}

for($cont=0;$cont<($w);$cont++){
 $campo[]= jsonPath($o, '$..'.$aaa[$cont]);
}}

$lat=jsonPath($o,"$..lat");
$long=jsonPath($o,"$..long");

if($value!=NULL){
for ($j=0; $id[$j]!="creator" ; $j++) {
	$loc = $root->appendChild($doc->createElement('location'));
	$loc->setAttribute('id',$id[$j]);
	$loc->setAttribute('lat',$lat[$j]);
	$loc->setAttribute('long',$long[$j]);
	$categoria=$loc->appendChild($doc->createElement('category'));
	$categoria->appendChild($doc->createTextNode($category[$j]));
	for($g=0;$g<$w;$g++){
	if( !is_array($campo[$g][$j]) ) {
		$tag=$loc->appendChild($doc->createElement($aaa[$g]));
		$tag->appendChild($doc->createTextNode($campo[$g][$j]));
	 } 
	}
}}

return $doc->saveXML();
}


function csv2xml($file){//Gabriele Cigna

error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', true);
ini_set('auto_detect_line_endings', true);

$inputFile  = fopen($file, 'rt');

$headers = fgetcsv($inputFile);

$posizioneCreator=0;
foreach($headers as $keys){
	if(strcasecmp($keys,'creator')==0){
		break;
	}
	$posizioneCreator++;
}

$posizioneId=0;
foreach($headers as $keys){
	if(strcasecmp($keys,'id')==0){
		break;
	}
	$posizioneId++;
}

$posizioneLat=0;
foreach($headers as $keys){
	if(strcasecmp($keys,'lat')==0){
		break;
	}
	$posizioneLat++;
}

$posizioneLong=0;
foreach($headers as $keys){
	if(strcasecmp($keys,'long')==0){
		break;
	}
	$posizioneLong++;
}

$doc = new DomDocument();
$doc->formatOutput = true;

$root = $doc->createElement('locations');
$root = $doc->appendChild($root);
$j=0;

while (($riga = fgetcsv($inputFile)) !== FALSE) 
{
if($j==0)
 {	
 $metadata = $root->appendChild($doc->createElement('metadata'));
 for($i=$posizioneCreator; $i<(sizeof($headers)); $i++) 
   {
     $nometag = $metadata->appendChild($doc->createElement(strtolower($headers[$i])));
     $nometag->appendChild($doc->createTextNode($riga[$i]));
   }
 }

	$location = $root->appendChild($doc->createElement('location'));
	$location->setAttribute("id", $riga[$posizioneId]);
	$location->setAttribute("lat", $riga[$posizioneLat]);
	$location->setAttribute("long", $riga[$posizioneLong]);		
	for($i=0; $i<$posizioneCreator; $i++)
	{
	  if($i!==$posizioneId and $i!==$posizioneLat and $i!==$posizioneLong)
	  {
	  $nometag = $location->appendChild($doc->createElement(strtolower($headers[$i])));
     $nometag->appendChild($doc->createTextNode($riga[$i]));
	  }
	}
$j=1;
}

return $doc->saveXML();
}

function turtle2xml($file) {//Andrea Mazzocchi

	//Includo le librerie necessarie per gestire Turtle
	include_once("semsol-arc2-c7c03da/ARC2.php");
	
	//Instanziazione del parser...
	$parser = ARC2::getTurtleParser();
	$parser -> parse($file);
	//...parsing delle triple
	$triples = $parser->getSimpleIndex();	

	//Inizializzo nuovo documento DOM (con versione, codifica e DTD)
	$doc = new DOMDocument("1.0", "UTF-8");
	$imple = new DOMImplementation();
	$dtd = $imple->createDocumentType('locations', '', 'http://vitali.web.cs.unibo.it/twiki/pub/TechWeb12/DTDs/locations.dtd');
	$doc = $imple->createDocument('', '', $dtd);
	$doc->formatOutput = true;

	//Inializzo il nodo radice "locations"
	$root = $doc->appendChild($doc->createElement("locations"));
	//Prelevo le chiavi dell'array associativo
	$index = array_keys($triples);

	//Eseguo un ciclo su quante sono le chiavi
	for ($i=0; $i < count($index); $i++) {
		//Separo l'URL secondo gli "/"
		$temp = explode("/", $index[$i]);
		$a = (count($temp)-1);
		$id = $temp[$a];
		
		//Codice per il riempimento del tag "metadata"
		if (stristr($id, '.ttl') == true) {
			//"metadata" viene riconosciuto con ".ttl" al termine del prefix "this"
	   	$root2 = $root->appendChild($doc -> createElement("metadata"));
      	$temp1 = $index[$i];
      	//Secondo le direttive del DTD, un database XML non ha il tag
      	//"description" ("version" dell'XML corrisponde a "description"
      	//del Turtle), quindi sostituisco "description" (dal Turtle)
      	//in "version" (per XML)
      	foreach ($triples[$temp1] as $deep1 => $deep2) {
				if (stristr($temp1, "description") == true) {
			 		$temp1 = "version";
		  		}	
		     	else {
		      	$b = explode("/", $deep1);
		     	}
		     	//Costruisco i "metadata" nel DOM
				$root30 = $root2 -> appendChild($doc -> createElement($b[count($b)-1]));
				$root30 -> appendChild($doc -> createTextNode($deep2[0]));
				}
		}
		//Codice per il riempimento del tag "location"
		else {
			$root3 = $root->appendChild($doc -> createElement("location"));
			$id = $temp[$a];
			//"id" deve essere attributo di "location"
			$root3 -> setAttribute("id", $id);
			$temp2 = $index[$i];
			//Costruisco le varie "location"
			foreach ($triples[$temp2] as $key => $value) {
				if (stristr($key, "fn") == true) {
					$key = "name";
				}
				if (stristr($key, "extended-address") == true) {
					$key = "address";
				}
				//anche "latitude" e "longitude" devono essere attributi di "location"
				if (stristr($key, "latitude") == true) {
					$root3 -> setAttribute("lat", $value[0]);
					unset($value);			
			 	}
				if (stristr($key, "longitude") == true) {
					$root3 -> setAttribute("long", $value[0]);
					unset($value);
				}
		      if (stristr($key, "opening") == true or stristr($key, "closing") == true) {
			   	$b= explode("/", $key);
				}	
				else {
					$b= explode("ns#", $key);
				}
				foreach ($value as $deep1 => $deep2) {
					$root30 = $root3 -> appendChild($doc -> createElement($b[count($b)-1]));
					$root30 -> appendChild($doc -> createTextNode($deep2));
				}
			}
		}
	}
	//Salvo il documento DOM in XML
	echo $doc->saveXML();
}



function xml2turtle($file) {//Andrea Mazzocchi

	include_once("semsol-arc2-c7c03da/ARC2.php");

	//Leggo il database in una stringa
	$string = file_get_contents($file);

	//Interpreto una stringa XML in un oggetto
	$obj = simplexml_load_string($string);

	//Ritorna la rappresentazione JSON di un valore, prende una
	//stringa JSON encoded e la converte in una variabile 
	$data = json_decode((json_encode((array) $obj)), 1);

	//Array associativo utile per la gestione dei prefix
	$namespace = array(':'=>'http://www.essepuntato.it/resource/',
			'vcard'=>'http://www.w3.org/2006/vcard/ns#',
			'cs'=>'http://cs.unibo.it/ontology/',
			'dcterms'=>'http://purl.org/dc/terms/',
			'xsd'=>'http://www.w3.org/2001/XMLSchema#',
			'this'=>'http://vitali.web.cs.unibo.it/twiki/pub/TechWeb12/DataSource2/posteBO2011.ttl');

	$namespace2 = array("ns" => $namespace);

	//Elimino "@attributes" creando nuove chiavi apposta per "id", "lat" 
	//e "long", salvandole dalla rimozione di "@attributes"
	for($i = 0; $i < count($data['location']); $i++) {
		//Memorizzo le chiavi dell'array associativo
		$attr = array_keys($data['location'][$i]['@attributes']);
		for($a = 0; $a < count($attr); $a++) {
			$attr_temp = $attr[$a];
			$data['location'][$i][$attr_temp] = $data['location'][$i]['@attributes'][$attr_temp];
		}
	//Cancello le posizioni, ormai inutili, dove 
	//"id", "lat" e "long" erano prima contenuti
	unset($data['location'][$i]['@attributes']);
	}

	//Per la conversione in Turtle ho convertito da XML ad array associativo
	//che memorizza il documento da convertire in Turtle
	$newarray = array();

	foreach($data as $key => $value) {

		//Codice per il riempimento del blocco "metadata"
		if(strcasecmp($key, 'metadata') == 0) {
			//Memorizzo le chiavi di $value...    
   		$s = array_keys($value);
   		//...eseguendo un ciclo di riempimento 
		   for($j = 0; $j < count($value); $j++) {
		  		//$deep memorizza i nomi dei tag XML del
		  		//campo metadata (creator, created, ...)
				$deep = $s[$j];
				//Richiamo da $namespace i prefix per i metadata
				$meta_subj = $namespace['this'];
				$dcterms = $namespace['dcterms'];
				$xsd = $namespace['xsd'];
				//"created" e "valid" sono delle date e devono essere validati 
				//dal prefix xsd in modo da distinguerli da semplice stringhe 
				if((stristr($deep,'created')) or (stristr($deep,'valid'))) {
					$newarray[$meta_subj.'metadata'][$dcterms.$deep] = array($data['metadata'][$deep], $xsd.'date');
				}		
				else {
					$newarray[$meta_subj.'metadata'][$dcterms.$deep] = $data['metadata'][$deep];
				}
			}
		}

		//Codice per il riempimento del blocco "location"
		if(strcasecmp ($key, 'location') == 0) {
			for($z = 0; $z < count($value); $z++) {
				//Richiamo i quattro prefix che serviranno per questo blocco
				$n_this = $namespace['this'];
				$loc_subj = $namespace[':'];
				$v_card = $namespace['vcard'];
				$cs = $namespace['cs'];
				//$id memorizza i vari "id" (far0001, far0002, ...)
				$id = $value[$z]['id'];
				//Cancello "id" in modo da renderlo soggetto con ":",
				//altrimenti sarebbe stato l'oggetto di "vcard"
				unset($data['location'][$z]['id']);
				//$keys_z contiene un'array associativo i cui valori sono
				//i nomi dei tag XML (category, subcategory, name, ..)
				$keys_z = array_keys($data['location'][$z]);
				//Con questo ciclo for popolo l̈́'array da passare alla conversione
				for($kz = 0; $kz < count($keys_z); $kz++) {
					$temp = $keys_z[$kz];
					//I campi "opening" e "closing" sono oggetti del predicato "cs"...
					if((stristr($temp,'opening')) or (stristr($temp,'closing'))) {
						$newarray[$loc_subj.$id][$cs.$temp] = $data['location'][$z][$temp];
					}
					//...mentre i restanti campi sono oggetti del predicato "vcard"
					else {
						$newarray[$loc_subj.$id][$v_card.$temp] = $data['location'][$z][$temp];
					}
				}
			}
		}
	}
			
	//Salvo in formato Turtle valido	
	$doc = ARC2::getTurtleSerializer($namespace2);
	$fromXMLtoTTL = $doc->getSerializedIndex($newarray);
	return $fromXMLtoTTL;
	
}



function xml2csv($file){//Gabriele Cigna

   $document = file_get_contents($file); 
   $xml= simplexml_load_string ($document);
   $arrayxml = json_decode((json_encode((array) $xml)), 1);
   $xmlarray=$arrayxml["location"];

if(is_array($xmlarray[0])){

   for ($i=0, $i_massimo= count($xmlarray); $i<$i_massimo; $i++)
   {	
      $attributi= array_keys($xmlarray[$i]["@attributes"]);
      for($j=0, $j_massimo=count($attributi); $j < $j_massimo; $j++)
      {
      	$indice=$attributi[$j]; 
      	$xmlarray[$i][$indice] = $xmlarray[$i]["@attributes"][$indice];
      } 
      unset ($xmlarray[$i]["@attributes"]);
		
   }
   for ($i=0, $i_massimo= count($xmlarray); $i<$i_massimo; $i++)
   {
	 
     //Se la categoria è un array la converto 
     $campi =  array_keys($xmlarray[$i]);
     for ($j = 0, $j_massimo = count($campi); $j < $j_massimo; $j++)
     {
       if (strcasecmp($campi[$j], "category") == 0)
       {
       	$cat = $campi[$j];
       	$arr_cat = $xmlarray[$i][$cat];
       	$xmlarray[$i][$cat] = array($arr_cat);
       }
     }
     $temp = $xmlarray[$i]['id'];
     $new["locations"][$temp] = $xmlarray[$i];
     unset ($new["locations"][$temp]['id']);
   }
}else{
	
      $attributi= array_keys($xmlarray["@attributes"]);
      for($j=0, $j_massimo=count($attributi); $j < $j_massimo; $j++)
      {
      	$indice=$attributi[$j]; 
      	$xmlarray[$indice] = $xmlarray["@attributes"][$indice];
      } 
      unset ($xmlarray["@attributes"]);
		

	 
     //Se la categoria è un array la converto 
     $campi =  array_keys($xmlarray);
     for ($j = 0, $j_massimo = count($campi); $j < $j_massimo; $j++)
     {
       if (strcasecmp($campi[$j], "category") == 0)
       {
       	$cat = $campi[$j];
       	$arr_cat = $xmlarray[$cat];
       	$xmlarray[$cat] = array($arr_cat);
       }
     }
     $temp = $xmlarray['id'];
     $new["locations"][$temp] = $xmlarray;
     unset ($new["locations"][$temp]['id']);
   
}
   $new['metadata'] = $arrayxml["metadata"];
	
	$riga = new RecursiveArrayIterator($new);
	$campicsv = '"id",';

$conto=0;
foreach($riga as $a){
	foreach($a as $b=>$c){//qui vedo solo gli id dei sup
		foreach($c as $d=>$e){
			if($conto>(count($campi)-2))break;
			$nomicampi[]=$d;
			$conto++;
			 }}}
	 

for($t=0;$t<($conto);$t++){
	$campicsv .= '"'.$nomicampi[$t].'"'.',';}	

//TROVO I tag/dati METADATA
foreach($riga as $q=>$valu)
{//locations
	if( strcasecmp($q,'metadata')==0)
	{
	foreach($valu as $supe=>$values)
	{	
		$campicsv .= '"'.$supe.'"'.',';
	}
	}
}			 
$campicsv = rtrim($campicsv, ',');

$campicsv .= "\n";		 

$metadata = "";

//TROVO I METADATA
foreach($riga as $q=>$valu)
{//locations
	if(strcasecmp($q,'metadata')==0)
	{
	foreach($valu as $supe=>$values)
	{	
		$metadata .= '"'.$values.'"'.',';
	}
	}
}
$metadata = rtrim($metadata, ',');

//TROVO IL RESTO
foreach($riga as $r=>$val)
{//locations
	if(strcasecmp($r,'locations')==0)
	{ 
	foreach($val as $sup=>$value)
	 {//qui vedo solo gli id dei sup
		$campicsv .= '"'.$sup.'"'.',';
		foreach($value as $dati)
		{//sono dentro i sup e vedo i dati	
			if(is_array($dati))
			{
				$arrStringa = "";
				foreach($dati as $datis){
					$arrStringa .= $datis;
				}
				$campicsv .= '"'.$arrStringa.'"'.',';
				continue;
			} 
			else 
				{
					$campicsv .= '"'.$dati.'"'.',';
				}
		} $campicsv .= $metadata."\n";
	 }
	}
} 
	return $campicsv;
}


function xml2json($file){ // Margherita Donnici
	
$documento = file_get_contents($file); // Leggo il file XML in una stringa $document
$xml= simplexml_load_string ($documento);  // Trasformo la stringa xml in oggetto XML
$arrayxml = json_decode((json_encode((array) $xml)), 1); // Applico la json_encode e la json_decode per ottenere un array associativo
$xmlarray=$arrayxml["location"]; // Assegno a xmlarray l'array delle location

	if(is_array($xmlarray[0])){  // Se il primo elemento è un array (non è vuoto)

		for ($i=0; $i < count($xmlarray); $i++) { // Per ogni location

// "Faccio uscire" i campi id, lat e long dall'array @attributes per inserirli come campi di ogni location

// Prendo le keys dell'array "attributes" (quindi id, lat e long) con la funzione array_keys che restituisce un array di tutte le 		key dell'array di input
		$attributi= array_keys($xmlarray[$i]["@attributes"]); 

				for($j=0; $j < count($attributi); $j++) { // Per ogni campo di attributi
      				
				// Inserisco il valore corrispondente alla key in un campo di nome key nell'array xmlarray
      				$xmlarray[$i][$attributi[$j]] = $xmlarray[$i]["@attributes"][$attributi[$j]]; }
 

		unset ($xmlarray[$i]["@attributes"]); // Elimino il campo "@attributes" nell'array xml con la funzione unset

	 
   		//In json, la categoria è un array, al contrario di XML
		// Creo un array con in ogni campo il nome delle key di ogni location ( quindi id, lat, long, closing...)
     		$campi =  array_keys($xmlarray[$i]); 
     
				for ($j = 0; $j < count($campi); $j++) { // Per ogni campo di questo array
// Cerco il campo category con strcasecmp che confronta le stringhe senza fare distinzione tra maiuscole e minuscole e restituisce 0 se sono uguali.
       				if (strcasecmp($campi[$j], "category") == 0) {
				// Creo un campo 'category' che è un array nell'arrayxml
       				$cat = $campi[$j];
       				$arr_cat = $xmlarray[$i][$cat];
				$xmlarray[$i][$cat] = array($arr_cat);
       				}
     }	
// Creo il nuovo array risultato inserendoci un array di location di cui la key per ogni location è l'id ( invece di un numero intero come in xmlarray )
     $temp = $xmlarray[$i]['id']; // Temp contiene l'id della location
     $new["locations"][$temp] = $xmlarray[$i]; // Ci inserisco le location
     unset ($new["locations"][$temp]['id']); // Elimino il campo id nell'array della location
}

		} else { // Se il primo elemento non è un array 
	
		$attributi= array_keys($xmlarray["@attributes"]); // Creo un array con gli attributi
			
			for($j=0; $j < count($attributi); $j++) {// Scandisco quest'array
      				$indice=$attributi[$j]; 
      				$xmlarray[$indice] = $xmlarray["@attributes"][$indice]; } // Creo in xmlarray un campo con le key contenute in @attributes
      			unset ($xmlarray["@attributes"]); // Elimino il campo @attributes
		

	 		//Se la categoria è un array la converto 
			$campi =  array_keys($xmlarray);
     				for ($j = 0, $j_massimo = count($campi); $j < $j_massimo; $j++) {
     					  if (strcasecmp($campi[$j], "category") == 0) {
       						$cat = $campi[$j];
       						$arr_cat = $xmlarray[$cat];
       						$xmlarray[$cat] = array($arr_cat); }
		
// Creo il nuovo array risultato inserendoci un array di location di cui la key per ogni location è l'id ( invece di un numero intero come in xmlarray )
										}
     $temp = $xmlarray['id'];
     $new["locations"][$temp] = $xmlarray;
     unset ($new["locations"][$temp]['id']);
   
} 
   $new['metadata'] = $arrayxml["metadata"]; // Aggiungo i metadata

   $filejson = json_encode($new); //Converto l'array in json con la json_encode che restituisce la rappresentazione json di $new
   
   return $filejson;
	
}

?>
