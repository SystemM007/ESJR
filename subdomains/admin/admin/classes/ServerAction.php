<?php
class ServerAction extends Module_Life
{
	protected $callback;
	protected $memo;
	protected $id;
	
	public function __construct($callback, $memo = array())
	{
		if(!is_callable($callback))
		{
			throw new Exception("Callback is not callable: " . print_r($callback, true));
		}
		$this->callback = $callback;
		
		if(! is_array($memo) )
		{
			$memo = array($memo);
		}
		$this->memo = $memo;
		
		$this->registerLife();
	}
	
	public function __toString()
	{
		return (string) $this->getLifeId();
	}
	
	public function call()
	{
		call_user_func_array($this->callback, $this->memo);
	}


	public function isRequestable($function)
	{
		return true; // dit is juist de lol! Er kan geen gekke input vanaf buiten komen omdat in de sessie staat opgeslagen welke functie moet worden aangeroepen!!!
	}
}
?>