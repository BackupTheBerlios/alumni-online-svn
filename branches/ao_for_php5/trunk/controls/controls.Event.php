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
Class: Event
	Handles system events
*/
class Event {

	/*
		Object: $main
			Main class instance
	*/
	var $main;
   
   /*
		Function: fireEvent
			fires given event
			
			$eventName - Name of the event to be fired.
			$args      - Event arguments.
	*/
   function fireEvent($eventName, $args){
	   $recordSet = $this->main->databaseConnection->Execute("SELECT * 
	   													FROM
															{$this->main->databaseTablePrefix}events_listeners AS events_listeners
														LEFT OUTER JOIN
															{$this->main->databaseTablePrefix}events_adapters AS events_adapters
														ON
															events_listeners.events_adapters_id  = events_adapters.events_adapters_id 
														WHERE
															is_enable = 1
														AND
															events_listeners.event_name = ".$this->main->databaseConnection->qstr($eventName));
															
		$fileName = $recordSet->Fields("control_src");
		$className    = str_replace('.php', '', $fileName);
		if($fileName!=""){
			require_once("eventControls".DIRECTORY_SEPARATOR.$fileName);
			
			$classInstance = new $className;
			if(is_a($classInstance, 'EventHandler')){
				$classInstance->main = $this->main;
				$classInstance->actionPerform($eventName, $args);
			}
		}
   }
}
?>
