<?php // Gabriele Cigna - Gianmarco Ascenzo
require_once('../funzioni.php');

//Header della risposta
$headers = ('Content-Type: text/csv');

//Prendo i valori dall'url
$URL_key = strtolower($_GET["key"]);
$URL_comp = strtolower($_GET["comp"]);
$URL_value = strtolower($_GET["value"]);

//Elimino eventuali spazi vuoti dai valori
$URL_key = trim($URL_key);
$URL_comp = trim($URL_comp);
$URL_value = trim($URL_value);

//Database CSV
$file  = fopen('scuolematerneBO2011.csv', 'r');

//Gestione degli Errori
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
	$body = "";
}
else
{
   sendHTTPError($Errore[406], "Parametro non settato");
}

$contatore=0;

//Stringa con le chiavi del database
$body = '"Id","Category","Name","Address","Lat","Long","subcategory","note","Opening","Closing","Creator","Created","Valid","Version","Source"'."\n";

//Se i parametri sono tutti assenti
//restituisco il database intero
if (!$URL_key and !$URL_comp and !$URL_value)
{	
	$body = "";
	while (($line = fgetcsv($file)) !== FALSE) 
	{  
   	list($Id,$Category,$Name,$Address,$Lat,$Long,$subcategory,$note,$Opening,$Closing,$Creator,$Created,$Valid,$Version,$Source) = $line;

      $body .= "\"$Id\",\"$Category\",\"$Name\",\"$Address\",\"$Lat\",\"$Long\",\"$subcategory\",\"$note\",\"$Opening\",\"$Closing\",\"$Creator\",\"$Created\",\"$Valid\",\"$Version\",\"$Source\"\n";
	}
}//Se esistono i parametri procedo con le comparazioni possibili
else if ($URL_comp == 'ncontains')
{   
	switch ($URL_key)
	{
		case 'name':	
			{
				while (($line = fgetcsv($file)) !== FALSE) 
				{  
   				list($Id,$Category,$Name,$Address,$Lat,$Long,$subcategory,$note,$Opening,$Closing,$Creator,$Created,$Valid,$Version,$Source) = $line;
					if(!stristr($Name,$URL_value))
      			{	if($contatore!=0){
      				$body .= "\"$Id\",\"$Category\",\"$Name\",\"$Address\",\"$Lat\",\"$Long\",\"$subcategory\",\"$note\",\"$Opening\",\"$Closing\",\"$Creator\",\"$Created\",\"$Valid\",\"$Version\",\"$Source\" \n";
      			} }$contatore++;
				}
			}; break;		
		case 'category':
			{
				while (($line = fgetcsv($file)) !== FALSE) 
				{  
   				list($Id,$Category,$Name,$Address,$Lat,$Long,$subcategory,$note,$Opening,$Closing,$Creator,$Created,$Valid,$Version,$Source) = $line;
					if(!stristr($Category,$URL_value))
      			{	if($contatore!=0){
      				$body .= "\"$Id\",\"$Category\",\"$Name\",\"$Address\",\"$Lat\",\"$Long\",\"$subcategory\",\"$note\",\"$Opening\",\"$Closing\",\"$Creator\",\"$Created\",\"$Valid\",\"$Version\",\"$Source\" \n";
      			} }$contatore++;
				}
			}; break;
		case 'opening':
			{
				while (($line = fgetcsv($file)) !== FALSE) 
				{  
   				list($Id,$Category,$Name,$Address,$Lat,$Long,$subcategory,$note,$Opening,$Closing,$Creator,$Created,$Valid,$Version,$Source) = $line;
					if(!stristr($Opening,$URL_value))
      			{	if($contatore!=0){
      				$body .= "\"$Id\",\"$Category\",\"$Name\",\"$Address\",\"$Lat\",\"$Long\",\"$subcategory\",\"$note\",\"$Opening\",\"$Closing\",\"$Creator\",\"$Created\",\"$Valid\",\"$Version\",\"$Source\" \n";
      			} }$contatore++;
				}
			}; break;		
		case 'closing':	
			{
				while (($line = fgetcsv($file)) !== FALSE) 
				{  
   				list($Id,$Category,$Name,$Address,$Lat,$Long,$subcategory,$note,$Opening,$Closing,$Creator,$Created,$Valid,$Version,$Source) = $line;
					if(!stristr($Closing,$URL_value))
      			{	if($contatore!=0){
      				$body .= "\"$Id\",\"$Category\",\"$Name\",\"$Address\",\"$Lat\",\"$Long\",\"$subcategory\",\"$note\",\"$Opening\",\"$Closing\",\"$Creator\",\"$Created\",\"$Valid\",\"$Version\",\"$Source\" \n";
      			} }$contatore++;
				}
			}; break;	
   }	
}
else if ($URL_comp == 'contains')
{	  
	switch ($URL_key)
	{
		case 'id':
			{
				while (($line = fgetcsv($file)) !== FALSE) 
				{  
   				list($Id,$Category,$Name,$Address,$Lat,$Long,$subcategory,$note,$Opening,$Closing,$Creator,$Created,$Valid,$Version,$Source) = $line;
					if(stristr($Id,$URL_value))
      			{	if($contatore!=0){
      				$body .= "\"$Id\",\"$Category\",\"$Name\",\"$Address\",\"$Lat\",\"$Long\",\"$subcategory\",\"$note\",\"$Opening\",\"$Closing\",\"$Creator\",\"$Created\",\"$Valid\",\"$Version\",\"$Source\" \n";
      			} }$contatore++;
				}
			}; break;
		case 'name':	
			{
				while (($line = fgetcsv($file)) !== FALSE) 
				{  
   				list($Id,$Category,$Name,$Address,$Lat,$Long,$subcategory,$note,$Opening,$Closing,$Creator,$Created,$Valid,$Version,$Source) = $line;			
					if(stristr($Name,$URL_value))
      			{	if($contatore!=0){
      				$body .= "\"$Id\",\"$Category\",\"$Name\",\"$Address\",\"$Lat\",\"$Long\",\"$subcategory\",\"$note\",\"$Opening\",\"$Closing\",\"$Creator\",\"$Created\",\"$Valid\",\"$Version\",\"$Source\" \n";
      			} }$contatore++;
				}
			}; break;
		case 'address':
			{
				while (($line = fgetcsv($file)) !== FALSE) 
				{  
   				list($Id,$Category,$Name,$Address,$Lat,$Long,$subcategory,$note,$Opening,$Closing,$Creator,$Created,$Valid,$Version,$Source) = $line;
					if(stristr($Address,$URL_value))
      			{	if($contatore!=0){
      				$body .= "\"$Id\",\"$Category\",\"$Name\",\"$Address\",\"$Lat\",\"$Long\",\"$subcategory\",\"$note\",\"$Opening\",\"$Closing\",\"$Creator\",\"$Created\",\"$Valid\",\"$Version\",\"$Source\" \n";
      			} }$contatore++;
				}
			}; break;		
		case 'category':
			{
				while (($line = fgetcsv($file)) !== FALSE) 
				{  
   				list($Id,$Category,$Name,$Address,$Lat,$Long,$subcategory,$note,$Opening,$Closing,$Creator,$Created,$Valid,$Version,$Source) = $line;
					if(stristr($Category,$URL_value))
      			{	if($contatore!=0){
      				$body .= "\"$Id\",\"$Category\",\"$Name\",\"$Address\",\"$Lat\",\"$Long\",\"$subcategory\",\"$note\",\"$Opening\",\"$Closing\",\"$Creator\",\"$Created\",\"$Valid\",\"$Version\",\"$Source\" \n";
      			} }$contatore++;
				}
			}; break;
		case 'opening':
			{
				while (($line = fgetcsv($file)) !== FALSE) 
				{  
   				list($Id,$Category,$Name,$Address,$Lat,$Long,$subcategory,$note,$Opening,$Closing,$Creator,$Created,$Valid,$Version,$Source) = $line;
					if(stristr($Opening,$URL_value))
      			{	if($contatore!=0){
      				$body .= "\"$Id\",\"$Category\",\"$Name\",\"$Address\",\"$Lat\",\"$Long\",\"$subcategory\",\"$note\",\"$Opening\",\"$Closing\",\"$Creator\",\"$Created\",\"$Valid\",\"$Version\",\"$Source\" \n";
      			} }$contatore++;
				}
			}; break;		
		case 'closing':	
			{
				while (($line = fgetcsv($file)) !== FALSE) 
				{  
   				list($Id,$Category,$Name,$Address,$Lat,$Long,$subcategory,$note,$Opening,$Closing,$Creator,$Created,$Valid,$Version,$Source) = $line;
					if(stristr($Closing,$URL_value))
      			{	if($contatore!=0){
      				$body .= "\"$Id\",\"$Category\",\"$Name\",\"$Address\",\"$Lat\",\"$Long\",\"$subcategory\",\"$note\",\"$Opening\",\"$Closing\",\"$Creator\",\"$Created\",\"$Valid\",\"$Version\",\"$Source\" \n";
      			} }$contatore++;
				}
			}; break;	
   }
}
else
{
	switch ($URL_key)
	{
		case 'id':
		{
		   switch ($URL_comp)
		   { 
		      case 'eq' : 
					{
						while (($line = fgetcsv($file)) !== FALSE) 
						{  
   						list($Id,$Category,$Name,$Address,$Lat,$Long,$subcategory,$note,$Opening,$Closing,$Creator,$Created,$Valid,$Version,$Source) = $line;
							if(strcasecmp($URL_value, $Id) == 0)
      					{	if($contatore!=0){
      				$body .= "\"$Id\",\"$Category\",\"$Name\",\"$Address\",\"$Lat\",\"$Long\",\"$subcategory\",\"$note\",\"$Opening\",\"$Closing\",\"$Creator\",\"$Created\",\"$Valid\",\"$Version\",\"$Source\" \n";
      			} }$contatore++;
						}
					}; break;
				 case 'ne' : 
					{
						while (($line = fgetcsv($file)) !== FALSE) 
						{  
   						list($Id,$Category,$Name,$Address,$Lat,$Long,$subcategory,$note,$Opening,$Closing,$Creator,$Created,$Valid,$Version,$Source) = $line;
							if(strcasecmp($URL_value, $Id) != 0)
      					{	if($contatore!=0){
      				$body .= "\"$Id\",\"$Category\",\"$Name\",\"$Address\",\"$Lat\",\"$Long\",\"$subcategory\",\"$note\",\"$Opening\",\"$Closing\",\"$Creator\",\"$Created\",\"$Valid\",\"$Version\",\"$Source\" \n";
      			} }$contatore++;
						}
					}; break;	
		   }
		}; break;
		case 'name':
		{
		   switch ($URL_comp)
		   { 
		      case 'eq' : 
					{
						while (($line = fgetcsv($file)) !== FALSE) 
						{  
   						list($Id,$Category,$Name,$Address,$Lat,$Long,$subcategory,$note,$Opening,$Closing,$Creator,$Created,$Valid,$Version,$Source) = $line;
							if(strcasecmp($URL_value, $Name) == 0)
      					{	if($contatore!=0){
      				$body .= "\"$Id\",\"$Category\",\"$Name\",\"$Address\",\"$Lat\",\"$Long\",\"$subcategory\",\"$note\",\"$Opening\",\"$Closing\",\"$Creator\",\"$Created\",\"$Valid\",\"$Version\",\"$Source\" \n";
      			} }$contatore++;
						}
					}; break;
				 case 'ne' : 
					{
						while (($line = fgetcsv($file)) !== FALSE) 
						{  
   						list($Id,$Category,$Name,$Address,$Lat,$Long,$subcategory,$note,$Opening,$Closing,$Creator,$Created,$Valid,$Version,$Source) = $line;
							if(strcasecmp($URL_value, $Name) != 0)
      					{	if($contatore!=0){
      				$body .= "\"$Id\",\"$Category\",\"$Name\",\"$Address\",\"$Lat\",\"$Long\",\"$subcategory\",\"$note\",\"$Opening\",\"$Closing\",\"$Creator\",\"$Created\",\"$Valid\",\"$Version\",\"$Source\" \n";
      			} }$contatore++;
						}
					}; break;	
		   }
		}; break;
		case 'category':
		{
		   switch ($URL_comp)
		   { 
		      case 'eq' : 
					{
						while (($line = fgetcsv($file)) !== FALSE) 
						{  
   						list($Id,$Category,$Name,$Address,$Lat,$Long,$subcategory,$note,$Opening,$Closing,$Creator,$Created,$Valid,$Version,$Source) = $line;
							if(strcasecmp($URL_value, $Category) == 0)
      					{	if($contatore!=0){
      				$body .= "\"$Id\",\"$Category\",\"$Name\",\"$Address\",\"$Lat\",\"$Long\",\"$subcategory\",\"$note\",\"$Opening\",\"$Closing\",\"$Creator\",\"$Created\",\"$Valid\",\"$Version\",\"$Source\" \n";
      			} }$contatore++;
						}
					}; break;
				 case 'ne' : 
					{
						while (($line = fgetcsv($file)) !== FALSE) 
						{  
   						list($Id,$Category,$Name,$Address,$Lat,$Long,$subcategory,$note,$Opening,$Closing,$Creator,$Created,$Valid,$Version,$Source) = $line;
							if(strcasecmp($URL_value, $Category) != 0)
      					{	if($contatore!=0){
      				$body .= "\"$Id\",\"$Category\",\"$Name\",\"$Address\",\"$Lat\",\"$Long\",\"$subcategory\",\"$note\",\"$Opening\",\"$Closing\",\"$Creator\",\"$Created\",\"$Valid\",\"$Version\",\"$Source\" \n";
      			} }$contatore++;
						}
					}; break;	
		   }
		}; break;				
		case 'lat':
		{
		   switch ($URL_comp)
		   { 
		      case 'eq' : 
					{
						while (($line = fgetcsv($file)) !== FALSE) 
						{  
   						list($Id,$Category,$Name,$Address,$Lat,$Long,$subcategory,$note,$Opening,$Closing,$Creator,$Created,$Valid,$Version,$Source) = $line;
							if($URL_value == $Lat)
      					{	if($contatore!=0){
      				$body .= "\"$Id\",\"$Category\",\"$Name\",\"$Address\",\"$Lat\",\"$Long\",\"$subcategory\",\"$note\",\"$Opening\",\"$Closing\",\"$Creator\",\"$Created\",\"$Valid\",\"$Version\",\"$Source\" \n";
      			} }$contatore++;
						}
					}; break;
				 case 'ne' : 
					{
						while (($line = fgetcsv($file)) !== FALSE) 
						{  
   						list($Id,$Category,$Name,$Address,$Lat,$Long,$subcategory,$note,$Opening,$Closing,$Creator,$Created,$Valid,$Version,$Source) = $line;
							if($URL_value != $Lat)
      					{	if($contatore!=0){
      				$body .= "\"$Id\",\"$Category\",\"$Name\",\"$Address\",\"$Lat\",\"$Long\",\"$subcategory\",\"$note\",\"$Opening\",\"$Closing\",\"$Creator\",\"$Created\",\"$Valid\",\"$Version\",\"$Source\" \n";
      			} }$contatore++;
						}
					}; break;	
				 case 'lt' : 
					{
						while (($line = fgetcsv($file)) !== FALSE) 
						{  
   						list($Id,$Category,$Name,$Address,$Lat,$Long,$subcategory,$note,$Opening,$Closing,$Creator,$Created,$Valid,$Version,$Source) = $line;
							if($URL_value < $Lat)
      					{	if($contatore!=0){
      				$body .= "\"$Id\",\"$Category\",\"$Name\",\"$Address\",\"$Lat\",\"$Long\",\"$subcategory\",\"$note\",\"$Opening\",\"$Closing\",\"$Creator\",\"$Created\",\"$Valid\",\"$Version\",\"$Source\" \n";
      			} }$contatore++;
						}
					}; break;	
				 case 'le' : 
					{
						while (($line = fgetcsv($file)) !== FALSE) 
						{  
   						list($Id,$Category,$Name,$Address,$Lat,$Long,$subcategory,$note,$Opening,$Closing,$Creator,$Created,$Valid,$Version,$Source) = $line;
							if($URL_value <= $Lat)
      					{	if($contatore!=0){
      				$body .= "\"$Id\",\"$Category\",\"$Name\",\"$Address\",\"$Lat\",\"$Long\",\"$subcategory\",\"$note\",\"$Opening\",\"$Closing\",\"$Creator\",\"$Created\",\"$Valid\",\"$Version\",\"$Source\" \n";
      			} }$contatore++;
						}
					}; break;
				 case 'gt' : 
					{
						while (($line = fgetcsv($file)) !== FALSE) 
						{  
   						list($Id,$Category,$Name,$Address,$Lat,$Long,$subcategory,$note,$Opening,$Closing,$Creator,$Created,$Valid,$Version,$Source) = $line;
							if($URL_value >= $Lat)
      					{	if($contatore!=0){
      				$body .= "\"$Id\",\"$Category\",\"$Name\",\"$Address\",\"$Lat\",\"$Long\",\"$subcategory\",\"$note\",\"$Opening\",\"$Closing\",\"$Creator\",\"$Created\",\"$Valid\",\"$Version\",\"$Source\" \n";
      			} }$contatore++;
						}
					}; break;	
				 case 'ge' : 
					{
						while (($line = fgetcsv($file)) !== FALSE) 
						{  
   						list($Id,$Category,$Name,$Address,$Lat,$Long,$subcategory,$note,$Opening,$Closing,$Creator,$Created,$Valid,$Version,$Source) = $line;
							if($URL_value > $Lat)
      					{	if($contatore!=0){
      				$body .= "\"$Id\",\"$Category\",\"$Name\",\"$Address\",\"$Lat\",\"$Long\",\"$subcategory\",\"$note\",\"$Opening\",\"$Closing\",\"$Creator\",\"$Created\",\"$Valid\",\"$Version\",\"$Source\" \n";
      			} }$contatore++;
						}
					}; break;														
		   }
		}; break;		
		case 'long':
		{
		   switch ($URL_comp)
		   { 
		      case 'eq' : 
					{
						while (($line = fgetcsv($file)) !== FALSE) 
						{  
   						list($Id,$Category,$Name,$Address,$Lat,$Long,$subcategory,$note,$Opening,$Closing,$Creator,$Created,$Valid,$Version,$Source) = $line;
							if($URL_value == $Long)
      					{	if($contatore!=0){
      				$body .= "\"$Id\",\"$Category\",\"$Name\",\"$Address\",\"$Lat\",\"$Long\",\"$subcategory\",\"$note\",\"$Opening\",\"$Closing\",\"$Creator\",\"$Created\",\"$Valid\",\"$Version\",\"$Source\" \n";
      			} }$contatore++;
						}
					}; break;
				 case 'ne' : 
					{
						while (($line = fgetcsv($file)) !== FALSE) 
						{  
   						list($Id,$Category,$Name,$Address,$Lat,$Long,$subcategory,$note,$Opening,$Closing,$Creator,$Created,$Valid,$Version,$Source) = $line;
							if($URL_value != $Long)
      					{	if($contatore!=0){
      				$body .= "\"$Id\",\"$Category\",\"$Name\",\"$Address\",\"$Lat\",\"$Long\",\"$subcategory\",\"$note\",\"$Opening\",\"$Closing\",\"$Creator\",\"$Created\",\"$Valid\",\"$Version\",\"$Source\" \n";
      			} }$contatore++;
						}
					}; break;	
				 case 'lt' : 
					{
						while (($line = fgetcsv($file)) !== FALSE) 
						{  
   						list($Id,$Category,$Name,$Address,$Lat,$Long,$subcategory,$note,$Opening,$Closing,$Creator,$Created,$Valid,$Version,$Source) = $line;
							if($URL_value < $Long)
      					{	if($contatore!=0){
      				$body .= "\"$Id\",\"$Category\",\"$Name\",\"$Address\",\"$Lat\",\"$Long\",\"$subcategory\",\"$note\",\"$Opening\",\"$Closing\",\"$Creator\",\"$Created\",\"$Valid\",\"$Version\",\"$Source\" \n";
      			} }$contatore++;
						}
					}; break;	
				 case 'le' : 
					{
						while (($line = fgetcsv($file)) !== FALSE) 
						{  
   						list($Id,$Category,$Name,$Address,$Lat,$Long,$subcategory,$note,$Opening,$Closing,$Creator,$Created,$Valid,$Version,$Source) = $line;
							if($URL_value <= $Long)
      					{	if($contatore!=0){
      				$body .= "\"$Id\",\"$Category\",\"$Name\",\"$Address\",\"$Lat\",\"$Long\",\"$subcategory\",\"$note\",\"$Opening\",\"$Closing\",\"$Creator\",\"$Created\",\"$Valid\",\"$Version\",\"$Source\" \n";
      			} }$contatore++;
						}
					}; break;
				 case 'gt' : 
					{
						while (($line = fgetcsv($file)) !== FALSE) 
						{  
   						list($Id,$Category,$Name,$Address,$Lat,$Long,$subcategory,$note,$Opening,$Closing,$Creator,$Created,$Valid,$Version,$Source) = $line;
							if($URL_value >= $Long)
      					{	if($contatore!=0){
      				$body .= "\"$Id\",\"$Category\",\"$Name\",\"$Address\",\"$Lat\",\"$Long\",\"$subcategory\",\"$note\",\"$Opening\",\"$Closing\",\"$Creator\",\"$Created\",\"$Valid\",\"$Version\",\"$Source\" \n";
      			} }$contatore++;
						}
					}; break;	
				 case 'ge' : 
					{
						while (($line = fgetcsv($file)) !== FALSE) 
						{  
   						list($Id,$Category,$Name,$Address,$Lat,$Long,$subcategory,$note,$Opening,$Closing,$Creator,$Created,$Valid,$Version,$Source) = $line;
							if($URL_value > $Long)
      					{	if($contatore!=0){
      				$body .= "\"$Id\",\"$Category\",\"$Name\",\"$Address\",\"$Lat\",\"$Long\",\"$subcategory\",\"$note\",\"$Opening\",\"$Closing\",\"$Creator\",\"$Created\",\"$Valid\",\"$Version\",\"$Source\" \n";
      			} }$contatore++;
						}
					}; break;														
		   }
		}; break;		
	}
}


//Mando il risultato
sendHTTPResult($headers, $body);

?>



