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
// $Id: myXSLT.php,v 0.4 2004/02/06 10:48:39 anter Exp $

/**
* @package      myXML
* @subpackage   myXSLT
*/
/**
* Error handling.
*/
require_once('Error.php');

/**
* Maximum processing loops.
* 
* When xsl:apply-templates is used to process elements that are not descendants
* of the current node, the possibility arises of non-terminating loops.
* For example,
* <code>
* <xsl:template match="foo">
*   <xsl:apply-templates select="."/>
* </xsl:template>
* </code>
*/
define('MAX_LOOPS_XSLT', 255);

/**
* myXSLT class.
* 
* @author       Tereshchenko Andrey <tereshchenko@anter.com.ua>
* @copyright    Tereshchenko Andrey 2002-2003
* @version      0.4 2004/02/06
* @access       public
* @package      myXML
* @subpackage   myXSLT
* @link         http://phpmyxml.sourceforge.net/
*/
class myXSLT extends PEAR
{
    /**
    * Reference on object Document.
    * 
    * Stylesheet.
    * 
    * @var      object Document
    * @access   private
    */
    var $_stylesheet;
    
    /**
    * Reference on object Document.
    * 
    * Input document.
    * 
    * @var      object Document
    * @access   private
    */
    var $_inputDoc;
    
    /**
    * Reference on object Document.
    * 
    * Output document.
    * 
    * @var      object Document
    * @access   private
    */
    var $_outputDoc;
    
    /**
    * Reference on object myXPath.
    * 
    * @var      object myXPath
    * @access   private
    */
    var $_xpath;
    
    /**
    * Reference on object Element.
    * 
    * Current element of input document.
    * 
    * @var      object Element
    * @access   private
    */
    var $_currInputNode;
    
    /**
    * Reference on object Element.
    * 
    * Current element of output document.
    * 
    * @var      object Element
    * @access   private
    */
    var $_currOutputNode;
    
    /**
    * XSLT elements.
    * 
    * See XSLT Transformations for details.
    * 
    * @var      array
    * @access   private
    */
    var $_elements = array(
            'stylesheet' => 'stylesheetHandler', 'transform' => 'transformHandler',
            'import' => 'importHandler', 'include' => 'includeHandler',
            'strip-space' => 'stripSpaceHandler', 'preserve-space' => 'preserveSpaceHandler', 
            'output' => 'outputHandler', 'key' => 'keyHandler', 
            'decimal-format' => 'decimalFormatHandler', 'namespace-alias' => 'namespaceAliasHandler', 
            'variable' => 'variableHandler', 'param' => 'paramHandler', 'template' => 'templateHandler', 
            'apply-templates' => 'applyTemplatesHandler', 'call-template' => 'callTemplateHandler',
            'element' => 'elementHandler', 'attribute' => 'attributeHandler',
            'attribute-set' => 'attributeSetHandler', 'text' => 'textHandler',
            'processing-instruction' => 'processingInstructionHandler', 'comment' => 'commentHandler',
            'copy' => 'copyHandler', 'value-of' => 'valueOfHandler',
            'number' => 'numberHandler', 'for-each' => 'forEachHandler',
            'if' => 'ifHandler', 'choose' => 'chooseHandler',
            'when' => 'whenHandler', 'otherwise' => 'otherwiseHandler',
            'sort' => 'sortHandler', 'copy-of' => 'copyOfHandler',
            'with-param' => 'withParamHandler', 'message' => 'messageHandler',
            'fallback' => 'fallbackHandler'
            );
    
    /**
    * List of reference on object Element with templates.
    * 
    * @var      array
    * @access   private
    */
    var $_templates = array();
    
    /**
    * Dependence table.
    * 
    * @var      array
    * @access   private
    */
    var $_dependence = array();
    
    /**
     * Constructor.
     * 
     * @param   object Document Input document
     * @param   object Document Output document
     * @param   object Document Stylesheet document
     * @param   object myXPath  XPath processor
     * @return  object myXSLT
     * @access  public
     * @static  method
     */
    function &create(&$input, &$output, &$stylesheet, &$xpath)
    {
        $proc =& new myXSLT;
        $proc->PEAR('XSLT_Error');
        $proc->setInputDocument(&$input);
        $proc->setOutputDocument(&$output);
        $proc->setStylesheet(&$stylesheet);
        $proc->setXPathObject(&$xpath);
        return $proc;
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
     * Sets reference on object Document with input data.
     * 
     * @param   object Document
     * @access  public
     */
    function setInputDocument(&$input)
    {
        if (!Document::isInherited($input)) {
            return $this->raiseError('the "$input" argument must be object DOM');
        }
        $this->_inputDoc =& $input;
        $this->_inputDoc->setErrorHandling(PEAR_ERROR_CALLBACK, array(&$this, '_handleSlaveObjectsError'));
        $this->_currInputNode =& $input;
    }
    
    /**
     * Sets reference on object Document with output data.
     * 
     * @param   object Document
     * @access  public
     */
    function setOutputDocument(&$output)
    {
        if (!Document::isInherited($output)) {
            return $this->raiseError('the "$output" argument must be object DOM');
        }
        $this->_outputDoc =& $output;
        $this->_outputDoc->setErrorHandling(PEAR_ERROR_CALLBACK, array(&$this, '_handleSlaveObjectsError'));
        $this->_currOutputNode =& $output;
    }
    
    /**
     * Sets reference on object Document with stylesheet.
     * 
     * @param   object Document
     * @access  public
     */
    function setStylesheet(&$stylesheet)
    {
        if (!Document::isInherited($stylesheet)) {
            return $this->raiseError('the "$stylesheet" argument must be object DOM');
        }
        $this->_stylesheet =& $stylesheet;
        $this->_stylesheet->setErrorHandling(PEAR_ERROR_CALLBACK, array(&$this, '_handleSlaveObjectsError'));
    }
    
    /**
     * Sets reference on object myXPath.
     * 
     * @param   object myXPath
     * @access  public
     */
    function setXPathObject(&$xpath)
    {
        if (!is_object($xpath) || !is_a($xpath, 'myXPath')) {
            return $this->raiseError('the "$xpath" argument must be object myXPath');
        }
        $this->_xpath =& $xpath;
        $this->_xpath->setErrorHandling(PEAR_ERROR_CALLBACK, array(&$this, '_handleSlaveObjectsError'));
    }
    
    /**
     * Handler of debug messages.
     * 
     * @param   string
     * @return  string
     * @access  private
     */
    function showDebug($msg)
    {
        $msg = "<br>myXSLT::".htmlentities($msg);
        print($msg);
        flush;
    }
    
    /**
     * Translation.
     * 
     * @return  object Document
     * @access  public
     */
    function &translate()
    {
        $this->_isPrepared();
        $this->_setBuiltInTemplates();
        if ($this->_stylesheet->documentElement->hasChildNodes()) {
            $this->_handleTopLevelElements($this->_stylesheet->documentElement->firstChild);
        }
        $templateNode =& $this->_getDependentTemplate($this->_currInputNode->_ID);
        if ($templateNode->hasChildNodes()) {
            $this->_templateWorm(&$templateNode->firstChild);
        }
        return $this->_outputDoc;
    }
    
    /**
     * myXSLT::_attachDependentTemplate()
     * 
     * @access  private
     */
    function _attachDependentTemplate(&$template, $pattern, $id)
    {
        if ($this->_getDependentTemplate($id)) {
            $firstPattern = $this->_getDependentPattern($id);
            $firstPriority = $this->_calculatePriority($firstPattern);
            $secondPriority = $this->_calculatePriority($pattern);
            if ($secondPriority >= $firstPriority) {
                $this->_setDependentTemplate(&$template, $pattern, $id);
            }
        } else {
            $this->_setDependentTemplate(&$template, $pattern, $id);
        }
    }
    
    /**
     * myXSLT::_calculatePriority()
     * 
     * @access  private
     */
    function _calculatePriority($pattern)
    {
        global $XSLTPriority_0, $XSLTPriority_25, $XSLTPriority_5;
        if (preg_match("/^ ( $XSLTPriority_0 ) $/x", $pattern)) {
            return 0;
        } elseif (preg_match("/^ ( $XSLTPriority_25 ) $/x", $pattern)) {
            return -0.25;
        } elseif (preg_match("/^ ( $XSLTPriority_5 ) $/x", $pattern)) {
            return -0.5;
        }
        return 0.5;
    }
    
    /**
     * myXSLT::_getDependentTemplate()
     * 
     * @access  private
     */
    function &_getDependentTemplate($id)
    {
        return $this->_dependence[$id][0];
    }
    
    /**
     * myXSLT::_getDependentPattern()
     * 
     * @access  private
     */
    function &_getDependentPattern($id)
    {
        return $this->_dependence[$id][1];
    }
    
    /**
     * myXSLT::_handleTopLevelElements()
     * 
     * @access  private
     */
    function _handleTopLevelElements(&$node)
    {
        $this->_debug and $this->showDebug('_handleTopLevelElements: '.$node->toString($deep = false));
        $this->_prepareXPath();
        $this->_handleXSLElement(&$node);
        if ($node->nextSibling) {
            $this->_handleTopLevelElements(&$node->nextSibling);
        }
    }
    
    /**
     * myXSLT::_handlePattern()
     * 
     * @access  private
     */
    function _handlePattern(&$template, $pattern)
    {
        $nodeSet = $this->_xpath->evaluate($pattern);
        $length = sizeof($nodeSet);
        for ($i = 0; $i < $length; $i++) {
            $this->_attachDependentTemplate(&$template, $pattern, $nodeSet[$i]->_ID);
        }
    }
    
    /**
     * myXSLT::_handleSlaveObjectsError()
     * 
     * @access  private
     */
    function _handleSlaveObjectsError(&$error)
    {
        ini_set('error_reporting', E_USER_ERROR | E_USER_WARNING | E_USER_NOTICE);
        $this->raiseError($error->getMessage(), null, null, null, $error->getUserInfo());
    }
    
    /**
     * Check preparing object.
     * 
     * @return  boolean
     * @access  private
     */
    function _isPrepared()
    {
        if (!$this->_stylesheet) {
            return $this->raiseError('set the stylesheet document before translating');
        }
        if (!$this->_inputDoc) {
            return $this->raiseError('set the input document before translating');
        }
        if (!$this->_outputDoc) {
            return $this->raiseError('set the output document before translating');
        }
        if (!$this->_xpath) {
            return $this->raiseError('set the XPath object before translating');
        }
    }
    
    /**
     * myXSLT::_prepareXPath()
     * 
     * @access  private
     */
    function _prepareXPath()
    {
        $this->_xpath->setDOMDocument(&$this->_inputDoc);
        $context = $this->_xpath->evaluate('descendant-or-self::node()');
        $this->_xpath->setContext($context);
    }
    
    /**
     * myXSLT::_separatePattern()
     * 
     * @access  private
     */
    function _separatePattern($pattern)
    {
        $rule = "/[^\|]+/";
        preg_match_all($rule, $pattern, $matches);
        return $matches[0];
    }
    
    /**
     * Sets built-in templates.
     * 
     * See XSLT Transformations for details.
     * 
     * @access  private
     */
    function _setBuiltInTemplates()
    {
        if ($root =& $this->_stylesheet->documentElement) {
            $namespaceURI = $this->_stylesheet->resolveNS('xsl');
            
            /**
            * built-in template
            * <xsl:template match="processing-instruction()|comment()">
            */
            $node =& $this->_stylesheet->createElementNS($namespaceURI, 'xsl:template');
            $node->setAttribute('match', 'processing-instruction()|comment()');
            $this->_stylesheet->documentElement->insertBefore(&$node, &$this->_stylesheet->documentElement->firstChild);
            
            /**
            * built-in template
            * <xsl:template match="text()|@*">
            *   <xsl:value-of select="."/>
            * </xsl:template>
            */
            $node =& $this->_stylesheet->createElementNS($namespaceURI, 'xsl:template');
            $node->setAttribute('match', 'text()|@*');
            $childNode =& $this->_stylesheet->createElementNS($namespaceURI, 'xsl:value-of');
            $childNode->setAttribute('select', '.');
            $node->appendChild(&$childNode);
            $this->_stylesheet->documentElement->insertBefore(&$node, &$this->_stylesheet->documentElement->firstChild);
            
            /**
            * built-in template
            * <xsl:template match="*|/">
            *   <xsl:apply-templates/>
            * </xsl:template>
            */
            $node =& $this->_stylesheet->createElementNS($namespaceURI, 'xsl:template');
            $node->setAttribute('match', '*|/');
            $childNode =& $this->_stylesheet->createElementNS($namespaceURI, 'xsl:apply-templates');
            $node->appendChild(&$childNode);
            $this->_stylesheet->documentElement->insertBefore(&$node, &$this->_stylesheet->documentElement->firstChild);
        }
    }
    
    /**
     * myXSLT::_setDependentTemplate()
     * 
     * @access  private
     */
    function _setDependentTemplate(&$template, $pattern, $id)
    {
        $this->_dependence[$id] = array(&$template, $pattern);
    }
    
#******************************************************************************#
#                                                                              #
#                             XSLT elements handlers                           #
#                                                                              #
#******************************************************************************#

    /**
     * See XSLT Transformations for details.
     * 
     * @param   object Element
     * @access  private
     */
    function stylesheetHandler(&$node) //?????
    {
        if ($node->hasChildNodes()) {
            $this->_templateWorm(&$node->firstChild);
        }
    }
    
    /**
     * See XSLT Transformations for details.
     * 
     * @param   object Element
     * @access  private
     */
    function translateHandler(&$node) //?????
    {
        if ($node->hasChildNodes()) {
            $this->_templateWorm(&$node->firstChild);
        }
    }
    
    /**
     * See XSLT Transformations for details.
     * 
     * @param   object Element
     * @access  private
     */
    function templateHandler(&$node)
    {
        $pattern = $node->getAttribute('match');
        $union = $this->_separatePattern($pattern);
        foreach ($union as $pattern) {
            $this->_handlePattern(&$node, $pattern);
        }
    }
    
    /**
     * See XSLT Transformations for details.
     * 
     * @param   object Element
     * @access  private
     */
    function applyTemplatesHandler(&$node)
    {
        $this->_xpath->setDOMDocument(&$this->_inputDoc);
        $this->_xpath->setContext(array(&$this->_currInputNode));
        $pattern = ($node->hasAttribute('select')) ? $node->getAttribute('select') : '*';
        $nodeSet = $this->_xpath->evaluate($pattern);
        $count = sizeof($nodeSet);
        if ($count == 0) {
            return;
        }
        $currInputNode =& $this->_currInputNode;
        for ($n = 0; $n < $count; $n++) {
            $childNode =& $nodeSet[$n];
            if ($templateNode =& $this->_getDependentTemplate($childNode->_ID)) {
                $this->_currInputNode =& $childNode;
                if ($templateNode->hasChildNodes()) {
                    $this->_templateWorm(&$templateNode->firstChild);
                }
            }
        }
        $this->_currInputNode =& $currInputNode;
    }
    
    /**
     * See XSLT Transformations for details.
     * 
     * @param   object Element
     * @access  private
     */
    function forEachHandler(&$node)
    {
        $this->_xpath->setDOMDocument(&$this->_inputDoc);
        $this->_xpath->setContext(array(&$this->_currInputNode));
        if ($node->hasAttribute('select')) {
            $pattern = $node->getAttribute('select');
        } else {
            return $this->raiseError('the "for-each" element must have attribute "select"');
        }
        $nodeSet = $this->_xpath->evaluate($pattern);
        $count = sizeof($nodeSet);
        if ($count == 0) {
            return;
        }
        $currInputNode =& $this->_currInputNode;
        for ($n = 0; $n < $count; $n++) {
            $childNode =& $nodeSet[$n];
            $this->_currInputNode =& $childNode;
            if ($node->hasChildNodes()) {
                $this->_templateWorm(&$node->firstChild);
            }
        }
        $this->_currInputNode =& $currInputNode;
    }
    
    /**
     * See XSLT Transformations for details.
     * 
     * @param   object Element
     * @access  private
     */
    function attributeHandler(&$node)
    {
        if ($node->hasAttribute('name')) {
            $attrName = $node->getAttribute('name');
        } else {
            return $this->raiseError('the element "attribute" must have attribute "name"');
        }
        if ($node->hasChildNodes()) {
            $oldCurrentNode =& $this->_currOutputNode;
            $this->_currOutputNode =& $this->_outputDoc->createElement('temp');
            $this->_templateWorm(&$node->firstChild);
            $value = trim($this->_valueOfWorm(&$this->_currOutputNode));
            $this->_currOutputNode =& $oldCurrentNode;
        }
        if ($node->hasAttribute('namespace')) {
            $attrNamespace = $node->getAttribute('namespace');
            $this->_currOutputNode->setAttributeNS($attrNamespace, $attrName, $value);
        } else {
            $this->_currOutputNode->setAttribute($attrName, $value);
        }
    }
    
    /**
     * See XSLT Transformations for details.
     * 
     * @param   object Element
     * @access  private
     */
    function valueOfHandler(&$node)
    {
        $this->_xpath->setDOMDocument(&$this->_inputDoc);
        $this->_xpath->setContext(array(&$this->_currInputNode));
        $pattern = ($node->hasAttribute('select')) ? $node->getAttribute('select') : '.';
        $nodeSet = $this->_xpath->evaluate($pattern);
        $selNode =& $nodeSet[0];
        if ($selNode->nodeType == ELEMENT_NODE && $selNode->hasChildNodes()) {
            $value = $this->_valueOfWorm(&$selNode->firstChild);
        } elseif ($selNode->nodeType == ATTRIBUTE_NODE) {
            $value = $selNode->value;
        } elseif ($selNode->nodeType == TEXT_NODE || $node->nodeType == CDATA_SECTION_NODE) {
            $value = $selNode->data;
        }
        $textNode =& $this->_outputDoc->createTextNode($value);
        $this->_currOutputNode->appendChild(&$textNode);
    }
    
    /**
     * See XSLT Transformations for details.
     * 
     * @param   object Element
     * @access  private
     */
    function ifHandler(&$node)
    {
        if ($node->hasAttribute('test')) {
            $expression = 'self::node()['.$node->getAttribute('test').']';
        } else {
            return $this->raiseError('the element "if" must have attribute "test"');
        }
        $this->_xpath->setDOMDocument(&$this->_inputDoc);
        $this->_xpath->setContext(array(&$this->_currInputNode));
        $nodeSet = $this->_xpath->evaluate($expression);
        if (sizeof($nodeSet) > 0 && $node->hasChildNodes()) {
            $this->_templateWorm(&$node->firstChild);
        }
        
    }
    
    /**
     * See XSLT Transformations for details.
     * 
     * @param   object Element
     * @access  private
     */
    function chooseHandler(&$node)
    {
        if ($node->firstChild->nodeName == 'xsl:when') {
            $this->_chooseWorm(&$node->firstChild);
        }
    }
    
    /**
     * See XSLT Transformations for details.
     * 
     * @param   object Element
     * @access  private
     */
    function whenHandler(&$node)
    {
        if ($node->parentNode->nodeName != 'xsl:choose') {
            return $this->raiseError('the element "when" must have the parent element "choose"');
        }
        if ($node->hasAttribute('test')) {
            $expression = 'self::node()['.$node->getAttribute('test').']';
        } else {
            return $this->raiseError('the element "when" must have attribute "test"');
        }
        $this->_xpath->setDOMDocument(&$this->_inputDoc);
        $this->_xpath->setContext(array(&$this->_currInputNode));
        $nodeSet = $this->_xpath->evaluate($expression);
        if (sizeof($nodeSet) > 0 && $node->hasChildNodes()) {
            $this->_templateWorm(&$node->firstChild);
            return 1;
        } else {
            return -1;
        }
    }
    
    /**
     * See XSLT Transformations for details.
     * 
     * @param   object Element
     * @access  private
     */
    function otherwiseHandler(&$node)
    {
        if ($node->parentNode->nodeName != 'xsl:choose') {
            return $this->raiseError('the element "otherwise" must have the parent element "choose"');
        }
        $this->_templateWorm(&$node->firstChild);
    }
    
    /**
     * See XSLT Transformations for details.
     * 
     * Not implemented in this version.
     * 
     * @param   object Element
     * @access  private
     */
    function importHandler(&$node)
    {
        return $this->raiseError('the element "import" not implemented in this version');
    }
    
    /**
     * See XSLT Transformations for details.
     * 
     * @param   object Element
     * @access  private
     */
    function includeHandler(&$node)
    {
        if (!$node->hasAttribute('href')) {
            return $this->raiseError('the element xsl:include must have attribute href');
        }
        $incStylesheet = new Document;
        $incStylesheet->setErrorHandling(PEAR_ERROR_CALLBACK, array(&$this, '_handleSlaveObjectsError'));
        $result = $incStylesheet->parseFile($node->getAttribute('href'));
        $nextSibling =& $node->nextSibling;
        for ($i = 0; $i < $incStylesheet->documentElement->childNodes->length; $i++) {
            $incTopElement =& $incStylesheet->documentElement->childNodes->item($i);
            $importedNode =& $this->_stylesheet->importNode(&$incTopElement, $deep = true);
            $result = $this->_stylesheet->documentElement->insertBefore(&$importedNode, &$nextSibling);
        }
    }
    
    /**
     * See XSLT Transformations for details.
     * 
     * Not implemented in this version.
     * 
     * @param   object Element
     * @access  private
     */
    function stripSpaceHandler(&$node)
    {
        return $this->raiseError('the element "strip-space" not implemented in this version');
    }
    
    /**
     * See XSLT Transformations for details.
     * 
     * Not implemented in this version.
     * 
     * @param   object Element
     * @access  private
     */
    function preserveSpaceHandler(&$node)
    {
        return $this->raiseError('the element "preserve-space" not implemented in this version');
    }
    
    /**
     * See XSLT Transformations for details.
     * 
     * Not implemented in this version.
     * 
     * @param   object Element
     * @access  private
     */
    function outputHandler(&$node)
    {
        return $this->raiseError('the element "output" not implemented in this version');
    }
    
    /**
     * See XSLT Transformations for details.
     * 
     * Not implemented in this version.
     * 
     * @param   object Element
     * @access  private
     */
    function keyHandler(&$node)
    {
        return $this->raiseError('the element "key" not implemented in this version');
    }
    
    /**
     * See XSLT Transformations for details.
     * 
     * Not implemented in this version.
     * 
     * @param   object Element
     * @access  private
     */
    function decimalFormatHandler(&$node)
    {
        return $this->raiseError('the element "decimal-format" not implemented in this version');
    }
    
    /**
     * See XSLT Transformations for details.
     * 
     * Not implemented in this version.
     * 
     * @param   object Element
     * @access  private
     */
    function namespaceAliasHandler(&$node)
    {
        return $this->raiseError('the element "namespace-alias" not implemented in this version');
    }
    
    /**
     * See XSLT Transformations for details.
     * 
     * Not implemented in this version.
     * 
     * @param   object Element
     * @access  private
     */
    function attributeSetHandler(&$node)
    {
        return $this->raiseError('the element "attribute-set" not implemented in this version');
    }
    
    /**
     * See XSLT Transformations for details.
     * 
     * Not implemented in this version.
     * 
     * @param   object Element
     * @access  private
     */
    function variableHandler(&$node)
    {
        return $this->raiseError('the element "variable" not implemented in this version');
    }
    
    /**
     * See XSLT Transformations for details.
     * 
     * Not implemented in this version.
     * 
     * @param   object Element
     * @access  private
     */
    function paramHandler(&$node)
    {
        return $this->raiseError('the element "param" not implemented in this version');
    }
    
    /**
     * See XSLT Transformations for details.
     * 
     * Not implemented in this version.
     * 
     * @param   object Element
     * @access  private
     */
    function callTemplateHandler(&$node)
    {
        return $this->raiseError('the element "call-template" not implemented in this version');
    }
    
    /**
     * See XSLT Transformations for details.
     * 
     * Not implemented in this version.
     * 
     * @param   object Element
     * @access  private
     */
    function elementHandler(&$node)
    {
        return $this->raiseError('the element "element" not implemented in this version');
    }
    
    /**
     * See XSLT Transformations for details.
     * 
     * Not implemented in this version.
     * 
     * @param   object Element
     * @access  private
     */
    function textHandler(&$node)
    {
        return $this->raiseError('the element "text" not implemented in this version');
    }
    
    /**
     * See XSLT Transformations for details.
     * 
     * Not implemented in this version.
     * 
     * @param   object Element
     * @access  private
     */
    function processingInstructionHandler(&$node)
    {
        return $this->raiseError('the element "processing-instruction" not implemented in this version');
    }
    
    /**
     * See XSLT Transformations for details.
     * 
     * @param   object Element
     * @access  private
     */
    function commentHandler(&$node)
    {
        if ($node->hasChildNodes()) {
            $value = $this->_valueOfWorm(&$node->firstChild);
        }
        $comment =& $this->_outputDoc->createComment($value);
        $this->_currOutputNode->appendChild(&$comment);
    }
    
    /**
     * See XSLT Transformations for details.
     * 
     * @param   object Element
     * @access  private
     */
    function copyHandler(&$node)
    {
        $newOutNode =& $this->_outputDoc->importNode(&$this->_currInputNode);
        $this->_currOutputNode->appendChild(&$newOutNode);
        $this->_currOutputNode =& $newOutNode;
        if ($node->hasChildNodes()) {
            $this->_templateWorm(&$node->firstChild);
        }
        $this->_currOutputNode =& $this->_currOutputNode->parentNode;
    }
    
    /**
     * See XSLT Transformations for details.
     * 
     * Not implemented in this version.
     * 
     * @param   object Element
     * @access  private
     */
    function numberHandler(&$node)
    {
        return $this->raiseError('the element "number" not implemented in this version');
    }
    
    /**
     * See XSLT Transformations for details.
     * 
     * Not implemented in this version.
     * 
     * @param   object Element
     * @access  private
     */
    function sortHandler(&$node)
    {
        return $this->raiseError('the element "sort" not implemented in this version');
    }
    
    /**
     * See XSLT Transformations for details.
     * 
     * Not implemented in this version.
     * 
     * @param   object Element
     * @access  private
     */
    function copyOfHandler(&$node)
    {
        return $this->raiseError('the element "copy-of" not implemented in this version');
    }
    
    /**
     * See XSLT Transformations for details.
     * 
     * Not implemented in this version.
     * 
     * @param   object Element
     * @access  private
     */
    function withParamHandler(&$node)
    {
        return $this->raiseError('the element "with-param" not implemented in this version');
    }
    
    /**
     * See XSLT Transformations for details.
     * 
     * Not implemented in this version.
     * 
     * @param   object Element
     * @access  private
     */
    function messageHandler(&$node)
    {
        return $this->raiseError('the element "message" not implemented in this version');
    }
    
    /**
     * See XSLT Transformations for details.
     * 
     * Not implemented in this version.
     * 
     * @param   object Element
     * @access  private
     */
    function fallbackHandler(&$node)
    {
        return $this->raiseError('the element "fallback" not implemented in this version');
    }
    
#******************************************************************************#
#                                                                              #
#                            Functions auxiliary.                              #
#                                                                              #
#******************************************************************************#
    
    /**
     * myXSLT::_attrValueTemplateHandler()
     * 
     * @param   object Element
     * @return  object Element
     * @access  private
     */
    function &_attrValueTemplateHandler(&$node)
    {
        if ($node->nodeType == ELEMENT_NODE && $node->hasAttributes()) {
            $this->_xpath->setDOMDocument(&$this->_inputDoc);
            $this->_xpath->setContext(array(&$this->_currInputNode));
            for ($n = 0; $n < $node->attributes->length; $n++) {
                $attr =& $node->attributes->item($n);
                if (preg_match_all('/{([^{}]+)}/', $attr->value, $matches)) {
                    $replace = array();
                    foreach ($matches[1] as $value) {
                        $data = '';
                        $nodeSet = $this->_xpath->evaluate($value);
                        if ($nodeSet[0]->nodeType == TEXT_NODE) {
                            $data = $nodeSet[0]->data;
                        } elseif ($nodeSet[0]->nodeType == ATTRIBUTE_NODE) {
                            $data = $nodeSet[0]->value;
                        } elseif ($nodeSet[0]->nodeType == ELEMENT_NODE && $nodeSet[0]->hasChildNodes()) {
                            $data = $this->_valueOfWorm(&$nodeSet[0]->firstChild);
                        }
                        array_push($replace, $data);
                    }
                    $attr->value = str_replace($matches[0], $replace, $attr->value);
                }
            }
        }
    }
    
    /**
     * Recursive function.
     * 
     * Uses of element handler <xsl:choose>.
     * 
     * @param   object Element
     * @access  private
     */
    function _chooseWorm(&$node)
    {
        $result = $this->whenHandler(&$node);
        if ($result == -1) {
            if ($node->nextSibling->nodeName == 'xsl:when') {
                $this->_chooseWorm(&$node->nextSibling);
            } elseif ($node->nextSibling->nodeName == 'xsl:otherwise') {
                $this->otherwiseHandler(&$node->nextSibling);
            }
        }
    }
    
    /**
     * myXSLT::_handleStylesheetElement()
     * 
     * @access  private
     */
    function _handleXSLElement(&$node)
    {
        if ($method = $this->_elements[$node->localName]) {
            $this->$method(&$node);
        } else {
            return $this->raiseError("unknown element '{$node->localName}'");
        }
    }
    
    /**
     * myXSLT::_handleElement()
     * 
     * @access  private
     */
    function _handleElement(&$node)
    {
        $newOutNode =& $this->_outputDoc->importNode(&$node);
        $this->_attrValueTemplateHandler(&$newOutNode);
        $this->_currOutputNode->appendChild(&$newOutNode);
        $this->_currOutputNode =& $newOutNode;
        if ($node->hasChildNodes()) {
            $this->_templateWorm(&$node->firstChild);
        }
        $this->_currOutputNode =& $this->_currOutputNode->parentNode;
    }
    
    /**
     * Main recursive function.
     * 
     * Uses elements handlers.
     * 
     * @param   object Element
     * @access  private
     */
    function _templateWorm(&$node)
    {
        static $level = 0;
        if ($level > MAX_LOOPS_XSLT) {
            return $this->raiseError('detect non-terminating loops');
        }
        $level++;
        do {
            if ($node->prefix == 'xsl') {
                $this->_handleXSLElement(&$node);
            } else {
                $this->_handleElement(&$node);
            }
        } while ($node =& $node->nextSibling);
        $level--;
    }
    
    /**
     * Concatenation function.
     * 
     * Uses of element handler <xsl:value-of>.
     * 
     * @param   object Element
     * @return  string
     * @access  private
     */
    function _valueOfWorm(&$node)
    {
        if ($node->nodeType == ELEMENT_NODE) {
            if ($node->hasChildNodes()) {
                $value.= $this->_valueOfWorm(&$node->firstChild);
            }
        } elseif ($node->nodeType == TEXT_NODE || $node->nodeType == CDATA_SECTION_NODE) {
            $value.= $node->data;
        }
        if ($node->nextSibling) {
            $value.= $this->_valueOfWorm(&$node->nextSibling);
        }
        return $value;
    }    
}

/**
* XPath_Error class
* 
* @access   private
*/
class XSLT_Error extends Error
{
    var $error_message_prefix = 'myXSLT error: ';
    var $skipClass = 'myxslt';
    
    function XSLT_Error($message = 'unknown error', $code = null,
                        $mode = null, $options = null, $userinfo = null)
    {
        $this->Error($message, $code, $mode, $options, $userinfo);
    }
}


$Letter         = " [^\d\W] ";
$Digit          = " \d ";
$NCNameChar     = " $Letter | $Digit | \. | - | _ ";
$NCName         = " (?: $Letter | _ )(?: $NCNameChar )* ";
$QName          = " (?: $NCName: )? $NCName ";
$Literal        = " \"[^\"]*\" | '[^']*' ";

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

$NodeTest       = "   ( $NameTest )
                    | ( $NodeType )\(\)
                    | processing-instruction\(( $Literal )\) ";

$ChildOrAttrAxis= "   @ | ( child | attribute ):: ";

$Predicate      = " \[ [^\[\]]* \] ";

global $XSLTPriority_0, $XSLTPriority_25, $XSLTPriority_5;

$XSLTPriority_0 = "   ( $ChildOrAttrAxis )? $QName ( $Predicate )* 
                    | ( $ChildOrAttrAxis )? processing-instruction\(( $Literal )\) ( $Predicate )* ";

$XSLTPriority_25= " ( $ChildOrAttrAxis )? $NCName:\* ( $Predicate )* ";

$XSLTPriority_5 = " ( $ChildOrAttrAxis )? ( $NodeTest ) ( $Predicate )* ";

?>