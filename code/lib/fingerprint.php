<?php

function finger_print ($str)
{
	// clean any weirdness
	$str = str_replace('&#039;', "'", $str);

	// translate some characters to character pairs
	$str = preg_replace('/ä/u', 'ae', $str);
	$str = preg_replace('/ö/u', 'oe', $str);
	$str = preg_replace('/ü/u', 'ue', $str);

	$str = preg_replace('/æ/u', 'ae', $str);
	$str = preg_replace('/œ/u', 'oe', $str);
	
	// Convert author abbreviations
	$str = str_replace('Latr.', 'Latreille', $str);
	$str = str_replace('L.', 'Linnaeus', $str);

	// Convert accented characters
	$str = strtr(utf8_decode($str), 
			utf8_decode("ÀÁÂÃÄÅàáâãäåĀāĂăĄąÇçĆćĈĉĊċČčÐðĎďĐđÈÉÊËèéêëĒēĔĕĖėĘęĚěĜĝĞğĠġĢģĤĥĦħÌÍÎÏìíîïĨĩĪīĬĭĮįİıĴĵĶķĸĹĺĻļĽľĿŀŁłÑñŃńŅņŇňŉŊŋÒÓÔÕÖØòóôõöøŌōŎŏŐőŔŕŖŗŘřŚśŜŝŞşŠšſŢţŤťŦŧÙÚÛÜùúûüŨũŪūŬŭŮůŰűŲųŴŵÝýÿŶŷŸŹźŻżŽž"),
			"aaaaaaaaaaaaaaaaaaccccccccccddddddeeeeeeeeeeeeeeeeeegggggggghhhhiiiiiiiiiiiiiiiiiijjkkkllllllllllnnnnnnnnnnnoooooooooooooooooorrrrrrsssssssssttttttuuuuuuuuuuuuuuuuuuuuwwyyyyyyzzzzzz");
		 
	$str = utf8_encode($str);
	
	// lowercase
	$str = strtolower($str);
	
	// normalise space
	$str = preg_replace('/\s\s+/', ' ', $str);
	
	// strip punctuation
	$str = preg_replace('/[,|\.|\(|\)|-]/', '', $str);

	// strip and|&
	$str = preg_replace('/ and /', ' ', $str);
	$str = preg_replace('/ & /', ' ', $str);

	return $str;
}

?>