<?php
#####################################################################################
#        Alumni-OnLine: Alumni Organization Web Management System                   #
#####################################################################################
#                                                                                   #
# Copyright © 2004 by Fatih BOY		                                            #
# http://alumni-online.enterprisecoding.com/                                        #
#                                                                                   #
# This program is free software. You can redistribute it and/or modify              #
# it under the terms of the GNU General Public License as published by              #
# the Free Software Foundation; either version 2 of the License.                    #
#####################################################################################

require_once('controls.Settings.php');
require_once(DIR_CONTRIB."phplayersmenu/template.inc.php");	// taken from PHPLib
require_once(DIR_CONTRIB."phplayersmenu/layersmenu.inc.php");


/*
Class: Menu 
	Parses mail templates that placed on database

Note:
	Extends LayersMenu class and binds initial settings
for mail templates.
	See Also:
		<LayersMenu>
*/
class Menu extends LayersMenu {

	var $menuName;
	var $type;
	
	
	/*
		Object: $main
			Main class instance
	*/
	var $main;
	
	/*
		Constructor: Menu
			Default constructer
			
		Parameters:

			$main		- Alumni-Online main class
			$menuName	- Name of the menu
			$type		- Menu type. It would be one of the followings:
							* Horizontal : Displays horizantel menu
							* Vertical   : Displays vertical menu
							* Tree       : Displays tree menu
			
		Note:
			Initializes Menu class, binds initial settings		
	*/
   function Menu($main, $menuName, $type) {
   	$this->LayersMenu(); 
	
	$this->menuName = $menuName;
	$this->type     = $type;
	$this->main     = $main;
	
	$this->setDirroot(DIR_CONTRIB."phplayersmenu");
	$this->setLibdir("lib/");
	$this->setLibjsdir("libjs/");
	$this->setLibjswww("libjs/");
	$this->setTpldir("templates/");
	$this->setImgdir(DIR_TEMPLATES_SITE.$main->applicationSettings["theme"]."/images/");
	$this->setImgwww("templates/skins/".$main->applicationSettings["theme"]."/images/"); 
	$this->setPrependedUrl("index.php?tab=");
	
	$this->setDownArrowImg("menu-down-arrow.png");
	$this->setForwardArrowImg("menu-forward-arrow.png");
	
	$recordSet = $main->databaseConnection->Execute("SELECT tab_id AS id, parent_id, tab_name AS text, tab_id AS link, title, '' AS icon, '' AS target, '' AS expanded
														FROM {$main->databaseTablePrefix}tabs 
														WHERE 
															is_visible=1 
															AND tab_id>=0 
															AND (authorized_roles LIKE '%;{$main->userGroup};%' OR authorized_roles LIKE '%;1;%') 
														ORDER BY 
															tab_order");
	
	//Check for error, if an error occured then report that error
	if (!$recordSet) {
		trigger_error("Unable to get tabs\nreason is : ".$skin->main->databaseConnection->ErrorMsg());
		die();
	}
	  
	$rows = $recordSet->GetRows();	
	$this->generateMenu($rows, $menuName);
	
	
	if($type=='Horizontal'){
		$this->newHorizontalMenu($menuName);
	}else if($type=='Vertical'){
		$this->newVerticalMenu($menuName);
	}else if($type=='Tree'){
		$this->newTreeMenu($menuName);
	}
   }
   
   function getMenuScript(){
	if($this->type!='Tree'){
		$result  = $this->printHeader();
		$result .= $this->printMenu($this->menuName);
		$result .=$this->printFooter();
	}else{
		$result  = $this->getTreeMenu($this->menuName);
	}

	return $result;
   }
}
?>
