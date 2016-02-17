<?php //Margherita Donnici
require_once('../funzioni.php');

//Prendo i valori dall'url
$orario1 = $_GET["orario1"]; //'Mon, Tue, Thu, Wed, Fri, Sat, Sun: 2105-0130'; 
$orario2 = $_GET["orario2"]; // '2012-02-02: 2135-2136';

//Gestione degli errori
if (!$orario1 or !$orario2)
{ 
	sendHTTPError($Errore[406], 'Parametri assenti');
}

$arrayorario1 = explode ('.', $orario1); // Divido la stringa degli orari della struttura in un array di stringhe con in ogni posizione un set di orari

$arrayorario2 = explode (':', $orario2); // Divido la stringa dell'orario ricercato in un array con il giorno in posizione 0 e l'intervallo di orari in posizione 1

$pattern = "/^[0-9]{4}\-[0-9]{1,2}\-[0-9]{1,2}$/";//Creo una espressione regolare che corrisponde alla data nel formato richiesto dal rpotocollo

//Se il giorno ricercato è nella forma yyyy-mm-dd lo converto
if (preg_match($pattern, $arrayorario2[0])) {

	// Conversione data => giorno settimanale
	//Trovo il gioprno della settimana con la funzione date che restituisce una stringa formattata secondo la
	//stringa formato passata come paramentro, in questo caso D usando il timestamp passato egualmente come parametro
	$data = explode ('-', $arrayorario2[0]);// $data[0] = anno, $data[1]=mese, $data[2]=giorno
	$h = mktime(0, 0, 0, $data[1], $data[2], $data[0]); //Creo un timestamp con la data ricercata
	$w= date("D", $h) ;

	$arrayorario2[0]= $w;
	
}

if (!stristr($orario1,$arrayorario2[0])) //Confronto le due stringhe per vedere se il giorno della settimana e' contenuto nell'opening
{
	sendHTTPResult("Content-type: text/plain","Giorno di chiusura");
	return FALSE; 
}
else 
{
	for ($i=0 ; $i < sizeof($arrayorario1) ; $i++) //scandisco l'array degli orari della struttura finchè trovo la stringa contenent il giorno che cerco
	{
		
			
			$orari2 = explode ("-", $arrayorario2[1]); // Divido l'orario ricercato in un array con l'orario iniziale in posizione 0 e l'orario finale in posizione 1

			$ap1 = $orari2[0]; // orario iniziale
			$ch1 = $orari2[1]; // orario finale
		

			if($ch1<$ap1)
			{
				$ch1+=2400;
			}
			

			$giorniorari1 = explode (":",$arrayorario1[$i]); // Divido la stringa contenente il giorno ricercato in un array con in 0 i giorni e in 1 gli orari
			$setorari = explode (",", $giorniorari1[1]); // Divido gli orari dell'array precedente in un array in cui ciascun elemente contiene un set di orari

			

			for ($a=0; $a < sizeof($setorari); $a++) { //Controllo se l'orario ricercato  contenuto in uno dei set di orari
			
			
			$ore1 = explode ("-", $setorari[$a]); // Divido la stringa con il set di orari per ottenere un array con l'orario di apertura in 0 e l'orario di chiusura in 1

			
			$ap2= $ore1[0]; //orario di apertura
			$ch2= $ore1[1]; //orario di chiusura

if($ch2<$ap2) {
			if ( ($ap1 < $ap2 and $ch1 < $ap2) and ($ap1 > $ch2 and $ch1 > $ch2) ) 
			{
				sendHTTPResult("Content-type: text/plain","Chiuso");
				return FALSE; 
			} // Se l'orario non è contenuto nell'intervallo ridò FALSE e controllo il prossimo set
			else 
			{
				sendHTTPResult("Content-type: text/plain","Aperto");
				return TRUE;  // Se l'orario è contenuto ridò true e esco dal for
			   break; 
			}
			}else{
			if ( ($ap1 < $ap2 and $ch1 < $ap2) or ($ap1 > $ch2 and $ch1 > $ch2) ) 
			{
				sendHTTPResult("Content-type: text/plain","Chiuso");
				return FALSE; 
			} // Se l'orario non è contenuto nell'intervallo ridò FALSE e controllo il prossimo set
			else 
			{
				sendHTTPResult("Content-type: text/plain","Aperto");
				return TRUE;  // Se l'orario è contenuto ridò true e esco dal for
			   break; 
			}
}
		}
	}
}

?>
