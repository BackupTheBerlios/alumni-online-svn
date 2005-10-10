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

/*
Class: Forms
	Alumni-Online's form processing class
*/
class Forms {
	/*
		Object: $skin
			Skin class instance
	*/
	var $skin;
	
	/*
		Object: $source
			Input for the filter
	*/
	var $source;
	
	
	/*
	 	Array: $filters
			Holds list of avaliable filters
	*/
	var $filters = array();
	
	/*
	 	String: $prefix
			Hold prefix for the form items
	*/
	var $prefix = 'aoForms';
	
	/*
	 	String: $left_delimiter
			Left delimiter for the smarty
	*/
	var $left_delimiter;
	
	/*
	 	String: $right_delimiter
			Right delimiter for the smarty
	*/
    var $right_delimiter;

	/*
		Constructor: Forms	
	*/
   function Forms($source, &$skin) {
	   $this->source          = $source;
	   $this->skin            = $skin;
	   
	   $this->left_delimiter  = $skin->left_delimiter;
       $this->right_delimiter = $skin->right_delimiter;
	   
	   
   }
   
   function processSource(){
	   preg_match_all('!<(/?)'.$this->prefix.':([\w]+)(.*?)?(/?)>!xsmi', $this->source, $foundTags, PREG_SET_ORDER);

	   foreach ($foundTags as $tag){
		   if ($tag[4] == '/'){ //single tag 
		   		$className = $tag[2];
				$classFile = DIR_CONTROLS.'formControls'.DIRECTORY_SEPARATOR.'filters'.DIRECTORY_SEPARATOR.$className.'.php';
				if(is_file($classFile)){
					require_once($classFile);

					$classInstance = new $className(&$this, trim($tag[3]));
					
					$this->source = preg_replace('/' . preg_quote($tag[0], '/') . '/', $classInstance->parse(), $this->source, 1);
				}
		   }else{ //block tag
		   }
	   }
       return $this->source;
   }
}
?>
