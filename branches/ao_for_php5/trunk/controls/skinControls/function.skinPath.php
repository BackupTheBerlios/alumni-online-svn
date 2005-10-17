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
File: skinPath

Function: smarty_function_skinPath
	Handles {skinPath_pane} function in template

Parameters:

	$params  - Parameters given in template
	&$smarty - Skin object that calls this function	

Returns:
	Path of active skin
*/
function smarty_function_skinPath($params, &$skin){
	return DIR_TEMPLATES_SITE.$skin->main->applicationSettings["theme"];
}
?>
