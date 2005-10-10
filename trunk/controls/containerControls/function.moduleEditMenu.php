<?php
#####################################################################################
#        Alumni-OnLine: Alumni Organization Web Management System                   #
#####################################################################################
#                                                                                   #
# Copyright © 2004 by Fatih BOY		                                                #
# http://alumni-online.enterprisecoding.com/                                        #
#                                                                                   #
# This program is free software. You can redistribute it and/or modify              #
# it under the terms of the GNU General Public License as published by              #
# the Free Software Foundation; either version 2 of the License.                    #
#####################################################################################

/*
File: moduleEditMenu

Function: smarty_function_moduleEditMenu
	Handles {moduleEditMenu} function in template
	
Parameters:

	$params  - Parameters given in template
	&$container - Container object that calls this function	

Returns:
	Displays edit menu for module
*/
function smarty_function_moduleEditMenu($params, &$container){
	$specialModules = array_flip($container->main->getSpecialModules());
	
	if(!isset($specialModules[$container->main->selectedTab]) && canUserEditModule($container->main, $container->moduleID)){
		$result = getVariables($container->moduleID);
		$result .= getMenu($container->main, $container->moduleID);
	
		return $result;
	}else{
		return '';
	}
}

/*
	Function: getMenu
		Generates editing menu. This menu allows authorized users to edit module.
			
		$main     - Main class of Alumni-Online
		$moduleID - Module id to generate menu
			
	Returns:
		If user has right to edit given module, return module editing
	menu.
	
	See Also:
		<getModulePane>
		<getModuleControls>
*/
function getMenu($main, $moduleID){	
	$modulePane = getModulePane($main, $moduleID);
	
	$result = "<ilayer>\n";
	$result .= "	<layer visibility=show>\n";
	$result .= "		<div class=ModuleMenuWrap1>\n";
	$result .= "			<span class=ModuleMenuWrap2 onClick=\"dropit(event, 'dropmenu{$moduleID}');event.cancelBubble=true;return false\">\n";
	$result .= "				<a href=\"#\" onClick=\"if(ns4) return dropit(event, 'document.dropmenu{$moduleID}')\"><img src='templates/skins/{$main->applicationSettings['theme']}/images/edit.gif' border='0'></a>\n";
	$result .= "			</span>\n";
	$result .= "		</div>\n";
	$result .= "	</layer>\n";
	$result .= "</ilayer>\n";
		
	$result .= "<div id=dropmenu{$moduleID} class='ModuleMenu'>\n";
	$result .= " 	<table width='100%'  border='0' cellspacing='2' cellpadding='0'>\n";
		
	$controls = getModuleControls($main, $moduleID);
	foreach($controls  as $key => $value){
		$result .= "  		<tr>\n";
		$result .= "    		<td>&nbsp;</td>\n";
		$result .= "    		<td nowrap><a href='index.php?DisplayModule:{$moduleID}:{$value}' class=ModuleMenuLink>$key</a></td>\n";
		$result .= "  		</tr>\n";
	}
	
	if(sizeof($controls)>0){
		$result .= "  		<tr>\n";
		$result .= "    		<td colspan='2'><hr></td>\n";
		$result .= "  		</tr>\n";
	}
	
	if(getModuleDefinitionId($main, $moduleID)>=0){
		$result .= "  		<tr>\n";
		$result .= "    		<td><img src='templates/skins/{$main->applicationSettings['theme']}/images/editModule.gif'></td>\n";
		$result .= "    		<td nowrap><a href='index.php?EditModule:{$moduleID}' class=ModuleMenuLink>Module Settings</a></td>\n";
		$result .= "  		</tr>\n";
		$result .= "  		<tr>\n";
		$result .= "    		<td><img src='templates/skins/{$main->applicationSettings['theme']}/images/delete.gif'></td>\n";
		$result .= "    		<td nowrap><a href='index.php?DeleteModule:{$moduleID}' class=ModuleMenuLink>Delete Module</a></td>\n";
		$result .= "  		</tr>\n";
		$result .= "  		<tr>\n";
		$result .= "    		<td colspan='2'><hr></td>\n";
		$result .= "  		</tr>\n";
	}
		
	$result .= "   		<tr>\n";
	$result .= "    		<td><img src='templates/skins/{$main->applicationSettings['theme']}/images/arrowUp.gif'></td>\n";
	$result .= "    		<td nowrap><a href='index.php?MoveModule:Up:{$moduleID}' class=ModuleMenuLink>Move Up</a></td>\n";
	$result .= "  		</tr>\n";
	
	if($modulePane!='leftpane'){
		$result .= "  		<tr>\n";
		$result .= "    		<td><img src='templates/skins/{$main->applicationSettings['theme']}/images/arrowLeft.gif'></td>\n";
		$result .= "    		<td nowrap><a href='index.php?MoveModule:Left:{$moduleID}' class=ModuleMenuLink>Move to LeftPane</a></td>\n";
		$result .= "  		</tr>\n";
	}
		
	if($modulePane!='contentpane'){
		$result .= "   		<tr>\n";
		$result .= "    		<td><img src='templates/skins/{$main->applicationSettings['theme']}/images/menuBullet.gif'></td>\n";
		$result .= "    		<td nowrap><a href='index.php?MoveModule:Content:{$moduleID}' class=ModuleMenuLink>Move to ContentPane</a></td>\n";
		$result .= "  		</tr>\n";
	}
	
	if($modulePane!='rightpane'){
		$result .= "   		<tr>\n";
		$result .= "    		<td><img src='templates/skins/{$main->applicationSettings['theme']}/images/arrowRight.gif'></td>\n";
		$result .= "    		<td nowrap><a href='index.php?MoveModule:Right:{$moduleID}' class=ModuleMenuLink>Move to RightPane</a></td>\n";
		$result .= "  		</tr>\n";
	}
	
		$result .= "   		<tr>\n";
		$result .= "    		<td><img src='templates/skins/{$main->applicationSettings['theme']}/images/arrowDown.gif'></td>\n";
		$result .= "    		<td nowrap><a href='index.php?MoveModule:Down:{$moduleID}' class=ModuleMenuLink>Move Down</a></td>\n";
		$result .= "  		</tr>\n";		
	
	$result .= "	</table>\n";	
	
	$result .= "	<script language='JavaScript'>\n";
	$result .= "		if (document.all)\n";
	$result .= "			dropmenu{$moduleID}.style.padding='4px'\n";
	$result .= "	</script>\n";
	$result .= "</div>\n";
	
	return $result;
}

/*
	Function: getModuleControls
		Generates list of avaliable controls for the given module.
			
		$main     - Main class of Alumni-Online
		$moduleID - Module id to list controls
			
	Returns:
		Avaliable control list with control_title as key and
	control_key as value.
*/
function getModuleControls($main, $moduleID){
	$result = array();
	$recordSet = $main->databaseConnection->Execute("SELECT  controls.control_key, controls.control_title  
														FROM {$main->databaseTablePrefix}modules AS modules, 
															 {$main->databaseTablePrefix}module_controls AS controls 
														WHERE 
															modules.module_definition_id = controls.module_definition_id 
															AND controls.control_key IS NOT NULL 
															AND modules.module_id = $moduleID
														ORDER BY 
															controls.control_key");

	//Check for error, if an error occured then report that error
	if (!$recordSet) {
		trigger_error("Unable to get module controls\nreason is : ".$main->databaseConnection->ErrorMsg());
		die();
	}
	  
	while (!$recordSet->EOF) {
		$result[$recordSet->fields['control_title']] = $recordSet->fields['control_key'];
		$recordSet->MoveNext();
	}
	
	return $result;
}

function getModuleDefinitionId($main, $moduleID){
	$recordSet = $main->databaseConnection->Execute("SELECT  modules.module_definition_id 
														FROM {$main->databaseTablePrefix}modules AS modules, 
															 {$main->databaseTablePrefix}module_controls AS controls 
														WHERE 
															modules.module_definition_id = controls.module_definition_id 
															AND modules.module_id = $moduleID");

	//Check for error, if an error occured then report that error
	if (!$recordSet) {
		trigger_error("Unable to get module definition id\nreason is : ".$main->databaseConnection->ErrorMsg());
		die();
	}
	  
	$rows = $recordSet->GetRows();
	if(sizeof($rows)>0){
		return $rows[0]["module_definition_id"];
	}
	
	return 0;
}

/*
	Function: getModulePane
		Finds pane name that module placed.
			
		$main     - Main class of Alumni-Online.
		$moduleID - Module id to get pane name.
			
	Returns:
		Name of the pane that module placed.
*/
function getModulePane($main, $moduleID){
	$result = array();
	$recordSet = $main->databaseConnection->Execute("SELECT pane_name FROM {$main->databaseTablePrefix}modules WHERE module_id = $moduleID");

	//Check for error, if an error occured then report that error
	if (!$recordSet) {
		trigger_error("Unable to get module pane\nreason is : ".$main->databaseConnection->ErrorMsg());
		die();
	}
	  
	$rows = $recordSet->GetRows();
	if(count($rows)==1){
		return strtolower($rows[0]['pane_name']);
	}
	
	return '';
}

/*
	Function: canUserEditModule
		Indicates whether user authorized to edit module.
			
		$main     - Main class of Alumni-Online
		$moduleID - Module id to check authentication
			
	Returns:
		True if user authorized to edit module, false otherwise.
*/
function canUserEditModule($main, $moduleID){	
	$recordSet = $main->databaseConnection->Execute("SELECT pane_name FROM {$main->databaseTablePrefix}modules WHERE module_id = $moduleID AND (administrator_roles LIKE '%;".$main->userGroup.";%' OR administrator_roles LIKE '%;1;%')");

	//Check for error, if an error occured then report that error
	if (!$recordSet) {
		trigger_error("Unable to check whether user can edit\nreason is : ".$main->databaseConnection->ErrorMsg());
		return FALSE;
	}
	  
	$rows = $recordSet->GetRows();
	if(count($rows)==1){
		return TRUE;
	}
	
	return FALSE;
}

/*
	Function: getVariables
		Generates javascript to display edit menu.

		$moduleID - Module id to generate script.
			
	Returns:
		Generated javaScript.
*/
function getVariables($moduleID){
	$i = 0;
	$result = "<script language='JavaScript'>\n";
	$result .= "	if (document.layers){\n";
	$result .= "		document.dropmenu{$moduleID}.captureEvents(Event.CLICK)\n";
	$result .= "		document.dropmenu{$moduleID}.onclick=hidemenu\n";
	$result .= "	}\n";
	$result .= "</script>\n";
	
	return $result;
}
?>