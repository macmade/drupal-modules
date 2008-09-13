<?php

/**
 * Drop-Down Menu module for Drupal
 * 
 * @author          Jean-David Gadina <macmade@eosgarden.com>
 * @copyright       Copyright &copy; 2008
 * @version         0.1
 */
class ddmenu extends Oop_Drupal_ModuleBase
{
    /**
     * Class version constants.
     * Holds the version, the developpment state
     * and the PHP lower compatible version.
     */
    const CLASS_VERSION  = '0.1';
    const DEVEL_STATE    = 'alpha';
    const PHP_COMPATIBLE = '5.2.0';
    
    protected $_iconDir  = NULL;
    protected $_iconPage = NULL;
    protected $_path     = '';
    protected $_delta    = 0;
    
    /**
     * Gets the 'view' section of the module
     * 
     * @param   Oop_Xhtml_Tag   The placeholder for the module content
     * @param   int             The delta offset, used to generate different contents for different blocks
     * @return  NULL
     */
    protected function _getView( Oop_Xhtml_Tag $content, $delta )
    {
        $css = variable_get( $this->_modName . '_css_file', false );
        
        if( $css ) {
            
            $this->_includeCss( $css );
            
        } else {
            
            $this->_includeModuleCSS();
        }
        
        $this->_delta = $delta;
        
        $linkType = variable_get( $this->_modName . '_linktype_' . $this->_delta, 'primary' );
        
        switch( $linkType ) {
            
            case 'primary':
                
                $section = 'primary-links';
                break;
                
            case 'secondary':
                
                $section = 'secondary-links';
                break;
            
            default:
                
                $section = $linkType;
                break;
        }
        
        $this->_path = self::$_request->q;
        
        $this->_iconDir  = $this->_getIcon( 'folder.png' );
        $this->_iconPage = $this->_getIcon( 'page_white.png' );
        $this->_includeModuleScript();
        $list = $content->ul;
        $this->_getPages( $list, $section, 0 );
    }
    
    /**
     * 
     */
    protected function _getPages( Oop_Xhtml_Tag $list, $type, $parent )
    {
        // Parameters for the PDO query
        $sqlParams = array(
            ':menu_name' => $type,
            ':plid'      => $parent
        );
        
        // WHERE clause to select the pages
        $where = '{menu_links}.menu_name    = :menu_name
                    AND {menu_links}.plid   = :plid
                    AND {menu_links}.hidden = 0
                  ORDER BY {menu_links}.weight, {menu_links}.link_title';
        
        // Gets the pages
        $pages = Oop_Drupal_Page_Getter::getPages( $where, $sqlParams );
        
        // Process each pages
        foreach( $pages as $id => $page ) {
            
            if( !$page->isAccessible() ) {
                
                continue;
            }
            
            $li   = $list->li;
            $icon = $li->span;
            
            $li->addTextData( ' ' );
            $title = $li->span;
            $title->addChildNode( $this->_link( $page->getTitle(), array(), false, $page->getPath() ) );
            
            if( $page->has_children ) {
                
                $link           = $icon->a;
                $link[ 'href' ] = 'javascript:ddmenu.display( \'ddmenu-' . $this->_delta . '-page-' . $page->mlid . '\' );';
                
                $link->addChildNode( $this->_iconDir );
                
                $subList            = $li->ul;
                
                $subList[ 'id' ]    = 'ddmenu-' . $this->_delta . '-page-' . $page->mlid;
                
                if( $page->getPath( false ) === $this->_path ) {
                    
                    $this->_cssClass( $title, 'active' );
                    
                    $parent = $li->getParent();
                    
                    unset( $parent[ 'style' ] );
                    
                    while( $parent = $parent->getParent()->getParent() ) {
                        
                        unset( $parent[ 'style' ] );
                    }
                    
                } else {
                    
                    $subList[ 'style' ] = 'display: none;';
                }
                
                $this->_getPages( $subList, $type, $page->mlid );
                
            } else {
                
                $icon->addChildNode( $this->_iconPage );
                
                if( $page->getPath( false ) === $this->_path ) {
                    
                    $this->_cssClass( $title, 'active' );
                    
                    $parent = $li->getParent();
                
                    unset( $parent[ 'style' ] );
                    
                    while( $parent = $parent->getParent()->getParent() ) {
                        
                        unset( $parent[ 'style' ] );
                    }
                }
            }
        }
    }
    
    /**
     * 
     */
    public function validateAdminForm( $form, &$formState )
    {
        $number = $form[ '#post' ][ 'ddmenu_number_of_blocks' ];
        
        if( !is_numeric( $number ) ) {
            
            form_set_error( 'ddmenu_number_of_blocks', $this->_lang->notNumeric );
        }
    }
}
