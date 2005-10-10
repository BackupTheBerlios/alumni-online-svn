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
	Class: myInfo
		Allows user to view/edit personal information. Extends ControlBase
	for skin entegration.
	
	See Also:
		<ControlBase>
*/
class myInfo extends ControlBase{

	/*
   		Function: actionPerform
			Displays personal information form.	
			
			&$skin     - Skin class instance
			$moduleID  - Current module identifier
		
		See Also:
			<Skin>
   */
	function actionPerform(&$skin, $moduleID){
		$name=''; $surname=''; $email=''; $icq=''; $msn=''; $yahoo='';
		$query =  "SELECT  *  FROM {$skin->main->databaseTablePrefix}users WHERE username='".addslashes($_SESSION["username"])."'";
		$recordSet = $skin->main->databaseConnection->Execute($query);
				
		//Check for error, if an error occured then report that error
		if (!$recordSet) {
			trigger_error("Unable to get user information\nreason is : ".$skin->main->databaseConnection->ErrorMsg());
			$displayForm = true;
		}else{		
			$rows = $recordSet->GetRows();
					
			if(sizeof($rows)==1){
				$name    = $rows[0]['name'];
				$surname = $rows[0]['surname'];	
				$email   = $rows[0]['email'];
				$icq     = $rows[0]['icq'];
				$msn     = $rows[0]['msn'];
				$yahoo   = $rows[0]['yahoo'];				
			}
		}
		//Assign codeBehind variables
		$skin->main->controlVariables["myInfo"] = array(
													'name'    => $name,
													'surname' => $surname,
													'email'   => $email,
													'icq'     => $icq,
													'msn'     => $msn,
													'yahoo'   => $yahoo);
	}
}
?>