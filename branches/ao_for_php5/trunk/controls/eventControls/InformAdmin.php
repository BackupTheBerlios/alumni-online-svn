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

require_once("eventHandler.php");

require_once(DIR_CONTROLS.'controls.Mailer.php');
require_once(DIR_CONTROLS.'controls.MailTemplate.php');

/*
Class: Skin
	Alumni-Online's skin class

	See Also:
		<EventHandler>
*/
class InformAdmin extends EventHandler {

	/*
		Constructor: Skin
			Default constructer
			
		Note:
			Initializes Skin class, binds initial settings		
	*/
   function InformAdmin() {
   }
   
   function actionPerform($eventName, $args){
		$mailer = new Mailer($this->main);
		$mailTemplate = new MailTemplate($this->main);
					
		$mailTemplate->assign('username', $args);
		$mailer->addSystemAddress();		
		
		if($eventName=="login_fail"){					
			$mailer->Subject = $mailTemplate->fetch('event/loginFailSubject');
			$mailer->Body    = $mailTemplate->fetch('event/loginFaildBody');
			$mailer->Send();
		}elseif($eventName=="login_succeed"){
			$mailer->Subject = $mailTemplate->fetch('event/loginSucceedSubject');
			$mailer->Body    = $mailTemplate->fetch('event/loginSucceedBody');
			$mailer->Send();
		}elseif($eventName=="user_activate"){
			$mailer->Subject = $mailTemplate->fetch('event/userActivateSubject_Admin');
			$mailer->Body    = $mailTemplate->fetch('event/userActivateBody_Admin');
			$mailer->Send();
		}elseif($eventName=="user_deactivate"){
			$mailer->Subject = $mailTemplate->fetch('event/userDeactivateSubject_Admin');
			$mailer->Body    = $mailTemplate->fetch('event/userDeactivateBody_Admin');
			$mailer->Send();
		}
   }
}
?>
