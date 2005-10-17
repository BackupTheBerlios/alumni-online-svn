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
	Class: htmlEdit
		Edits html content. Allows authorized users to edit html content.
	Extends ControlBase	for skin entegration.
	
	See Also:
		<ControlBase>
*/
class htmlEdit extends ControlBase{

	/*
   		Function: actionPerform
			Allow authorized users to edit html content.
			
			&$skin     - Skin class instance
			$moduleID  - Current module identifier
		
		See Also:
			<Skin>
   */
	function actionPerform(&$skin, $moduleID){
		$content       = '';
		$updateMessage = '';
		
		if(isset($_POST["event"]) && $_POST["event"]=="htmlEdit_{$moduleID}"){
			$ret = $skin->main->databaseConnection->Replace($skin->main->databaseTablePrefix.'module_settings', 
															array(
																'module_id'     => $moduleID,
																'setting_key'   => 'html_content',
																'setting_value' => $_POST['content']
																),
															array(
																'module_id',
																'setting_key'
																),
															$autoquote = true);
								
			if($ret==1){
				$updateMessage = 'Html content successfully updated!';
			}elseif($ret==2){
				$updateMessage = 'Html content successfully inserted!';
			}else{
				trigger_error("Unable to bind html content information\nreason is : ".$skin->main->databaseConnection->ErrorMsg());
			}		
		}
		
		$query =  "SELECT  *  FROM {$skin->main->databaseTablePrefix}module_settings WHERE setting_key='html_content' AND module_id={$moduleID}";

		$recordSet = $skin->main->databaseConnection->Execute($query);
				
		//Check for error, if an error occured then report that error
		if (!$recordSet) {
			trigger_error("Unable to get html content information\nreason is : ".$skin->main->databaseConnection->ErrorMsg());
		}else{		
			$rows = $recordSet->GetRows();
					
			if(sizeof($rows)==1){
				$content = $rows[0]['setting_value'];
			}
		}
		
		//Assign codeBehind variables
		$skin->main->controlVariables['htmlEdit'] = array(
													'moduleId'    => $moduleID,
													'content'     => $content,
													'infoMessage' => $updateMessage);	
	}
}
?>