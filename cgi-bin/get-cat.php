<?php //Andrea Mazzocchi
//Script che legge il Metacatalogo e salva periodicamente id e url
//degli aggregatori di ogni gruppo sul file metacatalogo_store.xml
//require_once('./funzioni.php');

	$url = "http://vitali.web.cs.unibo.it/twiki/pub/TechWeb12/MetaCatalogo1112/metaCatalogo.xml";
	unlink("./metacatalogo_store.xml"); 	
	
	//Funzione che esegue una sessione curl sull'URL ricevuto in input
	//ritornandone il contenuto
	function download_page($path) {
		//Inizializzo la sessione cURL
		$curl = curl_init();
		//Specifico l'URL da esaminare
		curl_setopt($curl, CURLOPT_URL, $path);
		//Setto FALSE per non includere header nell'output
		curl_setopt($curl, CURLOPT_HEADER, 0);
		//Concedo un massimo di 5 secondi di esecuzione delle funzioni cURL
		curl_setopt($curl, CURLOPT_TIMEOUT, 5);
		//Setto TRUE per ritornare il valore dato da curl_exec()
		//come una stringa, invece di fare un output diretto
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		//Esegue la sessione cURL
		$retValue = curl_exec($curl);
		//Chiudo la sessione cURL
		curl_close($curl);
		return $retValue;
	}

	//Converto l'URL in stringa
	$sXML = download_page($url);
	//Converto l'URL da stringa a oggetto, per interagirci con xpath
	$oXML = new SimpleXmlElement($sXML);

	//Creo un nuovo documento DOM
	$doc = new DOMDocument("1.0", "UTF-8");
	//Inizializzo la radice Aggregatori
	$root = $doc->appendChild($doc -> createElement("Aggregatori"));

	//Eseguo ciclo sull'URL oggetto
	foreach($oXML -> xpath("//catalogo/@url") as $valore) {
		//Se riesce a caricare la porzione di URL interessata
		if($xml2 = simplexml_load_file($valore)) {
			//Per tutti gli aggregatori prelevo id e url			
			$id = $xml2 -> xpath("//aggregatore/@id");
			$url = $xml2 -> xpath("//aggregatore/@url");
			//Popolo il documento XML appendendo ogni aggregatore trovato
			//e settando per ciascuno gli attributi id e url
			for($in=0; $in<count($url); $in++) {
				$x = $root->appendChild($doc -> createElement("aggregatore"));
				$x -> setAttribute("id", $id[$in]);
				$x -> setAttribute("url", $url[$in]);
			}
		}
	}
	
	//Creo il documento XML dalla sua rappresentazione DOM 
	$output= $doc -> saveXML(); 
	
	//Salva il risultato in un file XML
	$ourFileHandle = fopen("metacatalogo_store".".xml", 'w+') or die("can't open file");
	fwrite($ourFileHandle, $output);
	fclose($ourFileHandle);

?>
