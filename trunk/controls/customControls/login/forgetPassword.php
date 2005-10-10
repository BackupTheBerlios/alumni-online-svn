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
require_once(DIR_CONTROLS.'controls.MailTemplate.php');

/*
	Class: forgetPassword
		Displays a forms to re-generate user password and send via e-mail
	
	See Also:
		<ControlBase>
*/
class forgetPassword extends ControlBase{
	/*
   		Function: actionPerform
			
			&$skin     - Skin class instance
			$moduleID  - Current module identifier
		
		See Also:
			<Skin>
   */
	function actionPerform(&$skin, $moduleID){
		$usernameError = '';
		
		if(isset($_POST["event"]) && $_POST["event"]=='forgetPassword'){
			//Check username
			//Inorder to avoid sql injection attacks both 
			//should contains characters form a to z and/or numbers only			
			if(isset($_POST["username"]) && (!$skin->main->checkString('[^a-zA-Z0-9]', $_POST["username"]) || $_POST["username"]=="")){
				$usernameError = "Username must contains numbers and/or character from a to z only";
			}else{
				$query =  "SELECT * FROM {$skin->main->databaseTablePrefix}users WHERE username=".$skin->main->databaseConnection->qstr($_POST["username"]);
				$recordSet = $skin->main->databaseConnection->Execute($query);
				
				$password = $this->randomNumber();
				$record = array('password' => md5($password));
				
				//Start Update Transaction
				$skin->main->databaseConnection->StartTrans();
				$updateSQL = $skin->main->databaseConnection->GetUpdateSQL($recordSet, $record);
				$skin->main->databaseConnection->Execute($updateSQL);
				$recordSet2 = $skin->main->databaseConnection->Execute("SELECT * FROM {$skin->main->databaseTablePrefix}templates 
															WHERE
																type='mail'
															AND
																(
																	name = 'forgetPasswordSubject'
																OR
																	name = 'forgetPasswordBody'
																)");
													
				$templates = array('forgetPasswordSubject' => '', 'forgetPasswordBody' => '');
				while (!$recordSet2->EOF) {
					$templates[$recordSet2->fields["name"]] = $recordSet2->fields["content"];
					$recordSet2->MoveNext();
				}																
				
				$mailer = new Mailer($skin->main);
				$mailTemplate = new MailTemplate($skin->main);
				
				$mailTemplate->assign('username', $_POST["username"]);
				$mailTemplate->assign('password', $password);
				
				$mailer->addUserAddress($_POST["username"]);
				
				$mailer->Subject = $mailTemplate->fetch('mail/forgetPasswordSubject');
				$mailer->Body    = $mailTemplate->fetch('mail/forgetPasswordBody');
				$mailer->Send();
				
				if($mailer->ErrorInfo){
					//Transaction failed
					$skin->main->databaseConnection->FailTrans();
					trigger_error("Unable to send password remind mail. Reason is : ". $mailer->ErrorInfo);
				}
				
				//Complete update transaction
				$skin->main->databaseConnection->CompleteTrans();
			}
		}
		
		//Assign codeBehind variables
		$skin->main->controlVariables["forgetPassword"] = array(
													'usernameError' => $usernameError);	
	}
	
	/*
   		Function: randomNumber
			Generates rundom password
			
			$number  - Number of digits in password
   */
	function randomNumber($number = 9)
	{
		$rndnumber = '';
		$myarray   = 'QWERTYUIOLKJHGFDSAZXCVBNM1234567890';
		$dim = strlen($myarray)-1;
		srand((double)microtime()*1000000);	
		for($i = 0; $i < $number; ++$i) {
		  	$index = rand(0, $dim);
			$rndnumber .= $myarray[$index];
		}
		return $rndnumber;
	}
}
?>