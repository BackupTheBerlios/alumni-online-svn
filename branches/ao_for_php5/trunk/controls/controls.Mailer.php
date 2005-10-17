<?php
#####################################################################################
#        Alumni-OnLine: Alumni Organization Web Management System                   #
#####################################################################################
#                                                                                   #
# Copyright © 2004 by Fatih BOY		                                            #
# http://alumni-online.enterprisecoding.com/                                        #
#                                                                                   #
# This program is free software. You can redistribute it and/or modify              #
# it under the terms of the GNU General Public License as published by              #
# the Free Software Foundation; either version 2 of the License.                    #
#####################################################################################

require_once(DIR_CONTRIB.'phpMailer'.DIRECTORY_SEPARATOR.'class.phpmailer.php');

/*
Class: Mailer
	Allows Alumni-Online to send mails. Extends phpmailer; binds initial
settings.
	
	See Also:
		<phpmailer>
*/
class Mailer extends phpmailer{

	/*
		Object: $main
			Main class instance
	*/
	var $main;
	
	function Mailer($main){		
		$this->main      = $main;
		
		$this->PluginDir = DIR_CONTRIB.'phpMailer'.DIRECTORY_SEPARATOR;
		$this->Sender    = $this->getConfig("email");
		$this->From      = $this->Sender;
		$this->FromName  = $this->getConfig("emailFrom");
		$this->Mailer    = $this->getConfig("emailMailer");
		$this->Username  = $this->getConfig("emailUsername");
		$this->Password  = $this->getConfig("emailPassword");
	}


        /*
		Function: getConfig
			Find value for given configuration key.

                        $configName - Key to return configuration value.
			
		Returns:
			Value for given configuration key, empty string if
                no entry found.
	*/	
	function getConfig($configName){
		$recordSet = $this->main->databaseConnection->Execute("SELECT config_value FROM {$this->main->databaseTablePrefix}configuration WHERE config_key='$configName'");
		
		if (!$recordSet) {
			trigger_error("Unable to get configuration '$configName'\nreason is : ".$this->main->databaseConnection->ErrorMsg());
			return "";
		}else{		
			$rows = $recordSet->GetRows();
			if(sizeof($rows)==1){
				return $rows[0]["config_value"];
			}
		}
	}
	
        /*
		Function: addUserAddress
			Find user details from given username, and adds address of that user.

                        $userName - Username to add its address.
	*/
	function addUserAddress($userName){
		$recordSet = $this->main->databaseConnection->Execute("SELECT name, surname, email FROM {$this->main->databaseTablePrefix}users WHERE username='$userName'");
		
		if (!$recordSet) {
			trigger_error("Unable to get user information\nreason is : ".$this->main->databaseConnection->ErrorMsg());
		}else{		
			$rows = $recordSet->GetRows();
			if(sizeof($rows)==1){
				$this->AddAddress($rows[0]["email"], $rows[0]["name"].' '.$rows[0]["surname"]);
			}
		}
	}
	
	function addSystemAddress(){
		$this->AddAddress($this->getConfig("email"), $this->getConfig("emailFrom"));
	}
}