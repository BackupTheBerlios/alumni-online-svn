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
require_once(DIR_CONTRIB.'smarty'.DIRECTORY_SEPARATOR.'Smarty.class.php');

/*
Class: Container 
	Alumni-Online's container class. Extends Smarty class and binds
initial settings for Alumni-Online containers.
*/
class Container extends Smarty {
	/*
		Object: $main
			Main class instance
	*/
	var $main;
	
	/*
		String: $containerName
			Name of the current container
			
		See Also:
			<getModuleContainer>
	*/
	var $containerName;
	
	/*
		String: $content
			Content of module to place in container
	*/
	var $content;
	
	/*
		Integer: $moduleID
			Current Module id
	*/
	var $moduleID;

	/*
		Constructor: Container
			Initializes Container class, binds initial settings
		
		Parameters:
			$main		- Main class instance
			$content	- Content to place in template
			$moduleID	- Current module id
	*/
   function Container($main, $content, $moduleID) {
        $this->Smarty();

		$this->main 		 = $main;
		$this->moduleID		 = $moduleID;
		$this->content		 = $content;
		$this->containerName = $this->getModuleContainer();		

        $this->force_compile = true;
        $this->template_dir  = DIR_TEMPLATES_CONTAINER.$this->containerName.DIRECTORY_SEPARATOR;
		$this->compile_dir   = DIR_SMARTY_BASE.'templates_c'.DIRECTORY_SEPARATOR;
		$this->plugins_dir	 = array_merge($this->plugins_dir, 
										array(
												realpath(DIR_CONTROLS.'containerControls'.DIRECTORY_SEPARATOR),
												realpath(DIR_CONTROLS.'formControls'.DIRECTORY_SEPARATOR.'functions'.DIRECTORY_SEPARATOR)
											)
										);

        $this->caching = false;
		//Turn security on
		$this->security = true;
		//List of secure directories
		$this->secure_dir = array(
									DIR_TEMPLATES_CONTAINER.$this->containerName.DIRECTORY_SEPARATOR,
									DIR_SMARTY_BASE.'configs'.DIRECTORY_SEPARATOR,
									DIR_CONTROLS.'customControls'.DIRECTORY_SEPARATOR
								);
		//List of directories that contains secure php scripts 
		//templates may use these php scripts with {include_php}
		$this->trusted_dir = array();
		
		$this->load_filter('pre', 'aoForms');
		
        $this->assign('app_name', APPLICATION_NAME);
        $this->assign('app_version', APPLICATION_VERSION);
   }
   
  function show($template){
  	return $this->fetch($template, $this->containerName);
  }
   
   /*
   		Function: getModuleContainer
			Finds container from given module id
			
		Returns:
			Container name for current module
   */
   function getModuleContainer(){
	$result = "classic";
	
   	$recordSet = $this->main->databaseConnection->Execute("SELECT  *  
											FROM {$this->main->databaseTablePrefix}configuration
											WHERE config_key = 'container'");
											
	//Check for error, if an error occured then report that error
	if (!$recordSet) {
		trigger_error("Unable to get default container\nreason is : ".$this->main->databaseConnection->ErrorMsg());
	}else{
		$rows = $recordSet->GetRows();
		if(sizeof($rows)==1){
			$result = $rows[0]["config_value"];
		}
	}
	
	$recordSet = $this->main->databaseConnection->Execute("SELECT  *  
											FROM {$this->main->databaseTablePrefix}module_settings
											WHERE 
												module_id  = {$this->moduleID}
												AND setting_key= 'container'");
												
	//Check for error, if an error occured then report that error
	if (!$recordSet) {
		trigger_error("Unable to get module container\nreason is : ".$this->main->databaseConnection->ErrorMsg());
	}else{
		$rows = $recordSet->GetRows();
		if(sizeof($rows)==1){
			$result = $rows[0]["setting_value"];
		}
	}
											
   	return $result;
   }
}
?>