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
// | Authors:                                                             |
// |     Tereshchenko Andrey <tereshchenko@anter.com.ua>                  |
// +----------------------------------------------------------------------+
//
// $Id: CharacterData.php,v 0.21 2004/01/30 10:03:20 anter Exp $

/**
* @package      myXML
* @subpackage   myDOM
*/
/**
* Base class for other nodes.
*/
require_once('Node.php');

/**
* CharacterData class.
* 
* See DOM for details.
* 
* @author       Tereshchenko Andrey <tereshchenko@anter.com.ua>
* @copyright    Tereshchenko Andrey 2002-2003
* @version      0.21 2004/01/30
* @access       public
* @package      myXML
* @subpackage   myDOM
* @link         http://phpmyxml.sourceforge.net/
*/
class CharacterData extends Node
{
    /**
    * The character data of the node that implements this interface.
    * 
    * @var      string
    * @access   public
    */
    var $data;
    
    /**
    * The number of 16-bit units that are available through data and the
    * substringData method below.
    * 
    * @var      integer
    * @access   public
    */
    var $length;
    
    /**
     * Constructor.
     * 
     * @param   string
     * @param   string
     * @param   object Document
     * @return  object CharacterData
     * @access  private
     */
    function CharacterData($name, $data, &$ownerDocument)
    {
        $this->Node($name, &$ownerDocument);
        $this->data = $data;
        $this->length = strlen($data);
    }
    
    /**
     * Extracts a range of data from the node.
     * 
     * Not implemented in this version.
     * 
     * @param   int
     * @param   int
     * @access  public
     */
    function substringData($offset, $count)
    {
        return $this->raiseError('the function "substringData" is not support in this version');
    }
    
    /**
     * Append the string to the end of the character data of the node.
     * 
     * Not implemented in this version.
     * 
     * @param   string
     * @access  public
     */
    function appendData($arg)
    {
        return $this->raiseError('the function "appendData" is not support in this version');
    }
    
    /**
     * Insert a string at the specified 16-bit unit offset.
     * 
     * Not implemented in this version.
     * 
     * @param   int
     * @param   string
     * @access  public
     */
    function insertData($offset, $arg)
    {
        return $this->raiseError('the function "insertData" is not support in this version');
    }
    
    /**
     * Remove a range of 16-bit units from the node.
     * 
     * Not implemented in this version.
     * 
     * @param   int
     * @param   int
     * @access  public
     */
    function deleteData($offset, $count)
    {
        return $this->raiseError('the function "deleteData" is not support in this version');
    }
    
    /**
     * Replace the characters starting at the specified 16-bit unit offset with
     * the specified string.
     * 
     * Not implemented in this version.
     * 
     * @param   int
     * @param   int
     * @param   string
     * @access  public
     */
    function replaceData($offset, $count, $arg)
    {
        return $this->raiseError('the function "replaceData" is not support in this version');
    }
    
    /**
     * CharacterData::toString()
     * 
     * @return  string
     * @access  public
     */
    function toString()
    {
        return $this->data;
    }
}

?>