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
Class: deleteTab
	Deletes current tab. Administrative custom control that deletes
active tab from the site. Extends ControlBase	for skin entegration.
	
	See Also:
		<ControlBase>
*/
class deleteTab extends ControlBase{

    /*
		Object: $main
			Main class instance
	*/
	var $main;

    /*
		Integer: $tabID
			Tab to add module
	*/
	var $tabID;

	/*	
		Function: actionPerform
			Overrides actionPerform function of ControlBase class. Controls
		postbacks, bind some variables for code behind and deletes tab.
			
		Parameters:
		
			$skin  - Application skin class
			$moduleID  - Current module identifier
			
		See Also:
			<delete>
			<getTabID>
			<Skin>
			<Main>
	*/
	function actionPerform(&$skin, $moduleID){
	    $this->main   = $skin->main;
		$this->tabID  = $this->getTabID($skin->main);
		
		//Assign initial codeBehind variables
		$skin->main->controlVariables["deleteTab"] = array(
															'tabID' 		=> $this->tabID,
															'tabName'		=> ''
														  );
														  
		if(!$this->canUserEdit()){
			trigger_error("Unauthorized access to delete tab '{$this->tabID}' by user group '{$this->main->userGroup}'");
			$skin->main->selectedTab = $skin->main->getInitialTab();
			$skin->main->revalidate  = TRUE;
			return;
		}
		
		if($this->tabID==NULL){
			return;
		}
		
		if(isset($_POST["event"]) && $_POST["event"]=='deleteTab'){
			//If tab deleted, refresh view
			if($this->delete($skin->main, $this->tabID)){
				$skin->main->selectedTab = $skin->main->getInitialTab();
				$skin->main->revalidate  = TRUE;
				$_POST["event"] = '';
			}
		}
		
		$skin->main->controlVariables["deleteTab"] = array(
															'tabID' 		=> $this->tabID,
															'tabName'		=> $this->getTabName($skin->main, $this->tabID)												
														  );
	}
	
	function canUserEdit(){
		$recordSet = $this->main->databaseConnection->Execute("SELECT *  FROM {$this->main->databaseTablePrefix}tabs WHERE  tab_id={$this->tabID} AND administrator_roles LIKE '%;{$this->main->userGroup};%'");

		//Check for error, if an error occured then report that error
		if (!$recordSet) {
			trigger_error("Unable to check user right for tab '{$this->tabID}'\nreason is : ".$this->main->databaseConnection->ErrorMsg());
			return false;
		}else{
			return (sizeof($recordSet->GetRows())==1);
		}
	}
	
	/*	
		Function: delete
			Deletes given tab from the site.
		
		Parameters:
			$main	- Alumni-Online's main class instance
			$tabID	- Id of the tab to delete
			
		Returns:
			True if delete succeed; false otherwise.
	*/
	function delete($main, $tabID){
		$recordSet = $main->databaseConnection->Execute("DELETE FROM {$main->databaseTablePrefix}tabs WHERE tab_id=$tabID");
			
		//Check for error, if an error occured then report that error
		if (!$recordSet) {
			trigger_error("Unable to delete tab\nreason is : ".$main->databaseConnection->ErrorMsg());
			return false;
		}
			
		return true;
	}
	
	function getTabName($main, $tabID){
		$recordSet = $main->databaseConnection->Execute("SELECT tab_name FROM {$main->databaseTablePrefix}tabs WHERE tab_id=$tabID");
			
		//Check for error, if an error occured then report that error
		if (!$recordSet) {
			trigger_error("Unable to get tab name\nreason is : ".$main->databaseConnection->ErrorMsg());
			return '';
		}
			
		$rows = $recordSet->GetRows();
		if(sizeof($rows)==1){
			return $rows[0]["tab_name"];
		}
		
		return '';
	}
	
	
	/*	
		Function: getTabID
			Finds tab id to delete.
			
		Returns:
			TabID given via queryString,  NULL otherwise.
			
		Note:
			This function parses supplied queryString. QueryString must be
		in the following format; DeleteTab:1 where 1 is the tab id.
		
		See Also:
			<Main>
	*/
	function getTabID($main){
		//Get tab id from query string
		//return empty string on fail!
		if(isset($_SERVER['QUERY_STRING'])){
			$commands = explode(':' ,$_SERVER['QUERY_STRING']);
			if($commands[0]=='DeleteTab' && $main->checkString('[^0-9]', $commands[1])){
				return $commands[1];
			}else{
				trigger_error("Tab id to delete is not supplied!");
				return NULL;
			}
		}else{
			trigger_error("Tab id to delete is not supplied!");
			return NULL;
		}
	}
}
?>