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
	Class: pollEditQuestion
		Edits poll's question.
	
	See Also:
		<ControlBase>
*/
class pollEditQuestion extends ControlBase{

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
		$this->main->controlVariables["pollEditQuestion"]["moduleId"] = $moduleID;
		$this->main->controlVariables["pollEditQuestion"]["updated"] = false;
		$this->main->controlVariables["pollEditQuestion"]["question"] = "";		
		
		if(isset($_POST["event"]) && $_POST["event"]=="pollEditQuestion_{$moduleID}"){
			if($this->isQuestionExist()){
				$this->updateQuestion();
			}else{
				$this->insertQuestion();
			}
		}		
		
		$this->setPollQuestion();
	}
	
	/*
   		Function: isQuestionExist
			Checks whether a question entry exist for current module.
   */
	function isQuestionExist(){
		$query =  "SELECT  *  FROM {$this->main->databaseTablePrefix}poll_questions WHERE poll_module_id ={$this->moduleID}";

		$recordSet = $this->main->databaseConnection->Execute($query);
					
		//Check for error, if an error occured then report that error
		if ($recordSet) {
			$rows = $recordSet->GetRows();
			
			return (sizeof($rows)==1);
		}
		
		return false;
	}
	
	/*
   		Function: updateQuestion
			Updates poll's question.
   */
	function updateQuestion(){
		$query =  "UPDATE {$this->main->databaseTablePrefix}poll_questions
					SET
						question = '".addslashes($_POST["question"])."'
				  	WHERE poll_module_id ={$this->moduleID}";

		$recordSet = $this->main->databaseConnection->Execute($query);
					
		//Check for error, if an error occured then report that error
		if (!$recordSet) {
			trigger_error("Unable to update poll question\nreason is : ".$this->main->databaseConnection->ErrorMsg());
		}else{
			$this->main->controlVariables["pollEditQuestion"]["updated"] = true;
		}
	}
	
	/*
   		Function: insertQuestion
			Insert poll question into database
   */
	function insertQuestion(){
		$query =  "INSERT INTO {$this->main->databaseTablePrefix}poll_questions
						(
							poll_module_id,
							question
						)
						VALUES
						(
							{$this->moduleID},
							'".addslashes($_POST["question"])."'
						)";

		$recordSet = $this->main->databaseConnection->Execute($query);
					
		//Check for error, if an error occured then report that error
		if (!$recordSet) {
			trigger_error("Unable to insert poll question\nreason is : ".$this->main->databaseConnection->ErrorMsg());
		}else{
			$this->main->controlVariables["pollEditQuestion"]["updated"] = true;
		}
	}
	
	/*
   		Function: setPollQuestion
			Binds poll question.
   */
	function setPollQuestion(){
		$query =  "SELECT  *  FROM {$this->main->databaseTablePrefix}poll_questions WHERE poll_module_id ={$this->moduleID}";

		$recordSet = $this->main->databaseConnection->Execute($query);
					
		//Check for error, if an error occured then report that error
		if ($recordSet) {
			$rows = $recordSet->GetRows();
			
			if(sizeof($rows)==1){
				//Assign codeBehind variables
				$this->main->controlVariables["pollEditQuestion"]["question"] = $rows[0]["question"];
			}
		}
	}
}
?>