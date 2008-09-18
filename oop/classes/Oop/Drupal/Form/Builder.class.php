<?php

# $Id$

/**
 * Layer over the Drupal form functions
 * 
 * @author          Jean-David Gadina <macmade@eosgarden.com>
 * @copyright       Copyright &copy; 2008
 * @package         Drupal
 * @version         0.1
 */
class Oop_Drupal_Form_Builder
{
    /**
     * 
     */
    protected $_lang     = NULL;
    
    /**
     * 
     */
    protected $_form     = array();
    
    /**
     * 
     */
    protected $_modName  = '';
    
    /**
     * 
     */
    protected $_delta    = 0;
    
    /**
     * 
     */
    public function __construct( $confPath, $modName, Oop_Lang_Getter $lang, $delta = false )
    {
        // Checks if the file exists
        if( !file_exists( $confPath ) ) {
            
            // The file does not exists
            throw new Oop_Drupal_Form_Builder_Exception( 'The configuration file for the settings form does not exist (path: ' . $confPath . ')', Oop_Drupal_Form_Builder_Exception::EXCEPTION_NO_FILE );
        }
        
        // Includes the configuration file
        require( $confPath );
        
        // Checks if the configuration is defined
        if( !isset( $formConf ) ) {
            
            // The configuration is not defined
            throw new Oop_Drupal_Form_Builder_Exception( 'The form configuration array does not exist in file ' . $confPath, Oop_Drupal_Form_Builder_Exception::EXCEPTION_NO_CONF );
        }
        
        // Stores the module name
        $this->_lang     = $lang;
        
        // Stores the language object
        $this->_modName  = $modName;
        
        // Stores the delta
        $this->_delta    = $delta;
        
        // Creates the final configuration
        $this->_createFormConf( $formConf, $this->_form );
    }
    
    /**
     * 
     */
    protected function _createFormConf( array &$conf, array &$storage )
    {
        foreach( $conf as $field => &$fieldConf ) {
            
            if( substr( $field, 0, 1 ) === '#' ) {
                
                continue;
            }
            
            if( is_numeric( $this->_delta ) ) {
                
                $fieldName = $this->_modName . '_' . $field . '_' . $this->_delta;
                
            } else {
                
                $fieldName = $this->_modName . '_' . $field;
            }
            
            $storage[ $fieldName ] = array();
            
            foreach( $fieldConf as $confKey => $confValue ) {
                
                if( substr( $confKey, 0, 1 ) === '#' ) {
                    
                    $storage[ $fieldName ][ $confKey ] = $confValue;
                }
            }
            
            if( isset( $fieldConf[ '#type' ] ) && $fieldConf[ '#type' ] === 'fieldset' ) {
                
                $this->_createFormConf( $fieldConf, $storage[ $fieldName ] );
            }
            
            if( isset( $fieldConf[ '#type' ] ) && $fieldConf[ '#type' ] === 'submit' ) {
                
                $storage[ $fieldName ][ '#value' ] = $this->_lang->getLabel( $field . '_value', 'settings' );
                
            } else {
                
                $storage[ $fieldName ][ '#title' ]       = $this->_lang->getLabel( $field . '_title', 'settings' );
                $storage[ $fieldName ][ '#description' ] = $this->_lang->getLabel( $field . '_description', 'settings' );
            }
            
            $storage[ $fieldName ][ '#default_value' ] = variable_get( $fieldName, $fieldConf[ '#default_value' ] );
            
            if( $fieldConf[ '#type' ] === 'select' && isset( $fieldConf[ '#options' ] ) && !is_array( $fieldConf[ '#options' ] ) ) {
                
                $storage[ $fieldName ][ '#options' ] = array();
                
                $optionsValues = explode( ',', $fieldConf[ '#options' ] );
                
                foreach( $optionsValues as $optionValue ) {
                    
                    $storage[ $fieldName ][ '#options' ][ $optionValue ] = $this->_lang->getLabel( $field . '_option_' . $optionValue, 'settings' );
                }
            }
        }
    }
    
    /**
     * 
     */
    public function getConf()
    {
        return $this->_form;
    }
}
