<?php

# $Id$

/**
 * AOP test module for Drupal
 * 
 * A test module to show the AOP functionnalities of the OOP framework
 * 
 * @author          Jean-David Gadina <macmade@eosgarden.com>
 * @copyright       Copyright &copy; 2008
 * @version         0.1
 */
class aoptest extends Oop_Drupal_ModuleBase implements Oop_Drupal_Block_Interface
{
    /**
     * An array with the Drupal permission for the module
     */
    protected $_perms = array(
        'access aoptest block',
    );
    
    /**
     * 
     */
    protected function _traceConstruct( $object )
    {
        $div = new Oop_Xhtml_Tag( 'div' );
        $this->_cssClass( $div, 'aopDebug' );
        $div->pre = sprintf( $this->_lang->construct, get_class( $object ) );
        print $div;
    }
    
    /**
     * 
     */
    protected function _traceAfterBlock()
    {
        $div = new Oop_Xhtml_Tag( 'div' );
        $this->_cssClass( $div, 'aopDebug' );
        $div->pre = $this->_lang->afterBlock;
        print $div;
    }
    
    /**
     * Gets the block view
     *
     * @param   Oop_Xhtml_Tag   The placeholder for the module content
     * @param   int             The delta offset, used to generate different contents for different blocks
     * @return  NULL
     */
    public function getBlock( Oop_Xhtml_Tag $content, $delta )
    {
        $this->_includeModuleCss();
        $content->div = $this->_lang->info;
        
        Oop_Aop_Advisor::addAdvice(
            Oop_Aop_Advisor::ADVICE_TYPE_CONSTRUCT,
            array( $this, '_traceConstruct' ),
            'helloworld'
        );
        
        Oop_Aop_Advisor::addAdvice(
            Oop_Aop_Advisor::ADVICE_TYPE_AFTER_CALL,
            array( $this, '_traceAfterBlock' ),
            self::$_classManager->getModule( 'helloworld' ),
            'block'
        );
    }
}
