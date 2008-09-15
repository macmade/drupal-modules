<?php

/**
 * Terminal module for Drupal
 * 
 * @author          Jean-David Gadina <macmade@eosgarden.com>
 * @copyright       Copyright &copy; 2008
 * @version         0.1
 */
class shell extends Oop_Drupal_ModuleBase
{
    /**
     * Class version constants.
     * Holds the version, the developpment state
     * and the PHP lower compatible version.
     */
    const CLASS_VERSION  = '0.1';
    const DEVEL_STATE    = 'alpha';
    const PHP_COMPATIBLE = '5.2.0';
    
    /**
     * 
     */
    protected $_history         = true;
    
    /**
     * 
     */
    protected $_fontSize        = 0;
    
    /**
     * 
     */
    protected $_execCommand     = '';
    
    /**
     * 
     */
    protected $_backgroundColor = '';
    
    /**
     * 
     */
    protected $_foregroundColor = '';
    
    /**
     * 
     */
    protected $_promptColor     = '';
    
    /**
     * 
     */
    protected $_cwd             = '';
    
    /**
     * 
     */
    protected $_prompt          = '';
    
    /**
     * 
     */
    public function show( Oop_Xhtml_Tag $content )
    {
        $this->_includeModuleCss();
        
        $this->_history         = variable_get( $this->_modName . '_history',      true );
        $this->_fontSize        = variable_get( $this->_modName . '_font_size',    12 );
        $this->_execCommand     = variable_get( $this->_modName . '_exec_command', 'proc_open' );
        $this->_backgroundColor = variable_get( $this->_modName . '_background',   '#000000' );
        $this->_foregroundColor = variable_get( $this->_modName . '_foreground',   '#FFFFFF' );
        $this->_promptColor     = variable_get( $this->_modName . '_prompt',       '#00FF00' );
        
        $this->_cwd             = getcwd();
        $this->_prompt          = $_SERVER[ 'HTTP_HOST' ]
                                . ': '
                                . $GLOBALS[ 'user' ]->name
                                . '$';
        
        $cwd                    = $content->div;
        $cwd->addTextData( $this->_lang->cwd );
        
        $cwdPath                = $cwd->span;
        $cwdPath->addTextData( $this->_cwd );
        
        $shell                  = $content->div;
        $result                 = $shell->div;
        $prompt                 = $shell->div->form;
        $prompt[ 'action' ]     = '';
        $prompt[ 'method' ]     = 'post';
        $promptLabel            = $prompt->label;
        $promptLabel[ 'for' ]   = 'module-' . $this->_modName . '-command';
        $promptLabel->addTextData( $this->_prompt );
        $promptInput            = $prompt->input;
        $promptInput[ 'name' ]  = $this->_modName . '_command';
        $promptInput[ 'type' ]  = 'text';
        $promptInput[ 'size' ]  = 50;
        $script                 = $content->script;
        $script[ 'type' ]       = 'text/javascript';
        $script[ 'charset' ]    = 'utf-8';
        $script[ 'src' ]        = self::$_classManager->getModuleWebPath( 'shell' )
                                . $this->_modName
                                . '.js';
        
        $this->_id( $cwd, 'cwd' );
        $this->_id( $cwdPath, 'cwdPath' );
        $this->_id( $result, 'result' );
        $this->_id( $prompt, 'form' );
        $this->_id( $promptInput, 'command' );
        
        $this->_cssClass( $shell, 'shell' );
        $this->_cssClass( $promptLabel, 'prompt' );
        
        $shellStyle  = 'background-color: '
                     . $this->_backgroundColor
                     . '; color: '
                     . $this->_foregroundColor
                     . '; font-size: '
                     . $this->_fontSize
                     . ';';
                    
        $promptStyle = 'background-color: '
                     . $this->_backgroundColor
                     . '; color: '
                     . $this->_promptColor
                     . '; font-size: '
                     . $this->_fontSize
                    . ';';
        
        $shell[ 'style' ]       = $shellStyle;
        $promptInput[ 'style' ] = $shellStyle;
        $prompt[ 'style' ]      = $promptStyle;
    }
    
    /**
     * 
     */
    public function addMenuItems( array $items )
    {
        $items[ 'admin/shell' ] = array(
            'title'            => $this->_lang->menuTitle,
            'page callback'    => 'shell_show',
            'access arguments' => array('access administration pages'),
        );
        
        return $items;
    }
    
    /**
     * 
     */
    public function validateAdminForm( $form, &$formState )
    {
        $fontSize = $form[ '#post' ][ 'shell_font_size' ];
        
        if( !is_numeric( $fontSize ) ) {
            
            form_set_error( 'shell_font_size', $this->_lang->notNumeric );
        }
    }
}
