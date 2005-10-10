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
	Class: poll
		Displays poll.
	
	See Also:
		<ControlBase>
*/
class poll extends ControlBase{

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
			Displays poll for voting.
			
			&$skin     - Skin class instance
			$moduleID  - Current module identifier
		
		See Also:
			<Skin>
   */
	function actionPerform(&$skin, $moduleID){
		$this->main     = $skin->main;
		$this->moduleID = $moduleID;
		$answered       = isset($_COOKIE["vote4_$moduleID"]);
		
		$skin->main->controlVariables["poll"]["question"] = "";
		$skin->main->controlVariables["poll"]["answered"] = $answered;
		$skin->main->controlVariables["poll"]["tabId"]    = $skin->main->selectedTab;
		$skin->main->controlVariables["poll"]["moduleId"] = $moduleID;
		
		$this->setPoll();
		
		if(isset($_POST["event"]) && $_POST["event"]=="poll_{$moduleID}" && isset($_POST["answer"])){
			if(!isset($_COOKIE["vote4_$moduleID"])){
				$this->voteFor($_POST["answer"]);
				setcookie("vote4_$moduleID", '1', time()+3600);
			}
		}
	
		if($answered){
			$this->displayScores();
		}
	}
	
	/*
   		Function: voteFor
			Increments score for given answer.
			
			$answerId    - Answer to increment score.
   */
	function voteFor($answerId){
		$this->main->databaseConnection->Execute("UPDATE {$this->main->databaseTablePrefix}poll_answers SET score = score +1 WHERE poll_answers_id=$answerId");
	}
	
	/*
   		Function: setPoll
			Bind poll question and answers.
   */
	function setPoll(){
		$query =  "SELECT  *  FROM {$this->main->databaseTablePrefix}poll_questions WHERE poll_module_id ={$this->moduleID}";

		$recordSet = $this->main->databaseConnection->Execute($query);
					
		//Check for error, if an error occured then report that error
		if ($recordSet) {
			$rows = $recordSet->GetRows();
			
			if(sizeof($rows)==1){
				//Assign codeBehind variables
				$this->main->controlVariables["poll"]["question"] = $rows[0]["question"];
				
				$query2 =  "SELECT  *  FROM {$this->main->databaseTablePrefix}poll_answers WHERE poll_module_id ={$this->moduleID}";

				$recordSet2 = $this->main->databaseConnection->Execute($query2);
				$this->main->controlVariables["poll"]["answers"] = $recordSet2->GetRows();
			}
		}
	}
	
	/*
   		Function: displayScores
			Displays scores for current question
   */
	function displayScores(){
		$query =  "SELECT  SUM(score) AS score_sum  FROM {$this->main->databaseTablePrefix}poll_answers WHERE poll_module_id ={$this->moduleID} GROUP BY poll_module_id";
	
		$recordSet = $this->main->databaseConnection->Execute($query);
					
		//Check for error, if an error occured then report that error
		if ($recordSet) {
			$rows = $recordSet->GetRows();
			
			if(sizeof($rows)==1){
				//Assign codeBehind variables
				$scoreSum = $rows[0]["score_sum"];
				
				$query2 =  "SELECT  *, (score*100/$scoreSum) AS percent  FROM {$this->main->databaseTablePrefix}poll_answers WHERE poll_module_id ={$this->moduleID}";

				$recordSet2 = $this->main->databaseConnection->Execute($query2);
				$this->main->controlVariables["poll"]["result"] = $recordSet2->GetRows();
			}
		}
	}
}
?>