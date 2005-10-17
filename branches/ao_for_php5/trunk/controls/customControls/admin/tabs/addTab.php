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
Class: addTab
		Adds new tab. Administrative custom control that adds new tab to the site.
	Extends ControlBase	for skin entegration.
	
	See Also:
		<ControlBase>
*/
class addTab extends ControlBase{

	/*	
		Function: actionPerform
			Overrides actionPerform function of ControlBase class. Controls
		postbacks, bind some variables for code behind and adds new tab.
			
		Parameters:
		
			$skin  - Application skin class
			$moduleID  - Current module identifier
			
		See Also:
			<Skin>
			<Main>
	*/
	function actionPerform(&$skin, $moduleID){
		$displayForm  	= true;
		$tabList 		= array();
		
		//Assign codeBehind variables
		$skin->main->controlVariables["addTab"] = array(
													'theme'			=> $skin->main->applicationSettings['theme'],
													'displayForm'	=> $displayForm,
													'tabList' 		=> $tabList,
													'groupList'     => $this->getGroupList($skin->main),
													'tabnameError'  => false,
													'titleError'    => false,
													'editRolesError'=> false,
													'viewRolesError'=> false);
		
		//Detect postback
		if(isset($_POST["event"]) && $_POST["event"]=='addTab'){
			if($this->isReady2Add($skin->main)){
				$isHidden = isset($_POST["ishidden"]) ? 0 : 1;
	
				$recordSet = $skin->main->databaseConnection->Execute("SELECT MAX( tab_order ) AS max_tab_order FROM {$skin->main->databaseTablePrefix}tabs WHERE tab_order < 10000 ");
				
				//Check for error, if an error occured then report that error
				if (!$recordSet) {
					trigger_error("Unable to find maximum tab_order\nreason is : ".$skin->main->databaseConnection->ErrorMsg());
				}
				
				$rows = $recordSet->GetRows();
				$maxTabOrder = $rows[0]['max_tab_order'] + 1;
				
				$authorizedRoles    = ';'.str_replace(',' , ';', $_POST["viewUsersList"]).';';
				$administratorRoles = ';'.str_replace(',' , ';', $_POST["editUsersList"]).';';
	
				$recordSet = $skin->main->databaseConnection->Execute("INSERT INTO {$skin->main->databaseTablePrefix}tabs 
																	(
																		tab_order,
																		tab_name, 
																		authorized_roles, 
																		administrator_roles, 
																		parent_id, 
																		is_visible, 
																		title, 
																		description, 
																		keywords
																	)
																	VALUES
																	(
																		$maxTabOrder,
																		'".addslashes($_POST["tabname"])."', 
																		'$authorizedRoles', 
																		'$administratorRoles', 
																		".$_POST["parenttab"].", 
																		$isHidden, 
																		'".addslashes($_POST["title"])."', 
																		'".addslashes($_POST["description"])."', 
																		'".addslashes($_POST["keywords"])."'
																	)");
						
				//Check for error, if an error occured then report that error
				if (!$recordSet) {
					trigger_error("Unable to add new tab\nreason is : ".$skin->main->databaseConnection->ErrorMsg());
				}else{
					$recordSet2 = $skin->main->databaseConnection->Execute("SELECT *  
																			FROM {$skin->main->databaseTablePrefix}tabs  
																			WHERE
																				tab_order = $maxTabOrder
																				AND
																				tab_name = '".addslashes($_POST["tabname"])."'");
					if ($recordSet2) {
						$rows = $recordSet2->GetRows();
						if(sizeof($rows)==1){
							$skin->main->selectedTab = $rows[0]["tab_id"];
							$skin->main->revalidate  = TRUE;
						}
					}
				}
			}
		}
			
		$recordSet = $skin->main->databaseConnection->Execute("SELECT *  
														FROM {$skin->main->databaseTablePrefix}tabs  
														WHERE  
															parent_id=0  
															AND tab_id>0
															AND (tab_order BETWEEN 0 AND 100000)
														ORDER BY  
															tab_order");
		//Check for error, if an error occured then report that error
		if (!$recordSet) {
			trigger_error("Unable to get component list\nreason is : ".$skin->main->databaseConnection->ErrorMsg());
		}else{		
			while (!$recordSet->EOF) {
				$tabList[$recordSet->fields["tab_id"]] = $recordSet->fields["tab_name"];
				$recordSet->MoveNext();
			}
		}
	}
	
	/*	
		Function: isReady2Add
			Checks whether tab is ready to add. 
			
		Returns:
			True if all required fields filled; false otherwise.
	*/
	function isReady2Add($main){
		$result = true;
		
		if($_POST["tabname"]==""){
			$result = false;
			$main->controlVariables["addTab"]["tabnameError"] = true;
		}
		
		if($_POST["title"]==""){
			$result = false;
			$main->controlVariables["addTab"]["titleError"] = true;
		}
		
		if($_POST["editUsersList"]==""){
			$result = false;
			$main->controlVariables["addTab"]["editRolesError"] = true;
		}
		
		if($_POST["viewUsersList"]==""){
			$result = false;
			$main->controlVariables["addTab"]["viewRolesError"] = true;
		}
		
		return $result;
	}
	
	/*	
		Function: getGroupList
			Gets user group list.
		
			$main - Alumni-Online's main class. Used for database queries.
			
		Returns:
			Avaliable user groups.
			
		See Also:
			<Main>
	*/
	function getGroupList($main){
		$recordSet = $main->databaseConnection->Execute("SELECT *  FROM {$main->databaseTablePrefix}user_groups WHERE user_group_id<>1");
			
		//Check for error, if an error occured then report that error
		if (!$recordSet) {
			trigger_error("Unable to get user group list\nreason is : ".$main->databaseConnection->ErrorMsg());
		}
			
		$rows = $recordSet->GetRows();		
		return $rows;
	}
}
?>