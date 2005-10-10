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
File: adminHeader

Function: smarty_function_adminHeader
	Handles {adminHeader} function in template
	
Parameters:

	$params  - Parameters given in template
	&$smarty - Skin object that calls this function	

Returns:
	Display administrative tab editing options to administrator
*/
function smarty_function_adminHeader($params, &$skin){
	$recordSet1 = $skin->main->databaseConnection->Execute("SELECT  * 
															FROM  {$skin->main->databaseTablePrefix}tabs 
															WHERE
																tab_id >0
																AND (administrator_roles LIKE '%;{$skin->main->userGroup};%' OR administrator_roles LIKE '%;1;%')
																AND tab_id = {$skin->main->selectedTab}");

	//Check for error, if an error occured then report that error
	if (!$recordSet1) {
		trigger_error("smarty_function_adminHeader : Unable to check user authentication\nreason is : ".$skin->main->databaseConnection->ErrorMsg());
		return "";
	}
	
	$rows1 = $recordSet1->GetRows();
	
	if(count($rows1)==1){
		$result = "<form name='moduleForm' method='post' action='index.php?AddModule:{$skin->main->selectedTab}'>\n";
		$result .= "<table width='100%' height='10' border='0' cellspacing='0' cellpadding='0' class='AdminHeaderTable'>\n";
		$result .= "  <tr  valign='middle'>\n";
		$result .= "    <td width='40'>Tab :</td>\n";		
		
		//Only administrators can add new tab!
		if($skin->main->userGroup==2){
			$result .= "    <td width='20'>&nbsp;<a href='index.php?AddTab' class='AdminHeaderLink'>Add</a></td>\n";
		}
		
		$result .= "    <td width='20'>&nbsp;<a href='index.php?EditTab:{$skin->main->selectedTab}' class='AdminHeaderLink'>Edit</a></td>\n";
		$result .= "    <td width='20'>&nbsp;<a href='index.php?DeleteTab:{$skin->main->selectedTab}' class='AdminHeaderLink'>Delete</a></td>\n";
		$result .= "    <td width='80%' align='right'>&nbsp;Module :\n";
		$result .= "        <select name='moduleID'>\n";
		
		$recordSet = $skin->main->databaseConnection->Execute("SELECT  *  FROM  {$skin->main->databaseTablePrefix}module_definitons WHERE module_definition_id>0");

		//Check for error, if an error occured then report that error
		if (!$recordSet) {
			trigger_error("smarty_function_adminHeader: Unable to get component list\nreason is : ".$skin->main->databaseConnection->ErrorMsg());
			return "";
		}
		
		while (!$recordSet->EOF) {
			$result .= "          <option value='".$recordSet->fields["module_definition_id"]."'>".$recordSet->fields["friendly_name"]."</option>\n";
			$recordSet->MoveNext();
		}
	
		$result .= "        </select>\n";
		$result .= "        <input name='add' type='submit' id='add' value='Add' class='NormalTextBox'>\n";
		$result .= "      </td>\n"; 
		$result .= "  </tr>\n"; 
		$result .= "</table>\n";
		$result .= "</form>\n";
		
		return $result;
	}
	
	return '';
}
?>
