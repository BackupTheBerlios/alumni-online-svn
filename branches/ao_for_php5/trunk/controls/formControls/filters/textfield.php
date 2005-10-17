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
Class: Textfield
	Html 'Textfield' element processing class
*/
class Textfield {
	var $forms;
	var $params;	
	/*
		Constructor: Textfield	
	*/
   function Textfield(&$forms, $params) {
		$this->forms = $forms;
		$this->params = $params;
   }
   
   function parse(){
		return "{aoForms_TextField ".$this->params."}";
   }
}
?>
