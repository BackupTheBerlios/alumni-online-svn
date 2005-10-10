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

require_once(DIR_CONTROLS.'controls.Forms.php');

/*
File: prefilter.aoForms

Function: smarty_prefilter_aoForms
	Handles {skinPath_pane} function in template

Parameters:

	$source  - Parameters given in template
	&$smarty - Skin object that calls this function	

Returns:
	Filtered source
*/

function smarty_prefilter_aoForms($source, &$smarty) {
  $forms = new Forms($source, $smarty);
  return $forms->processSource();
}
?>
