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
// $Id: Automat.php,v 2.3 2004/02/06 15:22:44 anter Exp $

/**
* @package  myXML
* 
*/
/**
* Error handling.
*/
require_once('Error.php');

/**
* Base class for other parsers.
* 
* A idea taken from article "Парсер на РНР - это возможно!"
* http://detail.phpclub.net/2002-11-29.htm.
* 
* @author       Tereshchenko Andrey <tereshchenko@anter.com.ua>
* @copyright    Tereshchenko Andrey 2002-2003
* @version      2.3 2004/02/06
* @access       private
* @package      myXML
* @link         http://phpmyxml.sourceforge.net/
*/
class Automat extends PEAR
{
    /**
	* Input data.
    * 
	* @var      mixed   {string|array}
    * @access   private
	*/
    var $_data;
    
    /**
	* Size of input data.
    * 
	* @var      integer
    * @access   private
	*/
    var $_count;
    
    /**
	* Current item of input data.
    * 
	* @var      mixed
    * @access   private
	*/
    var $_item;
    
    /**
	* Position of cursor.
    * 
	* @var      integer
    * @access   private
	*/
    var $_cursor = 0;
    
    /**
	* Offset position.
    * 
	* @var      integer
    * @access   private
	*/
    var $_offset = null;
    
    /**
	* Stopping parsing.
    * 
    * If TRUE - stop parsing.
    * 
	* @var      boolean
    * @access   private
	*/
    var $_ending = false;
    
    /**
	* Name of object.
    * 
    * Default establishes a class name.
    * 
	* @var      string
    * @access   private
	*/
    var $_objectName;
    
    /**
	* Initial state of matrix.
    * 
	* @var      string
    * @access   private
	*/
    var $_state = 'begin';
    
    /**
	* Previous state of matrix.
    * 
	* @var      string
    * @access   private
	*/
    var $_prevState = 'begin';
    
    /**
	* Default state of matrix.
    * 
	* @var      string
    * @access   private
	*/
    var $_defState = null;
    
    /**
	* Matrix of states.
    * 
	* @var      array
    * @access   private
	*/
    var $_matrix = array();
    
    /**
	* List of keywords.
    * 
	* @var      array
    * @access   private
	*/
    var $_keywords = array();
    
    /**
	* List of events handlers.
    * 
	* @var      array
    * @access   private
	*/
    var $_stateHandlers = array();
    
    /**
     * Constructor.
     * 
     * @access  public
     */
    function Automat()
    {
        $this->PEAR('Automat_Error');
        $this->_objectName = get_class($this);
    }
    
    /**
     * Start parsing.
     * 
     * @access  public
     */
    function start()
    {
        $this->_item = null;
        $this->_cursor = ($this->_offset !== null) ? $this->_offset : 0;
        $this->_ending = false;
        $this->_state = 'begin';
        $this->_prevState = 0;
        $this->_debug and
            $this->showDebug('Start <b>beginHandler</b>');
		$this->beginHandler();
        while ($this->_cursor < $this->_count && !$this->_ending) {
            $this->_item = $this->_data[$this->_cursor];
            $inData = isset($this->_keywords[$this->_item]) ? $this->_keywords[$this->_item] : $this->_defState;
            $this->_prevState = $this->_state;
            $this->_state = $this->_matrix[$inData][$this->_prevState];
            $this->_debug and
                $this->showDebug('Item: - <b>'.$this->_item.'</b> - State = <b>'.$this->_state.'</b>');
            if ($this->_state == null || $this->_state < 0) {
                $this->_debug and
                    $this->showDebug('Start <b>errorHandler</b>');
			    $this->errorHandler($inData, $this->_prevState, $this->_state);
			}
            if ($method = $this->_stateHandlers[$this->_state]) {
                $this->_debug and
                    $this->showDebug('Start <b>'.$this->_stateHandlers[$this->_state].'</b>');
			    $this->$method();
			}
            if ($this->_cursor == $this->_count - 1) {
                $this->_debug and
                    $this->showDebug('Start <b>endingHandler</b>');
		        $this->endingHandler();
			}
            $this->_cursor++;
        }
        return false;
    }
    
    /**
     * Stop parsing.
     * 
     * @access  public
     */
    function stop()
    {
        $this->_ending = true;
    }
    
    /**
     * Sets input data.
     * 
     * @param   mixed       {string|array}
     * @access  public
     */
    function setData($data)
    {
        $this->_data = $data;
        if (is_array($data)) {
            $this->_count = count($data);
        } elseif (is_string($data)) {
            $this->_count = strlen($data);
        } else {
            return $this->raiseError('wrong data type for first argument');
        }
    }
    
    /**
     * Sets a matrix of states.
     * 
     * @param   array
     * @access  public
     */
    function setMatrix($matrix)
    {
        $line = trim(array_shift($matrix));
        $colKeys = explode(';', $line);
        array_shift($colKeys);
        foreach ($matrix as $line) {
            $rowKeys = explode(';', $line);
            $rowKey = trim(array_shift($rowKeys));
            foreach ($rowKeys as $col => $value) {
                $colKey = trim($colKeys[$col]);
                $this->_matrix[$rowKey][$colKey] = trim($value);
            }
        }
    }
    
    /**
     * Sets a name of object.
     * 
     * @param   string
     * @access  public
     */
    function setObjectName($name)
    {
        $this->_objectName = $name;
    }
    
    /**
     * Sets offset.
     * 
     * @param   integer
     * @access  public
     */
    function setOffset($offset = 0)
    {
        $this->_offset = (integer) $offset;
    }
    
    /**
     * Returns current item of data.
     * 
     * @return  mixed
     * @access  public
     */
    function item()
    {
        return $this->_data[$this->_cursor];
    }
    
    /**
     * Returns next item of data.
     * 
     * @return  mixed
     * @access  public
     */
    function nextItem()
    {
        return $this->_data[$this->_cursor + 1];
    }
    
    /**
     * Switching of debug message (TRUE - on, FALSE - off).
     * 
     * @param   boolean
     * @access  public
     */
    function debug($onOff = true)
    {
        $this->_debug = $onOff;
    }
    
    /**
     * Handler of debug messages.
     * 
     * @param   string
     * @return  string
     * @access  private
     */
    function showDebug($msg)
    {
        $msg = "<br>{$this->_objectName}::$msg";
        print($msg);
        flush;
    }
    
    /**
     * Handler of event "Matrix error".
     * 
     * @param   string  Row in matrix
     * @param   string  Column in matrix
     * @access  private
     */
    function errorHandler($row, $col, $code)
	{
	    $msg = $this->errorMessage($code);
        array_splice($this->_data, $this->_cursor, 0, array('^'));
        $path = implode('', $this->_data);
        return $this->raiseError("syntax error, $msg in path: $path");
	}
    
    /**
     * Returns error message by code.
     * 
     * @param   integer
     * @return  string
     * @access  public
     */
    function errorMessage($code)
    {
        static $messages;
        if (!isset($messages)) {
            $messages = array(
                -1 => 'unknown error',
                ); 
        }
        settype($code, 'integer');
        return $code ? $messages[$code] : $messages[-1];
    }
    
    /**
     * Abstract method.
     * 
     * Must be remould in descendants.
     * 
     * @access  private
     * @abstract
     */
    function beginHandler()
	{
	    //Abstract method
	}
    
    /**
     * Abstract method.
     * 
     * Must be remould in descendants.
     * 
     * @access  private
     * @abstract
     */
    function endingHandler()
	{
	    //Abstract method
	}
    
    /**
     * Automat::raiseError()
     * 
     * @access  private
     */
    function raiseError($message = 'unknown error', $code = null,
                        $mode = null, $options = null, $userinfo = null)
    {
        $this->stop();
        parent::raiseError($message, $code, $mode, $options, $userinfo);
    }
}

/**
* Automat_Error class
* 
* @access   private
*/
class Automat_Error extends Error
{
    var $skipClass = 'automat';
    
    function Automat_Error($message = 'unknown error', $code = null,
                           $mode = null, $options = null, $userinfo = null)
    {
        $this->Error($message, $code, $mode, $options, $userinfo);
    }
}

?>