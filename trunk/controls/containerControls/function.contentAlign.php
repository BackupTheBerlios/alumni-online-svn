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
File: contentAlign

Function: smarty_function_contentAlign
	Handles {contentAlign} function in template
	
Parameters:

	$params  - Parameters given in template
	&$container - Container object that calls this function	

Returns:
	Alignment setting of current module within container
*/
function smarty_function_contentAlign($params, &$container){
	$recordSet = $container->main->databaseConnection->Execute("SELECT alignment  
											FROM {$container->main->databaseTablePrefix}modules
											WHERE module_id = {$container->moduleID}");
	//Check for error, if an error occured then report that error
	if (!$recordSet) {
		trigger_error("Unable to get module alignment\nreason is : ".$this->main->databaseConnection->ErrorMsg());
	}else{
		$rows = $recordSet->GetRows();
		if(sizeof($rows)==1){
			return "align='".$rows[0]["alignment"]."'";
		}
	}
	
	return '';
}
?>