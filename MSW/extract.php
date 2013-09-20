<?php

// extract mammals from red list

$filename = 'msw3-all.txt';

$file_handle = fopen($filename, "r");

$clusters = array();

while (!feof($file_handle)) 
{
	$line = trim(fgets($file_handle));
	
	$parts = explode("\t", $line);
	
	//print_r($parts);
	
//	if (($parts[12] == 'SPECIES') || ($parts[12] == 'SUBSPECIES'))
	if ($parts[12] == 'SPECIES')
	{
		$name = $parts[8] . ' ' . $parts[10] . ' '. $parts[11];
		$name = trim($name);
		echo "Mammal Species of the World\t" . $name . "\n";
	}
}

?>
