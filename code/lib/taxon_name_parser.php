<?php

// PHP port of GBIF Java code for parsing taxonomic names, see
// http://code.google.com/p/gbif-ecat/source/browse/trunk/ecat-common/src/main/java/org/gbif/ecat/parser/NameParser.java

// Incomplete!



//--------------------------------------------------------------------------------------------------
// see http://code.google.com/p/gbif-ecat/source/browse/trunk/ecat-common/src/main/java/org/gbif/ecat/voc/NameType.java
// // http://stackoverflow.com/questions/254514/php-and-enums
class NameType
{
	const unknown 		= 0; // rdmp
	const sciname 		= 1; // a scientific name which is not well formed
	const wellformed 	= 2; // a well formed scientific name according to present nomenclatural rules. This is either the canonical or
							 // canonical with authorship
	const doubtful 		= 3; // doubtful whether this is a scientific name at all
	const blacklisted 	= 4; // surely not a scientific name
	const virus 		= 5; // a virus name
	const hybrid 		= 6; // a hybrid *formula* (not a hybrid name)
	const informal 		= 7; // a scientific name with some informal addition like "cf." or indetermined like Abies spec.
	const cultivar 		= 8; // a cultivated plant name
}	


//--------------------------------------------------------------------------------------------------
// preg_replace_callback callback
function capitalise($matches)
{
	return $matches[1] . mb_convert_case($matches[2], MB_CASE_LOWER, 'UTF-8');
}

//--------------------------------------------------------------------------------------------------
class Parser 
{
	protected $NAME_LETTERS = "A-ZÏËÖÜÄÉÈČÁÀÆŒ";
	protected $name_letters = "a-zïëöüäåéèčáàæœ";
	protected $EPHITHET_PREFIXES = "van|novae";
	
	protected $MONOMIAL = "";
	protected $INFRAGENERIC = "";
	protected $EPHITHET = "";
	
	protected $AUTHOR_LETTERS = "";
	protected $author_letters = "";
	protected $all_letters_numbers = "";
	protected $AUTHOR_PREFIXES = "";
	protected $AUTHOR = "";
	protected $AUTHOR_TEAM = "";
	protected $YEAR = "[12][0-9][0-9][0-9?][abcdh?]?(?:[\-][0-9]{1,4})?";
	
	protected $COMMA_BEFORE_YEAR = "(,+|[^0-9\\(\\[\"])\\s*(\\d{3})";
	
	protected $NORM_QUOTES = "([\"'`´]+)";
	protected $NORM_UPPERCASE_WORDS = "\\b(\\p{Lu})(\\p{Lu}{2,})\\b";
	protected $NORM_WHITESPACE = "\\s+";
	protected $NORM_NO_SQUARE_BRACKETS = "\\[(.*?)\\]";
	protected $NORM_BRACKETS_OPEN = "([{(\\[])\\s*,?";
	protected $NORM_BRACKETS_CLOSE = ",?\\s*([})\\]])";
	protected $NORM_BRACKETS_OPEN_STRONG = "( ?[{(\\[] ?)+";
	protected $NORM_BRACKETS_CLOSE_STRONG = "( ?[})\\]] ?)+";
	protected $NORM_AND = " (and|et|und|&amp;) ";
	protected $NORM_ET_AL = "& al\\.?";
	protected $NORM_AMPERSAND_WS = "&";
  	protected $NORM_HYPHENS = "\\s*-\\s*";
  	protected $NORM_COMMAS = "\\s*,+";	
  	
  	protected $NORM_HYBRIDS_FORM = " [×xX] ";
  	protected $NORM_HYBRIDS_GENUS = "";
  	protected $NORM_HYBRIDS_EPITH = "";
  	
 	protected $NORM_INDET = "((^| )(undet|indet|aff|cf)[#!?\\.]?)+(?![a-z])";
  	
    	
	protected $RANK_MARKER_MAP_INFRAGENERIC = array();
	protected $RANK_MARKER_SPECIES = array();
		
	//----------------------------------------------------------------------------------------------
	function __construct()
	{
		$this->MONOMIAL = "[" . $this->NAME_LETTERS 
			. "](?:\\.|[" . $this->name_letters 
			. "]+)(?:-[" . $this->NAME_LETTERS
			. "]?[" . $this->name_letters . "]+)?";  
			
			
		$this->RANK_MARKER_MAP_INFRAGENERIC = array("subgenus","subgen","subg","section","sect",
			"subsection","subsect","series","ser","subseries","subser","agg","species","spec", "spp","sp");
			
		$this->RANK_MARKER_SPECIES = "(?:notho)?(?:" . join(array("subsp","ssp","var","v","subvar","subv","sv",
			"forma","form","fo","f","subform","subf","sf","cv","hort","m","morph","nat","ab","aberration","\\*+"), "|")
			. "|agg)\\.?";
			
 		$this->INFRAGENERIC = "(?:" . "\\( ?([" . $this->NAME_LETTERS . "][" . $this->name_letters . "-]+) ?\\)"
      		. "|" . "(" . join($this->RANK_MARKER_MAP_INFRAGENERIC, "|") . ")\\.? ?([" . $this->NAME_LETTERS
      		. "][" . $this->name_letters . "-]+)" . ")";
			
		$this->EPHITHET = "(?:[0-9]+-)?"
				   . "(?:(?:" . $this->EPHITHET_PREFIXES . ") [a-z])?"
				   . "[" . $this->name_letters . "+-]{1,}(?<! d)[" . $this->name_letters . "](?<!\\bex)";
				   
		$this->NORM_HYBRIDS_GENUS = "^\\s*[+×xX]\\s*([" . $this->NAME_LETTERS . "])";
  		$this->NORM_HYBRIDS_EPITH = "^\\s*(×?" . $this->MONOMIAL . ")\\s+(?:×|[xX]\\s)\\s*("
      		. $this->EPHITHET . ")";
		
		$this->AUTHOR_LETTERS = $this->NAME_LETTERS . "\\p{Lu}"; // upper case unicode letter, not numerical
		// (\W is alphanum)
  		$this->author_letters = $this->name_letters . "\\p{Ll}"; // lower case unicode letter, not numerical
		// (\W is alphanum)
  		$this->all_letters_numbers = $this->name_letters . $this->NAME_LETTERS . "0-9";
  		$this->AUTHOR_PREFIXES = "(?:[vV](?:an)(?:[ -](?:den|der) )? ?|von[ -](?:den |der |dem )?|(?:del|Des|De|de|di|Di|da|N)[`' _]|le |d'|D'|de la|Mac|Mc|Le|St\\.? ?|Ou|O')";
 		$this->AUTHOR = "(?:" .
		  // author initials
			  "(?:" . "(?:[" . $this->AUTHOR_LETTERS . "]{1,3}\\.?[ -]?){0,3}" .
		  // or full first name
			  "|[" . $this->AUTHOR_LETTERS . "][" . $this->author_letters . "?]{3,}" . " )?" .
			  // common prefixes
			  $this->AUTHOR_PREFIXES . "?" .
			  // only allow v. in front of Capital Authornames - if included in AUTHOR_PREFIXES parseIgnoreAuthors fails
			  "(?:v\\. )?" .
			  // regular author name
			  "[" . $this->AUTHOR_LETTERS . "]+[" . $this->author_letters . "?]*\\.?" .
			  // potential double names, e.g. Solms-Laub.
			  "(?:(?:[- ](?:de|da|du)?[- ]?)[" . $this->AUTHOR_LETTERS . "]+[" . $this->author_letters . "?]*\\.?)?" .
			  // common name suffices (ms=manuscript, not yet published)
			  "(?: ?(?:f|fil|j|jr|jun|junior|sr|sen|senior|ms)\\.?)?" . ")";
			  
		$this->AUTHOR_TEAM = $this->AUTHOR . "?(?:(?: ?ex\\.? | & | et | in |, ?|; ?|\\.)(?:" . $this->AUTHOR
      . "|al\\.?))*";			  
   	}
      
	//----------------------------------------------------------------------------------------------
    function parse($string)
    {    
    
		$p = "/^" . 
		//"#1 genus\/monomial\n" .
		"(?<genus>" . $this->MONOMIAL . ")" . 
		
 		// #2 or #4 subgenus/section with #3 infrageneric rank marker
      	"(?:(?<!ceae) (?<infragenus>" . $this->INFRAGENERIC . "))?" .
			 
		//"#5 species\n" .
		"(\s(?<species>" . $this->EPHITHET . "))?" . 
		
		  "((?:" .
		  // #6 strip out intermediate, irrelevant authors or infraspecific ranks in case of quadnomials
		  "( .*?)?" .
		  // #7 infraspecies rank
		  "( (?<rank>" . $this->RANK_MARKER_SPECIES . "))" . ")?" .
	
		  // #8 infraspecies epitheton
		  "(?: (?<infraspecies>(×?\"?" . $this->EPHITHET . "\"?))" . "))?" .	
		  
		
     	//"#9 entire authorship incl basionyms and year\n" .
      	"(?<authorship>,?". "(?: ?\\("  .
      	
      	//" #10 basionym authors \n" .
      	"(?<basionymAuthorTeam>" . $this->AUTHOR_TEAM . ")?" .
      	
      	 //"#11 basionym year\n" . 
      	",?( ?(?<basionymYear>" . $this->YEAR . "))?" . "\\))?" .
      	
      	 // #12 authors
      	"( (?<authorTeam>" . $this->AUTHOR_TEAM . "))?" .

      	//"#13 year with or without brackets\n" . 
      	"(?: ?\\(?,? ?(?<year>" . $this->YEAR . ")\\)?)?" .      	
      	
      	")" .
       
		"/u";
		
		//echo "\n$p\n\n";

		$result = new stdclass;
		$result->scientificName = new stdclass;
		$result->scientificName->parsed = false;
		$result->scientificName->verbatim = $string;
		$result->scientificName->normalised = $this->normalise($result->scientificName->verbatim);
		$result->scientificName->hybrid = false;
		
		$result->scientificName->type = NameType::unknown;

		
		// Catch NCBI-style informal names
		if (preg_match('/^\w+\s+(\(\w+\)\s+)?(sp\.|aff\.|cf\.|n\. sp\.)/', $result->scientificName->verbatim))
		{
			return $result;
		}
		if (preg_match('/[A-Z]+-\d+/', $result->scientificName->verbatim))
		{
			return $result;
		}
		if (preg_match('/\s+group$/', $result->scientificName->verbatim))
		{
			return $result;
		}
		// Rhinolophus JLE sp. B
		if (preg_match('/\s+sp\.\s+/', $result->scientificName->verbatim))
		{
			return $result;
		}
		
		// Fungi/Metazoa group
		if (preg_match('/\//', $result->scientificName->verbatim))
		{
			return $result;
		}
		
		$result->scientificName->details = array();
		
		// Try and parse name
		if (preg_match($p, $result->scientificName->normalised, $m))
		{
			//print_r($m);
			
			$result->scientificName->parsed = true;	
			$result->scientificName->type = NameType::sciname;
			$details = new stdclass;
			
			// rank			
			if ($m['genus'] != '')
			{
				$details->genus = new stdclass;
				$details->genus->epitheton = $m['genus'];	
				
				if (($m['authorship'] != '') && ($m['infragenus'] == '') && ($m['species'] == ''))
				{
					$this->authorship($details->genus, $m);
				}				
			}

			if ($m['infragenus'] != '')
			{
				$details->infragenus = new stdclass;
				$details->infragenus->epitheton = $m['infragenus'];
				
				$details->infragenus->epitheton = preg_replace('/^\(/', '', $details->infragenus->epitheton);
				$details->infragenus->epitheton = preg_replace('/\)$/', '', $details->infragenus->epitheton);
				
				if (($m['authorship'] != '') && ($m['species'] == ''))
				{
					$this->authorship($details->infragenus, $m);
				}				
			}

			if ($m['species'] != '')
			{
				$details->species = new stdclass;
				$details->species->epitheton = $m['species'];
				
				if (($m['authorship'] != '') && ($m['infraspecies'] == ''))
				{
					$this->authorship($details->species, $m);
				}				
			}

			if ($m['infraspecies'] != '')
			{
				$details->infraspecies = new stdclass;
				$details->infraspecies->epitheton = $m['infraspecies'];
				if ($m['rank'] != '')
				{
					$details->infraspecies->rank = $m['rank'];
				}
				
				if ($m['authorship'] != '')
				{
					$this->authorship($details->infraspecies, $m);
				}				
			}
				
			$result->scientificName->details[] = $details;
				
		} 
		
		
		if (count($result->scientificName->details) == 1)
		{
			$result->scientificName->canonical = '';
			
			if (isset($result->scientificName->details[0]->genus))
			{
				$result->scientificName->canonical .= $result->scientificName->details[0]->genus->epitheton;
			}
			
			// need to decide whether we include this as it has implications for searching ION
			if (0)
			{
				if (isset($result->scientificName->details[0]->infragenus))
				{
					$result->scientificName->canonical .= ' (' . $result->scientificName->details[0]->infragenus->epitheton . ')';
				}			
			}
			if (isset($result->scientificName->details[0]->species))
			{
				$result->scientificName->canonical .= ' ' . $result->scientificName->details[0]->species->epitheton;
			}			
			if (isset($result->scientificName->details[0]->infraspecies))
			{
				$result->scientificName->canonical .= ' ' . $result->scientificName->details[0]->infraspecies->epitheton;
			}
		}
		
		if (count($result->scientificName->details) == 0)
		{
			unset($result->scientificName->details);
		}
		
		return $result;
    }
    
	//----------------------------------------------------------------------------------------------
    function normalise($string)
    {
    	if ($string == '')
    	{
    		return $string;
    	}
    
    	// emdash
    	$string = preg_replace('/—/u', '', $string);
 		// semicolon
    	$string = preg_replace('/:/u', '', $string);
 
    	// use commas before years
    	// ICZN §22A.2 http://www.iczn.org/iczn/includes/page.jsp?article=22&nfv=
    	if (preg_match('/' . $this->COMMA_BEFORE_YEAR . '/u', $string))
    	{
    		$string = preg_replace('/' . $this->COMMA_BEFORE_YEAR . '/u', '$1, $2', $string);
    	}
    	
    	// no whitespace around hyphens
   		if (preg_match('/' . $this->NORM_HYPHENS . '/u', $string))
    	{
    		$string = preg_replace('/' . $this->NORM_HYPHENS . '/u', '-', $string);
    	}

		// use whitespace with &
 		if (preg_match('/' . $this->NORM_AMPERSAND_WS . '/u', $string))
    	{
    		$string = preg_replace('/' . $this->NORM_AMPERSAND_WS . '/u', ' & ', $string);
    	}
      	
 		// whitespace before and after brackets, keeping the bracket style
 		if (preg_match('/' . $this->NORM_BRACKETS_OPEN . '/u', $string))
    	{
    		$string = preg_replace('/' . $this->NORM_BRACKETS_OPEN . '/', ' $1', $string);
    	}
 		if (preg_match('/' . $this->NORM_BRACKETS_CLOSE . '/u', $string))
    	{
    		$string = preg_replace('/' . $this->NORM_BRACKETS_CLOSE . '/uu', '$1 ', $string);
    	}
		
		// remove whitespace before commas and replace double commas with one
 		if (preg_match('/' . $this->NORM_COMMAS . '/u', $string))
    	{
    		$string = preg_replace('/' . $this->NORM_COMMAS . '/u', ', ', $string);
    	}
      	
      	// normalize hybrid markers
		if (preg_match('/' . $this->NORM_HYBRIDS_GENUS . '/u', $string))
    	{
    		$string = preg_replace('/' . $this->NORM_HYBRIDS_GENUS . '/uU', '×$1', $string);
    	}
 		if (preg_match('/' . $this->NORM_HYBRIDS_EPITH . '/u', $string))
    	{
    		$string = preg_replace('/' . $this->NORM_HYBRIDS_EPITH . '/uU', '$1 ×$2', $string);
    	}
 		if (preg_match('/' . $this->NORM_HYBRIDS_FORM . '/u', $string))
    	{
    		$string = preg_replace('/' . $this->NORM_HYBRIDS_FORM . '/u', ' × ', $string);
    	}
 
 		// capitalize all entire upper case words
 		if (preg_match('/' . $this->NORM_UPPERCASE_WORDS . '/u', $string))
    	{
    		$string = preg_replace_callback('/' . $this->NORM_UPPERCASE_WORDS . '/u', 'capitalise', $string);
    	} 

		$string = preg_replace('/\s\s+/u', ' ', $string);
		$string = preg_replace('/^\s/u', '', $string);
		$string = preg_replace('/\s$/u', '', $string);
		
    	return $string;
    }
	
	//----------------------------------------------------------------------------------------------
	function authorship(&$name, $m)
	{
		if ($m['authorship'] != '')
		{
			$name->authorship = $m['authorship'];
			$name->authorship = preg_replace('/^\s+/u', '', $name->authorship);
			
			if ($m['basionymAuthorTeam'] != '')
			{
				$name->basionymAuthorTeam = new stdclass;
				$name->basionymAuthorTeam->authorTeam = $m['basionymAuthorTeam'];
				if (isset($m['basionymYear']) && ($m['basionymYear'] != ''))
				{
					$name->basionymAuthorTeam->year = $m['basionymYear'];
				}
			}
			if (isset($m['authorTeam']) && ($m['authorTeam'] != ''))
			{
				if ($m['basionymAuthorTeam'] == '')
				{
					$name->basionymAuthorTeam = new stdclass;
					$name->basionymAuthorTeam->authorTeam = $m['authorTeam'];
					if (isset($m['year']) && ($m['year'] != ''))
					{
						$name->basionymAuthorTeam->year = $m['year'];						
					}
				}
				else
				{
					$name->combinationAuthorTeam = new stdclass;
					$name->combinationAuthorTeam->authorTeam = $m['authorTeam'];
					if (isset($m['year']) && ($m['year'] != ''))
					{
						$name->combinationAuthorTeam->year = $m['year'];						
					}
				}
			
			}

		}
	}
}

if (0)
{
	// tests
	$pp = new Parser();
	$n = 'Pinnotheres atrinicola Page, 1983';
	
	$n = 'Pinnotheres atrinicola Page, 1983';
	
	//$n = 'Pseudocercospora dendrobii U. Braun & Crous 2003';
	//$n = 'Polypogon monspeliensis (L.) Desf.';
	//$n = 'Demansia torquata (Günther, 1862)';
	
	//$n = 'Dennyus (Collodennyus) bartoni Clayton, Price & Johnson 2006';
	
	$n = 'Sténométope laevissimus Bibron 1855';
	
	//$n = 'Fagus sylvatica subsp. orientalis (Lipsky) Greuter & Burdet';
	//$n = 'Fagus sylvatica (Lipsky) Greuter & Burdet';
	
	//$n = 'Mycosphaerella eryngii (Fr. Duby) ex Oudem. 1897';
	
	//$n = 'Dennyus (Collodennyus) distinctus timjonesi Clayton, Price & Page 1996';
	
	//$n = 'Pseudocercospora Speg. 1910';
	
	//$n = 'Gonocephalus borneensis — MANTHEY & GROSSMANN 1997: 179';
	//$n = 'Dennyus (Collodennyus) distinctus timjonesi Clayton, Price&Page 1996';
	
	$n = 'Gonocephalus abbotti';
	
	$n = 'Uromastyx alfredschmidti WILMS & BÖHME 2001';
	//$n = 'Uromastyx alfredschmidti';
	
	$n = 'Pseudocercospora Speg. 1910';
	
	$n = 'Bactrocera (Hemizeugodacus) ektoalangiae Drew & Hancock 1999';
	
	// to do:
	$n = 'Steinernema cf. glaseri Konza IVAB-71';
	//$n = 'Arthopyrenia hyalospora X Hydnellum scrobiculatum';
	
	//$n = 'Coptotermes (Polycrinitermes) chaoxianensis (Huang & Li 1985)';
	
	//$n = 'Anodonthyla sp. ZSM 673/2003';
	
	$n = '"Spirochaeta interrogans" Stimson 1907';
	$n = 'Helicobacter pylori (Marshall et al. 1985) Goodwin et al. 1989';
	$n = 'not "Brucella ovis" van Drimmelen 1953';
	$n = '"Bacterium aquatilis" (sic) (Frankland and Frankland 1889) Chester 1897';
	$n = 'Pseudomonas fluorescens (biotype D)';
	$n = 'alpha proteobacterium endosymbiont of Paracatenula sp.';
	$n = 'Plocamium sp. 2telfairiae BOLD:AAO5906';
	$n = 'Influenza A virus (A/common teal/California/11285/2008(mixed))';
	$n = 'Lactobacillus delbrueckii subsp. bulgaricus CNCM I-1519';
	
	$n = 'Myopterus daubentonii subsp. albatus Thomas, 1915';
	$n = 'Myopterus daubentonii';
	
	$r = $pp->parse($n);
	
	print_r($r);
	
	echo json_encode($r);
}

?>