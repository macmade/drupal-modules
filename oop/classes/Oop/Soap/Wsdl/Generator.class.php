<?php

# $Id$

/**
 * WSDL file generator for SOAP
 * 
 * @author          Jean-David Gadina <macmade@eosgarden.com>
 * @copyright       Copyright &copy; 2008
 * @package         Oop/Soap/Wsdl
 * @version         0.1
 */
class Oop_Soap_Wsdl_Generator
{
    /**
     * The URI of the WSDL namespace
     */
    const NAMESPACE_WSDL          = 'http://schemas.xmlsoap.org/wsdl/';
    
    /**
     * The URI of the SOAP namespace
     */
    const NAMESPACE_SOAP          =  'http://schemas.xmlsoap.org/wsdl/soap/';
    
    /**
     * The URI of the SOAP encoding namespace
     */
    const NAMESPACE_SOAP_ENCODING =  'http://schemas.xmlsoap.org/soap/encoding/';
    
    /**
     * The URI of the SOAP HTTP namespace
     */
    const NAMESPACE_SOAP_HTTP     =  'http://schemas.xmlsoap.org/soap/http';
    
    /**
     * The URI of the XSD namespace
     */
    const NAMESPACE_XSD           =  'http://www.w3.org/2001/XMLSchema';
    
    /**
     * The instance of the XML writer
     */
    protected $_xml               = NULL;
    
    /**
     * The reflection object
     */
    protected $_reflection        = NULL;
    
    /**
     * The available SOAP procedures (the public methods of the handler class)
     */
    protected $_soapProcedures    = array();
    
    /**
     * The web service name
     */
    protected $_name              = '';
    
    /**
     * The URL of the web service
     */
    protected $_url               = '';
    
    /**
     * The web service target namespace
     */
    protected $_tns               = '';
    
    /**
     * The WSDL file
     */
    protected $_wsdl              = '';
    
    /**
     * Class constructor
     * 
     * @param   mixed   The class that will handle the SOAP requests, either as a string or as an object
     * @param   string  The URL of the web service
     * @return  NULL
     */
    public function __construct( $handlerClass, $url )
    {
        // Checks for reflection support
        if( !class_exists( 'ReflectionObject' ) ) {
            
            // Error - Reflection is not available
            throw new Oop_Soap_Wsdl_Generator_Exception( 'The PHP reflection classes are not available', Oop_Soap_Wsdl_Generator_Exception::EXCEPTION_NO_REFLECTION );
        }
        
        // Checks for the XmlWriter class
        if( !class_exists( 'XmlWriter' ) ) {
            
            // Error - XmlWriter is not available
            throw new Oop_Soap_Wsdl_Generator_Exception( 'The XmlWriter class is not available', Oop_Soap_Wsdl_Generator_Exception::EXCEPTION_NO_XML_WRITER );
        }
        
        // Checks if we have an object or a class name
        if( is_object( $handlerClass ) ) {
            
            // Creates the reflection object
            $this->_reflection = new ReflectionObject( $handlerClass );
            
            // Stores the web service name
            $this->_name       = get_class( $handlerClass );
            
        } else {
            
            // Creates the reflection object
            $this->_reflection = new ReflectionClass( $handlerClass );
            
            // Stores the web service name
            $this->_name       = $handlerClass;
        }
        
        // Stores the URL of the web service
        $this->_url            = $url;
        
        // Stores the web service target namespace
        $this->_tns            = ( isset( $_SERVER[ 'SSL' ] ) && $_SERVER[ 'SSL' ] ) ? 'https://' . $_SERVER[ 'HTTP_HOST' ] . '/' . $this->_name : 'http://' . $_SERVER[ 'HTTP_HOST' ] . '/' . $this->_name;
        
        // Gets and stores the available SOAP procedures
        $this->_soapProcedures = $this->_getSoapProcedures();
        
        // Creates the XML writer object
        $this->_xml            = new XmlWriter();
        $this->_xml->openMemory();
        $this->_xml->setIndent( 4 );
        
        // Creates the WSDL document
        $this->_createWsdl();
        
        // Ends the XML documents
        $this->_xml->endDocument();
        
        // Stores the XML output
        $this->_wsdl = $this->_xml->flush();
    }
    
    /**
     * Gets the WSDL file
     * 
     * @return  string  The WSDL file
     */
    public function __toString()
    {
        return $this->_wsdl;
    }
    
    /**
     * 
     */
    protected function _getSoapProcedures()
    {
        // Gets the available public methods
        $methods = $this->_reflection->getMethods( ReflectionMethod::IS_PUBLIC );
        
        // Storage
        $proc    = array();
        
        // Process each methods
        foreach( $methods as $method ) {
            
            // Do not process PHP magic methods nor static methods
            if( substr( $method->name, 0, 2 ) !== '__' && !$method->isStatic() ) {
                
                // Stores the method
                $proc[ $method->name ] = array();
                
                // Gets the parameters
                $params                = $method->getParameters();
                
                // Process each parameter
                foreach( $params as $param ) {
                    
                    // Stores the parameter
                    $proc[ $method->name ][] = $param->name;
                }
            }
        }
        
        // Returns the SOAP procedures
        return $proc;
    }
    
    /**
     * 
     */
    protected function _createWsdl()
    {
        // Starts the XML document
        $this->_xml->startDocument(
            '1.0',
            'utf-8'
        );
        
        // Starts the WSDL definitions element
        $this->_xml->startElement( 'wsdl:definitions' );
        
        // Adds the SOAP namespace
        $this->_xml->writeAttribute(
            'xmlns:soap',
            self::NAMESPACE_SOAP
        );
        
        // Adds the SOAP encoding namespace
        $this->_xml->writeAttribute(
            'xmlns:soapenc',
            self::NAMESPACE_SOAP_ENCODING
        );
        
        // Adds the XSD namespace
        $this->_xml->writeAttribute(
            'xmlns:xsd',
            self::NAMESPACE_XSD
        );
        
        // Adds the WSDL namespace
        $this->_xml->writeAttribute(
            'xmlns:wsdl',
            self::NAMESPACE_WSDL
        );
        
        // Adds the name parameter
        $this->_xml->writeAttribute(
            'name',
            $this->_name
        );
        
        // Adds the target namespace
        $this->_xml->writeAttribute(
            'targetNamespace',
            $this->_tns
        );
        $this->_xml->writeAttribute(
            'xmlns:tns',
            $this->_tns
        );
        
        // Creates the WSDL messages
        $this->_createWsdlMessages();
        
        // Creates the WSDL port type
        $this->_createWsdlPortType();
        
        // Creates the WSDL binding
        $this->_createWsdlBinding();
        
        // Creates the WSDL service
        $this->_createWsdlService();
    }
    
    /**
     * 
     */
    protected function _createWsdlMessages()
    {
        foreach( $this->_soapProcedures as $proc => $args ) {
            
            if( count( $args ) ) {
                
                $this->_xml->startElement( 'wsdl:message' );
                $this->_xml->writeAttribute( 'name', $proc . 'Request' );
                
                foreach( $args as $arg ) {
                    
                    $this->_xml->startElement( 'wsdl:part' );
                    $this->_xml->writeAttribute( 'name', $arg );
                    $this->_xml->writeAttribute( 'type', 'xsd:string' );
                    $this->_xml->endElement();
                }
                
                $this->_xml->endElement();
            }
            
            $this->_xml->startElement( 'wsdl:message' );
            $this->_xml->writeAttribute( 'name', $proc . 'Response' );
            
            $this->_xml->startElement( 'wsdl:part' );
            $this->_xml->writeAttribute( 'name', 'result' );
            $this->_xml->writeAttribute( 'type', 'xsd:string' );
            $this->_xml->endElement();
            
            $this->_xml->endElement();
        }
    }
    
    /**
     * 
     */
    protected function _createWsdlPortType()
    {
        $this->_xml->startElement( 'wsdl:portType' );
        $this->_xml->writeAttribute( 'name', $this->_name . 'PortType' );
        
        foreach( $this->_soapProcedures as $func => $args ) {
            
            $this->_xml->startElement( 'wsdl:operation' );
            $this->_xml->writeAttribute( 'name', $func );
            
            if( count( $args ) ) {
                
                $this->_xml->startElement( 'wsdl:input' );
                $this->_xml->writeAttribute( 'message', 'tns:' . $func . 'Request' );
                $this->_xml->endElement();
            }
            
            $this->_xml->startElement( 'wsdl:output' );
            $this->_xml->writeAttribute( 'message', 'tns:' . $func . 'Response' );
            $this->_xml->endElement();
            
            $this->_xml->endElement();
        }
        
        $this->_xml->endElement();
    }
    
    /**
     * 
     */
    protected function _createWsdlBinding()
    {
        $this->_xml->startElement( 'wsdl:binding' );
        $this->_xml->writeAttribute( 'name', $this->_name . 'Binding' );
        $this->_xml->writeAttribute( 'type', $this->_name . 'PortType' );
        
        $this->_xml->startElement( 'soap:binding' );
        $this->_xml->writeAttribute( 'style', 'rpc' );
        $this->_xml->writeAttribute( 'transport', self::NAMESPACE_SOAP_HTTP );
        $this->_xml->endElement();
        
        foreach( $this->_soapProcedures as $func => $args ) {
            
            $this->_xml->startElement( 'wsdl:operation' );
            $this->_xml->writeAttribute( 'name', $func );
            
            $this->_xml->startElement( 'soap:operation' );
            $this->_xml->writeAttribute( 'soapAction', 'urn:xmethods-' . $this->_name . '#' . $func );
            $this->_xml->endElement();
            
            if( count( $args ) ) {
                
                $this->_xml->startElement( 'wsdl:input' );
                $this->_xml->startElement( 'soap:body' );
                $this->_xml->writeAttribute( 'use', 'encoded' );
                $this->_xml->writeAttribute( 'namespace', 'urn:xmethods-' . $this->_name );
                $this->_xml->writeAttribute( 'encodingStyle', self::NAMESPACE_SOAP_ENCODING );
                $this->_xml->endElement();
                $this->_xml->endElement();
            }
            
            $this->_xml->startElement( 'wsdl:output' );
            $this->_xml->startElement( 'soap:body' );
            $this->_xml->writeAttribute( 'use', 'encoded' );
            $this->_xml->writeAttribute( 'namespace', 'urn:xmethods-' . $this->_name );
            $this->_xml->writeAttribute( 'encodingStyle', self::NAMESPACE_SOAP_ENCODING );
            $this->_xml->endElement();
            $this->_xml->endElement();
            
            $this->_xml->endElement();
        }
        
        $this->_xml->endElement();
    }
    
    /**
     * 
     */
    protected function _createWsdlService()
    {
        $this->_xml->startElement( 'wsdl:service' );
        $this->_xml->writeAttribute( 'name', $this->_name . 'Service' );
        
        $this->_xml->startElement( 'wsdl:port' );
        $this->_xml->writeAttribute( 'name', $this->_name . 'Port' );
        $this->_xml->writeAttribute( 'binding', $this->_name . 'Binding' );
        
        $this->_xml->startElement( 'soap:address' );
        $this->_xml->writeAttribute( 'location', $this->_url );
        $this->_xml->endElement();
        
        $this->_xml->endElement();
        
        $this->_xml->endElement();
    }
}
