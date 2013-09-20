<?php

// genus-species clusters within family

require_once (dirname(__FILE__) . '/adodb5/adodb.inc.php');
require_once (dirname(__FILE__) . '/lib/lcs.php');
require_once (dirname(__FILE__) . '/lib/taxon_name_parser.php');

//--------------------------------------------------------------------------------------------------
function compare ($str1, $str2)
{
	$n1 = strlen($str1);
	$n2 = strlen($str2);
	
	//echo "\n$str1\n$str2\n";
	
	/*
	
	$str = '';
	$l = LongestCommonSubstring($str1, $str2, $str);
	
	echo "$l\n";
	*/
	
	$lc = new LongestCommonSequence	($str1, $str2);
	$l = $lc->diff();
	
	$l = round(100 * (1-$l));
	
	//echo "$l\n";
	
	return $l;
}

//--------------------------------------------------------------------------------------------------
$gbif_db = NewADOConnection('mysql');
$gbif_db->Connect("localhost", 
	'root', '', 'gbif-backbone');

// Ensure fields are (only) indexed by column name
$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;

//--------------------------------------------------------------------------------------------------
function p(&$o)
{
	$taxon_parser = new Parser();
	$parsed = $taxon_parser->parse($o->scientificName);

	if ($parsed->scientificName->parsed)
	{
		$o->canonicalName = $parsed->scientificName->canonical;
		
		foreach ($parsed->scientificName->details as $details)
		{
			if (isset($details->species))
			{
				if (isset($details->species->epitheton))
				{
					$o->specificEpithet = $details->species->epitheton;
				}
				if (isset($details->species->authorship))
				{
					$o->author = $details->species->authorship;
				}
			
			}
			if (isset($details->infraspecies))
			{
				if (isset($details->infraspecies->epitheton))
				{
					$o->infraSpecificEpithet = $details->infraspecies->epitheton;
				}
				if (isset($details->infraspecies->authorship))
				{
					$o->author = $details->infraspecies->authorship;
				}
			
			}
			
			
			//print_r($details);
			/*
			foreach ($details as $k => $v)
			{
				if (isset($v->epitheton))
				{
					$o->specificEpithet = $v->epitheton;
				}
			
				if (isset($v->authorship))
				{
					$o->author = $v->authorship;
				}
			}
			*/
		}
			
	}
}





//--------------------------------------------------------------------------------------------------
// get species/subspecies names for genus
function get_species ($genus, $family = '')
{
	global $gbif_db;
	global $config;
	
	$species = array();

	$sql = 'SELECT * FROM taxon WHERE genus = ' . $gbif_db->qstr($genus) . ' AND specificEpithet <> ""';
	
	if ($family != '')
	{
		$sql .= ' AND family=' . $gbif_db->qstr($family);
	}
	
	$sql .= ' AND taxonomicStatus="accepted" ORDER BY specificEpithet';
	
	$result = $gbif_db->Execute($sql);
	if ($result == false) die("failed [" . __FILE__ . ":" . __LINE__ . "]: " . $sql);
	
	while (!$result->EOF) 
	{
		$concept = new stdclass;
		$concept->_id = 'gbif/' . $result->fields['taxonID'];
		$concept->type = "taxonConcept";
		
		$concept->source = 'http://ecat-dev.gbif.org/checklist/1';
		$concept->nameAccordingTo = utf8_encode($result->fields['nameAccordingTo']);
		$concept->sourceIdentifier = $result->fields['id'];
		$concept->scientificName = utf8_encode($result->fields['scientificName']);
		$concept->taxonRank = $result->fields['taxonRank'];
		
		p($concept);
		
		$species[] = $concept;
		
		$result->MoveNext();	
	}
	
	//print_r($species);
	
	return $species;
}

//--------------------------------------------------------------------------------------------------
function get_genera($family)
{
	global $gbif_db;
	global $config;
	
	$genera = array();

	$sql = 'SELECT DISTINCT canonicalName FROM taxon WHERE family = ' . $gbif_db->qstr($family) . ' AND taxonRank="genus" AND taxonomicStatus="accepted"';
	
	$result = $gbif_db->Execute($sql);
	if ($result == false) die("failed [" . __FILE__ . ":" . __LINE__ . "]: " . $sql);
	
	while (!$result->EOF) 
	{
		$genera[] =  $result->fields['canonicalName'];
		
		$result->MoveNext();	
	}

	return $genera;
}

//--------------------------------------------------------------------------------------------------
function clean_author($author)
{
	// parentheses, commas
	$author = preg_replace('/[\(|\)|,]/', '', $author);
	
	// Initials
	$author = preg_replace('/([A-Z]\.\s*)+/', '', $author);
	
	// Accents
	// translate some characters to character pairs
	$author = preg_replace('/ä/u', 'ae', $author);
	$author = preg_replace('/ö/u', 'oe', $author);
	$author = preg_replace('/ü/u', 'ue', $author);

	$author = preg_replace('/æ/u', 'ae', $author);
	$author = preg_replace('/œ/u', 'oe', $author);

	// Convert accented characters
	$author = strtr(utf8_decode($author), 
			utf8_decode("ÀÁÂÃÄÅàáâãäåĀāĂăĄąÇçĆćĈĉĊċČčÐðĎďĐđÈÉÊËèéêëĒēĔĕĖėĘęĚěĜĝĞğĠġĢģĤĥĦħÌÍÎÏìíîïĨĩĪīĬĭĮįİıĴĵĶķĸĹĺĻļĽľĿŀŁłÑñŃńŅņŇňŉŊŋÒÓÔÕÖØòóôõöøŌōŎŏŐőŔŕŖŗŘřŚśŜŝŞşŠšſŢţŤťŦŧÙÚÛÜùúûüŨũŪūŬŭŮůŰűŲųŴŵÝýÿŶŷŸŹźŻżŽž"),
			"aaaaaaaaaaaaaaaaaaccccccccccddddddeeeeeeeeeeeeeeeeeegggggggghhhhiiiiiiiiiiiiiiiiiijjkkkllllllllllnnnnnnnnnnnoooooooooooooooooorrrrrrsssssssssttttttuuuuuuuuuuuuuuuuuuuuwwyyyyyyzzzzzz");
		 
	$author = utf8_encode($author);
	
	
	$author = trim($author);
	
	return $author;

}

//--------------------------------------------------------------------------------------------------
function stem_name($string)
{
	$done = false;
	
	if (!$done)
	{
		if (preg_match('/us$/', $string))
		{
			$string = preg_replace('/us$/', '', $string);
		}
	}
	if (!$done)
	{
		if (preg_match('/a$/', $string))
		{
			$string = preg_replace('/a$/', '', $string);
		}
	}
	if (!$done)
	{
		if (preg_match('/ii$/', $string))
		{
			$string = preg_replace('/i$/', '', $string);
		}
	}
	
	return $string;
}



$family = 'Molossidae Gervais, 1856';

//$family = 'Hylobatidae Gray, 1871';

//$family = 'Polychrotidae'; // Anolis and relatives, messy

//$family = 'Diomedeidae G. R. Gray, 1840'; // ok
//$family = 'Procellariidae Leach, 1820'; // some problems

// fish?

//$family = 'Poeciliidae Garman, 1895'; // some problems
$family = 'Cercopithecidae Gray, 1821';

//$family = 'Salticidae Blackwall, 1841'; // Rainbow 1920 same epithet different genera in same paper (!)

//$family = 'Alpheidae Rafinesque, 1815';

$family = 'Molossidae Gervais, 1856';

$family = 'Hylobatidae Gray, 1871';
$family = 'Cercopithecidae Gray, 1821';
$family = 'Delphinidae Gray, 1821';
$family = 'Muridae Illiger, 1815';

//$family = 'Rhinolophidae Gray, 1825';
//$family = 'Natalidae Gray, 1866';
//$family = 'Vespertilionidae Gray, 1821';

$family = 'Molossidae Gervais, 1856';

$family = 'Polychrotidae';

$family = 'Pyxicephalidae Bonaparte, 1850';
$family = 'Ceratobatrachidae Boulenger, 1884'; // false positive guppyi Boulenger 1884
$family = 'Mantellidae Laurent, 1946';  // false positive Mantella
$family = 'Rhacophoridae Hoffman, 1932';

$family = 'Canacidae';

$family = 'Asteiidae';  // some examples of names with same epithet

$family = 'Drosophilidae';


$family = 'Molossidae Gervais, 1856';

$family = 'Diopsidae';

$family = 'Attelabidae Billberg, 1820';

$family = 'Evaniidae';

//$family = 'Diapriidae';

//$family = 'Hylobatidae Gray, 1871';

$family='Callitrichidae Gray, 1821';

$family = 'Polychrotidae';
$family = 'Gekkonidae Gray, 1825'; // interesting
$family = 'Colubridae Oppel, 1811';

$family = 'Leptotyphlopidae Stejneger, 1892';

$family = 'Drosophilidae';

$family = 'Rhacophoridae Hoffman, 1932';

$family = 'Molossidae Gervais, 1856';




$genera = get_genera($family);
//print_r($genera);

$add_authority = true; // add taxon authority to reduce ambiguity
$stem = false; // trim potentially variable endings

$count = 0;
foreach ($genera as $genus)
{
	$species = get_species($genus, $family);
	
	//print_r($species);
	
	$combinations = array();
	
	foreach ($species as $s)
	{
		/*
		if ($count > 0)
		{
			echo "\n";
		}
		$count++;
		*/
		
		
//		echo $genus . "\t" . $s->specificEpithet;
//		echo $genus . "\t";
		
		$name = $s->specificEpithet;
		
		// do we have a subspecies?
		if (isset($s->infraSpecificEpithet))
		{
			// emit species + subspecies
			$string = $name;
			
			if ($stem)
			{
				$string = stem_name($string);
			}
			
			$string .= ' ' . $s->infraSpecificEpithet;
			
			if ($stem)
			{
				$string = stem_name($string);
			}			
			
			if (isset($s->author) && $add_authority)
			{
				$author = $s->author;
				$author = clean_author($author);
				$string .= ' ' . $author;
			}
			$combinations[] = $string;
			
			// emit just subspecies
			$string = $s->infraSpecificEpithet;
			
			if ($stem)
			{
				$string = stem_name($string);
			}			
			
			if (isset($s->author) && $add_authority)
			{
				$author = $s->author;
				$author = clean_author($author);
				$string .= ' ' . $author;
			}
			
			$combinations[] = $string;
		}
		else
		{
			// just species
			$string = $name;		
			
			if ($stem)
			{
				$string = stem_name($string);
			}
		
			if (isset($s->author) && $add_authority)
			{
				$author = $s->author;
				$author = clean_author($author);
				$string .= ' ' . $author;
			}
			$combinations[] = $string;
		}
		
		//print_r($s);

	}
	//echo "Combinations $genus\n";
	$combinations = array_unique($combinations);
	
	if (count($combinations) > 0)
	{
	
		foreach ($combinations as $s)
		{
			if ($count > 0)
			{
				echo "\n";
			}
			$count++;
		
			echo "$genus\t" . $s;
		}
	}
	//print_r($combinations);
}




?>