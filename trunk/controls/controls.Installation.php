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
Class: Installation
	Installs Alumni-Online application.

Note:
		Checks whether controls.Settings.php file exists; if not
	starts installation process.
*/
class Installation {
	var $dirSeperator    = DIRECTORY_SEPARATOR;
	var $applicationBase;
	var $dirContrib;
	var $installed = false;
	
	/*
		Constructer: Installation
			Checks Alumni-Online installation. Starts installation 
		process if 'controls.Settings.php' file does not exists.
	*/
	function Installation(){
		$this->applicationBase = realpath(dirname(__FILE__)."{$this->dirSeperator}..");
		$this->dirContrib      = "{$this->applicationBase}{$this->dirSeperator}contrib{$this->dirSeperator}";
	
		if (!file_exists("{$this->applicationBase}{$this->dirSeperator}controls{$this->dirSeperator}controls.Settings.php")) { 
			$this->startInstallation();
		}else{
			$this->installed = true;
		}
	}
	
	/*
		Function: startInstallation
			Starts and manages installation process.
	*/
	function startInstallation(){
		require_once("{$this->dirContrib}adodb{$this->dirSeperator}adodb.inc.php");
		require_once("{$this->dirContrib}smarty{$this->dirSeperator}Smarty.class.php");
		require_once("{$this->dirContrib}adodb{$this->dirSeperator}adodb-xmlschema.inc.php" );
		
		$DIR_APPLICATION_BASE    = realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'..').DIRECTORY_SEPARATOR;
		$DIR_WWW_ROOT            = $DIR_APPLICATION_BASE.'www'.DIRECTORY_SEPARATOR;
		$DIR_CONTRIB             = $DIR_APPLICATION_BASE.'contrib'.DIRECTORY_SEPARATOR;
		$DIR_CONTROLS            = $DIR_APPLICATION_BASE.'controls'.DIRECTORY_SEPARATOR;
		$DIR_SMARTY_BASE         = $DIR_APPLICATION_BASE.'skins'.DIRECTORY_SEPARATOR;
		
		$template = $this->getSmarty();	
		$template->assign('skinList', $this->getFileList("{$this->applicationBase}{$this->dirSeperator}www{$this->dirSeperator}templates{$this->dirSeperator}skins"));
		$template->assign('containerList', $this->getFileList("{$this->applicationBase}{$this->dirSeperator}www{$this->dirSeperator}templates{$this->dirSeperator}containers"));
		$template->assign('dirAppBase', $DIR_APPLICATION_BASE);
		$template->assign('dirwwwRoot', $DIR_WWW_ROOT);
		$template->assign('dirContrib', $DIR_CONTRIB);
		$template->assign('dirControls', $DIR_CONTROLS);
		$template->assign('dirSmartyBase', $DIR_SMARTY_BASE);
		$template->assign('errorMessage', '');
		$template->assign('installationComplated', false);
		
		if(isset($_POST["event"])){
			$smarty = $this->getSmarty();

			$smarty->assign('host', $_POST["dbHost"]);
			$smarty->assign('user', $_POST["dbUser"]);
			$smarty->assign('password', $_POST["dbPasswd"]);
			$smarty->assign('type', $_POST["dbType"]);
			$smarty->assign('name', $_POST["dbName"]);
			$smarty->assign('prefix', $_POST["dbPrefix"]);
			$smarty->assign('dirAppBase', addslashes($_POST["dirAppBase"]));
			$smarty->assign('dirwwwRoot', addslashes($_POST["dirwwwRoot"]));
			$smarty->assign('dirContrib', addslashes($_POST["dirContrib"]));
			$smarty->assign('dirControls', addslashes($_POST["dirControls"]));
			$smarty->assign('dirSmartyBase', addslashes($_POST["dirSmartyBase"]));
			$smarty->assign('errorMessage', '');
			
			$settings = $smarty->fetch('controls.Settings.tpl');
			if (!$settingsHandle = fopen("{$this->applicationBase}{$this->dirSeperator}controls{$this->dirSeperator}controls.Settings.php", "x")){
				$template->assign('errorMessage', "Cannot save settings file 'controls.Settings.php'. Please write followings manually :<p>&lt;?php\n$settings\n?&gt;</p>");
			}else{
				if (!fwrite($settingsHandle, "<?php\n".$settings."\n?>")){
					$template->assign('errorMessage', "Cannot save settings file 'controls.Settings.php'. Please write followings manually :<p>&lt;?php\n$settings\n?&gt;</p>");
				}else{
					$template->assign('installationComplated', true);
				}
				
				fclose($settingsHandle);
				
				$databaseConnection = ADONewConnection($_POST["dbType"]);
				$databaseConnection->Connect($_POST["dbHost"], $_POST["dbUser"], $_POST["dbPasswd"], $_POST["dbName"]);	
		
				$dbScript = $this->getSmarty();
				$dbScript->left_delimiter  = "{{";
				$dbScript->right_delimiter = "}}";
				
				$dbScript->assign('databaseTablePrefix', $_POST["dbPrefix"]);
				$dbScript->assign('parameters', $_POST);
				$dbScript->assign('adminPassword', md5($_POST["adminUserPasswd"]));
				
				$pieces       = array();
				$this->splitSqlFile($pieces, $dbScript->fetch('dbScript.tpl'));
				
				//Perform database generation within transaction
				$databaseConnection->StartTrans();
				
				foreach ($pieces as $query) { 	
					$recordSet = $databaseConnection->Execute($query['query']);
					 
					//Check for error, if an error occured then report that error
					if (!$recordSet) {
						trigger_error("Unable to initialize database\nreason is : ".$databaseConnection->ErrorMsg());
					}
				}

				session_register('username');
				$_SESSION["username"] = $_POST["adminUser"];
				$this->installed = true;
				//Complate transaction
				$databaseConnection->CompleteTrans();			
			}
		}else{				
			echo $template->fetch('installation.tpl');
		}
	}
	
	function getFileList($dir){
		$containers = array();
		$containersDir = @opendir($dir);

		while (false !== ($filename = readdir($containersDir))) {
			if($filename!="." && $filename!=".." && is_dir($dir.DIRECTORY_SEPARATOR.$filename)){
		   		array_push($containers, $filename);
			}
		}
		
		closedir($containersDir);
		return $containers;
	}

	
	function getSmarty(){
		$smarty = new Smarty();
		$smarty->template_dir = realpath("{$this->applicationBase}{$this->dirSeperator}skins{$this->dirSeperator}templates{$this->dirSeperator}");
		$smarty->compile_dir  = realpath("{$this->applicationBase}{$this->dirSeperator}skins{$this->dirSeperator}templates_c{$this->dirSeperator}");

		return $smarty;
	}
	
	/*
		Function: splitSqlFile
			Splits given sql statements inro array per query.
			
			Note:
				This function is taken from PhpMyAdmin v2.6.0-rc1. Thanks
			PMA team for providing this function.
	*/
	function splitSqlFile(&$ret, $sql)
	{
		$sql          = trim($sql);
		$sql_len      = strlen($sql);
		$char         = '';
		$string_start = '';
		$in_string    = FALSE;
		$nothing      = TRUE;
		$time0        = time();
	
		for ($i = 0; $i < $sql_len; ++$i) {
			$char = $sql[$i];
	
			// We are in a string, check for not escaped end of strings except for
			// backquotes that can't be escaped
			if ($in_string) {
				for (;;) {
					$i         = strpos($sql, $string_start, $i);
					// No end of string found -> add the current substring to the
					// returned array
					if (!$i) {
						$ret[] = $sql;
						return TRUE;
					}
					// Backquotes or no backslashes before quotes: it's indeed the
					// end of the string -> exit the loop
					else if ($string_start == '`' || $sql[$i-1] != '\\') {
						$string_start      = '';
						$in_string         = FALSE;
						break;
					}
					// one or more Backslashes before the presumed end of string...
					else {
						// ... first checks for escaped backslashes
						$j                     = 2;
						$escaped_backslash     = FALSE;
						while ($i-$j > 0 && $sql[$i-$j] == '\\') {
							$escaped_backslash = !$escaped_backslash;
							$j++;
						}
						// ... if escaped backslashes: it's really the end of the
						// string -> exit the loop
						if ($escaped_backslash) {
							$string_start  = '';
							$in_string     = FALSE;
							break;
						}
						// ... else loop
						else {
							$i++;
						}
					} // end if...elseif...else
				} // end for
			} // end if (in string)
		   
			// lets skip comments (/*, -- and #)
			else if (($char == '-' && $sql[$i + 1] == '-' && $sql[$i + 2] <= ' ') || $char == '#' || ($char == '/' && $sql[$i + 1] == '*')) {
				$i = strpos($sql, $char == '/' ? '*/' : "\n", $i);
				// didn't we hit end of string?
				if ($i === FALSE) {
					break;
				}
				if ($char == '/') $i++;
			}
	
			// We are not in a string, first check for delimiter...
			else if ($char == ';') {
				// if delimiter found, add the parsed part to the returned array
				$ret[]      = array('query' => substr($sql, 0, $i), 'empty' => $nothing);
				$nothing    = TRUE;
				$sql        = ltrim(substr($sql, min($i + 1, $sql_len)));
				$sql_len    = strlen($sql);
				if ($sql_len) {
					$i      = -1;
				} else {
					// The submited statement(s) end(s) here
					return TRUE;
				}
			} // end else if (is delimiter)
	
			// ... then check for start of a string,...
			else if (($char == '"') || ($char == '\'') || ($char == '`')) {
				$in_string    = TRUE;
				$nothing      = FALSE;
				$string_start = $char;
			} // end else if (is start of string)
	
			elseif ($nothing) {
				$nothing = FALSE;
			}
	
			// loic1: send a fake header each 30 sec. to bypass browser timeout
			$time1     = time();
			if ($time1 >= $time0 + 30) {
				$time0 = $time1;
				header('X-pmaPing: Pong');
			} // end if
		} // end for
	
		// add any rest to the returned array
		if (!empty($sql) && preg_match('@[^[:space:]]+@', $sql)) {
			$ret[] = array('query' => $sql, 'empty' => $nothing);
		}
	
		return TRUE;
	}
}
?>
