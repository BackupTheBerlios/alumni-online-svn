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
File: user

Function: smarty_function_user
	Handles {user} function in template
	
Parameters:

	$params  - Parameters given in template
	&$smarty - Skin object that calls this function	

Returns:
	If user has been log in displays username with
a link to allow user edit his/her preferences;
displays '*Guest*' otherwise with no link.
*/
function smarty_function_user($params, &$skin){
	$cssClass  = (empty($params['cssClass'])) ? '' : " class='".$params['cssClass']."'";

	if(session_is_registered("username")){
		$recordSet = $skin->main->databaseConnection->Execute("SELECT  *  FROM {$skin->main->databaseTablePrefix}users WHERE username='".$_SESSION["username"]."'");
		
		//Check for error, if an error occured then report that error
		if (!$recordSet) {
			trigger_error("Unable to get user information\nreason is : ".$skin->main->databaseConnection->ErrorMsg());
			return "";
		}
		
		$rows = $recordSet->GetRows();
		return "<a href='index.php?MyInfo'$cssClass>".$rows[0]["name"]." ".$rows[0]["surname"]."</a>";
	}else{
		return "<a href=''$cssClass>Guest</a>";
	}
}
?>
