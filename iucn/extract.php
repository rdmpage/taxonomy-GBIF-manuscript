<?php

// extract mammals from red list

$filename = 'taxon.txt';

$file_handle = fopen($filename, "r");

$clusters = array();

while (!feof($file_handle)) 
{
	$line = trim(fgets($file_handle));
	
	$parts = explode("\t", $line);
	
	if ($parts[1] == 'MAMMALIA')
	{
		$name = $parts[9];
		$name = str_replace('ssp. ', '', $name);
		
		if (preg_match('/^\w+ \w+$/', $name))
		{
			echo "IUCN Red List\t" . $name . "\n";
		}
	}
}

?>
