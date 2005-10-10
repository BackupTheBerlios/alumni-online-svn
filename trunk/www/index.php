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
File: Index
	Index.php is the only file that users can access.
	Initializes <Main> class and performs needed operations.
*/
ob_start();
session_start();

/*
#####################################################################################
	Check AO installation
#####################################################################################
*/
require_once(dirname(__FILE__).'/../controls/controls.Installation.php');
$installation = new Installation();

/*
#####################################################################################
		If AO installed, then continue to execute script
#####################################################################################
*/
if($installation->installed){
	unset($installation);
	require_once(dirname(__FILE__).'/../controls/controls.Main.php');
	
	$main = new Main();
	$main->displayOutput();

}
ob_end_flush(); 
?>
