<?php //Gabriele Cigna
//Script per la comunicazione con domini esterni (Cross-domain)

$URL = $_GET['url'];
$URL = str_replace(" ", "",  $URL );
$curl = curl_init($URL);
curl_setopt($curl,CURLOPT_HTTPHEADER, array("Accept: application/xml"));
curl_setopt($curl,CURLOPT_RETURNTRANSFER, true);
$content = curl_exec($curl);
$headers = curl_getinfo($curl, CURLINFO_CONTENT_TYPE);

header("Content-type: $headers; charset=UTF-8", true);

echo $content;

?>
