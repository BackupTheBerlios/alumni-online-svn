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
// $Id: Entity.php,v 0.2 2003/05/23 18:03:39 anter Exp $

/**
* @package      myXML
* @subpackage   myDOM
*/
/**
* Base class for other nodes.
*/
require_once('Node.php');

/**
* Entity class.
* 
* See DOM for details.
* 
* @author       Tereshchenko Andrey <tereshchenko@anter.com.ua>
* @copyright    Tereshchenko Andrey 2002-2003
* @version      0.2 2003/05/23
* @access       private
* @package      myXML
* @subpackage   myDOM
* @link         http://phpmyxml.sourceforge.net/
*/
class Entity extends Node
{
    /**
    * The public identifier associated with the entity, if specified.
    * 
    * @var      string
    * @access   public
    */
    var $publicId;
    
    /**
    * The system identifier associated with the entity, if specified.
    * 
    * @var      string
    * @access   public
    */
    var $systemId;
    
    /**
    * For unparsed entities, the name of the notation for the entity.
    * For parsed entities, this is null.
    * 
    * @var      string
    * @access   public
    */
    var $notationName;
  
    /**
     * Constructor.
     * 
     * @param   object Document
     * @param   string
     * @param   string
     * @param   string
     * @return  object Entity
     * @access  private
     */
    function Entity(&$ownerDocument, $publicId = null, $systemId = null, $notationName = null)
    {
        $this->Node($publicId, &$ownerDocument);
        $this->nodeType = ENTITY_NODE;
        $this->publicId = $publicId;
        $this->systemId = $systemId;
        $this->notationName = $notationName;
    }
    
}

?>