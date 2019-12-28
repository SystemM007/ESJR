<?php

class Module_ImageSplitPage_Children_Subpages extends Module_Section_Children
{
	protected function listWhere($where = "")
	{
		return "AND u_cores.module != 'PageImage' " . $where;
	}
}