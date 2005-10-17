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
// $Id: Text.php,v 0.2 2003/05/23 18:10:01 anter Exp $

/**
* @package      myXML
* @subpackage   myDOM
*/
/**
* Base class for other character data nodes.
*/
require_once('CharacterData.php');

/**
* Text class.
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
class Text extends CharacterData
{
    /**
     * Constructor.
     * 
     * @param   string
     * @param   object Document
     * @return  object Text
     * @access  private
     */
    function Text($data, &$ownerDocument)
    {
        $this->CharacterData('#text', $data, &$ownerDocument);
        $this->nodeType = TEXT_NODE;
    }
    
    /**
     * Breaks this node into two nodes at the specified offset, keeping both
     * in the tree as siblings.
     * 
     * Not implemented in this version.
     * 
     * @param   integer
     * @access  public
     */
    function splitText($offset)
    {
        return $this->raiseError('the function "splitText" is not support in this version');
    }
    
    // Introduced in this DOM Implementation:
    /**
     * See DOM for details.
     * 
     * @param   boolean
     * @return  object Text
     * @access  public
     */
    function &cloneNode($deep = false)
    {
        return new Text($this->data, &$this->ownerDocument);
    }
    
}

?>