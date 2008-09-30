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
        $div->strong = $this->_lang->trace;
        $div->span   = sprintf( $this->_lang->construct, get_class( $object ) );
        $div->span   = sprintf( $this->_lang->advice, __METHOD__ );
        print $div;
    }
    
    /**
     * 
     */
    protected function _traceBeforeCall()
    {
        $div = new Oop_Xhtml_Tag( 'div' );
        $this->_cssClass( $div, 'aopDebug' );
        $div->strong = $this->_lang->trace;
        $div->span   = $this->_lang->beforeCall;
        $div->span   = sprintf( $this->_lang->advice, __METHOD__ );
        print $div;
    }
    
    /**
     * 
     */
    protected function _traceBeforeReturn( $returnValue )
    {
        $div = new Oop_Xhtml_Tag( 'div' );
        $this->_cssClass( $div, 'aopDebug' );
        $div->strong = $this->_lang->trace;
        $div->span   = sprintf( $this->_lang->beforeReturn, substr( htmlspecialchars( $returnValue[ 'content' ] ), 0, 100 ) ) . ' [...]';
        $div->span   = sprintf( $this->_lang->advice, __METHOD__ );
        print $div;
        return $returnValue;
    }
    
    /**
     * 
     */
    protected function _traceAfterCall()
    {
        $div = new Oop_Xhtml_Tag( 'div' );
        $this->_cssClass( $div, 'aopDebug' );
        $div->strong = $this->_lang->trace;
        $div->span   = $this->_lang->afterCall;
        $div->span   = sprintf( $this->_lang->advice, __METHOD__ );
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
        $content->spacer( 10 );
        $description = $content->div;
        $description->addTextData( $this->_lang->description );
        $this->_cssClass( $description, 'description' );
        
        Oop_Aop_Advisor::addAdvice(
            Oop_Aop_Advisor::ADVICE_TYPE_CONSTRUCT,
            array( $this, '_traceConstruct' ),
            'helloworld'
        );
        
        Oop_Aop_Advisor::addAdvice(
            Oop_Aop_Advisor::ADVICE_TYPE_BEFORE_CALL,
            array( $this, '_traceBeforeCall' ),
            self::$_classManager->getModule( 'helloworld' ),
            'block'
        );
        
        Oop_Aop_Advisor::addAdvice(
            Oop_Aop_Advisor::ADVICE_TYPE_BEFORE_RETURN,
            array( $this, '_traceBeforeReturn' ),
            self::$_classManager->getModule( 'helloworld' ),
            'block'
        );
        
        Oop_Aop_Advisor::addAdvice(
            Oop_Aop_Advisor::ADVICE_TYPE_AFTER_CALL,
            array( $this, '_traceAfterCall' ),
            self::$_classManager->getModule( 'helloworld' ),
            'block'
        );
    }
}
