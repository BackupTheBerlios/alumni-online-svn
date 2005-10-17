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

require_once(DIR_CONTROLS.'controls.ControlBase.php');

/*
File: rightPane

Function: smarty_function_rightPane
	Handles {rightPane} function in template
	
Parameters:

	$params  - Parameters given in template
	&$smarty - Skin object that calls this function	
	
Note:
	If you don't want to view containers use "NoContainer=true"

Returns:
	Right panel contents
*/
function smarty_function_rightPane($params, &$skin){
	$result = "&nbsp;";
	$moduleSelect = "(modules.tab_id={$skin->main->selectedTab} OR modules.display_all_tabs=1)";
	$specialModules = array_flip($skin->main->getSpecialModules());
	
	//Check whether module is a special module
	//If so, don't display modules with 'display_all_tabs' flag on
	if(isset($specialModules[$skin->main->selectedTab])){
		$moduleSelect = "modules.tab_id={$skin->main->selectedTab}";
	}
	
  	$recordSet = $skin->main->databaseConnection->Execute("SELECT  *  
														FROM {$skin->main->databaseTablePrefix}modules AS modules, 
															 {$skin->main->databaseTablePrefix}module_controls AS controls 
														WHERE 
															modules.module_definition_id = controls.module_definition_id 
															AND controls.control_key IS NULL 
															AND modules.pane_name='rightpane' 
															AND $moduleSelect
															AND (modules.authorized_roles LIKE '%;{$skin->main->userGroup};%' OR modules.authorized_roles LIKE '%;1;%')
														ORDER BY 
															modules.module_order");
	  
	//Check for error, if an error occured then report that error
	if (!$recordSet) {
		trigger_error("Unable to get content panel components\nreason is : ".$skin->main->databaseConnection->ErrorMsg());
		die();
	}
	  
	while (!$recordSet->EOF) {
		if($skin->main->displayModuleId==-1 || $recordSet->fields['module_id']!=$skin->main->displayModuleId){
			$controlSource = ereg_replace('[/\\]', DIRECTORY_SEPARATOR, $recordSet->fields["control_src"]);
		
			/*require_once(DIR_CONTROLS.'customControls'.DIRECTORY_SEPARATOR.$controlSource);
			
			$pathElements = explode(DIRECTORY_SEPARATOR, $controlSource);
			$fileName     = array_pop($pathElements);
			$className    = str_replace('.php', '', $fileName);
			
			$classInstance = new $className;
			if(is_a($classInstance, 'ControlBase')){
				$classInstance->actionPerform($skin, $recordSet->fields['module_id']);
				if (empty($params['NoContainer'])) {
					$content = $skin->fetch(DIR_CONTROLS."customControls".DIRECTORY_SEPARATOR.str_replace('.php', '.html', $controlSource));
					$container = new Container($skin->main, $content, $recordSet->fields['module_id']);
					
					$result .= $container->show('container.html');
				}else{
					$result .= $skin->fetch(DIR_CONTROLS."customControls".DIRECTORY_SEPARATOR.str_replace('.php', '.html', $controlSource));
				}
			}*/
			
			require_once(DIR_CONTROLS.'customControls'.DIRECTORY_SEPARATOR."test.php");	
			$container = new Test($skin->main, $recordSet->fields['module_id']);
			
			
		}else{
			$recordSet2 = $skin->main->databaseConnection->Execute("SELECT module.module_id, module_control.* 
																	FROM 
																		{$skin->main->databaseTablePrefix}modules AS module, 
																		{$skin->main->databaseTablePrefix}module_controls  AS module_control 
																	WHERE 
																		module_control.module_definition_id = module.module_definition_id 
																	AND module_control.control_key = '{$skin->main->displayModuleKey}' 
																	AND module.module_id ={$skin->main->displayModuleId}");
			
			//Check for error, if an error occured then report that error
			if ($recordSet2) {
				$rows2 = $recordSet2->GetRows();
				
				if(sizeof($rows2)==1){
					$controlSource = ereg_replace('[/\\]', DIRECTORY_SEPARATOR, $rows2[0]["control_src"]);
			
					require_once(DIR_CONTROLS.'customControls'.DIRECTORY_SEPARATOR.$controlSource);
					
					$pathElements = explode(DIRECTORY_SEPARATOR, $controlSource);
					$fileName     = array_pop($pathElements);
					$className    = str_replace('.php', '', $fileName);
					$classInstance = new $className;
					if(is_a($classInstance, 'ControlBase')){
						$classInstance->actionPerform($skin, $rows2[0]['module_id']);
						if (empty($params['NoContainer'])) {
							$content   = $skin->fetch(DIR_CONTROLS."customControls".DIRECTORY_SEPARATOR.str_replace('.php', '.html', $controlSource));
							$container = new Container($skin->main, $content, $recordSet->fields['module_id']);
							
							$result .= $container->fetch('container.html');
						}else{
							$result .= $skin->fetch(DIR_CONTROLS."customControls".DIRECTORY_SEPARATOR.str_replace('.php', '.html', $controlSource));
						}
					}
				}
			}else{
				trigger_error("Unable to get control for editModule mode\nreason is : ".$skin->main->databaseConnection->ErrorMsg());
			}
		}
		
		//Read next record
		$recordSet->MoveNext();
	}

    return $result;
}
?>
