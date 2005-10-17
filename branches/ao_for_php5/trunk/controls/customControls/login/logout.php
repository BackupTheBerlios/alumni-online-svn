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
	Class: logout
		Unsets session variables and destroys session. Extends ControlBase
	for skin entegration.
	
	See Also:
		<ControlBase>
*/
class logout extends ControlBase{

	/*
   		Function: actionPerform
			Performs logout operation. Unsets session and destroys
		session variables. Sets selected tab to initial tab, recalculates
		user group and revalidates output.		
			
			&$skin     - Skin class instance
			$moduleID  - Current module identifier
		
		See Also:
			<Skin>
   */
	function actionPerform(&$skin, $moduleID){
		session_unset();
		session_destroy();
		
		$skin->main->selectedTab = $skin->main->getInitialTab();
		$skin->main->userGroup   = $skin->main->getUserGroup();
		$skin->main->revalidate  = TRUE;
	}
}
?>