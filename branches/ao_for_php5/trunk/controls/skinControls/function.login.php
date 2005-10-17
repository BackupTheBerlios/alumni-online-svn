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
File: login

Function: smarty_function_login
	Handles {login} function in template

Parameters:

	$params  - Parameters given in template
	&$smarty - Skin object that calls this function	

Returns:
	If user has been log in displays '*LogOut*' with
a link to allow user logout from system; otherwise
displays '*Login*' with a link to allow user to
login.
*/
function smarty_function_login($params, &$skin){
	if(session_is_registered("username")){
		$result = "<a href='index.php?logout'";
	}else{
		$result = "<a href='index.php?login'";
	}
	
	if(!empty($params['cssClass'])){
		$result .= " class='".$params['cssClass']."'";
	}
	
	if(session_is_registered("username")){
		$result .= ">Logout";
	}else{
		$result .= ">Login";
	}
	
	$result .= "</a>";
	
	return $result;
}
?>
