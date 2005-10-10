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
// $Id: myDOM.php,v 0.3 2004/02/06 10:55:17 anter Exp $

/**
* @package      myXML
* @subpackage   myDOM
*/
/**
* Base class for other nodes.
*/
require_once('Node.php');

/** 
* DOMImplementation class.
*/
require_once('DOMImplementation.php');

/**
* DocumentType class.
*/
require_once('DocumentType.php');
 
/** 
* DocumentFragment class.
*/
require_once('DocumentFragment.php');

/** 
* Element class.
*/
require_once('Element.php');

/** 
* Attr class.
*/
require_once('Attr.php');

/** 
* Text class.
*/
require_once('Text.php');

/** 
* Comment class.
*/
require_once('Comment.php');

/** 
* Entity class.
*/
require_once('Entity.php');

/** 
* EntityReference class.
*/
require_once('EntityReference.php');

/** 
* ProcessingInstruction class.
*/
require_once('ProcessingInstruction.php');

/** 
* CDATASection class.
*/
require_once('CDATASection.php');

/**
* Notation class.
*/
require_once('Notation.php');


// ExceptionCode
define('INDEX_SIZE_ERR', 1); 
define('DOMSTRING_SIZE_ERR', 2); 
define('HIERARCHY_REQUEST_ERR', 3); 
define('WRONG_DOCUMENT_ERR', 4); 
define('INVALID_CHARACTER_ERR', 5);
define('NO_DATA_ALLOWED_ERR', 6);
define('NO_MODIFICATION_ALLOWED_ERR', 7);
define('NOT_FOUND_ERR', 8);
define('NOT_SUPPORTED_ERR', 9);
define('INUSE_ATTRIBUTE_ERR', 10);

// Introduced in DOM Level 2:
define('INVALID_STATE_ERR', 11);
define('SYNTAX_ERR', 12);
define('INVALID_MODIFICATION_ERR', 13);
define('NAMESPACE_ERR', 14);
define('INVALID_ACCESS_ERR', 15);

// Introduced in this DOM Implementation:
define('ROOT_EXIST_ERR', 30);

// NodeType
define('ELEMENT_NODE', 1);
define('ATTRIBUTE_NODE', 2);
define('TEXT_NODE', 3);
define('CDATA_SECTION_NODE', 4);
define('ENTITY_REFERENCE_NODE', 5);
define('ENTITY_NODE', 6);
define('PROCESSING_INSTRUCTION_NODE', 7);
define('COMMENT_NODE', 8);
define('DOCUMENT_NODE', 9);
define('DOCUMENT_TYPE_NODE', 10);
define('DOCUMENT_FRAGMENT_NODE', 11);
define('NOTATION_NODE', 12);


/**
* Document class.
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
class Document extends Node
{
    /**
    * The Document Type Declaration (see DocumentType) associated with
    * this document.
    * 
    * @var      object DocumentType
    * @access   public 
    */
    var $doctype;
    
    /**
    * The Implementation object that handles this document.
    * 
    * @var      object Implementation
    * @access   public 
    */
    var $implementation;
    
    /**
    * This is a convenience attribute that allows direct access to the child
    * node that is the root element of the document. 
    * 
    * @var      object Element
    * @access   public 
    */
    var $documentElement;
    
    /**
    * Method of display of the document.
    * 
    * {xml|html|text} ('text' not support yet)
    * 
    * @var      string
    * @access   private
    */
    var $_method = 'html';

    /**
    * Encoding of document.
    * 
    * (not support yet)
    * 
    * @var      string
    * @access   private
    */
    var $_encoding;

    /**
    * Add additional whitespace when outputting the result tree.
    * 
    * @var      boolean
    * @access   private
    */
    var $_indent = false;
    
    /**
    * Level of the current rendered element.
    * 
    * @var      integer
    * @access   private
    */
    var $_level = 0;
    
    /**
    * Reference on xml parser object.
    * 
    * @var      object Parser
    * @access   private
    */
    var $_parser = null;
    
    /**
     * Constructor.
     * 
     * @return  object Document
     * @access  public
     */
    function Document()
    {
        $this->Node('#document', &$this);
        $this->nodeType = DOCUMENT_NODE;
        $this->doctype =& new DocumentType('ns', &$this);
        $this->implementation =& new Implementation;
        $this->declareNS('http://www.w3.org/2000/xmlns/', 'xmlns');
    }
    
    /**
     * Creates an element of the type specified.
     * 
     * @param   string
     * @return  object Element
     * @access  public
     */
    function &createElement($tagName)
    {
        return new Element($tagName, &$this);
    }
    
    /**
     * Creates an empty DocumentFragment object.
     * 
     * @return  object DocumentFragment
     * @access  public
     */
    function &createDocumentFragment()
    {
        return new DocumentFragment(&$this);
    }
    
    /**
     * Creates a Text node given the specified string.
     * 
     * @param   string
     * @return  object Text
     * @access  public
     */
    function &createTextNode($data)
    {
        return new Text($data, &$this);
    }
    
    /**
     * Creates a Comment node given the specified string.
     * 
     * @param   string
     * @return  object Comment
     * @access  public
     */
    function &createComment($data)
    {
        return new Comment($data, &$this);
    }
    
    /**
     * Creates a CDATASection node whose value is the specified string.
     * 
     * @param   string
     * @return  object CDATASection
     * @access  public
     */
    function &createCDATASection($data)
    {
        return new CDATASection($data, &$this);
    }
    
    /**
     * Creates a ProcessingInstruction node given the specified name and
     * data strings. 
     * 
     * @param   string
     * @param   string
     * @return  object ProcessingInstruction
     * @access  public
     */
    function &createProcessingInstruction($target, $data)
    {
        return new ProcessingInstruction($target, $data, &$this);
    }
    
    /**
     * Creates an Attr of the given name.
     * 
     * @param   string
     * @return  object Attr
     * @access  public
     */
    function &createAttribute($name)
    {
        return new Attr($name, &$this);
    }
    
    /**
     * Creates an EntityReference object.
     * 
     * @param   string
     * @return  object EntityReference
     * @access  public
     */
    function &createEntityReference($name)
    {
        return new EntityReference($name, &$this);
    }
    
    /**
     * Returns a NodeList of all the Elements with a given tag name in the order
     * in which they are encountered in a preorder traversal of the Document tree. 
     * 
     * Not implemented in this version.
     * 
     * @param   string
     * @return  array
     * @access  public
     */
    function getElementsByTagName($tagName)
    {
        return $this->raiseError('the function "getElementsByTagName" is not support in this version');
    }
    
    // Introduced in DOM Level 2:
    /**
     * Imports a node from another document to this document.
     * 
     * @param   object Node
     * @param   boolean
     * @return  object Node
     * @access  public
     */
    function &importNode(&$importedNode, $deep = false)
    {
        if ($importedNode->nodeType < 1 || $importedNode->nodeType > 12) {
            return $this->raiseError(NOT_SUPPORTED_ERR);
        }
        if ($importedNode->namespaceURI) {
            $this->declareNS($importedNode->namespaceURI, $importedNode->prefix);
        }
        $newNode =& $importedNode->cloneNode($deep);
        $this->_nodeWorm(&$newNode);
        return $newNode;
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
        if ($this->documentElement->_ID == $refChild->_ID) {
            return $this->raiseError(ROOT_EXIST_ERR);
        }
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
        if ($this->documentElement->_ID == $oldChild->_ID) {
            $this->documentElement =& $newChild;
        }
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
        $this->_removeChild(&$oldChild);
        if ($this->documentElement->_ID == $oldChild->_ID) {
            unset($this->documentElement);
        }
        return $oldChild;
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
        if ($newChild->nodeType == ELEMENT_NODE && $this->documentElement) {
            return $this->raiseError(ROOT_EXIST_ERR);
        }
        if ($newChild->nodeType == ELEMENT_NODE ||
            $newChild->nodeType == PROCESSING_INSTRUCTION_NODE ||
            $newChild->nodeType == COMMENT_NODE) {
                $this->_appendChild(&$newChild);
        }
        $newChild->nodeType == ELEMENT_NODE and
            $this->documentElement =& $newChild;
        return $newChild;
    }
    
    /**
     * Creates an element of the given qualified name and namespace URI.
     * 
     * @param   string
     * @param   string
     * @return  object Element
     * @access  public
     */
    function &createElementNS($namespaceURI, $qualifiedName)
    {
        $prefix = substr($qualifiedName, 0, strpos($qualifiedName, ':'));
        if (!$this->resolveNS($prefix)) {
            return $this->raiseError(NAMESPACE_ERR);
        }
        return new Element($qualifiedName, &$this, $namespaceURI);
    }
    
    /**
     * Creates an attribute of the given qualified name and namespace URI.
     * 
     * @param   string
     * @param   string
     * @return  object Attr
     * @access  public
     */
    function &createAttributeNS($namespaceURI, $qualifiedName)
    {
        $prefix = substr($qualifiedName, 0, strpos($qualifiedName, ':'));
        if (!$this->resolveNS($prefix)) {
            return $this->raiseError(NAMESPACE_ERR);
        }
        return new Attr($qualifiedName, &$this, $namespaceURI);
    }
    
    /**
     * Returns a NodeList of all the Elements with a given local name and
     * namespace URI in the order in which they are encountered in a preorder
     * traversal of the Document tree. 
     * 
     * Not implemented in this version.
     * 
     * @param   string
     * @param   string
     * @return  array
     * @access  public
     */
    function getElementsByTagNameNS($namespaceURI, $localName)
    {
        return $this->raiseError('the function "getElementsByTagNameNS" is not support in this version');
    }
    
    /**
     * Returns the Element whose ID is given by elementId.
     * 
     * Not implemented in this version.
     * 
     * @param   string
     * @return  object Element
     * @access  public
     */
    function getElementById($elementId)
    {
        return $this->raiseError('the function "getElementById" is not support in this version');
    }
    
    // Introduced in this DOM Implementation:
    
    /**
     * Parses XML string.
     * 
     * This method parses string given in the passed parameter. Creates child
     * nodes and append it.
     * 
     * @param   string      XML data.
     * @param   boolean     end of file.
     * @access  public
     * @see     parseFile()
     */
    function parse($data, $eof = true)
    {
        if ($this->_parser == null) {
            require_once('Parser.php');
            $this->_parser =& Parser::create(&$this);
            $this->_parser->setErrorHandling(PEAR_ERROR_CALLBACK, array(&$this, '_handleParserError'));
        }
        $this->_parser->parse($data, $eof);
    }
    
    /**
     * Parses XML file.
     * 
     * @param   string      Filename (full path)
     * @access  public
     * @see     parse()
     */
    function parseFile($file)
    {
        $handle = @fopen($file, "rb");
        if (!is_resource($handle)) {
            return $this->raiseError($php_errormsg);
        }
        while ($data = fread($handle, 2048)) {
            $this->parse($data, feof($handle));
        }
        fclose($handle);
    }
    
    /**
     * Declare namespace.
     * 
     * @param   string
     * @param   string
     * @access  public
     */
    function declareNS($namespaceURI, $prefix)
    {
        $entity =& new Entity(&$this, $prefix, $namespaceURI);
        $this->doctype->_setEntity(&$entity);
    }
    
    /**
     * Resolve namespace by prefix.
     * 
     * @param   string  $prefix Namespace prefix
     * @return  string          Namespace URI
     * @access  public
     */
    function resolveNS($prefix)
    {
        $entity =& $this->doctype->_getEntity($prefix);
        if (is_object($entity)) {
            return $entity->systemId;
        }
        return false;
    }
    
    /**
     * Document::isInherited()
     * 
     * @param   object  inherited from Document
     * @return  boolean
     * @access  public
     * @static  method
     */
    function isInherited(&$node)
    {
        return (is_object($node) && is_a($node, 'Document'));
    }
    
    /**
     * Document::toString()
     * 
     * @return  string
     * @access  public
     */
    function toString()
    {
        for ($i = 0; $i < $this->childNodes->length; $i++) {
            $childNode =& $this->childNodes->item($i);
            $str.= $childNode->toString();
        }
        return $str;
    }
    
    /**
     * Sets options of display of the document.
     * 
     * @param   string  option name
     * @param   mixed   value
     * @access  public
     */
    function setOption($name, $value)
    {
        switch ($name) {
        case 'method':
            if (!preg_match('/xml|html|text/', $value)) {
                $this->raiseError('illegal value for option "method", expected {xml|html|text}', E_USER_WARNING);
            }
            $this->_method = $value;
            break;
        case 'encoding':
            $this->_encoding = $value;
            break;
        case 'indent':
            $this->_indent = (bool) $value;
            break;
        default:
            $this->raiseError("unknown option '$name'", E_USER_WARNING);
        }
    }
    
    /**
     * Document::_handleParserError()
     * 
     * @access  private
     */
    function _handleParserError(&$error)
    {
        ini_set('error_reporting', E_USER_ERROR | E_USER_WARNING | E_USER_NOTICE);
        $this->raiseError($error->getMessage(), null, null, null, $error->getUserInfo());
    }
    
    /**
     * Changes parameter ownerDocument.
     * 
     * @param   object Element
     * @access  private
     */
    function _nodeWorm(&$node)
    {
        $node->ownerDocument =& $this;
        if ($node->hasAttributes()) {
            $length = $node->attributes->length;
            for ($n = 0; $n < $length; $n++) {
                $attrNode =& $node->attributes->item($n);
                $attrNode->ownerDocument =& $this;
            }
        }
        if ($node->hasChildNodes()) {
            $this->_nodeWorm(&$node->firstChild);
        }
        if ($node->nextSibling) {
            $this->_nodeWorm(&$node->nextSibling);
        }
    }

}

/**
* Error messages
*/
$DOM_Error = array(
        DOMSTRING_SIZE_ERR => 'the specified range of text does not fit into a DOMString.',
        HIERARCHY_REQUEST_ERR => 'node is inserted somewhere it doesn\'t belong.',
        INDEX_SIZE_ERR => 'index or size is negative, or greater than the allowed value.',
        INUSE_ATTRIBUTE_ERR => 'added an attribute is already in use elsewhere.',
        INVALID_ACCESS_ERR => 'parameter or an operation is not supported by the underlying object.',
        INVALID_CHARACTER_ERR => 'invalid or illegal character is specified, such as in a name.',
        INVALID_MODIFICATION_ERR => 'modify the type of the underlying object.',
        INVALID_STATE_ERR => 'use an object that is not, or is no longer, usable.',
        NAMESPACE_ERR => 'create or change an object in a way which is incorrect with regard to namespaces.', 
        NOT_FOUND_ERR => 'reference a node in a context where it does not exist.',
        NOT_SUPPORTED_ERR  => 'the implementation does not support the requested type of object or operation.',
        NO_DATA_ALLOWED_ERR => 'data is specified for a node which does not support data.',
        NO_MODIFICATION_ALLOWED_ERR => 'modify an object where modifications are not allowed.',
        SYNTAX_ERR => 'invalid or illegal string is specified.',
        WRONG_DOCUMENT_ERR => 'node is used in a different document than the one that created it.',
        ROOT_EXIST_ERR => 'the document element of DOM document already exists.',
        0 => 'unknown error'
        );

?>