<?php
require_once(DIR_CONTROLS.'controls.Container.php');
require_once(DIR_CONTROLS.'formControls/controls/AOFormsTextBox.php');

class Test extends Container{
	var $testBox;
	
	function Test($main, $moduleID) {
		$this->Container($main, "", $moduleID);
		//$this->security = false;
		$this->testBox = new AOFormsTextBox($this);
		$this->show(DIR_CONTROLS."customcontrols/test.html");echo "/-/-/*";
	}
}
?>