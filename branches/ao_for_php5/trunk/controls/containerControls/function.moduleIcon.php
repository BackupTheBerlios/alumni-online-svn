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
File: moduleIcon

Function: smarty_function_moduleIcon
	Handles {moduleIcon} function in template
	
Parameters:

	$params  - Parameters given in template
	&$container - Container object that calls this function	

Returns:
	Displays user defined module icon
*/
function smarty_function_moduleIcon($params, &$container){
	$recordSet = $container->main->databaseConnection->Execute("SELECT module_icon  
											FROM {$container->main->databaseTablePrefix}modules
											WHERE module_id = {$container->moduleID}");
	//Check for error, if an error occured then report that error
	if (!$recordSet) {
		trigger_error("Unable to get module title\nreason is : ".$this->main->databaseConnection->ErrorMsg());
	}else{
		$rows = $recordSet->GetRows();
		if(sizeof($rows)==1 && $rows[0]["module_icon"]!=NULL){
			return "<img src='images/".$rows[0]["module_icon"]."'>";
		}
	}
	
	return '';
}
?>