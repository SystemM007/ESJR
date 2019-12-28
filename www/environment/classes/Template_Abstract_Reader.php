<?php

abstract class Template_Abstract_Reader
{
	protected $attributes;
	protected $elements = array();
	
	
	public static $debug = false;
	
	private $file;
	private $readLine;
	
	public function __construct(array $attributes, $innerContent, $fileName = NULL, $readLine = 1)
	{
		$this->attributes = $attributes;
				
		// deze twee zijn puur voor het werpen van exceptions
		$this->file = $fileName;
		$this->readLine = $readLine;
		
		$this->parse($innerContent);
	}
	
	abstract protected function addTag($tagName, array $attributes, $innerContent);
	
	abstract protected function addString($string);
	
	abstract public function makeElement();
	
	/*
	* addElement wordt gebruikt om alle elementen die
	* worden gevonden in dit element op te slaan
	*/
	final protected function addElement($element)
	{
		$this->elements[] = $element;
	}
	
	/*
	* getFileName wordt aangeroepen door Template_Exception
	*/
	final public function getFile()
	{
		return $this->file;
	}
	
	/*
	* getReadLine wordt aangeroepen door Template_Exception
	*/
	final public function getReadLine()
	{
		return $this->readLine;
	}
	
	/*
	* deze functie doorloopt geheel $this->parse
	* tot alles op is
	*/
	final private function parse($parse)
	{
		$this->debug("Parsing Start<br /> <hr /><hr /><hr />");
		
		$parse = $this->stripPrecedingWhitespace($parse);
		
		while($parse = $this->parseString($parse))
		{
			$parse = $this->parseTag($parse);
		}
		$this->debug("Parsing Complete<br /> <hr /><hr /><hr />");
	}
	
	/*
	 * Ieder stukje string dat van het begin van $parse afgeknipt wordt
	 * moet naar deze functie worden doorgespeeld!
	 * Deze functie houd bij hoeveel regels er zijn gelezen.
	 */
	final private function parsedString($str)
	{
		$this->readLine += substr_count($str, "\n");
	}
	
	/*
	 * Deze functie knipt alle whitespace tussen de openingstag en het eerste stukje innerContent weg
	 * 
	 * Oorspronkelijk gemaakt om zo xml openingstags op de eerste regel te kunnen krijgen.
	 * Mogelijk ook in het algemeen erg handig
	 */
	final private function stripPrecedingWhitespace($parse)
	{
		$matched = preg_match("/(^\s*)(.*)/s", $parse, $match);
		if(!$matched) throw new Exception("Als dit niet matched weet ik het ook niet meer: '$parse'", $this);
		
		$whitespace = $match[1];
		$parse = $match[2];
		
		$this->parsedString($whitespace);
		return $parse;
	}
	
	/*
	* parseString pakt de string TOT de volgende template TAG OF het einde van parse
	* en retourneert $parse
	*/
	final private function parseString($parse)
	{
		/*
		* 			Zoeken naar
		* string	1. ieder willekeurig karater vanaf het begin
		* parse		2. tot het einde van het document OF het begin van een tag tot en met het einde van het document
		*/
		$matched = preg_match("/^(.*?)($|<%.*$)/s", $parse, $match);
		
		if(!$matched)
		{
			throw new Template_Exception("Bizar! kan geen string vinden. Programma fout.", $this);
		}
		
		$string = $match[1];
		$parse = $match[2];
		
		if($string)
		{
			$this->addString($string);
			$this->debug("String found", $string);
			$this->parsedString($string);
		}
		return $parse;
		
	}
	
	/*
	 * Door de alternate in parseString is deze functie niet meer nodig 
	 * 
	 * 
	 *
	
	final private function parseRestString($parse)
	{
		if(!$parse)
		{
			$this->debug("Geen rest string");
			return;
		}
		{
			$this->addString($parse);
			$this->debug("Rest string", $parse);
		}
	}*/
	
	
	
	
	
	/*
	* deze functie verwerkt een gevonden tag
	* er wordt een verdeling gemaakt tussen de tagContent en de innerContent
	*	<tagContent ...>innerContent<...>
	* er wordt onderscheid gemaakt tussen selfClosing en normale tag
	*
	* MERK OP!!
	* er is nog geen enkele vorm van escape mogelijkheid ingebouwd voor > karaters in de attributen!!!
	*/
	
	final private function parseTag($parse)
	{
		/*				Zoeken vanaf het begin naar
		* tagContent	1. ieder willekeurig karater
		* selfClosing	2. eventueel een /
		*				een >
		* parse			3. de rest van de meuk
		*/
		$matched = preg_match("/^<%(.*?)(\/?)>(.*)$/s", $parse, $match);
		if(!$matched) throw new Exception("Onjuist geformateerde templatetag: '$parse'", $this);
		
		$tagContent = $match[1];
		$selfClosing = (bool) $match[2];
		$parse = $match[3];
		
		$this->debug("Tag start", "<%$tagContent". ($selfClosing ? "/" : "") . ">");
		
		list($tagName, $attributes) = $this->parseTagContent($tagContent);
				
		if($selfClosing)
		{
			$innerContent = NULL;	
			$this->debug("Tag is selfclosing");
		}
		else
		{
			list($innerContent, $parse) = $this->getInnerContent($parse, $tagName);
		}
			
		$this->addTag($tagName, $attributes, $innerContent);
		$this->parsedString($innerContent); 
		
		return $parse;
	}
	
	/*
	* deze functie verwerkt de tagContent
	* er wordt onderscheid gemaakt tussen de tagName en de attributen
	*	<tagName attributen>
	* de attributen dienen key="value" paren te zijn
	* 	<tagName key1="value1" key2="value2">
	*/
	
	final private function parseTagContent($tagContent)
	{
		list($tagName, $tagContent) = $this->getTagName($tagContent);
		
		$attributes = array();
		while($tagContent)
		{
			list($tagContent, $attribute) = $this->getNextAttribute($tagContent);
			$attributes = array_merge($attributes, $attribute);
		}
		
		return array($tagName, $attributes);
	}
	
	final private function getTagName($tagContent)
	{
		/*
		* 				zoeken VANAF HET BEGIN
		* tagName		1. naar één of meer alfanumerieke karaters \w = [a-zA-Z0-9_]
		* whitespace:	2. gevolgd door één of meer whitespace karaters \s = [ \t\n\r\f] of het einde van de string
		* tagContent:	3. gevolgd door de rest van de meuk
		*/
		$matched = preg_match("/^(\w+?)([\s]+|$)(.*)$/s", $tagContent, $match);
		if(!$matched) throw new Template_Exception("Geen geldige tagname gevonden in '$tagContent'", $this);
		
		$tagName = $match[1];
		$whitespace = $match[2];
		$tagContent = $match[3];
		
		$this->debug("Tagname found", $tagName);
		$this->parsedString($whitespace);
		
		return array($tagName, $tagContent);
	}
	
	final private function getNextAttribute($tagContent)
	{		
		/*
		* 				zoeken VANAF HET BEGIN
		* attribuut:	1. naar één of meer alfanumerieke karaters \w = [a-zA-Z0-9_]
		* 				totaan de eerste =
		*				2. gevolgd door OF
		* waarde:			3. een tekst tussen " die niet eindigd met \
		* waarde:			4. een tekst tussen ' die niet eindigd met \
		* whitespace:	5. gevolgd door nul of meer  whitespace karaters \s = [ \t\n\r\f] 
		*					niet één of meer!: het einde van de tag kan ook nul spaties bevatten
		*					ex. <tagName attribute="value">
		* tagContent:	6. rest meuk
		* 
		*/
		
		$matched = preg_match("/^(\w+?)=(\"(.*?[^\\\])\"|'(.*?[^\\\])')(\s*)(.*)$/s", $tagContent, $match);
		if(!$matched) throw new Template_Exception("Geen geldige atribuut constructie: [$tagContent]", $this);
		
		$attribute = $match[1];
		$value = $match[3] ? $match[3] : $match[4];
		$whitespace = $match[5];
		$tagContent = $match[6];
		
		$this->debug("attribute found", "$attribute=\"$value\"");
		$this->parsedString($whitespace);
		
		$attribute = array(
			$match[1] => $match[3] 
		);		
		return array($tagContent, $attribute);
	}
	
	/*
	 * Loopt door parse heen en zoekt naar closing tag voor huidige tag
	 * Dit wordt gedaan door te zoeken naar closing tag,
	 * te tellen hoeveel tags inmiddels zijn geopend, zoveel tags te doorlopen, enz
	 */
	
	final private function getInnerContent($parse, $tagName)
	{
		// depth staat op 1, want deze tag is al geopend in parse
		$depth = 1;
		$innerContent = "";
		do
		{
			list($scannedContent, $closingTag, $parse) = $this->scanTagContent($parse, $tagName); 
			
			$depth += $this->countClosingTags($scannedContent, $tagName);
		
			// depth met ééntje verlagen (er is nu namelijk gezocht tot en met één eind tag)
			// wanneer deze diepte niet nul is, moet de closing tag gewoon worden gerekend als een deel van scannedContent
			if(--$depth > 0)
			{
				$scannedContent = $scannedContent .= $closingTag;
			}
			
			$innerContent .= $scannedContent;
		}
		while($depth);
		
		$this->debug("innercontent found", $innerContent);
		// INNERCONTENT NOG NIET  naar parsedString sturen
		// dit wordt namelijk nog verder geparsed door het element waar het naar toe gestuurd wordt
		return array($innerContent, $parse);
	}
	
	final private function scanTagContent($parse, $tagName)
	{
			/*				Zoeken vanaf het begin naar
			* scannedContent	1. ieder willekeurig karater
			* 				tot aan 
			* closingTag	2. de sluittag
			* parse			3. de rest van de meuk
			*/
			
			$matched = preg_match("/^(.*?)(<\/%$tagName>)(.*)$/s", $parse, $match);
			if(!$matched) throw new Template_Exception("Geen closingtag gevonden voor '$tagName'", $this);
			$scannedContent = $match[1];
			$closingTag = $match[2];
			$parse = $match[3];
			
			//$this->debug("scanned content", $scannedContent);

			return array($scannedContent, $closingTag, $parse);
	}
	
	final private function countClosingTags($scannedContent, $tagName)
	{
		// hoeveel tags zijn er in de scannedContent geopend?
		// dit toevoegen aan depth
		$innerTags = preg_match_all("/<%$tagName/s", $scannedContent, $matches);
		
		if($innerTags)
		{
			//$this->debug("inner tags", "$innerTags in \n$scannedContent");
		}
		
		return $innerTags;
	}
	
	
	final protected function debug($message, $content = NULL)
	{
		if(!self::$debug) return;
		echo ""
			. "<h3>$message</h3>"
			. "<code>" . nl2br(htmlspecialchars($content)) . "</code><br/><br/>"
			. "<small>Class ". get_class($this) ."</small>" . "<br />"
			. "<small>In <strong>$this->file</strong> on line <strong>$this->readLine</strong></small><br />"
			. "<hr />";
	}
}