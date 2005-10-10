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
// $Id: DocumentType.php,v 0.2 2003/05/23 18:01:30 anter Exp $

/**
* @package      myXML
* @subpackage   myDOM
*/
/**
* Base class for other nodes.
*/
require_once('Node.php');

/**
* DocumentType class.
* 
* See DOM for details.
* 
* @author       Tereshchenko Andrey <tereshchenko@anter.com.ua>
* @copyright    Tereshchenko Andrey 2002-2003
* @version      0.2 2003/05/23
* @access       public
* @package      myXML
* @subpackage   myDOM
* @link         http://phpmyxml.sourceforge.net/
*/
class DocumentType extends Node
{
    /**
    * The name of DTD; i.e., the name immediately following the DOCTYPE keyword.
    * 
    * @var      string
    * @access   public
    */
    var $name;
    
    /**
    * A NamedNodeMap containing the general entities, both external and
    * internal, declared in the DTD.
    * 
    * @var      object NamedNodeMap
    * @access   public
    */
    var $entities;
    
    /**
    * A NamedNodeMap containing the notations declared in the DTD.
    * 
    * @var      object NamedNodeMap
    * @access   public
    */
    var $notations;
    
    // Introduced in DOM Level 2:
    /**
    * The public identifier of the external subset.
    * 
    * @var      string
    * @access   public
    */
    var $publicId;
    
    /**
    * The system identifier of the external subset.
    * 
    * @var      string
    * @access   public
    */
    var $systemId;
    
    /**
    * The internal subset as a string.
    * 
    * @var      string
    * @access   public
    */
    var $internalSubset;
    
    /**
     * Constructor.
     * 
     * @param   string
     * @param   object Document
     * @return  object DocumentType
     * @access  private
     */
    function DocumentType($name, &$ownerDocument)
    {
        $this->Node($name, &$ownerDocument);
        $this->name = $name;
        $this->nodeType = DOCUMENT_TYPE_NODE;
        $this->entities = new NamedNodeMap;
        $this->notations = new NamedNodeMap;
    }
    
    //Introduced in this DOM implementation:
    /**
     * Adds object Entity to the list.
     * 
     * @param   object Entity
     * @access  private
     */
    function _setEntity(&$entity)
    {
        $this->entities->setNamedItem(&$entity);
        
    }
    
    /**
     * Returns object Entity.
     * 
     * @param   string
     * @return  object Entity
     * @access  private
     */
    function &_getEntity($name)
    {
        return $this->entities->getNamedItem($name);
    }
    
}

?>