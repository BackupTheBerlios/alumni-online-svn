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
// $Id: Parser.php,v 2.32 2004/05/23 13:06:05 anter Exp $

/**
* @package      myXML
* @subpackage   myDOM
*/
/**
* Error handling.
*/
require_once('Error.php');

/**
* Parses XML-document into DOM-document.
* 
* @author       Tereshchenko Andrey <tereshchenko@anter.com.ua>
* @copyright    Tereshchenko Andrey 2002-2003
* @version      2.32 2004/05/23
* @access       private
* @package      myXML
* @subpackage   myDOM
* @link         http://phpmyxml.sourceforge.net/
*/
class Parser extends PEAR
{
    /**
    * Reference on object Document.
    * 
    * @var      object Document
    * @access   private
    */
    var $_dom;
    
    /**
    * Element which append child element.
    * 
    * @var      object  Element
    * @access   private
    */
    var $_parent = null;
    
    /**
    * Reference on SAX parser.
    * 
    * @var      object
    * @access   private
    */
    var $_parser = null;
    
    /**
     * Constructor.
     * 
     * @param   object Document
     * @return  object Parser
     * @access  public
     * @static  method
     */
    function &create(&$parent)
    {
        $parser = new Parser;
        $parser->PEAR('Parser_Error');
        $parser->_prepare(&$parent);
        return $parser;
    }
    
    /**
     * Parses string.
     * 
     * This method parses string given in the passed parameter.
     * 
     * @param   string      XML data.
     * @param   boolean     end of file.
     * @access  public
     * @see     parseFile()
     */
    function parse($data, $eof = true)
    {
        if (!is_resource($this->_parser)) {
            $this->_parser = xml_parser_create();
            xml_set_object($this->_parser, &$this);
            xml_parser_set_option($this->_parser, XML_OPTION_CASE_FOLDING, 0);
            xml_set_element_handler($this->_parser, '_startElementHandler', '_endElementHandler');
            xml_set_character_data_handler($this->_parser, '_characterDataHandler');
            xml_set_processing_instruction_handler($this->_parser, '_processingInstructionHandler');
            xml_set_default_handler($this->_parser, '_defaultHandler');
            //xml_set_external_entity_ref_handler($this->_parser, '_externalEntityRefHandler');
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
        $pinode =& $this->_dom->createProcessingInstruction($target, $data);
        $this->_parent->appendChild(&$pinode);
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
     * Parser::_prepare()
     * 
     * @access  private
     */
    function _prepare(&$parent)
    {
        if (!Node::isInherited($parent)) {
            return $this->raiseError('the first argument must be object inherited from Node', E_USER_ERROR, 4);
        }
        $this->_parent =& $parent;
        if (Element::isInherited($parent)) {
            $this->_dom =& $parent->ownerDocument;
        } else {
            $this->_dom =& $parent;
        } 
    }
    
    /**
     * Parser::raiseError()
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
* Parser_Error class.
* 
* @access   private
*/
class Parser_Error extends Error
{
    var $skipClass = 'parser';
    
    function Parser_Error($message = 'unknown error', $code = null,
                          $mode = null, $options = null, $userinfo = null)
    {
        $this->Error($message, $code, $mode, $options, $userinfo);
    }
}

?>