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
	Class: pollAddAnswer
		Add answers to poll.
	
	See Also:
		<ControlBase>
*/
class pollAddAnswer extends ControlBase{

	/*
		Object: $main
			Main class instance
	*/
	var $main;
	
	/*
		Object: $moduleID
			Id of current module
	*/
	var $moduleID;

	/*
   		Function: actionPerform
			Displays/Edits poll
			
			&$skin     - Skin class instance
			$moduleID  - Current module identifier
		
		See Also:
			<Skin>
   */
	function actionPerform(&$skin, $moduleID){
		$this->main     = $skin->main;
		$this->moduleID = $moduleID;
		
		//Assign codeBehind variables
		$this->main->controlVariables["pollAddAnswer"]["moduleId"] = $moduleID;
		$this->main->controlVariables["pollAddAnswer"]["answers"]  = array();
		$this->main->controlVariables["pollAddAnswer"]["added"]    = false;
		
		if(isset($_POST["event"]) && $_POST["event"]=="pollAddAnswer_{$moduleID}" && isset($_POST["answer"])){
			$this->addAnswer();
		}
	}
	
	/*
   		Function: addAnswer
			Add given answer to poll.
   */
	function addAnswer(){
		$query =  "INSERT INTO {$this->main->databaseTablePrefix}poll_answers
						(
						 poll_module_id,
						 value
						)
						VALUES
						(
						  {$this->moduleID},
						  '".addslashes($_POST["answer"])."'
						)";

		$recordSet = $this->main->databaseConnection->Execute($query);
					
		//Check for error, if an error occured then report that error
		if (!$recordSet) {
			trigger_error("Unable to add answer\nreason is : ".$this->main->databaseConnection->ErrorMsg());
		}else{
			$this->main->controlVariables["pollAddAnswer"]["added"]    = true;
		}
	}
}
?>