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
// | Author: Tereshchenko Andrey <tereshchenko@anter.com.ua>              |
// +----------------------------------------------------------------------+
//
// $Id: XML_Preprocessor.php,v 2.27 2004/05/23 10:13:31 anter Exp $

/**
* @package  myXML
*/
/**
* Error handling.
*/
require_once('Error.php');

/**
* Parses XML-document into DOM-document.
* 
* The XML_Preprocessor allows to execute a PHP-code on the fly, during parsing
* the XML-document. the PHP-code can be written down in three ways:
* 1. in processing-instruction;
* 2. In value of attribute, using braces
* 3. In value of attribute, using the instruction "php:".
* The result of performance of a PHP-code is inserted into the DOM-document as
* the text node (in that case if it is a string), or as an element. In value of
* attribute the text is saved only.
* 
* @author       Tereshchenko Andrey <tereshchenko@anter.com.ua>
* @copyright    Tereshchenko Andrey 2002-2003
* @version      2.27 2004/05/23
* @access       public
* @package      myXML
* @link         http://phpmyxml.sourceforge.net/
*/
class XML_Preprocessor extends PEAR
{
    /**
    * Reference on object Document.
    * 
    * @var      object Document
    * @access   private
    */
    var $_dom;
    
    /**
    * Contains xml parser resource.
    * 
    * @var      resource
    * @access   private
    */
    var $_parser = null;
    
    /**
    * Element which append child element.
    * 
    * @var      object  Element
    * @access   private
    */
    var $_parent = null;
    
    /**
    * Enables or disables parse of attributes value.
    * 
    * @var      boolean
    * @access   private
    */
    var $_parseAttrValue;
    
    /**
     * Constructor.
     * 
     * @param   object Document
     * @return  object XML_Preprocessor
     * @access  public
     * @static  method
     */
    function &create(&$dom)
    {
        $parser = new XML_Preprocessor;
        $parser->PEAR('XML_Error');
        $parser->setDOMDocument(&$dom);
        return $parser;
    }
    
    /**
     * Parses string.
     * 
     * This method parses string given in the passed parameter. The optional
     * parameter $parseValue disables the parse of curly braces syntax in
     * attributes values. On the instruction "php:" this parameter does not
     * influence.
     * 
     * @param   string      XML data.
     * @param   boolean     end of file.
     * @param   boolean     Enables or disables parse of attributes value.
     * @access  public
     * @see     parseFile()
     */
    function parse($data, $eof = true, $parseValue = true)
    {
        $this->_parseAttrValue = $parseValue;
        if (!is_resource($this->_parser)) {
            $this->_parser = xml_parser_create();
            xml_set_object($this->_parser, &$this);
            xml_parser_set_option($this->_parser, XML_OPTION_CASE_FOLDING, 0);
            xml_set_element_handler($this->_parser, '_startElementHandler', '_endElementHandler');
            xml_set_character_data_handler($this->_parser, '_characterDataHandler');
            xml_set_processing_instruction_handler($this->_parser, "_processingInstructionHandler");
            xml_set_default_handler($this->_parser, "_defaultHandler");
            //xml_set_external_entity_ref_handler($this->_parser, "_externalEntityRefHandler");
            //xml_set_start_namespace_decl_handler($this->_parser, '_namespaceDeclHandler');
        }
        if (!xml_parse($this->_parser, $data, $eof)) {
            return $this->raiseError(sprintf("%s at line %d position %d\n",
                xml_error_string(xml_get_error_code($this->_parser)),
                xml_get_current_line_number($this->_parser),
                xml_get_current_column_number($this->_parser)));
        }
        if ($eof == true) {
            xml_parser_free($this->_parser);
            unset($this->_parser);
        }
    }
    
    /**
     * Parses file.
     * 
     * @param   string      Filename (full path)
     * @param   boolean     Enables or disables parse of attributes value.
     * @access  public
     * @see     parse()
     */
    function parseFile($file, $parseValue = true)
    {
        $handle = @fopen($file, "rb");
        if (!is_resource($handle)) {
            return $this->raiseError($php_errormsg);
        }
        while ($data = fread($handle, 2048)) {
            $this->parse($data, feof($handle), $parseValue);
        }
        fclose($handle);
    }
    
    /**
     * Sets reference on object Document.
     * 
     * @param   object Document
     * @return  bool
     * @access  public
     */
    function setDOMDocument(&$dom)
    {
        if (!Document::isInherited($dom)) {
            return $this->raiseError('the first argument must be object DOM');
        }
        $this->_dom =& $dom;
        $this->_dom->setErrorHandling(PEAR_ERROR_CALLBACK, array(&$this, '_handleDOMError'));
        $this->_parent =& $dom;
    }
    
    /**
     * XML_Preprocessor::_handleDOMError()
     * 
     * @access  private
     */
    function _handleDOMError(&$error)
    {
        ini_set('error_reporting', E_USER_ERROR | E_USER_WARNING | E_USER_NOTICE);
        $this->raisError($error->getMessage(), null, null, null, $error->getUserInfo());
    }
    
    /**
     * Handler of event "startElement".
     * 
     * @param   object XMLParser
     * @param   string
     * @param   array
     * @access  private
     */
    function _startElementHandler($parser, $name, $attributes)
    {
        $this->_xmlnsHandler($attributes);
        //Creates element
        $pattern = '/^(?:((?:[^\d\W]|_)(?:[^\d\W]|\d|\.|-|_)*):)?(?:[^\d\W]|_)(?:[^\d\W]|\d|\.|-|_)*$/';
        preg_match($pattern, $name, $matches);
        list($name, $prefix) = $matches;
        if (isset($prefix)) {
            $namespaceURI = $this->_dom->resolveNS($prefix);
            $element =& $this->_dom->createElementNS($namespaceURI, $name);
        } else {
            $element =& $this->_dom->createElement($name);
        }
        //Creates atttributes
        foreach ($attributes as $name => $value) {
            $value = $this->_attrValueHandler($value);
            preg_match($pattern, $name, $matches);
            list($name, $prefix) = $matches;
            if (isset($prefix)) {
                $namespaceURI = $this->_dom->resolveNS($prefix);
                $element->setAttributeNS($namespaceURI, $name, $value);
            } else {
                $element->setAttribute($name, $value);
            }
        }
        //Inserts element into tree
        $this->_parent->appendChild(&$element);
        $this->_parent =& $element;
    }
    
    /**
     * Handler of event "endElement".
     * 
     * @param   object XMLParser
     * @param   string
     * @access  private
     */
    function _endElementHandler($parser, $name)
    {
        $this->_parent =& $this->_parent->parentNode;
    }
    
    /**
     * Handler of event "characterData".
     * 
     * @param   object XMLParser
     * @param   string
     * @access  private
     */
    function _characterDataHandler($parser, $text)
    {
        if ($this->_cdata) {
            $this->_cdata = is_string($this->_cdata) ? $this->_cdata.$text : $text;
        } elseif ($text = $this->_whiteSpaceHandler($text)) {
            $textNode =& $this->_dom->createTextNode($text);
            $this->_parent->appendChild(&$textNode);
        }
    }
    
    /**
     * Handler of event "processingInstruction".
     * 
     * @param   object XMLParser
     * @param   string
     * @param   string
     * @access  private
     */
    function _processingInstructionHandler($parser, $target, $data)
    {
        if (strtolower($target) == 'xml-stylesheet') {
            $pinode =& $this->_dom->createProcessingInstruction($target, $data);
            $this->_parent->appendChild(&$pinode);
        } elseif (strtolower($target) == 'php') {
            $this->_handlePHPPI($data);
        }
    }
    
    /**
     * Handler of event "default".
     * 
     * @param   object XMLParser
     * @param   string
     * @access  private
     */
    function _defaultHandler($parser, $data)
    {
        if (preg_match('/<\?xml (\w+=".+")\?>/', $data, $matches)) {
            $pinode =& $this->_dom->createProcessingInstruction('xml', $matches[1]);
            $this->_parent->appendChild(&$pinode);
        } elseif ($data == '<![CDATA[') {
            $this->_cdata = true;
        } elseif ($data == ']]>') {
            $this->_cdata = $this->_cdata !== true ? $this->_cdata : '';
            $CDATANode =& $this->_dom->createCDATASection($this->_cdata);
            $this->_parent->appendChild(&$CDATANode);
            $this->_cdata = false;
        }
    }
    
    /**
     * Whitespace handler.
     * 
     * @param   string
     * @return  string
     * @access  private
     */
    function _whiteSpaceHandler($text)
    {
        $S = array(
            " " => 1, "\t" => 1, "\n" => 1,
            "\r" => 1, "\0" => 1, "\x0B" => 1
            );
        $end = strlen($text) - 1;
        if ($S[$text{0}]) {
            $text = ltrim($text);
            if (strlen($text) != 0) {
                $text = ' '.$text;
            } else {
                return false;
            }
        } elseif ($S[$text{$end}]) {
            $text = rtrim($text).' ';
        }
        return $text;
    }
    
    /**
     * Processing attribute "xmlns".
     * 
     * @param   array
     * @access  private
     */
    function _xmlnsHandler($attributes)
    {
        $pattern = '/^xmlns(:([^\d\W]|_)([^\d\W]|\d|\.|-|_)*)?$/';
        $keys = array_keys($attributes);
        $matches = preg_grep($pattern, $keys);
        foreach ($matches as $name) {
            if ($name == 'xmlns') {
                $prefix = 'default';
            } else {
                $prefix = str_replace('xmlns:', '', $name);
            }
            $namespaceURI = $attributes[$name];
            $this->_dom->declareNS($namespaceURI, $prefix);
        }
    }
    
    /**
     * Processing php-code in processing-instruction.
     * 
     * @access  private
     */
    function _handlePHPPI($data)
    {
        $_PI_RESULT = null;
        $keys = array_keys($GLOBALS);
        foreach ($keys as $key) {
            $$key =& $GLOBALS[$key];
        }
        $eval_result = eval($data.';');
        if ($_PI_RESULT !== null) {
            $result = &$_PI_RESULT;
        } else {
            $result = $eval_result;
        }
        if ($result === false) {
            $errmsg = sprintf('processing-instruction error on line: %s, column: %s',
                xml_get_current_line_number($this->_parser),
                xml_get_current_column_number($this->_parser));
            return $this->raiseError($errmsg);
        } elseif (is_string($result)) {
            $this->_characterDataHandler($this->_parser, $result);
        } elseif (Node::isInherited($result)) {
            $child =& $this->_dom->importNode(&$result, true);
            $this->_parent->appendChild(&$child);
        }
    }
    
    /**
     * Processing attribute value.
     * 
     * @param   string
     * @return  string
     * @access  private
     */
    function _attrValueHandler($data)
    {
        $value = $this->_phpCodeHandler($data);
        if ($value != $data) {
            return $value;
        } elseif ($this->_parseAttrValue) {
            return $this->_curlyBracesHandler($data);
        }
        return $data;
    }
    
    /**
     * Processing php code syntax in attribute value.
     * 
     * @param   string
     * @return  string
     * @access  private
     */
    function _phpCodeHandler($data)
    {
        if (preg_match('/\s*^php:(.+)/', $data, $matches)) {
            $keys = array_keys($GLOBALS);
            foreach ($keys as $key) {
                $$key =& $GLOBALS[$key];
            }
            return (string) eval($matches[1].';');
        }
        return $data;
    }
    
    /**
     * Processing curly braces syntax in attribute value.
     * 
     * @param   string
     * @return  string
     * @access  private
     */
    function _curlyBracesHandler($data)
    {
        if (preg_match_all('/{([^{}]+)}/', $data, $matches)) {
            $replace = array();
            foreach ($matches[1] as $value) {
                $value = $this->_variableHandler($value);
                array_push($replace, $value);
            }
            return str_replace($matches[0], $replace, $data);
        }
        return $data;
    }
    
    /**
     * Processing variables and constant in attribute value.
     * 
     * @param   string
     * @return  string
     * @access  private
     */
    function _variableHandler($data)
    {
        if (preg_match('/\$(\w+)/', $data, $matches)) {
            return $GLOBALS[$matches[1]];
        } elseif (defined($data)) {
            return constant($data);
        } else {
            return '{'.$data.'}';
        }
    }
    
    /**
     * XML_Preprocessor::raiseError()
     * 
     * @access  private
     */
    function &raiseError($message = 'unknown error', $code = null,
                        $mode = null, $options = null, $userinfo = null)
    {
        if (is_resource($this->_parser)) {
            xml_parser_free($this->_parser);
        }
        return parent::raiseError($message, $code, $mode, $options, $userinfo);
    }    
}

/**
* XML_Error class
* 
* @access   private
*/
class XML_Error extends Error
{
    var $skipClass = 'xml_preprocessor';
    
    function XML_Error($message = 'unknown error', $code = null,
                       $mode = null, $options = null, $userinfo = null)
    {
        $this->Error($message, $code, $mode, $options, $userinfo);
    }
}

?>