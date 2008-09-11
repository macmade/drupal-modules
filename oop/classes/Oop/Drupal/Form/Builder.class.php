<?php

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
     * Class version constants.
     * Holds the version, the developpment state
     * and the PHP lower compatible version.
     */
    const CLASS_VERSION  = '0.1';
    const DEVEL_STATE    = 'alpha';
    const PHP_COMPATIBLE = '5.2.0';
    
    protected $_lang     = NULL;
    protected $_formConf = array();
    protected $_form     = array();
    protected $_modName  = '';
    
    public function __construct( $confPath, $modName, Oop_Lang_Getter $lang )
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
        
        // Stores the configuration
        $this->_formConf = $formConf;
        
        // Stores the module name
        $this->_lang     = $lang;
        
        // Stores the language object
        $this->_modName  = $modName;
        
        // Creates the final configuration
        $this->_createFormConf();
    }
    
    protected function _createFormConf()
    {
        foreach( $this->_formConf as $key => &$value ) {
            
            $fieldName = $this->_modName . '_' . $key;
            
            $this->_form[ $fieldName ] = $value;
            
            $this->_form[ $fieldName ][ '#title' ]         = $this->_lang->getLabel( $key . '_title', 'settings' );
            $this->_form[ $fieldName ][ '#description' ]   = $this->_lang->getLabel( $key . '_description', 'settings' );
            
            if( isset( $value[ '#default_value' ] ) ) {
                
                $this->_form[ $fieldName ][ '#default_value' ] = variable_get( $fieldName, $value[ '#default_value' ] );
            }
            
            if( $value[ '#type' ] === 'select' && isset( $value[ '#options' ] ) && !is_array( $value[ '#options' ] ) ) {
                
                $this->_form[ $fieldName ][ '#options' ] = array();
                
                $optionsValues = explode( ',', $value[ '#options' ] );
                
                foreach( $optionsValues as $optionValue ) {
                    
                    $this->_form[ $fieldName ][ '#options' ][ $optionValue ] = $this->_lang->getLabel( $key . '_option_' . $optionValue, 'settings' );
                }
            }
        }
    }
    
    public function getConf()
    {
        return $this->_form;
    }
}
