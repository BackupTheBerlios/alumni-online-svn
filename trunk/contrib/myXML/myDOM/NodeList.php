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
// $Id: NodeList.php,v 0.3 2004/02/06 10:52:57 anter Exp $

/**
* @package      myXML
* @subpackage   myDOM
*/
/**
* NodeList class.
* 
* See DOM for details.
* 
* @author       Tereshchenko Andrey <tereshchenko@anter.com.ua>
* @copyright    Tereshchenko Andrey 2002-2003
* @version      0.3 2004/02/06
* @access       private
* @package      myXML
* @subpackage   myDOM
* @link         http://phpmyxml.sourceforge.net/
*/
class NodeList
{
    /**
    * The number of nodes in the list.
    * 
    * @var      integer
    * @access   public 
    */
    var $length = 0;
    
    /**
    * List of reference on object Node.
    * 
    * @var      array
    * @access   private 
    */
    var $_list = array();
    
    /**
     * Returns the indexth item in the collection.
     * 
     * @param   integer
     * @return  object Node
     * @access  public
     */
    function &item($index)
    {
        if (!array_key_exists($index, $this->_list)) {
            raiseError('myDOM error: index is negative, or greater than the allowed value');
        }
        return $this->_list[$index];
    }
    
    /**
     * Adds new node to the list.
     * 
     * @param   object Node
     * @access  public
     */
    function addItem(&$item, $offset = null, $replace = 0)
    {
        $offset = ($offset !== null) ? $offset : $this->length;
        array_splice($this->_list, $offset, $replace, array(&$item));
        $this->length = sizeof($this->_list);
    }
    
    /**
     * Removes node from the list.
     * 
     * @param   object Node
     * @access  public
     */
    function &removeItem($index)
    {
        $oldChild =& $this->_list[$index];
        array_splice($this->_list, $index, 1);
        $this->length = sizeof($this->_list);
        return $oldChild;
    }
    
    /**
     * NodeList::isExists()
     * 
     * @param   object
     * @return  boolean
     * @access  public
     */
    function isExists(&$node)
    {
        for ($i = 0; $i < $this->length; $i++) {
            if ($node->_ID == $this->_list[$i]->_ID) {
                return $i;
            }
        }
        return false;
    }
}


?>