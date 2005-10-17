<?php
//
// +----------------------------------------------------------------------+
// | PHP Version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 2002-2003 Tereshchenko Andrey. All rights reserved.    |
// +----------------------------------------------------------------------+
// | This source file is free software; you can redistribute it and/or    |
// | modify it under the terms of the GNU Lesser General Public           |
// | License as published by the Free Software Foundation; either         |
// | version 2.1 of the License, or (at your option) any later version.   |
// |                                                                      |
// | This source file is distributed in the hope that it will be useful,  |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of       |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU    |
// | Lesser General Public License for more details.                      |
// +----------------------------------------------------------------------+
// | Author: Tereshchenko Andrey <tereshchenko@anter.com.ua>              |
// +----------------------------------------------------------------------+
//
// $Id: Error.php,v 1.0 2004/02/05 17:18:10 anter Exp $

/**
* @package  myXML
*/
/**
* Base class for other PEAR classes.
*/
require_once('PEAR.php');

PEAR::setErrorHandling(PEAR_ERROR_RETURN, E_USER_ERROR);

/**
* Contains user-defined error handler
* 
* @global   string  $GLOBALS['_ErrorHandler']
* @name     $_ErrorHandler
*/
$GLOBALS['_ErrorHandler'] = null;

/**
* Design mode flag.
* 
* If you the designer of a class where error occurred, use the design mode
* "true" for errors debug. If you the user of a class where error occurred, use
* the design mode "false".
* 
* @global   boolean $GLOBALS['_Design_Mode']
* @name     $_Design_Mode
*/
$GLOBALS['_Design_Mode'] = false;

/**
* Levels of call. 
*/
define('TOP_LEVEL', 0);
define('FUNC_LEVEL', 1);
define('CLASS_LEVEL', 2);

/**
 * Generates a user-level error/warning/notice message.
 * 
 * Is identical to the PHP function "trigger_error()", but it uses possibilities
 * of PHP function "debug_backtrace()" for display of the debug information of
 * caller function. See error_test.php file for example.
 * 
 * Function "debug_backtrace()" present in PHP since version 4.3.0. In previous
 * versions "raiseError()" calls "trigger_error()".
 * 
 * @param   string  error message.
 * @param   integer optional parameter, error type, default type E_USER_ERROR.
 * @param   integer optional parameter, level of caller function, default level FUNC_LEVEL.
 * @see     setErrorHandler
 */
function raiseError($message, $type = null, $callLevel = FUNC_LEVEL)
{
    if (!function_exists('debug_backtrace')) {
        return trigger_error($message, $type);
    }
    $format = "<br/><b>%s:</b> %s in <b>%s</b> on line <b>%s</b><br/>";
    $trace = debug_backtrace();
    $file = ($trace[$callLevel]['file']) ? $trace[$callLevel]['file'] : $trace[TOP_LEVEL]['file'];
    $line = ($trace[$callLevel]['line']) ? $trace[$callLevel]['line'] : $trace[TOP_LEVEL]['line'];
    $type = ($type === null) ? E_USER_ERROR : $type;
    $error_reporting = ini_get('error_reporting');
    if (function_exists($GLOBALS['_ErrorHandler'])) {
        $GLOBALS['_ErrorHandler']($message, $type, $file, $line);
    } else {
        if ($error_reporting & $type) {
            switch ($type) {
            case E_USER_NOTICE:
                $message = sprintf($format, "Notice", $message, $file, $line);
                print($message);
                break;
            case E_USER_WARNING:
                $message = sprintf($format, "Warning", $message, $file, $line);
                print($message);
                break;
            case E_USER_ERROR:
                $message = sprintf($format, "Fatal error", $message, $file, $line);
                exit($message);
            }
        }
    }
}

/**
 * Sets a user-defined error handler function.
 * 
 * Is identical to the PHP function "set_error_handler()", but for
 * "raiseError()".
 * 
 * The user function needs to accept four parameters: a string describing the
 * error, the error type, the filename in which the error occurred, and the line
 * number in which the error occurred.
 * 
 * @param   string  function name.
 * @see     raiseError
 */
function setErrorHandler($handler)
{
    if (function_exists($handler)) {
        $GLOBALS['_ErrorHandler'] = $handler;
    } else {
        raiseError("function '$handler' not exists", E_USER_WARNING);
    }
}

/**
 * Returns name of caller function.
 * 
 * It uses possibilities of PHP function "debug_backtrace()", which present in
 * PHP since version 4.3.0.
 * 
 * @return  string  function name if present.
 * @see     getCallerClass
 */
function getCallerMethod()
{
    if (function_exists('debug_backtrace')) {
        $trace = debug_backtrace();
        return $trace[2]['function'];
    } else {
        raiseError("function 'debug_backtrace' not exists in this version PHP", E_USER_NOTICE);
    }
}

/**
 * Returns name of class of caller method.
 * 
 * It uses possibilities of PHP function "debug_backtrace()", which present in
 * PHP since version 4.3.0.
 * 
 * @return  string  class name if present.
 * @see getCallerMethod
 */
function getCallerClass()
{
    if (function_exists('debug_backtrace')) {
        $trace = debug_backtrace();
        return $trace[2]['class'];
    } else {
        raiseError("function 'debug_backtrace' not exists in this version PHP", E_USER_NOTICE);
    }
}

/**
* Error class.
* 
* This class works when mode PEAR_ERROR_RETURN is established. In other modes
* he works as is stipulated in PEAR error handling. See PEAR manual for details.
* 
* @access   public
*/
class Error extends PEAR_Error
{    
    /**
    * Name in lowercase of class, that must be skipped.
    * 
    * Usually a class which errors are processed.
    * 
    * @var      string
    * @access   public
    */
    var $skipClass = 'error';
    
    /**
     * Error constructor.
     * 
     * @return  object  Error
     * @access  public
     */
    function Error($message = 'unknown error', $code = null,
                   $mode = null, $options = null, $userinfo = null)
    {
        $this->PEAR_Error($message, $code, $mode, $options, $userinfo);
        if ($this->mode & PEAR_ERROR_RETURN) {
            $callLevel = ($GLOBALS['_Design_Mode']) ? 4 : $this->_skipClass();
            raiseError($this->getMessage().', userinfo: '.$this->getUserInfo(), $this->level, $callLevel);
        }
    }
    
    /**
     * Returns the index of first occurence of method of class specified.
     * 
     * @return  integer
     * @access  private
     */
    function _skipClass()
    {
        for ($i = sizeof($this->backtrace); $i >= 0; $i--) {
            if ($this->backtrace[$i]['class'] == $this->skipClass ||
                get_parent_class($this->backtrace[$i]['class']) == $this->skipClass) {
                return $i;
            }
        }
    }
}

?>