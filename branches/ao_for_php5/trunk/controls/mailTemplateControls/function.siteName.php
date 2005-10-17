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
File: siteName

Function: smarty_function_siteName
	Handles {siteName} function in mail template
	
Parameters:

	$params  - Parameters given in template
	&$mailTemplate - MailTemplate object that calls this function	

Returns:
	Display administrative tab editing options to administrator
*/
function smarty_function_siteName($params, &$mailTemplate){
	return 'siteName';
}
?>