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
// $Id: Element.php,v 0.3 2004/02/06 10:52:16 anter Exp $

/**
* @package      myXML
* @subpackage   myDOM
*/
/**
* Base class for other nodes.
*/
require_once('Node.php');

/**
* Element class.
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
class Element extends Node
{
    /**
    * The name of the element.
    * 
    * @var      string
    * @access   public
    */
    var $tagName;
    
    /**
    * List of empty tags.
    * 
    * @var      array
    * @access   private
    */
    var $_emptyTags = array(
            'area'=> true, 'base'=> true, 'basefont' => true, 'br' => true,
            'col' => true, 'frame' => true, 'hr' => true, 'img' => true,
            'input' => true, 'isindex' => true, 'link' => true, 'meta' => true,
            'param' => true
            );
    
    /**
     * Constructor.
     * 
     * @param   string
     * @param   object Document
     * @param   string
     * @return  object Element
     * @access  private
     */
    function Element($tagName, &$ownerDocument, $namespaceURI = null)
    {
        $this->Node($tagName, &$ownerDocument);
        $this->nodeType = ELEMENT_NODE;
        $this->tagName = $tagName;
        $this->namespaceURI = $namespaceURI;
        if ($namespaceURI) {
            $this->prefix = substr($tagName, 0, strpos($tagName, ':'));
            $this->localName = substr($tagName, strpos($tagName, ':') + 1);
        }
        $this->attributes = new NamedNodeMap;
        $this->NamedNodeMap =& $this->attributes;
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
        return $this->_insertBefore(&$newChild, &$refChild);
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
        return $this->_replaceChild(&$newChild, &$oldChild);
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
        return $this->_removeChild(&$oldChild);
    }
    
    /**
     * See DOM for details.
     * 
     * @param   object Node
     * @return  object Node
     * @access  public
     */
    function &appendChild(&$newChild)
    {
        return $this->_appendChild(&$newChild);
    }
    
    /**
     * Retrieves an attribute value by name.
     * 
     * @param   string
     * @return  string
     * @access  public
     */
    function getAttribute($name)
    {
        if ($attrNode =& $this->getAttributeNode($name)) {
            return $attrNode->value;
        }
        return '';
    }
    
    /**
     * Adds a new attribute.
     * 
     * @param   string
     * @param   string
     * @return  boolean
     * @access  public
     */
    function setAttribute($name, $value = '')
    {
        $attr =& $this->ownerDocument->createAttribute($name);
        $attr->value = $value;
        return $this->setAttributeNode($attr);
    }
    
    /**
     * Removes an attribute by name.
     * 
     * @param   string
     * @access  public
     */
    function removeAttribute($name)
    {
        if ($this->attributes->isExists($name) === false) {
            return $this->raiseError(NOT_FOUND_ERR);
        }
        return $this->attributes->removeNamedItem($name);
    }
    
    /**
     * Retrieves an attribute node by name.
     * 
     * @param   string
     * @return  object Attr
     * @access  public
     */
    function &getAttributeNode($name)
    {
        if ($this->hasAttribute($name)) {
            return $this->attributes->getNamedItem($name);
        }
        return null;
    }
    
    /**
     * Adds a new attribute node.
     * 
     * @param   object Attr
     * @return  object Attr
     * @access  public
     */
    function &setAttributeNode(&$newAttr)
    {
        if (!Attr::isInherited($newAttr)) {
            return $this->raiseError(HIERARCHY_REQUEST_ERR);
        }
        if ($this->ownerDocument->_ID != $newAttr->ownerDocument->_ID) {
            return $this->raiseError(WRONG_DOCUMENT_ERR);
        }
        if ($newAttr->ownerElement != null) {
            return $this->raiseError(INUSE_ATTRIBUTE_ERR);
        }
        $newAttr->ownerElement = &$this;
        return $this->attributes->setNamedItem(&$newAttr);
    }
    
    /**
     * Removes the specified attribute node.
     * 
     * @param   object Attr
     * @access  public
     */
    function removeAttributeNode($oldAttr)
    {
        if ($this->attributes->isExists($oldAttr->nodeName) === false) {
            return $this->raiseError(NOT_FOUND_ERR);
        }
        return $this->attibutes->removeNamedItem($oldAttr->nodeName);
    }
    
    /**
     * Returns a NodeList of all descendant Elements with a given tag name.
     * 
     * Not implemented in this version.
     * 
     * @param   string  $name
     * @access  public
     */
    function getElementsByTagName($name)
    {
        return $this->raiseError('the function "getElementsByTagName" is not support in this version');
    }
    
    /**
     * See DOM for details.
     * 
     * Not implemented in this version.
     * 
     * @access  public
     */
    function normalize()
    {
        return $this->raiseError('the function "normalize" is not support in this version');
    }
    
    // Introduced in DOM Level 2:
    /**
     * Retrieves an attribute value by local name and namespace URI.
     * 
     * Not implemented in this version.
     * 
     * @param   string
     * @param   string
     * @access  public
     */
    function getAttributeNS($namespaceURI, $localName)
    {
        return $this->raiseError('the function "getAttributeNS" is not support in this version');
    }
    
    /**
     * Adds a new attribute.
     * 
     * @param   string
     * @param   string
     * @param   string
     * @return  boolean
     * @access  public
     */
    function setAttributeNS($namespaceURI, $qualifiedName, $value)
    {
        $attr =& $this->ownerDocument->createAttributeNS($namespaceURI, $qualifiedName);
        $attr->value = $value;
        return $this->setAttributeNodeNS(&$attr);
    }
    
    /**
     * Removes an attribute by local name and namespace URI.
     * 
     * Not implemented in this version.
     * 
     * @param   string
     * @param   string
     * @access  public
     */
    function removeAttributeNS($namespaceURI, $localName)
    {
        return $this->raiseError('the function "removeAttributeNS" is not support in this version');
    }
    
    /**
     * Retrieves an Attr node by local name and namespace URI.
     * 
     * Not implemented in this version.
     * 
     * @param   string
     * @param   string
     * @access  public
     */
    function &getAttributeNodeNS($namespaceURI, $localName)
    {
        return $this->raiseError('the function "getAttributeNodeNS" is not support in this version');
    }
    
    /**
     * Adds a new attribute.
     * 
     * @param   object Attr
     * @return  object Attr
     * @access  public
     */
    function &setAttributeNodeNS(&$newAttr)
    {
        if (!Attr::isInherited($newAttr)) {
            return $this->raiseError(HIERARCHY_REQUEST_ERR);
        }
        if ($this->ownerDocument->_ID != $newAttr->ownerDocument->_ID) {
            return $this->raiseError(WRONG_DOCUMENT_ERR);
        }
        if ($newAttr->ownerElement != null) {
            return $this->raiseError(INUSE_ATTRIBUTE_ERR);
        }
        $newAttr->ownerElement = &$this;
        return $this->attributes->setNamedItemNS(&$newAttr);
    }
    
    /**
     * Returns a NodeList of all the descendant Elements with a given local
     * name and namespace URI .
     * 
     * Not implemented in this version.
     * 
     * @param   string  $namespaceURI
     * @param   string  $localName
     * @access  public
     */
    function getElementsByTagNameNS($namespaceURI, $localName)
    {
        return $this->raiseError('the function "getElementsByTagNameNS" is not support in this version');
    }
    
    /**
     * Returns true when an attribute with a given name is specified on this
     * element or has a default value, false otherwise. 
     * 
     * @param   string
     * @return  boolean
     * @access  public
     */
    function hasAttribute($name)
    {
        if ($this->hasAttributes()) {
            return (bool) $this->attributes->getNamedItem($name);
        }
        return false;
    }
    
    /**
     * Returns true when an attribute with a given local name and namespace URI
     * is specified on this element or has a default value, false otherwise.
     * 
     * Not implemented in this version.
     * 
     * @param   string
     * @param   string
     * @access  public
     */
    function hasAttributeNS($namespaceURI, $localName)
    {
        return $this->raiseError('the function "hasAttributeNS" is not support in this version');
    }
    
    // Introduced in this DOM Implementation:
    /**
     * See DOM for details.
     * 
     * @param   bool            $deep   Recursive clone
     * @return  object Element          Handle on new object Element
     * @access  public
     */
    function &cloneNode($deep = false)
    {
        $newNode =& new Element($this->tagName, &$this->ownerDocument, $this->namespaceURI);
        if ($this->hasAttributes()) {
            for ($n = 0; $n < $this->attributes->length; $n++) {
                $attrNode =& $this->attributes->item($n);
                $newAttr =& $attrNode->cloneNode();
                $newNode->setAttributeNode(&$newAttr);
            }
        }
        if ($deep && $this->hasChildNodes()) {
            for ($n = 0; $n < $this->childNodes->length; $n++) {
                $childNode =& $this->childNodes->item($n);
                $newChild =& $childNode->cloneNode($deep);
                $newNode->appendChild(&$newChild);
            }
        }
        return $newNode;
    }
    
    /**
     * Element::isInherited()
     * 
     * @param   object  inherited from Element
     * @return  boolean
     * @access  public
     * @static  method
     */
    function isInherited(&$node)
    {
        return (is_object($node) && is_a($node, 'Element'));
    }
    
    /**
     * Parses XML string.
     * 
     * This method parses string given in the passed parameter. Creates child
     * nodes and append it.
     * 
     * @param   string      XML data.
     * @param   boolean     end of file.
     * @access  public
     */
    function parse($data, $eof = true)
    {
        require_once('Parser.php');
        $parser =& Parser::create(&$this);
        $parser->setErrorHandling(PEAR_ERROR_CALLBACK, array(&$this, '_handleParserError'));
        $parser->parse($data, $eof);
        unset($parser);
    }
    
    /**
     * Element::toString()
     * 
     * @param   boolean
     * @return  string
     * @access  public
     */
    function toString($deep = true)
    {
        if ($this->_emptyTags[$this->nodeName]
            || $deep !== true
            || $this->ownerDocument->_method == 'xml' && !$this->hasChildNodes()) {
            return $this->_renderEmptyElement();
        } else {
            return $this->_renderElement();
        }
    }
    
    /**
     * Element::_handleParserError()
     * 
     * @access  private
     */
    function _handleParserError(&$error)
    {
        ini_set('error_reporting', E_USER_ERROR | E_USER_WARNING | E_USER_NOTICE);
        $this->raiseError($error->getMessage(), null, null, null, $error->getUserInfo());
    }
    
    /**
     * Element::_renderElement()
     * 
     * @access  private
     */
    function _renderElement()
    {
        $str = '<'.$this->nodeName;
        $str.= $this->_renderAttributes();
        $str.= '>';
        $this->ownerDocument->_indent and $this->ownerDocument->_level++;
        $str.= $this->_renderChildNodes();
        $this->ownerDocument->_indent and $this->ownerDocument->_level--;
        $str.= ($this->childNodes->length > 1) ? chr(10).str_repeat(' ', $this->ownerDocument->_level * 2) : '';
        $str.= '</'.$this->nodeName.'>';
        return $str;
    }
    
    /**
     * Element::_renderEmptyElement()
     * 
     * @access  private
     */
    function _renderEmptyElement()
    {
        $str = '<'.$this->nodeName;
        $str.= $this->_renderAttributes();
        $str.= '/>';
        return $str;
    }
    
    /**
     * Element::_renderAttributes()
     * 
     * @access  private
     */
    function _renderAttributes()
    {
        for ($i = 0; $i < $this->attributes->length; $i++) {
            $attribute =& $this->attributes->item($i);
            $str.= $attribute->toString();
        }
        return $str;
    }
    
    /**
     * Element::_renderChildNodes()
     * 
     * @access  private
     */
    function _renderChildNodes()
    {
        for ($i = 0; $i < $this->childNodes->length; $i++) {
            $childNode =& $this->childNodes->item($i);
            $str.= ($this->childNodes->length > 1) ? chr(10).str_repeat(' ', $this->ownerDocument->_level * 2) : '';
            $str.= $childNode->toString();
        }
        return $str;
    }
}

?>