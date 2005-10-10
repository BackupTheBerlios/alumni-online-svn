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
// $Id: Attr.php,v 0.21 2004/01/30 09:56:38 anter Exp $

/**
* @package      myXML
* @subpackage   myDOM
*/
/**
* Base class for other nodes.
*/
require_once('Node.php');

/**
* Attr class.
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
class Attr extends Node
{
    /**
    * Returns the name of this attribute.
    * 
    * @var      string
    * @access   public
    */
    var $name;
    
    /**
    * See DOM for details.
    * 
    * @var      boolean
    * @access   public
    */
    var $specified = false;
    
    /**
    * On retrieval, the value of the attribute is returned as a string.
    * 
    * @var      string
    * @access   public
    */
    var $value = '';
    
    // Introduced in DOM Level 2:
    /**
    * The Element node this attribute is attached to or null if this attribute
    * is not in use.
    * 
    * @var      object Element
    * @access   public
    */
    var $ownerElement = null;

    /**
     * Constructor.
     * 
     * @param   string
     * @param   object Document
     * @param   string
     * @return  object Attr
     * @access  private
     */
    function Attr($name, &$ownerDocument, $namespaceURI = null)
    {
        $this->Node($name, &$ownerDocument);
        $this->name = $name;
        $this->nodeType = ATTRIBUTE_NODE;
        $this->namespaceURI = $namespaceURI;
        if ($namespaceURI) {
            $this->prefix = substr($name, 0, strpos($name, ':'));
            $this->localName = substr($name, strpos($name, ':') + 1);
        }
    }
    
    // Introduced in this DOM Implementation:
    /**
     * See DOM for details.
     * 
     * @param   boolean
     * @return  object Attr
     * @access  public
     */
    function &cloneNode($deep = false)
    {
        $newNode =& new Attr($this->name, &$this->ownerDocument, $this->namespaceURI);
        $newNode->specified = true;
        $newNode->value = $this->value;
        return $newNode;
    }
    
    /**
     * Attr::isInherited()
     * 
     * @param   object  inherited from Attr
     * @return  boolean
     * @access  public
     * @static  method
     */
    function isInherited(&$node)
    {
        return (is_object($node) && is_a($node, 'Attr'));
    }
    
    /**
     * Attr::toString()
     * 
     * @return  string
     * @access  public
     */
    function toString()
    {
        return ' '.$this->name.'="'.$this->value.'"';
    }
}

?>