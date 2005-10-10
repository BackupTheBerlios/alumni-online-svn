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
// $Id: XPredicateParser.php,v 0.3 2003/07/15 15:01:29 anter Exp $

/**
* @package      myXML
* @subpackage   myXPath
*/
/**
* Base class for other parsers.
*/
require_once('Automat.php');

/**
* Hadling of predicate.
* 
* @author       Tereshchenko Andrey <tereshchenko@anter.com.ua>
* @copyright    Tereshchenko Andrey 2002-2003
* @version      0.3 2003/07/15
* @access       private
* @package      myXML
* @subpackage   myXPath
* @link         http://phpmyxml.sourceforge.net/
*/
class XPredicateParser extends Automat
{
    /**
    * List of special symbols.
    * 
    * @var      array
    * @access   private
    */
    var $_keywords = array(
            '/' => '/', '//' => '//', '::' => '::', '*' => '*', ',' => ',',
            
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
    * List of XPath functions.
    * 
    * See XPath for details.
    * 
    * @var      array
    * @access   private
    */
    var $_functions = array(
            'count' => 'countHandler', 'document' => 'documentHandler', 'id' => 'idHandler', 'key' => 'keyHandler',
            'last' => 'lastHandler', 'local-name' => 'localNameHandler', 'name' => 'nameHandler',
            'namespace-uri' => 'namespaceUriHandler', 'position' => 'positionHandler',
                           
            'concat' => 'concatHandler', 'contains' => 'containsHandler', 'normalize-space' => 'normalizeSpaceHandler',
            'starts-with' => 'startsWithHandler', 'string' => 'stringHandler', 'string-length' => 'stringLengthHandler',
            'substring' => 'substringHandler', 'substring-after' => 'substringAfterHandler',
            'substring-before' => 'substringBeforeHandler', 'translate' => 'translateHandler',
            
            'boolean' => 'booleanHandler', 'false' => 'falseHandler', 'lang' => 'langHandler', 'not' => 'notHandler',
            'true' => 'trueHandler',
            
            'ceiling' => 'ceilingHandler', 'floor' => 'floorHandler', 'number' => 'numberHandler', 'round' => 'roundHandler',
            'sum' => 'sumHandler'
            );
    
    /**
    * List of event handlers.
    * 
    * @var      array
    * @access   private
    */
    var $_stateHandlers = array(
            'Unknown' => '_unknownHandler', 'FunctionName' => '_functionNameHandler',
            'FunctionCall' => '_functionCallHandler', 'ExprToken' => '_bracketsHandler',
            'Operator' => '_operatorHandler', 'Union' => '_unionHandler',
            'LocationPath' => '_locationPathHandler',
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
    * Contains a predicate expression.
    * 
    * @var      array
    * @access   private
    */
    var $_expression = array();
    
    /**
    * Contains a cursor position of location path beginning.
    * 
    * @var      integer
    * @access   private
    */
    var $_beginPath;
    
    /**
    * Contains a cursor position of function argument beginning.
    * 
    * @var      int
    * @access   private
    */
    var $_beginArgument;
    
    /**
    * Contains a difference between open and closed round brackets.
    * 
    * @var      integer
    * @access   private
    */
    var $_rndBrackets;
    
    /**
    * Contains difference between open and closed square brackets.
    * 
    * @var      int
    * @access   private
    */
    var $_sqrBrackets;
    
    /**
    * Contains name of function.
    * 
    * @var      string
    * @access   private
    */
    var $_funcName;
    
    /**
    * Location path flag.
    * 
    * @var      boolean
    * @access   private
    */
    var $_locationPath = false;
    
    /**
     * Constructor.
     * 
     * @return  object XPredicateParser
     * @access  private
     */
    function XPredicateParser()
    {
        static $matrix;
        $this->Automat();
        $this->setObjectName('XPredicateParser');
        if (empty($matrix)) {
            $matrix = file('predicate.csv', true);
        }
        $this->setMatrix($matrix);
        $this->_defState = 'Unknown';
    }
    
    /**
     * Sets the reference on object myXPath.
     * 
     * @param   object myXPath  &$owner
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
        $this->_expression = array();
        $this->_locationPath = false;
    }
    
    /**
     * End of parsing handler.
     * 
     * @access  private
     */
    function endPredicateHandler()
    {
        if ($this->_locationPath) {
            $this->_evalLocationPath();
        }
        $nodeSet = array();
        $count = sizeof($this->_owner->_nodeSet);
        for ($n = 0; $n < $count; $n++) {
            $node =& $this->_owner->_nodeSet[$n];
            if ($this->_evalExpression($node)) {
                array_push($nodeSet, &$node);
            }
        }
        unset($node);
        $this->_owner->_nodeSet = $nodeSet;
    }
    
    /**
     * Handler of event 'Unknown'.
     * 
     * @access  private
     */
    function _unknownHandler()
    {
        global $Literal, $QName;
        if (is_numeric($this->_item)) {
            array_push($this->_expression, $this->_item);
            $this->_state = 'Number';
        } elseif (preg_match("/$Literal/x", $this->_item)) {
            array_push($this->_expression, $this->_item);
            $this->_state = 'Literal';
        } elseif (preg_match("/$QName/x", $this->_item)) {
            $this->_state = 'QName';
            if (!$this->_locationPath) {
                $this->_locationPath = true;
                $this->_beginPath = $this->_cursor;
            }
        }
    }
    
    /**
     * Handler of event 'FunctionCall'.
     * 
     * @access  private
     */
    function _functionCallHandler()
    {
        $offset = $this->_beginArgument;
        $length = $this->_cursor - $this->_beginArgument;
        $arguments = array_slice($this->_data, $offset, $length);
        $this->_funcName = ($this->_item == '()') ? $this->_data[$this->_cursor - 1] : $this->_funcName;
        if (!$this->_functions[$this->_funcName]) {
            return $this->raiseError("unknown function '{$this->_funcName}'");
        }
        $method = $this->_functions[$this->_funcName];
        $result = $this->$method($arguments);
        array_push($this->_expression, $result);
        $this->_funcName = null;
        $this->_locationPath = false;
    }
    
    /**
     * Handler of event 'FunctionName'.
     * 
     * @access  private
     */
    function _functionNameHandler()
    {
        $this->_funcName = $this->_data[$this->_cursor - 1];
        if (!$this->_functions[$this->_funcName]) {
            return $this->raiseError("unknown function '{$this->_funcName}'");
        }
        $this->_beginArgument = $this->_cursor;
        $this->_locationPath = false;
    }
    
    /**
     * Handler of event 'LocationPath'.
     * 
     * @access  private
     */
    function _locationPathHandler()
    {
        if (!$this->_locationPath) {
            $this->_locationPath = true;
            $this->_beginPath = $this->_cursor;
        }
    }
    
    /**
     * Evaluates location path expression.
     * 
     * @access  private
     */
    function _evalLocationPath()
    {
        $offset = $this->_beginPath;
        $length = $this->_cursor - $this->_beginPath;
        $path = implode('', array_slice($this->_data, $offset, $length));
        $xpath = myXPath::create(&$this->_owner->_dom);
        $xpath->setErrorHandling(PEAR_ERROR_CALLBACK, array(&$this, '_handleXPathError'));
        $xpath->setContext($this->_owner->_nodeSet, true);
        $xpath->debug($this->_debug);
        $nodeSet = $xpath->evaluate($path);
        unset($xpath);
        if ($count = sizeof($nodeSet)) {
            for ($n = 0; $n < $count; $n++) {
                $node =& $nodeSet[$n];
                if (!$context = $node->_context) {
                    return $this->raiseError('internal error 001');
                }
                if ($node->nodeType == ELEMENT_NODE && $node->hasChildNodes()) {
                    $value = $this->_concatenationWorm(&$node->firstChild);
                    $values[$context][] = $value ? $value : 'null';
                } elseif ($node->nodeType == ATTRIBUTE_NODE) {
                    $values[$context][] = $node->value ? $node->value : 'null';
                } elseif ($node->nodeType == TEXT_NODE) {
                    $values[$context][] = $node->data ? $node->data : 'null';
                }
                if (!is_array($values[$context])) {
                    $values[$context][] = '__EXIST__';
                }
            }
        } else {
            $values = 'null';
        }
        array_push($this->_expression, $values);
        $this->_locationPath = false;
    }
    
    /**
     * Concatenates text in element node.
     * 
     * @param   object Node
     * @return  string
     * @access  private
     */
    function _concatenationWorm(&$node)
    {
        $value = null;
        if ($node->nodeType == ELEMENT_NODE && $node->hasChildNodes()) {
            $value.= $this->_concatenationWorm(&$node->firstChild);
        } elseif ($node->nodeType == TEXT_NODE) {
            $value = $node->data;
        }
        if ($node->nextSibling) {
            $value.= $this->_concatenationWorm(&$node->nextSibling);
        }
        return $value;
    }
    
    /**
     * Handler of event 'Operator'.
     * 
     * @access  private
     */
    function _operatorHandler()
    {
        if ($this->_sqrBrackets > 1) {
            $this->_state = ($this->_prevState == 'LocationPath') ? 'LocationPath' : $this->_state;
            return;
        } elseif ($this->_locationPath) {
            $this->_evalLocationPath();
        }
        switch ($this->_item) {
        case '=':
            array_push($this->_expression, '==');
            break;
        case 'mod':
            array_push($this->_expression, '%');
            break;
        case 'div':
            array_push($this->_expression, '/');
            break;
        default:
            array_push($this->_expression, $this->_item);
        }
    }
    
    /**
     * Handler of event 'ExprToken'.
     * 
     * @access  private
     */
    function _bracketsHandler()
    {
        switch ($this->_item) {
        case '(':
            $this->_rndBrackets++;
            array_push($this->_expression, $this->_item);
            break;
        case ')':
            $this->_rndBrackets--;
            array_push($this->_expression, $this->_item);
            break;
        case '[':
            $this->_sqrBrackets++;
            if ($this->_sqrBrackets == 1) {
                $this->_shortSyntaxHandler();
            }
            $this->_state = ($this->_prevState == 'LocationPath') ? 'LocationPath' : $this->_state;
            break;
        case ']':
            $this->_sqrBrackets--;
            if ($this->_sqrBrackets == 0) {
                $this->endPredicateHandler();
                $this->stop();
            }
            $this->_state = ($this->_prevState == 'LocationPath') ? 'LocationPath' : $this->_state;
            break;
        }
    }
    
    /**
     * Short syntax handler.
     * 
     * @access  private
     */
    function _shortSyntaxHandler()
    {
        if (is_numeric($this->nextItem()) && $this->_data[$this->_cursor+2] == ']') {
            $result = $this->positionHandler();
            array_push($this->_expression, $result, '==', $this->nextItem());
            $this->endPredicateHandler();
            $this->_cursor += 2;
            $this->stop();
        }
    }
    
    /**
     * Evaluates predicate expression.
     * 
     * @param   object Node
     * @return  boolean
     * @access  private
     */
    function _evalExpression($node)
    {
        reset($this->_expression);
        $key = $node->_context ? $node->_context : $node->_ID;
        return $this->_expressionWorm($key, 'return ');
    }
    
    /**
     * Makes predicate expression.
     * 
     * @param   integer
     * @param   string
     * @return  boolean
     * @access  private
     */
    function _expressionWorm($key, $expr)
    {
        $item = current($this->_expression);
        $next = next($this->_expression);
        if (is_array($item)) {
            if (isset($item[$key])) {
                foreach ($item[$key] as $value) {
                    if ($next !== false) {
                        if ($this->_expressionWorm($key, "$expr'$value' ")) {
                            return true;
                        }
                    } else {
                        $expr.= "'$value' ? true : false;";
                        $this->_debug and print('<br>Path: '.$this->_owner->_path.', Expression: '.$expr);
                        if (eval($expr)) {
                            return true;
                        }
                    }
                }
            } else {
                if ($this->_expressionWorm($key, $expr."null ")) {
                    return true;
                }
            }
        } else {
            if ($next !== false) {
                if ($this->_expressionWorm($key, $expr.$item.' ')) {
                    return true;
                }
            } else {
                $expr.= "$item ? true : false;";
                $this->_debug and print('<br>Path: '.$this->_owner->_path.', Expression: '.$expr);
                if (eval($expr)) {
                    return true;
                }
            }
        }
        if ($next === false) {
            end($this->_expression);
        } else {
            prev($this->_expression);
        }
        return false;
    }
    
    /**
     * XPredicateParser::_handleXPathError()
     * 
     * @access  private
     */
    function _handleXPathError(&$error)
    {
        ini_set('error_reporting', E_USER_ERROR | E_USER_WARNING | E_USER_NOTICE);
        $this->raiseError($error->message, null, null, null, $error->userinfo);
    }
    
#******************************************************************************#
#                                                                              #
#                          XPath functions handlers                            #
#                                                                              #
#******************************************************************************#
    
    /**
     * See XPath for details.
     * 
     * @access  private
     */
    function positionHandler()
    {
        $values = array();
        $count = sizeof($this->_owner->_nodeSet);
        for ($n = 0; $n < $count; $n++) {
            $position = 1;
            $node =& $this->_owner->_nodeSet[$n];
            $key = $node->_context ? $node->_context: $node->_ID;
            $position+= $this->_positionWorm(&$node, &$position);
            $values[$key][] = $position;
        }
        return $values;
    }
    
    /**
     * See XPath for details.
     * 
     * Not implemented in this version.
     * 
     * @access  private
     */
    function countHandler()
    {
        $this->raiseError('the function "count" is not implemented in this version');
    }
    
    /**
     * See XPath for details.
     * 
     * Not implemented in this version.
     * 
     * @access  private
     */
    function documentHandler()
    {
        $this->raiseError('the function "document" is not implemented in this version');
    }
    
    /**
     * See XPath for details.
     * 
     * Not implemented in this version.
     * 
     * @access  private
     */
    function idHandler()
    {
        $this->raiseError('the function "id" is not implemented in this version');
    }
    
    /**
     * See XPath for details.
     * 
     * Not implemented in this version.
     * 
     * @access  private
     */
    function keyHandler()
    {
        $this->raiseError('the function "key" is not implemented in this version');
    }
    
    /**
     * See XPath for details.
     * 
     * @access  private
     */
    function lastHandler()
    {
        $values = array();
        $count = sizeof($this->_owner->_nodeSet);
        for ($n = 0; $n < $count; $n++) {
            $last = 1;
            $node =& $this->_owner->_nodeSet[$n];
            $key = $node->_context ? $node->_context: $node->_ID;
            if ($node->parentNode && $node->parentNode->firstChild) {
                $firstChild =& $node->parentNode->firstChild;
            }
            $last+= $this->_lastWorm(&$firstChild);
            $values[$key][] = $last;
        }
        return $values;
    }
    
    /**
     * See XPath for details.
     * 
     * Not implemented in this version.
     * 
     * @access  private
     */
    function localNameHandler()
    {
        $this->raiseError('the function "local" is not implemented in this version');
    }
    
    /**
     * See XPath for details.
     * 
     * @access  private
     */
    function nameHandler()
    {
        $values = array();
        $count = sizeof($this->_owner->_nodeSet);
        for ($n = 0; $n < $count; $n++) {
            $node =& $this->_owner->_nodeSet[$n];
            $key = $node->_context ? $node->_context: $node->_ID;
            $values[$key][] = $node->nodeName;
        }
        return $values;
    }
    
    /**
     * See XPath for details.
     * 
     * Not implemented in this version.
     * 
     * @access  private
     */
    function namespaceUriHandler()
    {
        $this->raiseError('the function "namespace-uri" is not implemented in this version');
    }
    
    /**
     * See XPath for details.
     * 
     * Not implemented in this version.
     * 
     * @access  private
     */
    function concatHandler()
    {
        $this->raiseError('the function "concat" is not implemented in this version');
    }
    
    /**
     * See XPath for details.
     * 
     * Not implemented in this version.
     * 
     * @access  private
     */
    function containsHandler()
    {
        $this->raiseError('the function "contains" is not implemented in this version');
    }
    
    /**
     * See XPath for details.
     * 
     * Not implemented in this version.
     * 
     * @access  private
     */
    function normalizeSpaceHandler()
    {
        $this->raiseError('the function "normalize-space" is not implemented in this version');
    }
    
    /**
     * See XPath for details.
     * 
     * Not implemented in this version.
     * 
     * @access  private
     */
    function startsWithHandler()
    {
        $this->raiseError('the function "starts-with" is not implemented in this version');
    }
    
    /**
     * See XPath for details.
     * 
     * Not implemented in this version.
     * 
     * @access  private
     */
    function stringHandler()
    {
        $this->raiseError('the function "string" is not implemented in this version');
    }
    
    /**
     * See XPath for details.
     * 
     * Not implemented in this version.
     * 
     * @access  private
     */
    function stringLengthHandler()
    {
        $this->raiseError('the function "string-length" is not implemented in this version');
    }
    
    /**
     * See XPath for details.
     * 
     * Not implemented in this version.
     * 
     * @access  private
     */
    function substringHandler()
    {
        $this->raiseError('the function "substring" is not implemented in this version');
    }
    
    /**
     * See XPath for details.
     * 
     * Not implemented in this version.
     * 
     * @access  private
     */
    function substringAfterHandler()
    {
        $this->raiseError('the function "substring-after" is not implemented in this version');
    }
    
    /**
     * See XPath for details.
     * 
     * Not implemented in this version.
     * 
     * @access  private
     */
    function substringBeforeHandler()
    {
        $this->raiseError('the function "substring-before" is not implemented in this version');
    }
    
    /**
     * See XPath for details.
     * 
     * Not implemented in this version.
     * 
     * @access  private
     */
    function translateHandler()
    {
        $this->raiseError('the function "translate" is not implemented in this version');
    }
    
    /**
     * See XPath for details.
     * 
     * Not implemented in this version.
     * 
     * @access  private
     */
    function booleanHandler()
    {
        $this->raiseError('the function "boolean" is not implemented in this version');
    }
    
    /**
     * See XPath for details.
     * 
     * Not implemented in this version.
     * 
     * @access  private
     */
    function falseHandler()
    {
        $this->raiseError('the function "false" is not implemented in this version');
    }
    
    /**
     * See XPath for details.
     * 
     * Not implemented in this version.
     * 
     * @access  private
     */
    function langHandler()
    {
        $this->raiseError('the function "lang" is not implemented in this version');
    }
    
    /**
     * See XPath for details.
     * 
     * Not implemented in this version.
     * 
     * @access  private
     */
    function notHandler()
    {
        $this->raiseError('the function "not" is not implemented in this version');
    }
    
    /**
     * See XPath for details.
     * 
     * Not implemented in this version.
     * 
     * @access  private
     */
    function trueHandler()
    {
        $this->raiseError('the function "true" is not implemented in this version');
    }
    
    /**
     * See XPath for details.
     * 
     * Not implemented in this version.
     * 
     * @access  private
     */
    function ceilingHandler()
    {
        $this->raiseError('the function "ceiling" is not implemented in this version');
    }
    
    /**
     * See XPath for details.
     * 
     * Not implemented in this version.
     * 
     * @access  private
     */
    function floorHandler()
    {
        $this->raiseError('the function "floor" is not implemented in this version');
    }
    
    /**
     * See XPath for details.
     * 
     * Not implemented in this version.
     * 
     * @access  private
     */
    function numberHandler()
    {
        $this->raiseError('the function "number" is not implemented in this version');
    }
    
    /**
     * See XPath for details.
     * 
     * Not implemented in this version.
     * 
     * @access  private
     */
    function roundHandler()
    {
        $this->raiseError('the function "round" is not implemented in this version');
    }
    
    /**
     * See XPath for details.
     * 
     * Not implemented in this version.
     * 
     * @access  private
     */
    function sumHandler()
    {
        $this->raiseError('the function "sum" is not implemented in this version');
    }

#******************************************************************************#
#                                                                              #
#                            Functions auxiliary.                              #
#                                                                              #
#******************************************************************************#

    /**
     * Counts position of node.
     * 
     * @param   object Node
     * @access  private
     */
    function _positionWorm(&$node)
    {
        $position = 0;
        if ($node->previousSibling) {
            $position++;
            $position+= $this->_positionWorm(&$node->previousSibling);
        }
        return $position;
    }
    
    /**
     * Counts last position of node.
     * 
     * @param   object Node
     * @access  private
     */
    function _lastWorm(&$node)
    {
        $last = 0;
        if ($node->nextSibling) {
            $last++;
            $last+= $this->_lastWorm(&$node->nextSibling);
        }
        return $last;
    }
    
}

?>