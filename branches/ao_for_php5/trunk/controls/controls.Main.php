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

require_once('controls.Settings.php');
require_once('controls.Logger.php');
require_once('controls.Skin.php');
require_once('controls.Event.php');

require_once(DIR_CONTRIB.'adodb'.DIRECTORY_SEPARATOR.'adodb-errorhandler.inc.php');
require_once(DIR_CONTRIB.'adodb'.DIRECTORY_SEPARATOR.'adodb.inc.php');

/*
Class: Main
	Alumni-Online's main class

Note:
	Main class performs central operations. index.php file will use this class
to comminicate with Alumni-Online's framework.
*/
class Main{
	/*
	 	Object: $databaseConnection
			Used for performing database operations
	*/
	var $databaseConnection;	
	
	/*
	 	Array: $applicationSettings
			Holds application settings
			
		See Also:
			<readSettings>
	*/
	var $applicationSettings = array('theme' => 'default');
	
	/*
	 	Integer: $selectedTab
			Holds active tab information
			
		See Also:
			<getSelectedTab>
	*/
	var $selectedTab = 0;
	
	/*
	 	Integer: $userGroup
			Holds group of current user
			
		See Also:
			<getUserGroup>
	*/
	var $userGroup = 0;
	
	/*
	 	Integer: $displayModuleId
			Holds module id for displayModule mode.
		Initial value is -1, which means system is not
		in display module.
	*/
	var $displayModuleId = -1;
	
	/*
	 	Integer: $displayModuleKey
			Holds module key for displayModule mode.
	*/
	var $displayModuleKey = '';
	
	/*
	 	Object: $eventHandler
			Handles application events
	*/
	var $eventHandler;
	
	/*
	 	String: $databaseTablePrefix
			Hold database table prefix
	*/
	var $databaseTablePrefix = DATABASE_TABLE_PREFIX;
	
	/*
	 	Array: $controlVariables
			Holds application control variable. This variable
		will be used by skins and code behind.
	*/
	var $controlVariables = array();
	
	
	/*
	 	Boolean: $revalidate
			Indicates that skin should re-validated
			
		See Also:
			<displayOutput>
	*/
	var $revalidate = TRUE;
	
	/*
	 	Object: $logger
			Logs system errors.
	*/
	var $logger;
	
	/*
		Constructor: Main
			Default constructer
			
		Remarks:
			Initializes Main class and makes database connection
		
	*/
	function Main(){		
		$this->logger = new logger();
		$this->databaseConnection = ADONewConnection(DATABASE_TYPE);
		$this->databaseConnection->autoRollback = true;
		$this->databaseConnection->Connect(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_NAME);
		$this->databaseConnection->SetFetchMode(ADODB_FETCH_ASSOC);

		$this->userGroup = $this->getUserGroup();		
		$this->selectedTab = $this->getSelectedTab();
		$this->readSettings();
		
		$this->eventHandler = new Event();
		$this->eventHandler->main = &$this;
	}
	
	/*
		Function: getUserGroup
			Gives current user's group
			
		Returns:
			Group ID for currentUser
	*/
	function getUserGroup(){
		if(session_is_registered("username")){
			$recordSet = $this->databaseConnection->Execute("SELECT * FROM {$this->databaseTablePrefix}users WHERE username='".$_SESSION["username"]."'");
	  
		  	//Check for error, if an error occured then report that error
		  	if (!$recordSet) {
	  			trigger_error("Unable to get settings\nreason is : ".$this->databaseConnection->ErrorMsg());
				return 0;
		  	}
	  
	  		$rows = $recordSet->GetRows();	
			if(sizeof($rows)==1){
				return $rows[0]["user_group_id"];
			}
		}
		
		return 0;
	}
	
	/*
		Function: checkString
			Validates given string according to reqular expression
			
			$validationExpression - Regular expression to check for
			$subject              - Input to check for validation
			
		Returns:
			boolean. True if subject passes given expression
		false otherwise
	*/
	function checkString($validationExpression, $subject){
		$checkedSubject = ereg_replace($validationExpression, '', $subject);
		return ($checkedSubject==$subject);
	}
	
	/*
		Function: getSelectedTab
			Gets selected tab id
			
		Returns:
	      Selected tab Id
	*/
	function getSelectedTab(){
		$specialModules = $this->getSpecialModules();
		
		//Check whether there exists 'tab' variable in get array
		//also we are expecting 'tab' to be a numeric value which 
		//is greater then zero	
		if(isset($_GET["tab"]) && is_numeric($_GET["tab"]) && $_GET["tab"]>0){
			//Check whether user has right to see tab
  			$recordSet = $this->databaseConnection->Execute("SELECT * FROM {$this->databaseTablePrefix}modules WHERE tab_id={$_GET['tab']} AND authorized_roles LIKE '%;1;%' OR authorized_roles LIKE '%;{$this->userGroup};%'");

			//Check for error, if an error occured then report that error
			if ($recordSet) {
				if($this->databaseConnection->Affected_Rows()>0){
					return $_GET["tab"];
				}
			}
		}else{			
			//validate query string for special modules
			if(isset($_SERVER['QUERY_STRING'])){
				$commands = explode(':' ,$_SERVER['QUERY_STRING']);
			 	if($this->checkString('[^a-zA-Z0-9]', $commands[0])){
					$queryString = $commands[0];
					
					//DisplayModule is a special keyword
					//for instracting Alumni-Online to display module 
					//with the given module key for given module id
					if($queryString=="DisplayModule" && $this->checkString('[^0-9]', $commands[1]) && $this->checkString('[^a-zA-Z0-9]', $commands[2])){
						$this->displayModuleId  = $commands[1];
						$this->displayModuleKey = $commands[2];
						$recordSet = $this->databaseConnection->Execute("SELECT tab_id FROM {$this->databaseTablePrefix}modules WHERE module_id={$commands[1]}");

						//Check for error, if an error occured then report that error
						if ($recordSet) {
							$rows = $recordSet->GetRows();

							if(sizeof($rows)>0){
								return $rows[0]['tab_id'];
							}
						}
					}
					else if(isset($specialModules[$queryString])){
						//Check whether user has right to see tab
						$recordSet = $this->databaseConnection->Execute("SELECT * FROM {$this->databaseTablePrefix}modules WHERE tab_id={$specialModules[$queryString]} AND (authorized_roles LIKE '%;1;%' OR authorized_roles LIKE '%;{$this->userGroup};%')");

						//Check for error, if an error occured then report that error
						if ($recordSet) {
							if($this->databaseConnection->Affected_Rows()>0){
								return $specialModules[$queryString];
							}
						}
					}
				}
			}					
		}
		
		return $this->getInitialTab();
	}	
	
	/*
		Function: getInitialTab
			Finds initial tab for the web site
			
		Returns:
	      Initial tab id of the web site
	*/
	function getInitialTab(){
		$recordSet = $this->databaseConnection->Execute("SELECT tab_id FROM {$this->databaseTablePrefix}tabs WHERE (authorized_roles LIKE '%;1;%' OR authorized_roles LIKE '%;{$this->userGroup};%') AND tab_id>-1");

		//Check for error, if an error occured then report that error
		if ($recordSet) {
			$rows = $recordSet->GetRows();
			if(sizeof($rows)>0){
				return $rows[0]["tab_id"];
			}
		}
		
		return 0;
	}
	
	/*
		Function: getContainerList
			Gives list of avaliable containers. Reads containers directory
		placed under www/templates/ and returns directories placed there.
			
		Returns:
	      Container list
	*/
	function getContainerList(){
		$containers = array();
		$dir = DIR_WWW_ROOT.DIRECTORY_SEPARATOR."templates".DIRECTORY_SEPARATOR."containers";
		$containersDir = @opendir($dir);

		while (false !== ($filename = readdir($containersDir))) {
			if($filename!="." && $filename!=".." && is_dir($dir.DIRECTORY_SEPARATOR.$filename)){
		   		array_push($containers, $filename);
			}
		}
		
		closedir($containersDir);
		return $containers;
	}
	
	/*
		Function: getSkinList
			Gives list of avaliable skins. Reads containers directory
		placed under www/templates/ and returns directories placed there.
			
		Returns:
	      Skin list
	*/
	function getSkinList(){
		$containers = array();
		$dir = DIR_WWW_ROOT.DIRECTORY_SEPARATOR."templates".DIRECTORY_SEPARATOR."skins";
		$containersDir = @opendir($dir);

		while (false !== ($filename = readdir($containersDir))) {
			if($filename!="." && $filename!=".." && is_dir($dir.DIRECTORY_SEPARATOR.$filename)){
		   		array_push($containers, $filename);
			}
		}
		
		closedir($containersDir);
		return $containers;
	}

		
	/*
		Function: getSpecialModules
			Gets special module list. Special modules are the modules
		that are loaded via querystrings and have module_id less then zero.
			
		Returns:
	      Special module array with query_string as key and module_id value
	*/
	function getSpecialModules(){
		$result = array();
		$recordSet = $this->databaseConnection->Execute("SELECT * FROM {$this->databaseTablePrefix}module_special");
	  
	  	//Check for error, if an error occured then report that error
	  	if (!$recordSet) {
	  		trigger_error("Unable to get settings\nreason is : ".$this->databaseConnection->ErrorMsg());
			die();
	  	}
	  
	  	while (!$recordSet->EOF) {
			$result[$recordSet->fields["query_string"]] = $recordSet->fields["module_id"];
			$recordSet->MoveNext();
		}
		
		return $result;
	}
	
	/*
		Function: getTabTitle
			Gets title for current tab
	*/
	function getTabTitle(){
		$recordSet = $this->databaseConnection->Execute("SELECT * FROM {$this->databaseTablePrefix}tabs WHERE tab_id = {$this->selectedTab}");
	  
	  	//Check for error, if an error occured then report that error
	  	if (!$recordSet) {
	  		trigger_error("Unable to get settings\nreason is : ".$this->databaseConnection->ErrorMsg());
			die();
	  	}
		
  		$rows = $recordSet->GetRows();	  
		
	  	if(count($rows)==1){
			return $rows[0]["title"];			
		}		

		return "";
	}
	
	/*
		Function: displayOutput
			Parse template, skin classes and displays output
	*/
	function displayOutput(){
		$output = '';
		
		while($this->revalidate){
			$this->revalidate = FALSE;	
			$this->readSettings();
			
			$skin = new Skin();
			foreach ($this->applicationSettings as $key => $value) {
				$skin->assign($key, $value);
			}
			
			$skin->assign('tabTitle', $this->getTabTitle());
			$skin->assign('applicationName', APPLICATION_NAME);
			$skin->assign('applicationVersion', APPLICATION_VERSION);
			$skin->assign('metaDataList', $this->readMetaTags());
			$skin->assign('styleSheetList', $this->getFiles('css'));
			$skin->assign('javaScriptList', $this->getFiles('js'));
			$skin->assign('skinPath', DIR_TEMPLATES_SITE.$this->applicationSettings["theme"]);
			$skin->assign('charset', 'iso-8859-9');	
			$skin->assign('template', DIR_TEMPLATES_SITE.$this->applicationSettings["theme"].DIRECTORY_SEPARATOR."index.html");
			$skin->assign_by_ref('controlVariables', $this->controlVariables);
			$skin->main = &$this;
			
			$output = $skin->fetch('siteSkinBase.tpl');
		}
		
		echo $output;		
	}
	
	/*
		Function: getFiles
			Reads www and template directory and list files with given
		extension.
			
			$desiredExtension - Extension to search within
		theme directory
			
		Returns:
			List of target files in www and template directory
	*/
	function getFiles($desiredExtension){
		$result = array();
		
		//Analyze main directory
		if($dir  = @opendir(DIR_APPLICATION_BASE.'www')){
			while (false !== ($file = readdir($dir))) { 
				$exploded = explode('.',$file);
				if(sizeof($exploded)==2){
					list($fileName, $extension)=$exploded;
					if($extension==$desiredExtension){ 
						$result[$fileName] = $file;
					}
				}
			}
			closedir($dir);
		}
		
		//Analyze template directory
		if($dir  = @opendir(DIR_TEMPLATES_SITE.$this->applicationSettings["theme"])){
			while (false !== ($file = readdir($dir))) { 
				$exploded = explode('.',$file);
				if(sizeof($exploded)==2){
					list($fileName, $extension)=$exploded;
					if($extension==$desiredExtension){ 
						$result[$fileName] = "templates/skins/".$this->applicationSettings["theme"]."/".$file;
					}
				}
			}
			closedir($dir);
		}
		
		return $result;
	}
	
	/*
		Function: readSettings
			Reads application settings.
			
		Note:
			This function reads application settings from *configuration* table in database
		and stores them to _$applicationSettings_ variable.
	*/
	function readSettings(){
		$this->applicationSettings = array('theme' => 'default');
		$recordSet = $this->databaseConnection->Execute("SELECT * FROM {$this->databaseTablePrefix}configuration");
	  
	  	//Check for error, if an error occured then report that error
	  	if (!$recordSet) {
	  		trigger_error("Unable to get settings\nreason is : ".$this->databaseConnection->ErrorMsg());
			die();
	  	}
	  
	  	while (!$recordSet->EOF) {
	  		$this->applicationSettings[$recordSet->fields["config_key"]]=$recordSet->fields["config_value"];
			$recordSet->MoveNext();
	  	}
	}
	
	/*
		Function: readMetaTags
			Reads meta tag list.
			
		Note:
			This function reads application settings from *metatags* table in database.
			
		Returns:
			Array of meta tags with *tag_name* as key, and *tag_value* as value.
	*/
	function readMetaTags(){
		$result = array();
		$recordSet = $this->databaseConnection->Execute("SELECT * FROM {$this->databaseTablePrefix}metatags");
	  
	  	//Check for error, if an error occured then report that error
	  	if (!$recordSet) {
	  		trigger_error("Unable to get Meta Tags\nreason is : ".$this->databaseConnection->ErrorMsg());
	  	}
	  
	  	while (!$recordSet->EOF) {
	  		$result[$recordSet->fields["tag_name"]]=$recordSet->fields["tag_value"];
			$recordSet->MoveNext();
	  	}
		
		$recordSet2 = $this->databaseConnection->Execute("SELECT description, keywords FROM {$this->databaseTablePrefix}tabs WHERE tab_id = {$this->selectedTab}");
	  
	  	//Check for error, if an error occured then report that error
	  	if (!$recordSet2) {
	  		trigger_error("Unable to get description and keywords \nreason is : ".$this->databaseConnection->ErrorMsg());
	  	}
	  
	  	$rows2 = $recordSet2->GetRows();	  
	  	if(count($rows2)==1){
			$result["description"] = $rows2[0]["description"];
			$result["keywords"] = $rows2[0]["keywords"];
		}
		
		return $result;
	}	
}
?>