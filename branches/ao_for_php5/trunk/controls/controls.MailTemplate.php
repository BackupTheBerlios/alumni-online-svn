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
Class: MailTemplate
	Parses mail templates that placed on database

Note:
	Extends Smarty class and binds initial settings
for mail templates.
*/
class MailTemplate extends Smarty {
	/*
		Object: $main
			Main class instance
	*/
	var $main;

	/*
		Constructor: MailTemplate
			Default constructer
			
		Note:
			Initializes MailTemplate class, binds initial settings		
	*/
   function MailTemplate($main) {
        $this->Smarty();
		$this->main = $main;

        $this->default_resource_type = "mailTemplate";
        $this->template_dir          = realpath(DIR_SMARTY_BASE."templates".DIRECTORY_SEPARATOR);
		$this->compile_dir           = realpath(DIR_SMARTY_BASE.'templates_c'.DIRECTORY_SEPARATOR);
		$this->plugins_dir	         = array_merge($this->plugins_dir, 
										         array(realpath(DIR_CONTROLS.'mailTemplateControls'.DIRECTORY_SEPARATOR)));
        
        $this->caching = SMARTY_CACHING;
		//Turn security on
		$this->security = true;
		//List of secure directories
		$this->secure_dir = array();
		//List of directories that contains secure php scripts 
		//templates may use these php scripts with {include_php}
		$this->trusted_dir = array();
   }
}
?>
