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
// $Id: Comment.php,v 0.21 2004/01/30 10:02:24 anter Exp $

/**
* @package      myXML
* @subpackage   myDOM
*/
/**
* Base class for other character data nodes.
*/
require_once('CharacterData.php');

/**
* Comment class.
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
class Comment extends CharacterData
{
    /**
     * Constructor.
     * 
     * @param   string
     * @param   object Document
     * @return  object Comment
     * @access  private
     */
    function Comment($data, &$ownerDocument)
    {
        $this->CharacterData('#comment', $data, &$ownerDocument);
        $this->nodeType = COMMENT_NODE;
    }
    
    /**
     * Comment::toString()
     * 
     * @return  string
     * @access  public
     */
    function toString()
    {
        return '<!--'.parent::toString().'-->';
    }
}

?>