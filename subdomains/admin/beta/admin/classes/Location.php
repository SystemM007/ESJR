<?php
class Location
{
	private static $idRegister = 0;
	
	// tijdelijke opslag tussen construct en toString
	private $content;
	
	// wordt aangemaakt door toString, daarna niet wijzigbaar
	private $id;
	
	// mogelijkheid om te kiezen voor een inline (span) tag of een block niveau tag (div)
	private $inline = false;
	
	/*
	 * In de constructor wordt de content tijdelijk opgeslagen in $this->content
	 * en kan er worden aangegeven of dit een inline location moet zijn
	 * MERK OP dat hier nog geen id wordt aangemaakt
	*/
	public function __construct($content = "", $inline = false)
	{
		$this->update($content);
		$this->inline = $inline;
	}
	
	/*
	 * Met update kan de inhoud van de locatie worden gewijzigd.
	 * Wanneer de locatie al weggeschreven is, 
	 * dan wordt het desbetreffende veld in de template gewijzigd
	 * anders wordt het opgeslagen voor het wegschrijven
	 */
	public function update($content)
	{
		$content = (string) $content;
		
		if(isset($this->id))
		{
			Response::fieldErase($this->id);
			Response::field($this->id, $content);
		}
		else
		{
			$this->content = $content;
		}
	}
	
	/*
	 * Deze functie vouwt de content in een tag en voorziet deze van een id
	 * zodat deze later kan worden aangesproken
	 * deze functie mag maar ��n keer worden aangeroepen!
	 */
	public function __toString()
	{
		if(isset($this->id))
		{
			throw new Exception("toString voor de tweede maal aangeroepen.");
		}
	
		$this->id = "location_" . self::$idRegister++;
		$content = $this->content;
		$tag = $inline ? "span" : "div";
		
		$string =  "<$tag id='$this->id' > $this->content </$tag>";
		
		unset($this->content);
		
		return $string;
	}
}