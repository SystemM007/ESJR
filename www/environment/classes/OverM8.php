<?php
class OverM8
{
	protected $text;
	private $tokens = array();
	private $tokenCount = array();

	protected $spamScore;
	private $intresting = array();
	
	const table_words = "u_bayes_words";

	// ------- public --------
	
	public function __construct()
	{
		$args = func_get_args();
		$this->text = implode(" ", $args);
	}
	
	public static function scoreIsSpam($score)
	{
		if(!$score) return true;
		return ($score > Settings::get("overm8_splitpoint"));
	}
	
	public function isSpam()
	{
		return self::scoreIsSpam($this->classify());
	}
	
	public function learnSelf()
	{
		return $this->learn($this->isSpam());
	}
	
	public function classify()
	{
		if($this->spamScore) return $this->spamScore;
		
		if(count($this->getTokens()) == 0) return false;
		
		if(Settings::get("overm8_texts_ham") == 0 || Settings::get("overm8_texts_spam") == 0)
		{
			trigger_error("At least one Ham and one Spam text has to be saved to be able to categorize a text!<br />\n", E_USER_ERROR);
		}
		
		$intrestingWords = $this->getIntrestingWords();
		
		// Calculate spamminess and hamminess with the ratings of the intresting tokens
		$hamminess  = 1;
		$spamminess = 1;
		$relevant = 1; // dit is een startwaarde ( om later niet door nul te moeten delen?
		
		foreach($intrestingWords as $word)
		{
			// Tokens that occur more than once also count more then once
			$ratingPow = pow($word["rating"], $word["count"]);
			
			$relevant += $word["count"];
			
			$hamminess  *= (1.0 - $ratingPow);
			$spamminess *= $ratingPow;
		}
		
		$hamminess  = 1 - pow($hamminess,  (1 / $relevant));
		$spamminess = 1 - pow($spamminess, (1 / $relevant));
		
		$spamScore = ($hamminess - $spamminess) / ($hamminess + $spamminess);
		$spamScore = (1 + $spamScore) / 2;

		return $this->spamScore = $spamScore;
	}

	
	public function learn($is_spam, $learn = true)
	{
		/* $learn = false => unlearn
		 letop de paradox: 
		 indien iets áfgeleerd moet worden geldt het volgende:
		 een bericht is (toch) geen spam bericht:
		 	- is_spam = true, learn = false
		 een bericht is toch een spam bericht
		 	- is_spam = false, learn = false
		 er moet dus gekeken worden welk aspect van de text het betreft (het ham zijn of het spam zijn)
		*/
		if(!( $tokens = $this->getTokens())) return false;
		
		$learnf = ($learn) ? 1 : -1;

		// update all tokens
		foreach($tokens as $word => $count)
		{
			if($is_spam)
			{
				$countSpam = $learnf*$count;
				$countHam = 0;
			}
			else
			{
				$countSpam =  0;
				$countHam = $learnf*$count;
			}
			
			$this->changeTokenCount($word, $countSpam, $countHam);
		}
		
		// Update the number of texts
		$this->updateNumberTexts($is_spam, $learnf);
				
		// reset saved vars that could have been changed
		$this->intresting = array();
		$this->spamScore = NULL;
		
		return true;
	}

	public function unlearn($is_spam)
	{
		// Delete a reference text
		// alias for learn($is_spam, false)
		return $this->learn($is_spam, false);
	}
	
	public function getIntrestingStr()
	{
		$str = "";
		foreach($this->getIntrestingWords() as $word => $token)
		{
			$str .= "$word: " . $token["count"] . " x " . $token["rating"] . ", \n";
		}
		return $str;
	}
	
	
	public static function calculateSplitPoint(array $spamScores)
	{
		$catchChance = Settings::get("overm8_catchchance");
			
		if($catchChance > 1 || $catchChance < 0) trigger_error("Fout in de instelling 'overm8_catchchance': deze waarde moet tussen de 0 en 1 liggen");
		
		elseif($catchChance == 0) $newSplitPoint = 1;
		
		elseif($catchChance == 1) $newSplitPoint = 0;
		
		else{
			if(count($spamScores) == 0)
			{
				// indien nog nooit spam is geweest
				// het spamfilter uitschakelen
				$newSplitPoint = 1;
			}
			else
			{
				// data array omzetten naar score array
				sort($spamScores, SORT_NUMERIC);
				
				$key = floor( (1-$catchChance) * (count($spamScores)-1) );
				$newSplitPoint = $spamScores[$key];
			}
		}
	
		Settings::update("overm8_splitpoint", $newSplitPoint);
	}
	
	protected function getIntrestingWords()
	{
		if($this->intresting) return $this->intresting;
	
		// number of words to test
		$numberTests = 15;

		$importance = array();
		foreach($this->getTokens() as $word => $count)
		{
			$token["$word"] = array(
				"count" => $count,
				"rating" => $this->rateWord($word)
			);
			$importance["$word"] = abs(0.5 - $token["$word"]["rating"]);
		}
		
		arsort($importance, SORT_NUMERIC);

		$x = 0;
		$intresting = array();
		foreach(array_keys($importance) as $word)
		{
			$intresting[$word] = $token[$word];
			if( ($x++) == $numberTests)	break;
		}
		
		return 	$this->intresting = $intresting;
	}
	
	protected function getTokens()
	{
		if($this->tokens) return $this->tokens;

		$text = strtolower($this->text);

		// Get internet and IP addresses
		preg_match_all("/[a-z0-9\_\-\.]+/", $text, $rawTokens);
	
		foreach($rawTokens[0] as $word)
		{
			if(strpos($word, ".") === false) continue;
			$this->setToken($word);

			// Delete the processed parts
			$text = str_replace($word, "", $text);

			// Also process the parts of the urls
			$urlParts = preg_split("/[^a-z0-9$\-_.+!*(),]/", $word); // only lowercase & only valid url symbols (exept ', never seen it in a url, and it can be part of the <a> tag
			foreach($urlParts as $word) $this->setToken($word);

		}

		// remove not-wanted html symbols
		str_replace(array("&quot;", "&amp;", "&lt;", "&gt;", "&nbsp;"), "", $text);
		
		$rawTokens = preg_split("/[^a-z0-9\$¤¥£äöüßéèêáàâóòôç&;]+/", $text); // only lowercase && no ?,!,',` // &,; added to check html texts

		foreach($rawTokens as $word) $this->setToken($word);

		// Get HTML
		preg_match_all("/(<.+?>)/", $text, $rawTokens);
		
		foreach($rawTokens[1] as $word)
		{
			// Schrijven als <tagname>
			if(strpos($word, " ") !== false)
			{
				preg_match("/(.+?)\s/", $word, $tmp);
				$word = $tmp[1] . ">";
			}
			if(strpos($word, "/")) continue;
			
			$this->setToken($word);
		}

		return $this->tokens;

	}
	
	private function setToken($word)
	{
		if($this->isValidToken($word))
		{
			if(!isset($this->tokens[$word])) $this->tokens[$word] = 0;
			$this->tokens[$word]++;
		}
	}

	private function isValidToken($word)
	{
		return ( strlen($word) >= 3 && strlen($word) <= 30 && !preg_match("/^[0-9]+$/", $word) );
	}

	protected function rateWord($word)
	{
		// Calculate the spamminess of a token
		list($hamcount, $spamcount) = $gtc = $this->getTokenCount($word);
		
		if(isset($hamcount) && isset($spamcount))
		{
			// The token is found in the database
			return $this->calcProbability($hamcount, $spamcount);
		}
		else{
			// Look up all degenerated tokens in the database
			$degRating = array();
			foreach($this->makeDegenerates($word) as $degWord)
			{
				list($hamcount, $spamcount) = $this->getTokenCount($degWord);
				if($hamcount && $spamcount) $degRating[] = $this->calcProbability($hamcount, $spamcount);
			}
			// Choose the rating which is the most far from 0.5
			// Default score is 0.4
			$rating = 0.4;
			foreach($degRating as $tmp) 
			{
				if(abs(0.5 - $tmp) > abs(0.5 - $rating)) $rating = $tmp;
			}
		}
		return $rating;
	}
	
	private function makeDegenerates($word)
	{
		$degenerates = array();
	
		// Things like SPxAxM get replaced
		$denegrates[] = str_replace("x", "", $word); 
		
		// any more features?
		
		// controleer op dubbelle
		foreach($degenerates as $key => $degWord)
		{
			if(in_array($degWord, $degenerates)) unset($degenerates["$key"]);
		}
		
		return $degenerates;	
	}


	protected function calcProbability($countHam, $countSpam)
	{
		// Definitely Ham, never occured in Spam
		$countHam = (int) $countHam;
		$countSpam = (int) $countSpam;
		
		// indien het woord voor beide 0 heeft de default retourneren.
		if(! ($countHam || $countSpam) ) return 0.4;
		
		if($countSpam == 0)
		{
			if($countHam > 10)	$rating = 0.0001;
			else				$rating = 0.0002;
		}
		// Definitely Spam, never occured in Ham
		elseif($countHam == 0)
		{
			if($count_spam > 10)$rating = 0.9999;
			else				$rating = 0.9998;
		}
		// Occured both in Ham and in Spam
		else{
			$textsHam = Settings::get("overm8_texts_ham");
			$textSpam = Settings::get("overm8_texts_spam");

			// Consider the number of Ham and Spam texts
			$hamRel  = $countHam / $textsHam;
			$spamRel = $countSpam / $textSpam;

			// Version of Mr. Graham
			$rating = $spamRel / ($spamRel + $hamRel);

			// Better probability
			$all     = $countHam + $countSpam;
			$rating  = (0.5 + ($all * $rating)) / (1 + $all);
		}

		return $rating;
	}
	
	protected function getTokenCount($token)
	{
		if($this->tokenCount[$token]) return $this->tokenCount[$token];

		$pdata = MySql::selectRow(array(
			"select" => array("bwSpamcount", "bwHamcount"),
			"from" => self::table_words,
			"where" => "bwToken = '$token'"
		));
		
		return $this->tokenCount[$token] = array($pdata["bwHamcount"], $pdata["bwSpamcount"]);
	}

	protected function changeTokenCount($token, $countSpam, $countHam)
	{
		MySql::query("
			INSERT INTO " . self::table_words . " 
				(bwToken,bwSpamcount,bwHamcount) 
			VALUES 
				('$token', $countSpam, $countHam) 
			ON DUPLICATE KEY UPDATE 
				bwSpamcount = bwSpamcount + $countSpam, 
				bwHamcount = bwHamcount + $countHam
		");
		
		/*
		list($hamcount, $spamcount) = $this->getTokenCount($token);
		
		if($hamcount || $spamcount)
		{
			$countSpam += (int) $spamcount;
			$countHam += (int) $hamcount;
			
			if($countSpam < 0)	$countSpam = 0;
			if($countHam < 0) $countHam = 0;
			
			if(!($countSpam || $countHam))
			{
				// nietszeggende tokens verwijderen
				$this->deleteToken($token);
			}
			
			$this->updateToken($token, $countSpam, $countHam);
		}
		else
		{
			$this->newToken($token, $countSpam, $countHam);
		}*/
	}
	
	/*protected function newToken($token, $countSpam, $countHam)
	{
		MySql::insert(array(
			"table" => self::table_words,
			"values" => array(
				"bwToken" => $token,
				"bwSpamcount" => $countSpam,
				"bwHamcount" => $countHam
			)
		));
		
		$this->tokenCount[$token] = array($countSpam, $countHam);
	}

	protected function updateToken($token, $countSpam, $countHam)
	{
		MySql::update(array(
			"table" => self::table_words,
			"where" => "bwToken = '$token'",
			"values" => array(
				"bwSpamcount" => $countSpam,
				"bwHamcount" => $countHam
			)
		));
		
		$this->tokenCount[$token] = array($countSpam, $countHam);
	}*/
	
	protected function deleteToken($token)
	{
		MySql::delete(array(
			"where" => "bwToken = '$token'",
			"table" => self::table_words,
			"limit" => 1
		));
		
		unset($this->tokenCount[$token]);
	}
	
	protected function updateNumberTexts($isSpam, $num)
	{
		$key = $isSpam ? "overm8_texts_spam" : "overm8_texts_ham";

		$num += (int) Settings::get($key); 
		if($num < 0) trigger_error("Geprobeerd '$num' teksten te verwijderen van '$key', daar waar er nog maar '$oldNum' waren", e);
		
		Settings::update($key, $num);
	}

}

?>