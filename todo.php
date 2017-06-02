<?php 

$tabMail = array(); 
$csvFile = fopen("data/CsvPublipostage.csv", "r"); 

do {
	$tabData = fgetcsv($csvFile, 1024, ';'); 
	print_r($tabData); 
	echo "\n";
if (intval($tabData[3]) > 0) {
	$tabMail[$tabData[3]] = $tabData[15]; 
}
} while ($tabData); 
fclose($csvFile); 

print_r($tabMail); 

$dir = "data/pdf";

// Ouvre un dossier bien connu, et liste tous les fichiers
if (is_dir($dir)) {
    if ($dh = opendir($dir)) {
        while (($file = readdir($dh)) !== false) {
if (strpos($file, "pdf") !== false) {
            echo "fichier : $file \n";
$tabFileName = explode(" - ", $file); 
	if (array_key_exists($tabFileName[0], $tabMail)) {
		echo "mail : ".$tabMail[$tabFileName[0]]."\n";
	}
}
        }
        closedir($dh);
    }
}

