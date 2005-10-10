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
// $Id: CDATASection.php,v 0.22 2004/01/30 10:03:57 anter Exp $

/**
* @package      myXML
* @subpackage   myDOM
*/
/**
* Base class for other text nodes.
*/
require_once('Text.php');

/**
* CDATASection class.
* 
* See DOM for details.
* 
* @author       Tereshchenko Andrey <tereshchenko@anter.com.ua>
* @copyright    Tereshchenko Andrey 2002-2003
* @version      0.22 2004/01/30
* @access       public
* @package      myXML
* @subpackage   myDOM
* @link         http://phpmyxml.sourceforge.net/
*/
class CDATASection extends Text
{
    /**
     * Constructor.
     * 
     * @param   string
     * @param   object Document
     * @return  object CDATASection
     * @access  private
     */
    function CDATASection($data, &$ownerDocument)
    {
        $this->CharacterData('#cdata-section', $data, &$ownerDocument);
        $this->nodeType = CDATA_SECTION_NODE;
    }
    
    // Introduced in this DOM Implementation:
    /**
     * See DOM for details.
     * 
     * @param   boolean
     * @return  object CDATASection
     * @access  public
     */
    function &cloneNode($deep = false)
    {
        return new CDATASection($this->data, &$this->ownerDocument);
    }
    
    /**
     * CDATASection::toString()
     * 
     * @return  string
     * @access  public
     */
    function toString()
    {
        return '<![CDATA['.parent::toString().']]>';
    }
}

?>