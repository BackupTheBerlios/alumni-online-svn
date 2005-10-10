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

function smarty_function_aoForms_TextField($params, &$container) 
{
	if(!isset($params["name"])){
		$skin->trigger_error("aoForms_TextField: missing 'name' parameter");
		return;
	}
	
	$name   = $params["name"];
	$enable = isset($params["enable"]) ? $params["enable"] : "true";

	if($enable=="false")
		return;
		
	return "<input type='text' name='$name'>"; 
} 
?> 