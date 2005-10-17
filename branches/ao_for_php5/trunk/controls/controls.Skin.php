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
Class: Skin
	Alumni-Online's skin class

Note:
	Extends Smarty class and binds initial settings
for Alumni-Online skins.
	See Also:
		<Smarty>
*/
class Skin extends Smarty {
	/*
		Object: $main
			Main class instance
	*/
	var $main;

	/*
		Constructor: Skin
			Default constructer
			
		Note:
			Initializes Skin class, binds initial settings		
	*/
   function Skin() {
        $this->Smarty();
		
        $this->template_dir = array_merge($this->template_dir,
										array(
											   realpath(DIR_SMARTY_BASE."templates".DIRECTORY_SEPARATOR),
											   realpath(DIR_WWW_ROOT)
										    )
										);
		$this->compile_dir  = realpath(DIR_SMARTY_BASE.'templates_c'.DIRECTORY_SEPARATOR);
		//$this->config_dir   = realpath(DIR_SMARTY_BASE.'configs'.DIRECTORY_SEPARATOR);
		//$this->cache_dir    = realpath(DIR_SMARTY_BASE.'cache'.DIRECTORY_SEPARATOR);
		$this->plugins_dir	= array_merge($this->plugins_dir, 
										array(
												realpath(DIR_CONTROLS.'skinControls'.DIRECTORY_SEPARATOR),
												realpath(DIR_CONTROLS.'formControls'.DIRECTORY_SEPARATOR.'functions'.DIRECTORY_SEPARATOR)
											)
										);
        
        $this->caching = SMARTY_CACHING;
		//Turn security on
		$this->security = true;
		//List of secure directories
		$this->secure_dir = array(								
									realpath(DIR_SMARTY_BASE."templates".DIRECTORY_SEPARATOR),
									realpath(DIR_TEMPLATES_SITE),
									realpath(DIR_CONTROLS.'customControls'.DIRECTORY_SEPARATOR)
								);
		//List of directories that contains secure php scripts 
		//templates may use these php scripts with {include_php}
		$this->trusted_dir = array();
		
		$this->load_filter('pre', 'aoForms'); 
		
        $this->assign('app_name', APPLICATION_NAME);
        $this->assign('app_version', APPLICATION_VERSION);
		
		//for test
		$this->force_compile = true;
   }
}
?>
