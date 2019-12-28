<?php

abstract class Module_Basic_Edit extends Module_Abstract_Edit implements Module_Basic_Edit_Interface
{
	public function getButtons()
	{
		$buttons = "";
		
		$buttons .= new Fragment_Button_Save($this->Core) ;
		$buttons .= new Fragment_Button_Reset();
		
		return $buttons;
	}
	
	public function getEditables()
	{
		return $this->makeEditables();
	}
	
	abstract protected function makeEditables();
	
	protected function coreName($desc = "Afdeling Naam")
	{
		$Edit = new Editable_Text($desc, array("u_cores", "ID", $this->getID(), "name"));
		$Edit->checkNonEmpty();
		$Edit->addCallBack(array($this, "onCoreName"));
		if($this->firstEdit) $Edit->optionGiveFocus();
		$this->addEditable($Edit);
		return $Edit;
	}
	
	protected function coreModule($desc = "Admin Module")
	{
		$Edit = new Editable_ClassName($desc, array("u_cores", "ID", $this->getID(), "module"));
		$Edit->setUnderscoreAllowed();
		$Edit->checkNonEmpty();
		$this->addEditable($Edit);
		return $Edit;
	}
	
	protected function coreReadLevel($desc = "Toegang toegestaan vanaf:")
	{
		$Edit = new Editable_Select($desc, array("u_cores", "ID", $this->getID(), "readLevel"));
		$Edit->optionTable("u_adminAccesslevels", "accessLevel", "name", "`accessLevel` >= '". User::getLevel() ."'");
		$this->addEditable($Edit);
		return $Edit;
	}
	
	protected function coreWriteLevel($desc = "Bewerken toegestaan vanaf:")
	{
		$Edit = new Editable_AccessSelect($desc, array("u_cores", "ID", $this->getID(), "writeLevel"));
		$this->addEditable($Edit);
		return $Edit;
	}
	
	protected function coreChildrenAllowed($desc = "Onderliggende afdelingen mogelijk:")
	{
		$Edit = new Editable_Bool($desc, array("u_cores", "ID", $this->getID(), "childrenAllowed"));
		$Edit->addCallBack(array($this, "onCoreChildrenAllowed"));
		$this->addEditable($Edit);
		return $Edit;
	}
	
	protected function coreChildCreateLevel($desc = "Onderliggende afdelingen aanmaken:")
	{
		//if($this->Core->childrenAllowed && $this->Core->childCreateAccess())
		if($this->Core->childrenAllowed)
		{
			$Edit = new Editable_AccessSelect($desc, array("u_cores", "ID", $this->getID(), "childCreateLevel"));
			$this->addEditable($Edit);
			return $Edit;
		}
		else
		{
			return "";
		}
	}
	
	protected function coreEnabled($desc = "Online")
	{
		$Edit = new Editable_Bool($desc, array("u_cores", "ID", $this->getID(), "enabled"));
		$this->addEditable($Edit);
		return $Edit;
	}
	
	// callbacks
	
	public function onCoreName($value)
	{
		$this->Core->refresh();
		$this->Core->Page->buildName();
	}
	
	public function onCoreChildrenAllowed()
	{
		Response::evalJs(new Fragment_JS_History(0));
	}
}