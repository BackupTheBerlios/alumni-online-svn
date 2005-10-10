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
File: containerPath

Function: smarty_function_containerPath
	Handles {containerPath} function in template
	
Parameters:

	$params  - Parameters given in template
	&$container - Container object that calls this function	

Returns:
	Path for current module container
*/
function smarty_function_containerPath($params, &$container){
	return DIR_TEMPLATES_CONTAINER.$container->containerName."/";
}
?>