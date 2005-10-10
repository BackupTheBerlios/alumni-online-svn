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

require_once(DIR_CONTROLS."controls.Menu.php");

/*
File: links

Function: smarty_function_links
	Handles {links} function in template

Parameters:

	$params  			- Parameters given in template
	&$smarty 			- Skin object that calls this function	
	
	$params.menuName	- Name for the menu, optional. Default 
						  value is 'MainMenu'.
	$params.type		- Menu type, optional. Default value is
						  'Horizontal'. Avaliable values are :
						  			* Horizontal
									* Vertical
									* Tree
						 

Returns:
	Module links with given parameters according to user rights
*/

function smarty_function_links($params, &$skin){
	$menuName  = (empty($params['menuName'])) ? 'MainMenu' : $params['menuName'];
	$type      = (empty($params['type'])) ? 'Horizontal' : $params['type'];
	
	$menu = new Menu($skin->main, $menuName, $type);
	return $menu->getMenuScript();
}
?>
