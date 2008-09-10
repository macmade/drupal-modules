<?php

/**
 * Hello World module for Drupal
 * 
 * @author          Jean-David Gadina <macmade@eosgarden.com>
 * @copyright       Copyright &copy; 2008
 * @version         0.1
 */
class helloworld extends Drupal_ModuleBase
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
    protected function _getView( Html_Tag $content, $delta )
    {
        $firstBlock = $content->div->strong;
        $firstBlock->addTextData( self::$_lang->hello );
        
        $content->spacer( 20 );
        $content->hr;
        $content->spacer( 20 );
        
        $content->div = sprintf( self::$_lang->method, __METHOD__ );
        
        $content->spacer( 20 );
        $content->hr;
        $content->spacer( 20 );
        
        $content->div->strong = self::$_lang->modules;
        
        $modulesBlock = $content->div;
    }
}
