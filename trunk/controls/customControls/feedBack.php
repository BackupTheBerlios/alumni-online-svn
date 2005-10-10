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

require_once(DIR_CONTROLS.'controls.Mailer.php');

/*
	Class: feedBack
		Allows visitors to send feedbacks.
	
	See Also:
		<ControlBase>
*/
class feedBack extends ControlBase{

	/*
   		Function: actionPerform
			Displays site map on a tree.	
			
			&$skin     - Skin class instance
			$moduleID  - Current module identifier
		
		See Also:
			<Skin>
   */
	function actionPerform(&$skin, $moduleID){
		$skin->main->controlVariables["feedBack"]['tabId']    = $skin->main->selectedTab;
		$skin->main->controlVariables["feedBack"]['error']    = false;
		$skin->main->controlVariables["feedBack"]['succeed']  = false;
		
		if(isset($_POST["event"]) && $_POST["event"]=='feedBack'){
			$mailer = new Mailer($skin->main);
			$mailer->AddAddress($mailer->From, $mailer->FromName);
			
			$mailer->Subject = "FeedBack from your web site";
			$mailer->Body    = $_POST["content"];
			$mailer->Send();
			
			$skin->main->controlVariables["feedBack"]['errorInfo']  = $mailer->ErrorInfo!="";
			$skin->main->controlVariables["feedBack"]['succeed']    = $mailer->ErrorInfo=="";
		}
	}
}
?>