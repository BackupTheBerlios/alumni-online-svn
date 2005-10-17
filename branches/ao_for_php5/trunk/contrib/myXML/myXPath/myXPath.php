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
// $Id: myXPath.php,v 0.3 2003/07/15 14:59:30 anter Exp $

/**
* @package      myXML
* @subpackage   myXPath
*/
/**
* Error handling.
*/
require_once('Error.php');

/**
* Separate XPath expression by steps.
*/
require_once('XPathParser.php');

/**
* Hadling of predicate.
*/
require_once('XPredicateParser.php');

/**
* myXPath class.
* 
* See XPath for details.
* 
* @author       Tereshchenko Andrey <tereshchenko@anter.com.ua>
* @copyright    Tereshchenko Andrey 2002-2003
* @version      0.3 2003/07/15
* @access       public
* @package      myXML
* @subpackage   myXPath
* @link         http://phpmyxml.sourceforge.net/
*/
class myXPath extends PEAR
{
    /**
    * XPath expression.
    * 
    * @var      string
    * @access   private
    */
    var $_path;
    
    /**
    * Reference on object Document.
    * 
    * @var      object Document
    * @access   private
    */
    var $_dom;
    
    /**
    * Reference on object XPathParser.
    * 
    * @var      object XPathParser
    * @access   private
    */
    var $_parser;
    
    /**
    * List of reference on object Node.
    * 
    * Uses for context selecting.
    * 
    * @var      array
    * @access   private
    */
    var $_context = array();
    
    /**
    * List of reference on object Node.
    * 
    * Contains selecting result.
    * 
    * @var      array
    * @access   private
    */
    var $_nodeSet = array();
    
    /**
    * List of reference on object Node.
    * 
    * Uses for result union.
    * 
    * @var      array
    * @access   private
    */
    var $_union = array();
    
    /**
    * Enable markup result;
    * 
    * @var      boolean
    * @access   private
    */
    var $_markup = false;
    
    /**
     * Constructor.
     * 
     * @param   object Document
     * @return  object myXPath
     * @access  public
     * @static  method
     */
    function &create(&$dom)
    {
        $proc =& new myXPath;
        $proc->setDOMDocument(&$dom);
        return $proc;
    }
    
    /**
     * Constructor. (deprecated)
     * 
     * @param   object Document
     * @return  object myXPath
     * @access  public
     */
    function myXPath()
    {
        $this->PEAR('XPath_Error');
        $this->_parser =& new XPathParser;
    }
    
    /**
     * Evaluates XPath expression.
     * 
     * @param   string
     * @return  array
     * @access  public
     */
    function evaluate($path)
    {
        $this->_isPrepared();
        $this->_path = trim($path);
        $data = $this->_separate($this->_path);
        $this->_nodeSet = $this->_context;
        $this->_parser->setErrorHandling(PEAR_ERROR_CALLBACK, array(&$this, '_handleParserError'));
        $this->_parser->debug($this->_debug);
        $this->_parser->setOwner(&$this);
        $this->_parser->setData($data);
        $this->_parser->start();
        return $this->_nodeSet;
    }
    
    /**
     * Sets reference on object Document.
     * 
     * @param   object Document
     * @return  boolean
     * @access  public
     */
    function setDOMDocument(&$dom)
    {
        if (!Document::isInherited($dom)) {
            return $this->raiseError('the first argument must be object DOM');
        }
        $this->_dom =& $dom;
        $this->setContext(array(&$dom));
    }
    
    /**
     * Sets selecting context.
     * 
     * Sets initial context of selecting. But, if you use absolute location
     * path, then method evaluate() selects from root of document.
     * 
     * @param   array       list of objects inherited from Node.
     * @param   boolean     for internal use.
     * @access  public
     */
    function setContext($context, $markup = false)
    {
        if (!is_array($context)) {
            return $this->raiseError('the first argument must be array');
        }
        foreach ($context as $node) {
            if (!Node::isInherited($node)) {
                return $this->raiseError('the context must contain objects Node');
            }
            if ($this->_dom->_ID != $node->ownerDocument->_ID) {
                return $this->raiseError('node is used in a different document
                    than the one that created it');
            }
        }
        $this->_context = $context;
        $this->_markup = (bool) $markup;
        if ($this->_markup) {
            $length = sizeof($this->_context);
            for ($n = 0; $n < $length; $n++) {
                $this->_context[$n]->_context = $this->_context[$n]->_context
                    ? $this->_context[$n]->_context
                    : $this->_context[$n]->_ID;
            }
        }
    }
    
    /**
     * Switching debug messages (TRUE - on, FALSE - off).
     * 
     * @param   boolean
     * @access  public
     */
    function debug($onOff = true)
    {
        $this->_debug = $onOff;
    }
    
    /**
     * myXPath::_handleParserError()
     * 
     * @access  private
     */
    function _handleParserError(&$error)
    {
        ini_set('error_reporting', E_USER_ERROR | E_USER_WARNING | E_USER_NOTICE);
        $this->raiseError($error->getMessage(), null, null, null, $error->getUserInfo());
    }
    
    /**
     * Checks preparing object.
     * 
     * @return  boolean
     * @access  private
     */
    function _isPrepared()
    {
        if (!$this->_dom) {
            return $this->raiseError('set the DOM document before evaluating', E_USER_ERROR, SUBCLASS_LEVEL);
        }
    }
    
    /**
     * Separates XPath expression into array of word.
     * 
     * @param   string
     * @return  array
     * @access  private
     */
    function _separate($path)
    {
        global $XPathPattern;
        @preg_match_all($XPathPattern, $path, $matches);
        if (is_array($matches[0])) {
            return $this->_stripWhiteSpace($matches[0]);
        } else {
            return $this->raiseError($php_errormsg);
        }
    }
    
    /**
     * myXPath::_stripWhiteSpace()
     * 
     * @access  private
     */
    function _stripWhiteSpace($array)
    {
        $filtered = array();
        foreach ($array as $item) {
            $item = trim($item);
            if ($item != '') {
                array_push($filtered, $item);
            }
        }
        return $filtered;
    }
}

/**
* XPath_Error class
* 
* @access   private
*/
class XPath_Error extends Error
{
    var $error_message_prefix = 'myXPath error: ';
    var $skipClass = 'myxpath';
    
    function XPath_Error($message = 'unknown error', $code = null,
                         $mode = null, $options = null, $userinfo = null)
    {
        $this->Error($message, $code, $mode, $options, $userinfo);
    }
}

global $QName, $Literal, $NameTest, $XPathPattern;

$Letter         = " [^\d\W] ";
$Digit          = " \d ";
$NCNameChar     = " $Letter | $Digit | \. | - | _ ";
$NCName         = " (?: $Letter | _ )(?: $NCNameChar )* ";
$QName          = " (?: $NCName: )? $NCName ";
$Literal        = " \"[^\"]*\" | '[^']*' ";
$Digits         = " [0-9]+ ";
$Number         = " $Digits(?: \.$Digits )? | \.$Digits ";
$OperatorName   = " and | or | mod | div ";
$Operator       = "
                    $OperatorName
                    | \/\/ | \/ | \| | \+ | - | = | != | <= | < | >= | > ";
$NameTest       = " \* | $NCName:\* | $QName ";
$NodeType       = " comment | text | processing-instruction | node ";
$AxisName       = " 
                    ancestor
                    | ancestor-or-self
                    | attribute
                    | child
                    | descendant
                    | descendant-or-self
                    | following
                    | following-sibling
                    | namespace
                    | parent
                    | preceding
                    | preceding-sibling
                    | self ";
$ExprToken      = "
                    \(\) | \( | \) | \[ | \] | \.\. | \. | @ | , | ::
                    | $NameTest
                    | $NodeType
                    | $Operator
                    | $AxisName
                    | $Literal
                    | $Number
                    | \s ";
$XPathPattern   = " /$ExprToken/x ";

?>