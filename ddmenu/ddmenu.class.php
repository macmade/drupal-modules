<?php

# $Id$

/**
 * Drop-Down Menu module for Drupal
 * 
 * Displays a drop-down menu for the primary, secondary or navigation links
 * 
 * @author          Jean-David Gadina <macmade@eosgarden.com>
 * @copyright       Copyright &copy; 2008
 * @version         0.1
 */
class ddmenu extends Oop_Drupal_ModuleBase
{
    /**
     * 
     */
    protected $_iconDir  = NULL;
    
    /**
     * 
     */
    protected $_iconPage = NULL;
    
    /**
     * 
     */
    protected $_path     = '';
    
    /**
     * 
     */
    protected $_delta    = 0;
    
    /**
     * Permissions array
     */
    protected $_perms    = array(
        'access ddmenu block',
        'access ddmenu block config',
        'access ddmenu admin'
    );
    
    /**
     * 
     */
    protected function _getPages( array $pages, Oop_Xhtml_Tag $list, $type )
    {
        // Process each pages
        foreach( $pages as $id => $page ) {
            
            $li   = $list->li;
            $icon = $li->span;
            
            $li->addTextData( ' ' );
            $title = $li->span;
            $title->addChildNode( $this->_link( $page->getTitle(), array(), false, $page->getPath() ) );
            
            $subPages = array();
            
            if( $page->has_children ) {
                
                // Parameters for the PDO query
                $sqlParams = array(
                    ':menu_name' => $type,
                    ':plid'      => $page->mlid
                );
                
                // WHERE clause to select the pages
                $where = '{menu_links}.menu_name    = :menu_name
                            AND {menu_links}.plid   = :plid
                            AND {menu_links}.hidden = 0
                          ORDER BY {menu_links}.weight, {menu_links}.link_title';
                
                // Gets the pages
                $subPages = Oop_Drupal_Page_Getter::getPages( $where, $sqlParams, true );
            }
            
            if( is_array( $subPages ) && count( $subPages ) ) {
                
                $link            = $icon->a;
                $link[ 'href' ]  = 'javascript:oopManager.getInstance().getModule( \'ddmenu\' ).display( \'ddmenu-' . $this->_delta . '-page-' . $page->mlid . '\' );';
                $link[ 'title' ] = $this->_lang->openClose;
                
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
                
                $this->_getPages( $subPages, $subList, $type );
                
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
     * Gets the block view
     * 
     * @param   Oop_Xhtml_Tag   The placeholder for the module content
     * @param   int             The delta offset, used to generate different contents for different blocks
     * @return  NULL
     */
    public function getBlock( Oop_Xhtml_Tag $content, $delta )
    {
        if( isset( $this->_modVars[ 'css_file' ] ) && $this->_modVars[ 'css_file' ] ) {
            
            $this->_includeCss( $this->_modVars[ 'css_file' ] );
            
        } else {
            
            $this->_includeModuleCSS();
        }
        
        $this->_delta = $delta;
        
        $linkType = ( isset( $this->_modVars[ 'linktype_' . $delta ] ) ) ? $this->_modVars[ 'linktype_' . $delta ] : 'primary';
        
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
        
        // Parameters for the PDO query
        $sqlParams = array(
            ':menu_name' => $section,
            ':plid'      => 0
        );
        
        // WHERE clause to select the pages
        $where = '{menu_links}.menu_name    = :menu_name
                    AND {menu_links}.plid   = :plid
                    AND {menu_links}.hidden = 0
                  ORDER BY {menu_links}.weight, {menu_links}.link_title';
        
        // Gets the pages
        $pages = Oop_Drupal_Page_Getter::getPages( $where, $sqlParams );
        
        if( is_array( $pages ) && count( $pages ) ) {
            
            $this->_getPages( $pages, $list, $section );
        }
    }
    
    /**
     * 
     */
    public function validateAdminForm( $form, &$formState )
    {
        $number = $formState[ 'values' ][ 'ddmenu_number_of_blocks' ];
        
        if( !is_numeric( $number ) ) {
            
            form_set_error( 'ddmenu_number_of_blocks', $this->_lang->notNumeric );
        }
    }
}
