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
// $Id: XPathParser.php,v 0.31 2004/01/27 16:15:09 anter Exp $

/**
* @package      myXML
* @subpackage   myXPath
*/
/**
* Base class for other parser.
*/
require_once('Automat.php');

/**
* Separate XPath expression by steps.
* 
* @author       Tereshchenko Andrey <tereshchenko@anter.com.ua>
* @copyright    Tereshchenko Andrey 2002-2003
* @version      0.31 2004/01/27
* @access       private
* @package      myXML
* @subpackage   myXPath
* @link         http://phpmyxml.sourceforge.net/
*/
class XPathParser extends Automat
{
    /**
    * List of special symbols.
    * 
    * @var      array
    * @access   private
    */
    var $_keywords = array(
            '/' => '/', '//' => '//', '::' => '::', '*' => '*',
            
            'ancestor' => 'AxisName', 'ancestor-or-self' => 'AxisName',
            'attribute' => 'AxisName', 'child' => 'AxisName',
            'descendant' => 'AxisName', 'descendant-or-self' => 'AxisName',
            'following' => 'AxisName', 'following-sibling' => 'AxisName',
            'namespace' => 'AxisName', 'parent' => 'AxisName',
            'preceding' => 'AxisName', 'preceding-sibling' => 'AxisName',
            'self' => 'AxisName',
            
            '(' => '(', ')' => ')', '()' => '()', '[' => '[', ']' => ']',
            
            '.' => 'AbbreviatedStep', '..' => 'AbbreviatedStep',
            '@' => 'AbbreviatedAxisSpecifier', '|' => 'Union',
            
            'or' => 'Operator', 'and' => 'Operator', '=' => 'Operator',
            '!=' => 'Operator', '<' => 'Operator', '>' => 'Operator',
            '<=' => 'Operator', '>=' => 'Operator', '+' => 'Operator',
            '-' => 'Operator', 'div' => 'Operator', 'mod' => 'Operator',
            
            'comment' => 'NodeType', 'text' => 'NodeType', 'node' => 'NodeType',
            'processing-instruction' => 'processing-instruction',
            
            ' ' => 'S'
            );

    
    /**
    * List of XPath axis.
    * 
    * See XPath for details.
    * 
    * @var      array
    * @access   private
    */
    var $_axis = array(
            'ancestor' => 'ancestorHandler', 'ancestor-or-self' => 'ancestorOrSelfHandler', 'attribute' => 'attributeHandler',
            'child' => 'childHandler', 'descendant' => 'descendantHandler', 'descendant-or-self' => 'descendantOrSelfHandler',
            'following' => 'followingHandler', 'following-sibling' => 'followingSiblingHandler', 'namespace' => 'namespaceHandler',
            'parent' => 'parentHandler', 'preceding' => 'precedingHandler', 'preceding-sibling' => 'precedingSiblingHandler',
            'self' => 'selfHandler', '.' => '_shortSyntaxHandler', '..' => '_shortSyntaxHandler', '@' => '_shortSyntaxHandler',
            );
    
    /**
    * List of nodes test.
    * 
    * See XPath for details.
    * 
    * @var      array
    * @access   private
    */
    var $_nodeTypeHandlers = array(
            'comment' => 'commentHandler', 'node' => 'nodeHandler',
            'processing-instruction' => 'processingInstructionHandler',
            'text' => 'textHandler'
            );
    
    /**
    * List of event handlers.
    * 
    * @var      array
    * @access   private
    */
    var $_stateHandlers = array(
            'AbsoluteLocationPath' => '_absolutePathHandler',
            'RelativeLocationPath' => '_stepHandler',
            'AbbreviatedRelativeLocationPath' => '_stepHandler',
            'AxisSpecifier' => '_axisHandler', 'NodeTest' => '_nodeTestHandler',
            'NodeType' => '_nodeTypeHandler',
            'processing-instruction' => '_nodeTypeHandler',
            'Predicate' => '_predicateHandler', 'Unknown' => '_unknownHandler',
            'Union' => '_unionHandler',
            'AbbreviatedAbsoluteLocationPath' => '_shortAbsolutePathHandler',
            'AbbreviatedRelativeLocationPath' => '_shortRelativePathHandler',
            'AbbreviatedStep' => '_shortStepHandler',
            'AbbreviatedAxisSpecifier' => '_shortAxisHandler'
            );
    
    /**
    * Reference on object myXPath.
    * 
    * Uses for access to variable of object myXPath.
    * 
    * @var      object myXPath
    * @access   private
    */
    var $_owner;
    
    /**
    * Reference on object predicateParser.
    * 
    * @var      object predicateParser
    * @access   private
    */
    var $_parser;
    
    /**
    * Contains literal data.
    * 
    * @var      string
    * @access   private
    */
    var $_literal;
    
    /**
    * Contains QName.
    * 
    * @var      string
    * @access   private
    */
    var $_QName;
    
    /**
    * Contains name of NodeTest.
    * 
    * @var      string
    * @access   private
    */
    var $_nodeType;
    
    /**
     * Constructor.
     * 
     * @return  object stepParser
     * @access  private
     */
    function XPathParser()
    {
        static $matrix;
        $this->Automat();
        $this->setObjectName('XPathParser');
        if (empty($matrix)) {
            $matrix = file('path.csv', true);
        }
        $this->setMatrix($matrix);
        $this->_defState = 'Unknown';
    }
    
    /**
     * Sets the reference on object myXPath.
     * 
     * @param   object myXPath
     * @access  public
     */
    function setOwner(&$owner)
    {
        $this->_owner =& $owner;
    }
    
    /**
     * Begin of parsing handler.
     * 
     * @access  private
     */
    function beginHandler()
    {
        $this->_QName = false;
        if ($this->item()!= '/' and $this->item()!= '//') {
            $this->_shortSyntaxHandler();
        }
    }
    
    /**
     * End of parsing handler.
     * 
     * @access  private
     */
    function endingHandler()
    {
        if ($this->_QName) {
            $this->_nodeNameHandler();
        }
        if ($this->_owner->_union) {
            $this->_owner->_nodeSet = array_merge($this->_owner->_union, $this->_owner->_nodeSet);
            $this->_owner->_union = array();
        }
    }
    
    /**
     * Return error message by code.
     * 
     * @param   integer
     * @return  string
     * @access  public
     */
    function errorMessage($code)
    {
        static $messages;
        if (empty($messages)) {
            $messages = array(
                -1 => 'unknown error',
                -2 => "unexpected '$this->_item'"
                ); 
        }
        settype($code, 'integer');
        return $code ? $messages[-2] : $messages[-1];
    }
    
    /**
     * Handler of event 'AbsoluteLocationPath'.
     * 
     * @access private
     */
    function _absolutePathHandler()
    {
        $this->_owner->_nodeSet = array(&$this->_owner->_dom);
        $this->_QName = false;
        $this->_shortSyntaxHandler();
    }
    
    /**
     * Handler of event 'RelativeLocationPath'.
     * 
     * @access private
     */
    function _stepHandler()
    {
        if ($this->_QName) {
            $this->_nodeNameHandler();
        }
        $this->_shortSyntaxHandler();
    }
    
    /**
     * Handler of event 'AxisSpecifier'.
     * 
     * @access  private
     */
    function _axisHandler()
    {
        $axisName = $this->_data[$this->_cursor - 1];
        if (!($axisHandler = $this->_axis[$axisName])) {
            return $this->raiseError("unknown axis '$axisName'");
        }
        $this->_QName = false;
        $this->$axisHandler();
    }
    
    /**
     * XPathParser::_nodeTypeHandler()
     * 
     * @access  private
     */
    function _nodeTypeHandler()
    {
        $this->_nodeType = $this->_item;
    }
    
    /**
     * Handler of event 'NodeTest'.
     * 
     * @access  private
     */
    function _nodeTestHandler()
    {
        if (!($nodeType = $this->_nodeTypeHandlers[$this->_nodeType])) {
            return $this->raiseError("unknown node type '$this->_nodeType'");
        }
        $this->_QName = false;
        $this->$nodeType($this->_literal);
    }
    
    /**
     * Handler of event 'Unknown'.
     * 
     * @access  private
     */
    function _unknownHandler()
    {
        global $Literal, $NameTest;
        if (preg_match("/$Literal/x", $this->_item)) {
            $this->_literal = trim($this->_item, '"\'');
            $this->_state = 'Literal';
        } elseif (preg_match("/$NameTest/x", $this->_item)) {
            $this->_QName = $this->_item;
            $this->_state = 'NameTest';
        } 
    }
    
    /**
     * Handler of event 'QName'.
     * 
     * @access  private
     */
    function _nodeNameHandler()
    {
        if ($pos = strpos($this->_QName, ':')) {
            $prefix = substr($this->_QName, 0, $pos);
            $localName = substr($this->_QName, $pos + 1);
        }
        $nodeSet = array();
        $count = sizeof($this->_owner->_nodeSet);
        for ($n = 0; $n < $count; $n++) {
            $node =& $this->_owner->_nodeSet[$n];
            if ($prefix) {
                if (($localName == '*' || $localName == $node->localName) && $node->prefix == $prefix) {
                    array_push($nodeSet, &$node);
                }
            } else {
                if ($this->_QName == '*' || $this->_QName == $node->nodeName) {
                    array_push($nodeSet, &$node);
                }
            }
        }
        unset($node);
        $this->_owner->_nodeSet = $nodeSet;
        $this->_QName = false;
    }
    
    /**
     * Handler of events 'Predicate'.
     * 
     * @access  private
     */
    function _predicateHandler()
    {
        if ($this->_QName) {
            $this->_nodeNameHandler();
        }
        if (empty($this->_parser)) {
            $this->_parser =& new XPredicateParser;
            $this->_parser->setOwner(&$this->_owner);
        }
        $this->_parser->setErrorHandling(PEAR_ERROR_CALLBACK, array(&$this, '_handleParserError'));
        $this->_parser->setData($this->_data);
        $this->_parser->setOffset($this->_cursor);
        $this->_parser->debug($this->_debug);
        $this->_parser->start();
        $this->_cursor = $this->_parser->_cursor - 1;
    }
    
    /**
     * Handler of event 'Union'.
     * 
     * @access  private
     */
    function _unionHandler()
    {
        if ($this->_QName) {
            $this->_nodeNameHandler();
        }
        $this->_owner->_union = array_merge($this->_owner->_union, $this->_owner->_nodeSet);
        $this->_owner->_nodeSet = $this->_owner->_context;
        $this->_shortSyntaxHandler();
    }
    
    /**
     * Short syntax handler.
     * 
     * @access  private
     */
    function _shortSyntaxHandler()
    {
        switch ($this->_item) {
        case '/':
            if ($this->nextItem() && !$this->_axis[$this->nextItem()]) {
                $this->childHandler();
            }
            break;
        case '//':
            if ($this->nextItem() && !$this->_axis[$this->nextItem()]) {
                $this->childHandler();
            }
            break;
        case '|':
            if ($this->nextItem() && !$this->_axis[$this->nextItem()]) {
                $this->childHandler();
            }
            break;
        default:
            if (!$this->_axis[$this->item()]) {
                $this->childHandler();
            }
        }
    }
    
    /**
     * XPathParser::_shortAbsolutePathHandler()
     * 
     * @access  private
     */
    function _shortAbsolutePathHandler()
    {
        $this->_owner->_nodeSet = array(&$this->_owner->_dom);
        $this->_QName = false;
        $this->descendantOrSelfHandler();
        $this->nodeHandler();
        $this->_shortSyntaxHandler();
    }
    
    /**
     * XPathParser::_shortRelativePathHandler()
     * 
     * @access  private
     */
    function _shortRelativePathHandler()
    {
        if ($this->_QName) {
            $this->_nodeNameHandler();
        }
        $this->descendantOrSelfHandler();
        $this->nodeHandler();
        $this->_shortSyntaxHandler();
    }
    
    /**
     * XPathParser::_shortStepHandler()
     * 
     * @access  private
     */
    function _shortStepHandler()
    {
        switch ($this->_item) {
        case '.':
            $this->selfHandler();
            $this->nodeHandler();
            break;
        case '..':
            $this->parentHandler();
            $this->nodeHandler();
            break;
        }
    }
    
    /**
     * XPathParser::_shortAxisHandler()
     * 
     * @access  private
     */
    function _shortAxisHandler()
    {
        $this->attributeHandler();
    }
    
    /**
     * XPathParser::_handleParserError()
     * 
     * @access  private
     */
    function _handleParserError(&$error)
    {
        ini_set('error_reporting', E_USER_ERROR | E_USER_WARNING | E_USER_NOTICE);
        $this->raiseError($error->getMessage(), null, null, null, $error->getUserInfo());
    }

#******************************************************************************#
#                                                                              #
#                       Aaxis and nodeType handlers                            #
#                                                                              #
#******************************************************************************#
    
    
    /**
     * Handler of axis "ancestor".
     * 
     * See XPath for details.
     * 
     * @access  private
     */
    function ancestorHandler()
    {
        $nodeSet = array();
        $count = sizeof($this->_owner->_nodeSet);
        for ($n = 0; $n < $count; $n++) {
            $node =& $this->_owner->_nodeSet[$n];
            if ($parentNode =& $node->parentNode) {
                $this->_owner->_markup and $parentNode->_context = $node->_context;
                $parents = $this->_ancestorWorm(&$parentNode);
                $nodeSet = array_merge($nodeSet, $parents);
                unset($parentNode);
            }
        }
        $this->_owner->_nodeSet = $nodeSet;
        unset($node);
    }
    
    /**
     * Handler of axis "ancestor-or-self".
     * 
     * See XPath for details.
     * 
     * @access  private
     */
    function ancestorOrSelfHandler()
    {
        $nodeSet = $this->_owner->_nodeSet;
        $count = sizeof($this->_owner->_nodeSet);
        for ($n = 0; $n < $count; $n++) {
            $node =& $this->_owner->_nodeSet[$n];
            if ($parentNode =& $node->parentNode){
                $this->_owner->_markup and $parentNode->_context = $node->_context;
                $parents = $this->_ancestorWorm(&$parentNode);
                $nodeSet = array_merge($nodeSet, $parents);
                unset($parentNode);
            }
        }
        $this->_owner->_nodeSet = $nodeSet;
        unset($node);
    }
    
    /**
     * Handler of axis "attribute".
     * 
     * See XPath for details.
     * 
     * @access  private
     */
    function attributeHandler()
    {
        $nodeSet = array();
        $count = sizeof($this->_owner->_nodeSet);
        for ($n = 0; $n < $count; $n++) {
            $node =& $this->_owner->_nodeSet[$n];
            if ($node->hasAttributes()) {
                for ($i = 0; $i < $node->attributes->length; $i++) {
                    $attrNode = $node->attributes->item($i);
                    $this->_owner->_markup and $attrNode->_context = $node->_context;
                    array_push($nodeSet, &$attrNode);
                    unset($attrNode);
                }
            }
        }
        $this->_owner->_nodeSet = $nodeSet;
        unset($node);
    }
    
    /**
     * Handler of axis "child".
     * 
     * See XPath for details.
     * 
     * @access  private
     */
    function childHandler()
    {
        $nodeSet = array();
        $count = sizeof($this->_owner->_nodeSet);
        for ($n = 0; $n < $count; $n++) {
            $node =& $this->_owner->_nodeSet[$n];
            if ($node->hasChildNodes()) {
                for ($i = 0; $i < $node->childNodes->length; $i++) {
                    $childNode =& $node->childNodes->item($i);
                    $this->_owner->_markup and $childNode->_context = $node->_context;
                    array_push($nodeSet, &$childNode);
                    unset($childNode);
                }
            }
        }
        $this->_owner->_nodeSet = $nodeSet;
        unset($node);
    }
    
    /**
     * Handler of axis "descendant".
     * 
     * See XPath for details.
     * 
     * @access  private
     */
    function descendantHandler()
    {
        $nodeSet = array();
        $count = sizeof($this->_owner->_nodeSet);
        for ($n = 0; $n < $count; $n++) {
            $node =& $this->_owner->_nodeSet[$n];
            if ($firstChild =& $node->firstChild) {
                $this->_owner->_markup and $firstChild->_context = $node->_context;
                $childs = $this->_descendantWorm(&$firstChild);
                $nodeSet = array_merge($nodeSet, $childs);
                unset($firstChild);
            }
        }
        $this->_owner->_nodeSet = $nodeSet;
        unset($node);
    }
    
    /**
     * Handler of axis "descendant-or-self".
     * 
     * See XPath for details.
     * 
     * @access  private
     */
    function descendantOrSelfHandler()
    {
        $nodeSet = $this->_owner->_nodeSet;
        $count = sizeof($this->_owner->_nodeSet);
        for ($n = 0; $n < $count; $n++) {
            $node =& $this->_owner->_nodeSet[$n];
            if ($firstChild =& $node->firstChild){
                $this->_owner->_markup and $firstChild->_context = $node->_context;
                $childs = $this->_descendantWorm(&$firstChild);
                $nodeSet = array_merge($nodeSet, $childs);
                unset($firstChild);
            }
        }
        $this->_owner->_nodeSet = $nodeSet;
        unset($node);
    }
    
    /**
     * Handler of axis "following".
     * 
     * Not implemented in this version.
     * 
     * @access  private
     */
    function followingHandler()
    {
        return $this->raiseError('the axis "following" is not implemented in this version');
    }
    
    /**
     * Handler of axis "following-sibling".
     * 
     * See XPath for details.
     * 
     * @access  private
     */
    function followingSiblingHandler()
    {
        $nodeSet = array();
        $count = sizeof($this->_owner->_nodeSet);
        for ($n = 0; $n < $count; $n++) {
            $node =& $this->_owner->_nodeSet[$n];
            if ($nextSibling =& $node->nextSibling){
                $this->_owner->_markup and $nextSibling->_context = $node->_context;
                $siblings = $this->_followingSiblingWorm(&$nextSibling);
                $nodeSet = array_merge($nodeSet, $siblings);
            }
        }
        $this->_owner->_nodeSet = $nodeSet;
        unset($node);
    }
    
    /**
     * Handler of axis "namespace".
     * 
     * Not implemented in this version.
     * 
     * @access  private
     */
    function namespaceHandler()
    {
        return $this->raiseError('The axis "namespace" is not implemented in this version');
    }
    
    /**
     * Handler of axis "parent".
     * 
     * See XPath for details.
     * 
     * @access  private
     */
    function parentHandler()
    {
        $nodeSet = array();
        $count = sizeof($this->_owner->_nodeSet);
        for ($n = 0; $n < $count; $n++) {
            $node =& $this->_owner->_nodeSet[$n];
            if ($parentNode =& $node->parentNode) {
                $this->_owner->_markup and $parentNode->_context = $node->_context;
                array_push($nodeSet, &$parentNode);
                unset($parentNode);
            }
        }
        $this->_owner->_nodeSet = $nodeSet;
        unset($node);
    }
    
    /**
     * Handler of axis "preceding".
     * 
     * Not implemented in this version.
     * 
     * @access  private
     */
    function precedingHandler()
    {
        return $this->raiseError('the axis "preceding" is not implemented in this version');
    }
    
    /**
     * Handler of axis "preceding-sibling".
     * 
     * See XPath for details.
     * 
     * @access  private
     */
    function precedingSiblingHandler()
    {
        $nodeSet = array();
        $count = sizeof($this->_owner->_nodeSet);
        for ($n = 0; $n < $count; $n++) {
            $node =& $this->_owner->_nodeSet[$n];
            if ($previousSibling =& $node->previousSibling){
                $this->_owner->_markup and $previousSibling->_context = $node->_context;
                $siblings = $this->_precedingSiblingWorm(&$previousSibling);
                $nodeSet = array_merge($nodeSet, $siblings);
            }
        }
        $this->_owner->_nodeSet = $nodeSet;
        unset($node);
    }
    
    /**
     * Handler of axis "self".
     * 
     * See XPath for details.
     * 
     * @access  private
     */
    function selfHandler()
    {
        //void
    }
    
    /**
     * Handler of node test "comment()".
     * 
     * See XPath for details.
     * 
     * @access  private
     */
    function commentHandler()
    {
        $this->_nodeType(COMMENT_NODE);
    }
    
    /**
     * Handler of node test "node()".
     * 
     * See XPath for details.
     * 
     * @access  private
     */
    function nodeHandler()
    {
        //void
    }
    
    /**
     * Handler of node test "processing-instruction()".
     * 
     * See XPath for details.
     * 
     * @access  private
     */
    function processingInstructionHandler($target)
    {
        $this->_nodeType(PROCESSING_INSTRUCTION_NODE);
        if ($target) {
            $nodeSet = array();
            $count = sizeof($this->_owner->_nodeSet);
            for ($n = 0; $n < $count; $n++) {
                $node =& $this->_owner->_nodeSet[$n];
                if ($node->target == $target) {
                    array_push($nodeSet, &$node);
                }
            }
            $this->_owner->_nodeSet = $nodeSet;
        }
    }
    
    /**
     * Handler of node test "text()".
     * 
     * See XPath for details.
     * 
     * @access  private
     */
    function textHandler()
    {
        $this->_nodeType(TEXT_NODE);
    }

#******************************************************************************#
#                                                                              #
#                            Functions auxiliary.                              #
#                                                                              #
#******************************************************************************#
    
    /**
     * Selecting all ancestors.
     * 
     * @param   object Node
     * @param   array
     * @access  private
     */
    function _ancestorWorm(&$node)
    {
        $nodeSet = array();
        array_push($nodeSet, &$node);
        if ($parentNode =& $node->parentNode) {
            $this->_owner->_markup and $parentNode->_context = $node->_context;
            $parents = $this->_ancestorWorm(&$parentNode);
            $nodeSet = array_merge($nodeSet, $parents);
            unset($parentNode);
        }
        return $nodeSet;
    }
    
    /**
     * Selecting all descendants.
     * 
     * @param   object Node
     * @param   array
     * @access  private
     */
    function _descendantWorm(&$node)
    {
        $nodeSet = array();
        array_push($nodeSet, &$node);
        if ($firstChild =& $node->firstChild) {
            $this->_owner->_markup and $parentNode->_context = $node->_context;
            $childs = $this->_descendantWorm(&$firstChild);
            $nodeSet = array_merge($nodeSet, $childs);
            unset($firstChild);
        }
        if ($nextSibling =& $node->nextSibling) {
            $this->_owner->_markup and $nextSibling->_context = $node->_context;
            $siblings = $this->_descendantWorm(&$nextSibling);
            $nodeSet = array_merge($nodeSet, $siblings);
            unset($nextSibling);
        }
        return $nodeSet;
    }
    
    /**
     * Selecting all following siblings.
     * 
     * @param   object Node
     * @param   array
     * @access  private
     */
    function _followingSiblingWorm(&$node)
    {
        $nodeSet = array();
        array_push($nodeSet, &$node);
        if ($nextSibling =& $node->nextSibling) {
            $this->_owner->_markup and $nextSibling->_context = $node->_context;
            $siblings = $this->_followingSiblingWorm(&$nextSibling);
            $nodeSet = array_merge($nodeSet, $siblings);
        }
        return $nodeSet;
    }
    
    /**
     * Selecting all preceding siblings.
     * 
     * @param   object Node
     * @param   array
     * @access  private
     */
    function _precedingSiblingWorm(&$node)
    {
        $nodeSet = array();
        array_push($nodeSet, &$node);
        if ($previousSibling =& $node->previousSibling) {
            $this->_owner->_markup and $previousSibling->_context = $node->_context;
            $siblings = $this->_precedingSiblingWorm(&$previousSibling);
            $nodeSet = array_merge($nodeSet, $siblings);
        }
        return $nodeSet;
    }
    
    /**
     * Selecting nodes by specified type.
     * 
     * @param   string
     * @access  private
     */
    function _nodeType($nodeType)
    {
        $nodeSet = array();
        $count = sizeof($this->_owner->_nodeSet);
        for ($n = 0; $n < $count; $n++) {
            $node =& $this->_owner->_nodeSet[$n];
            if ($node->nodeType == $nodeType) {
                array_push($nodeSet, &$node);
            }
        }
        $this->_owner->_nodeSet = $nodeSet;
    }
}

?>