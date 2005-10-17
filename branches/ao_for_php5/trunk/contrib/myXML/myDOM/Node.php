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
// $Id: Node.php,v 0.3 2004/02/06 10:50:20 anter Exp $

/**
* @package      myXML
* @subpackage   myDOM
*/
/**
* Error handling.
*/
require_once('Error.php');

/**
* NodeList class.
*/
require_once('NodeList.php');

/**
* NamedNodeMap class.
*/
require_once('NamedNodeMap.php');

/**
* Node class.
* 
* See DOM for details.
* 
* @author       Tereshchenko Andrey <tereshchenko@anter.com.ua>
* @copyright    Tereshchenko Andrey 2002-2003
* @version      0.3 2004/02/06
* @access       public
* @package      myXML
* @subpackage   myDOM
* @link         http://phpmyxml.sourceforge.net/
*/
class Node extends PEAR
{
    /**
    * The name of this node, depending on its type.
    * 
    * @var      string
    * @access   public
    */
    var $nodeName;
    
    /**
    * The value of this node, depending on its type.
    * 
    * @var      string
    * @access   public
    */
    var $nodeValue = null;
    
    /**
    * A code representing the type of the underlying object, as defined above.
    * 
    * @var      integer
    * @access   public
    */
    var $nodeType;
    
    /**
    * The parent of this node.
    * 
    * @var      object Element
    * @access   public
    */
    var $parentNode = null;
    
    /**
    * A NodeList that contains all children of this node.
    * 
    * @var      object NodeList
    * @access   public
    */
    var $childNodes = null;
    
    /**
    * The first child of this node.
    * 
    * @var      object Element
    * @access   public
    */
    var $firstChild = null;
    
    /**
    * The last child of this node.
    * 
    * @var      object Element
    * @access   public
    */
    var $lastChild = null;
    
    /**
    * The node immediately preceding this node.
    * 
    * @var      object Element
    * @access   public
    */
    var $previousSibling = null;
    
    /**
    * The node immediately following this node.
    * 
    * @var      object Element
    * @access   public
    */
    var $nextSibling = null;
    
    /**
    * A NamedNodeMap containing the attributes of this node.
    * 
    * @var      object NamedNodeMap
    * @access   public
    */
    var $attributes = null;
    
    /**
    * The Document object associated with this node.
    * 
    * @var      object Document
    * @access   public
    */
    var $ownerDocument;
    
    // Introduced in DOM Level 2:
    /**
    * The namespace URI of this node, or null if it is unspecified.
    * 
    * @var      string
    * @access   public
    */
    var $namespaceURI = null;
    
    /**
    * The namespace prefix of this node, or null if it is unspecified.
    * 
    * @var      string
    * @access   public
    */
    var $prefix = null;
    
    /**
    * Returns the local part of the qualified name of this node.
    * 
    * @var      string
    * @access   public
    */
    var $localName = null;
    
    // Introduced in this DOM Implementation:
    /**
    * Node ID
    * 
    * @var      integer
    * @access   private
    */
    var $_ID = null;
    
    /**
     * Constructor.
     * 
     * @param   string
     * @param   object Document
     * @return  object Node
     * @access  private
     */
    function Node($nodeName, &$ownerDocument)
    {
        $this->PEAR('DOM_Error');
        if (!$this->_checkName($nodeName)) {
            return $this->raiseError(INVALID_CHARACTER_ERR);
        }
        $this->nodeName = $nodeName;
        $this->_ID = mt_rand(1, 9999999999);
        $this->ownerDocument =& $ownerDocument;
        $this->childNodes =& new NodeList;
        $this->NodeList =& $this->childNodes;
    }
    
    /**
     * Inserts the node newChild before the existing child node refChild.
     * 
     * @param   object Element
     * @param   object Element
     * @access  public
     */
    function &insertBefore(&$newChild, &$refChild)
    {
        return $this->raiseError(HIERARCHY_REQUEST_ERR);
    }
    
    /**
     * Replaces the child node oldChild with newChild in the list of children,
     * and returns the oldChild node.
     * 
     * @param   object Element  $newChild
     * @param   object Element  $oldChild
     * @access  public
     */
    function replaceChild(&$newChild, &$oldChild)
    {
        return $this->raiseError(HIERARCHY_REQUEST_ERR);
    }
    
    /**
     * Removes the child node indicated by oldChild from the list of children,
     * and returns it.
     * 
     * @param   object Element
     * @access  public
     */
    function &removeChild(&$oldChild)
    {
        return $this->raiseError(HIERARCHY_REQUEST_ERR);
    }
    
    /**
     * Adds the node newChild to the end of the list of children of this node.
     * 
     * @param   object Node
     * @return  object Node
     * @access  public
     */
    function &appendChild(&$newChild)
    {
        return $this->raiseError(HIERARCHY_REQUEST_ERR);
    }
    
    /**
     * Returns whether this node has any children. 
     * 
     * @return  boolean
     * @access  public
     */
    function hasChildNodes()
    {
        return (bool) $this->childNodes->length;
    }
    
    /**
     * Returns a duplicate of this node.
     * 
     * @param   boolean
     */
    function &cloneNode($deep = false)
    {
      //Abstract method
    }
    
    // Introduced in DOM Level 2:
    /**
     * Tests whether the DOM implementation implements a specific feature and
     * that feature is supported by this node. 
     * 
     * Not implemented in this version.
     * 
     * @param   string
     * @param   string
     * @access  public
     */
    function isSupported($feature, $version)
    {
        return $this->raiseError('the function "isSupported" is not implemented in this version');
    }
    
    /**
     * Returns whether this node (if it is an element) has any attributes.
     * 
     * @return  boolean
     * @access  public
     */
    function hasAttributes()
    {
        return (bool) $this->attributes;
    }
    
    // Introduced in this DOM Implementation:
    
    /**
     * Node::isInherited()
     * 
     * @param   object  inherited from Node
     * @return  boolean
     * @access  public
     * @static  method
     */
    function isInherited(&$node)
    {
        return (is_object($node) && is_a($node, 'Node'));
    }
    
    /**
     * Node::toString()
     * 
     * @return  string
     * @access  private
     */
    function toString()
    {
        // abstract
    }
    
    /**
     * Node::_insertBefore()
     * 
     * @access  private
     */
    function &_insertBefore(&$newChild, &$refChild)
    {
        if (!Node::isInherited($newChild)) {
            return $this->raiseError('the first argument must be inherit of object Node');
        }
        if ($this->ownerDocument->_ID != $newChild->ownerDocument->_ID) {
            return $this->raiseError(WRONG_DOCUMENT_ERR);
        }
        if ($newChild->nodeType < 1 || $newChild->nodeType > 12) {
            return $this->raiseError(NOT_SUPPORTED_ERR);
        }
        if (false === ($index = $this->childNodes->isExists(&$refChild))) {
            return $this->raiseError(NOT_FOUND_ERR);
        }
        if ($newChild->parentNode !== null) {
            $newChild->parentNode->removeChild(&$newChild);
        }
        $newChild->parentNode =& $this;
        $newChild->previousSibling =& $refChild->previousSibling;
        $newChild->nextSibling =& $refChild;
        $refChild->previousSibling->nextSibling =& $newChild;
        $refChild->previousSibling =& $newChild;
        if ($this->firstChild->_ID = $refChild->_ID) {
            $this->firstChild =& $newChild;
        }
        $this->childNodes->addItem(&$newChild, $index);
        return $newChild;
        
    }
    
    /**
     * Node::_replaceChild()
     * 
     * @access  private
     */
    function _replaceChild($newChild, $oldChild)
    {
        if (!Node::isInherited($newChild)) {
            return $this->raiseError('the first argument must be inherit of object Node');
        }
        if ($this->ownerDocument->_ID != $newChild->ownerDocument->_ID) {
            return $this->raiseError(WRONG_DOCUMENT_ERR);
        }
        if ($newChild->nodeType < 1 || $newChild->nodeType > 12) {
            return $this->raiseError(NOT_SUPPORTED_ERR);
        }
        if (false === ($index = $this->childNodes->isExists(&$oldChild))) {
            return $this->raiseError(NOT_FOUND_ERR);
        }
        if ($newChild->parentNode !== null) {
            $newChild->parentNode->removeChild(&$newChild);
        }
        $newChild->parentNode =& $this;
        $newChild->previousSibling =& $oldChild->previousSibling;
        $newChild->nextSibling =& $oldChild->nextSibling;
        if ($this->firstChild->_ID = $oldChild->_ID) {
            $this->firstChild =& $newChild;
        }
        if ($this->lastChild->_ID = $oldChild->_ID) {
            $this->lastChild =& $newChild;
        }
        $this->childNodes->addItem(&$newChild, $index, $replace = 1);
        return $oldChild;
    }
    
    /**
     * Node::_removeChild()
     * 
     * @access  private
     */
    function &_removeChild(&$oldChild)
    {
        if (!Node::isInherited($oldChild)) {
            return $this->raiseError('the first argument must be inherited from Node');
        }
        if (false === ($index = $this->childNodes->isExists(&$oldChild))) {
            return $this->raiseError(NOT_FOUND_ERR);
        }
        if ($nextSibling =& $oldChild->nextSibling) {
            if ($previousSibling =& $oldChild->previousSibling) {
                $nextSibling->previousSibling =& $previousSibling;
            } else {
                unset($nextSibling->previousSibling);
            }
        }
        if ($previousSibling =& $oldChild->previousSibling) {
            if ($nextSibling =& $oldChild->nextSibling) {
                $previousSibling->nextSibling =& $nextSibling;
            } else {
                unset($previousSibling->nextSibling);
            }
        }
        if ($this->firstChild->_ID == $oldChild->_ID) {
            if ($oldChild->nextSibling) {
                $this->firstChild =& $oldChild->nextSibling;
            } else {
                unset($this->firstChild);
            }
        }
        if ($this->lastChild->_ID == $oldChild->_ID) {
            if ($oldChild->previousSibling) {
                $this->lastChild =& $previousSibling;
            } else {
                unset($this->lastChild);
            }
        }
        unset($oldChild->parentNode);
        unset($oldChild->previousSibling);
        unset($oldChild->nextSibling);
        return $this->childNodes->removeItem($index);
    }
    
    /**
     * Node::_appendChild()
     * 
     * @access  private
     */
    function &_appendChild(&$newChild)
    {
        if (!Node::isInherited($newChild)) {
            return $this->raiseError('the first argument must be inherited from Node');
        }
        if ($this->ownerDocument->_ID != $newChild->ownerDocument->_ID) {
            return $this->raiseError(WRONG_DOCUMENT_ERR);
        }
        if ($newChild->nodeType < 1 || $newChild->nodeType > 12) {
            return $this->raiseError(NOT_SUPPORTED_ERR);
        }
        if ($newChild->parentNode !== null) {
            $newChild->parentNode->removeChild(&$newChild);
        }
        if (!$this->firstChild) {
            $this->firstChild =& $newChild;
        }
        $newChild->parentNode =& $this;
        $newChild->previousSibling =& $this->lastChild;
        $newChild->nextSibling = null;
        if ($this->lastChild) {
            $this->lastChild->nextSibling =& $newChild;
        }
        $this->lastChild =& $newChild;
        $this->childNodes->addItem(&$newChild);
        return $newChild;
    }
    
    /**
     * Checks name of node.
     * 
     * @param   string
     * @return  bool
     * @access  private
     */
    function _checkName($nodeName)
    {
        $pattern = '/^(([^\d\W]|_|#)([^\d\W]|\d|\.|-|_)*:)?([^\d\W]|_|#)([^\d\W]|\d|\.|-|_)*$/';
        return preg_match($pattern, $nodeName);
    }
}

/**
* DOM_Error class.
* 
* @access   private
*/
class DOM_Error extends Error
{
    var $error_message_prefix = 'myDOM error: ';
    var $skipClass = 'node';
    
    function DOM_Error($message = 'unknown error', $code = null,
                       $mode = null, $options = null, $userinfo = null)
    {
        global $DOM_Error;
        if (is_integer($message)) {
            $message = $DOM_Error[$message];
        }
        $this->Error($message, $code, $mode, $options, $userinfo);
    }
}

?>