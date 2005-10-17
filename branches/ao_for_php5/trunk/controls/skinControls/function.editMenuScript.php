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
File: user

Function: smarty_function_editMenuScript
	Handles {editMenuScript} function in template
	
Parameters:

	$params  - Parameters given in template
	&$smarty - Skin object that calls this function	
*/
function smarty_function_editMenuScript($params, &$skin){
	return "<script language='JavaScript1.2'>
		//Drop down menu link- © Dynamic Drive (www.dynamicdrive.com)
		//Credit MUST stay intact for use
		
		//reusable/////////////////////////////
	
		//Drop down menu by http://www.dynamicdrive.com
	
		var zindex=100
		var ns4=document.layers
		var ns6=document.getElementById&&!document.all
		var ie4=document.all
		var opr=navigator.userAgent.indexOf('Opera')
	
		function dropit(e,whichone){
			curmenuID=ns6? document.getElementById(whichone).id : eval(whichone).id
			if (window.themenu&&themenu.id!=curmenuID)
				themenuStyle.visibility=ns4?'hide' : 'hidden'
	
			themenu=ns6? document.getElementById(whichone): eval(whichone)
			themenuStyle=(ns6||ie4)? themenu.style : themenu
	
			themenuoffsetX=(ie4&&opr==-1)? document.body.scrollLeft : 0
			themenuoffsetY=(ie4&&opr==-1)? document.body.scrollTop : 0
	
			themenuStyle.left=ns6||ns4? e.pageX-e.layerX : themenuoffsetX+event.clientX-event.offsetX
			themenuStyle.top=ns6||ns4? e.pageY-e.layerY+19 : themenuoffsetY+event.clientY-event.offsetY+18
	
			hiddenconst=(ns6||ie4)? 'hidden' : 'hide'
	
			if (themenuStyle.visibility==hiddenconst){
				themenuStyle.visibility=(ns6||ie4)? 'visible' : 'show'
				themenuStyle.zIndex=zindex++
			}
			else
				hidemenu()
			return false
		}
	
		function hidemenu(){
			if ((ie4||ns6)&&window.themenu)
				themenuStyle.visibility='hidden'
			else if (ns4)
				themenu.visibility='hide'
		}
	
		if (ie4||ns6)
			document.onclick=hidemenu
	
		//reusable/////////////////////////////
	</script>";
}
?>
