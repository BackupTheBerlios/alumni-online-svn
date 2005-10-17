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
Script: Settings

Group: Database Settings

Constant: DATABASE_HOST 
	Holds host address of database server. Default is 'localhost'
*/
define('DATABASE_HOST', '{$host}');

/*
Constant: DATABASE_USER 
	Holds user name to access database server. Default is 'root'
*/
define('DATABASE_USER', '{$user}');

/*
Constant: DATABASE_PASSWORD 
	Holds password of given user to access database server. Default is ''
*/
define('DATABASE_PASSWORD', '{$password}');

/*
Constant: DATABASE_TYPE 
	Holds type of database server. Default is 'mysql'
*/
define('DATABASE_TYPE', '{$type}');

/*
Constant: DATABASE_NAME 
	Holds database name that conatins Alumni-Online tables. Default is 'AlumniOnline'
*/
define('DATABASE_NAME', '{$name}');


/*
Constant: DATABASE_TABLE_PREFIX 
	Holds table prefix. Default is 'ao_'
*/
define('DATABASE_TABLE_PREFIX', '{$prefix}');

#####################################################################################
/*
Group: Directory Layout

Constant: DIR_APPLICATION_BASE 
	Holds application base directory (DO NOT CHANGE)
*/
define('DIR_APPLICATION_BASE', '{$dirAppBase}');


/*
Constant: DIR_WWW_ROOT 
	Holds directory for application www folder.
	Default is DIR_APPLICATION_BASE.'www'.DIRECTORY_SEPARATOR
*/
define('DIR_WWW_ROOT', '{$dirwwwRoot}');

/*
Constant: DIR_CONTRIB 
	Holds directory for contributed classes. 
	Default is DIR_APPLICATION_BASE.'contrib/'
*/
define('DIR_CONTRIB', '{$dirContrib}');

/*
Constant: DIR_CONTRIB 
	Holds directory for application controls.
	Default is DIR_APPLICATION_BASE.'controls/'
*/
define('DIR_CONTROLS', '{$dirControls}');

/*
Constant: DIR_SMARTY_BASE 
	Holds directory to place smarty generated codes.
	Default is DIR_APPLICATION_BASE.'skins/'
*/
define('DIR_SMARTY_BASE', '{$dirSmartyBase}');

/*
Constant: DIR_TEMPLATES_SITE 
	Holds directory for application skins.
	Default is 'templates/skins/'
*/
define('DIR_TEMPLATES_SITE', 'templates'.DIRECTORY_SEPARATOR.'skins'.DIRECTORY_SEPARATOR);

/*
Constant: DIR_TEMPLATES_CONTAINER 
	Holds directory for application containers.
	Default is 'templates/containers/'
*/
define('DIR_TEMPLATES_CONTAINER', 'templates'.DIRECTORY_SEPARATOR.'containers'.DIRECTORY_SEPARATOR);


#####################################################################################
/*
Group: Application Settings

Constant: APPLICATION_NAME 
	Holds application name (DO NOT CHANGE).
	Default is 'Alumni-Online'
*/
define('APPLICATION_NAME', 'Alumni-Online');

/*
Constant: APPLICATION_VERSION 
	Holds application version (DO NOT CHANGE).
	Default is '3.0.0.1'
*/
define('APPLICATION_VERSION', '3.0.0.1');

#####################################################################################
/*
Group: SmartCoding Tracker Settings

/*
Constant: WS_PROJECT_CODE 
	Projectid for SmartCoding tracker (DO NOT CHANGE).
	Default is '2'
*/
define('WS_PROJECT_CODE', 2);

/*
Constant: WS_COMPONENT_ID 
	Component id for SmartCoding tracker (DO NOT CHANGE).
	Default is '6'
*/
define('WS_COMPONENT_ID', 6);

/*
Constant: WS_VERSION_ID 
	Version id for SmartCoding tracker (DO NOT CHANGE).
	Default is '21'
*/
define('WS_VERSION_ID', 21);

/*
Constant: WS_ISSUE_TYPE 
	Issue type for SmartCoding tracker (DO NOT CHANGE).
	Default is '1' that is bug
*/
define('WS_ISSUE_TYPE', 1);

#####################################################################################
/*
Group: Misc

Constant: SMARTY_CACHING 
	Indicates whether smarty should cache generated templates.
	Default is false.
*/
define('SMARTY_CACHING', false);

/*
Constant: ERROR_REPORTING_LEVEL 
	Indicates error report level. Default is 0.
*/
define('ERROR_REPORTING_LEVEL', 0);