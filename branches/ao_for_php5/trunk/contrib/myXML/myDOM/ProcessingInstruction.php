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
// $Id: ProcessingInstruction.php,v 0.22 2004/01/30 10:07:28 anter Exp $

/**
* @package      myXML
* @subpackage   myDOM
*/
/**
* Base class for other nodes.
*/
require_once('Node.php');

/**
* ProcessingInstruction class.
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
class ProcessingInstruction extends Node
{
    /**
    * The target of this processing instruction.
    * 
    * @var      string
    * @access   public
    */
    var $target;
    
    /**
    * The content of this processing instruction.
    * 
    * @var      string
    * @access   public
    */
    var $data;
  
    /**
     * Constructor.
     * 
     * @param   string
     * @param   string
     * @param   object Document
     * @return  object ProcessingInstruction
     * @access  private
     */
    function ProcessingInstruction($target, $data, &$ownerDocument)
    {
        $this->Node($target, &$ownerDocument);
        $this->nodeType = PROCESSING_INSTRUCTION_NODE;
        $this->target = $target;
        $this->data = $data;
    }
    
    // Introduced in this DOM Implementation:
    /**
     * See DOM for details.
     * 
     * @param   bool                            $deep   Recursive clone
     * @return  object ProcessingInstruction
     * @access  public
     */
    function &cloneNode($deep = false)
    {
        return new ProcessingInstruction($this->target, $this->data, &$this->ownerDocument);
    }
    
    /**
     * ProcessingInstruction::toString()
     * 
     * @return  string
     * @access  public
     */
    function toString()
    {
        return '<?'.$this->target.' '.$this->data.'?>';
    }
}

?>