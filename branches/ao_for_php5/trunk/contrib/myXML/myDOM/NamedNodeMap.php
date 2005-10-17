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
// $Id: NamedNodeMap.php,v 0.3 2004/02/06 10:54:25 anter Exp $

/**
* @package      myXML
* @subpackage   myDOM
*/
/**
* NamedNodeMap class.
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
class NamedNodeMap
{
    /**
    * The number of nodes in this map.
    * 
    * @var      int
    * @access   public 
    */
    var $length;
    
    /**
    * Named list of reference on object Node.
    * 
    * @var      array
    * @access   private
    */
    var $_namedMap = array();
    
    /**
    * Index list of reference on object Node.
    * 
    * @var      array
    * @access   private
    */
    var $_indexMap = array();
    
    /**
     * Retrieves a node specified by name.
     * 
     * @param   string
     * @return  object Node
     * @access  public
     */
    function &getNamedItem($name)
    {
        return $this->_namedMap[$name];
    }
    
    /**
     * Adds a node using its nodeName attribute.
     * 
     * @param   object Node
     * @return  object Node
     * @access  public
     */
    function &setNamedItem(&$arg)
    {
        if (!$oldNode = $this->_namedMap[$arg->nodeName]) {
            array_push($this->_indexMap, &$arg);
        } else {
            $index = $this->isExists($arg->nodeName);
            $this->_indexMap[$index] =& $arg;
        }
        $this->_namedMap[$arg->nodeName] =& $arg;
        $this->length = sizeof($this->_indexMap);
        return $oldNode;
    }
    
    /**
     * Removes a node specified by name.
     * 
     * @param   string
     * @access  public
     */
    function &removeNamedItem($name)
    {
        $oldNode = $this->_namedMap[$name];
        unset($this->_namedMap[$name]);
        $index = $this->isExists($name);
        array_splice($this->_indexMap, $index, 1);
        $this->length = sizeof($this->_indexMap);
        return $oldNode;
    }
    
    /**
     * Returns the indexth item in the map. 
     * 
     * @param   integer
     * @return  object Node
     * @access  public
     */
    function &item($index)
    {
        if (!array_key_exists($index, $this->_indexMap)) {
            raiseError('myDOM error: index is negative, or greater than the allowed value');
        }
        return $this->_indexMap[$index];
    }
    
    // Introduced in DOM Level 2:
    /**
     * Retrieves a node specified by local name and namespace URI.
     * 
     * @param   string
     * @param   string
     * @return  object Node
     * @access  public
     */
    function &getNamedItemNS($namespaceURI, $localName)
    {
        return $this->_namedMap[$namespaceURI][$localName];
    }
    
    /**
     * Adds a node using its namespaceURI and localName. 
     * 
     * @param   object Node
     * @return  object Node
     * @access  public
     */
    function &setNamedItemNS(&$arg)
    {
        if (!$oldNode = $this->_namedMap[$arg->namespaceURI][$arg->localName]) {
            array_push($this->_indexMap, &$arg);
        } else {
            $index = $this->isExists($name);
            $this->_indexMap[$index] =& $arg;
        }
        $this->_namedMap[$arg->namespaceURI][$arg->localName] =& $arg;
        $this->length = sizeof($this->_indexMap);
        return $oldNode;
    }
    
    /**
     * Removes a node specified by local name and namespace URI.
     * 
     * Not implemented in this version.
     * 
     * @param   string
     * @param   string
     * @access  public
     */
    function removeNamedItemNS($namespaceURI, $localName)
    {
        raiseError('myDOM error: the function "removeNamedItemNS" is not support in this version');
    }
    
    /**
     * NamedNodeMap::isExists()
     * 
     * @param   string
     * @return  integer
     * @access  private
     */
    function isExists($name)
    {
        foreach ($this->_indexMap as $index => $node) {
            if ($node->nodeName == $name) {
                return $index;
            }
        }
        return false;
    }
}

?>