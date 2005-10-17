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

require_once(DIR_CONTROLS."controls.Menu.php");

/*
	Class: sitePath
		Displays site map on a tree.
	
	See Also:
		<ControlBase>
*/
class siteMap extends ControlBase{

	/*
   		Function: actionPerform
			Displays site map on a tree.	
			
			&$skin     - Skin class instance
			$moduleID  - Current module identifier
		
		See Also:
			<Skin>
   */
	function actionPerform(&$skin, $moduleID){		
		$menu = new Menu($skin->main, 'siteMap', 'Tree');
		$content = $menu->getMenuScript();
		
		//Assign codeBehind variable
		$skin->main->controlVariables["siteMap"] = array(
													'content'    => $content);
	}
}
?>