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
File: applicationVersion

Function: smarty_function_applicationVersion
	Handles {applicationVersion} function in mail template
	
Parameters:

	$params  - Parameters given in template
	&$mailTemplate - MailTemplate object that calls this function	
*/
function smarty_function_applicationVersion($params, &$mailTemplate){
	return APPLICATION_VERSION;
}
?>