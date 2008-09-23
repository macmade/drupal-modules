<?php

# $Id$

/**
 * Interface for the Drupal block modules
 * 
 * @author          Jean-David Gadina <macmade@eosgarden.com>
 * @copyright       Copyright &copy; 2008
 * @package         Oop/Drupal/Block
 * @version         0.1
 */
interface Oop_Drupal_Block_Interface
{
    public function getBlock( Oop_Xhtml_Tag $content, $delta );
}
