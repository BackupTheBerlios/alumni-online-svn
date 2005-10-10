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
Class: sendMessage2User
	Sends given message to user.
	
	See Also:
		<ControlBase>
		<Mailer>
*/
class sendMessage2User extends ControlBase{	
	/*	
		Function: actionPerform
			Overrides actionPerform function of ControlBase class. 
			
		Parameters:
		
			$skin  - Application skin class
			$moduleID  - Current module identifier
	*/
	function actionPerform(&$skin, $moduleID){
		$username  = $this->getUsername($skin->main);
	
		$skin->main->controlVariables["sendMessage2User"]['username']   = $username;
		$skin->main->controlVariables["sendMessage2User"]['errorInfo']  = "";
		$skin->main->controlVariables["sendMessage2User"]['succeed']    = false;
		
		if(isset($_POST["event"]) && $_POST["event"]=='sendMessage2User' && $username!=NULL){
			$mailer = new Mailer($skin->main);
			$mailer->addUserAddress($username);
			
			$mailer->Subject = $_POST["subject"];
			$mailer->Body    = $_POST["content"];
			$mailer->Send();
			
			$skin->main->controlVariables["sendMessage2User"]['errorInfo']  = $mailer->ErrorInfo;
			$skin->main->controlVariables["sendMessage2User"]['succeed']    = $mailer->ErrorInfo=="";
		}
	}
	
	/*	
		Function: getUsername
			Finds username to send message.
			
		Returns:
			Username given via queryString,  NULL otherwise.
			
		Note:
			This function parses supplied queryString. QueryString must be
		in the following format; SendMessage:fatih where fatih is the username.
		
		See Also:
			<Main>
	*/
	function getUsername($main){
		//Get username from query string
		//return empty string on fail!
		if(isset($_SERVER['QUERY_STRING'])){ 
			$commands = explode(':' ,$_SERVER['QUERY_STRING']);
			if(sizeof($commands)==2 && $commands[0]=='SendMessage' && $main->checkString('[^a-zA-Z0-9]', $commands[1])){
				return $commands[1];
			}else{
				trigger_error("Username to send message is not supplied! Query string is '".$_SERVER['QUERY_STRING']."'");
				return NULL;
			}
		}else{
			trigger_error("Username to send message is not supplied! Query string is '".$_SERVER['QUERY_STRING']."'");
			return NULL;
		}
	}
}
?>