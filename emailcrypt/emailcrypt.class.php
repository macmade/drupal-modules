<?php

/**
 * Email crypt filter for Drupal
 * 
 * @author          Jean-David Gadina <macmade@eosgarden.com>
 * @copyright       Copyright &copy; 2008
 * @version         0.1
 */
class emailcrypt extends Oop_Drupal_ModuleBase
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
     * The pattern to detect a valid email address
     */
    const EMAIL_PATTERN = '[A-Za-z0-9._-]+@[A-Za-z0-9._+-]+\.[A-Za-z]{2,4}';
    
    /**
     * Crypts an email address
     * 
     * @param   array   The matches from preg_replace_callback()
     * @return  string  The encrypted email address link
     */
    protected function _cryptEmail( $matches )
    {
        // Storage
        $email = array();
        
        // Gets the email address
        preg_match( '/' . self::EMAIL_PATTERN . '/', $matches[ 0 ], $email );
        
        // Returns the crypted email address
        return ( string )$this->_email( $email[ 0 ] );
    }
    
    /**
     * Prepare the email crypt filter
     * 
     * @param   int     Which of the module's filters to use
     * @param   int     Which input format the filter is being used
     * @param   string  The content to filter
     * @return  string  The prepared text
     */
    public function prepareFilter( $delta, $format, $text )
    {
        // Returns the text
        return $text;
    }
    
    /**
     * Process the email crypt filter
     * 
     * @param   int     Which of the module's filters to use
     * @param   int     Which input format the filter is being used
     * @param   string  The content to filter
     * @return  string  The processed text
     * @see     _cryptEmail
     */
    public function processFilter( $delta, $format, $text )
    {
        // Replaces the linked email addresses
        $text = preg_replace_callback(
            '/<a href="mailto:' . self::EMAIL_PATTERN . '">' . self::EMAIL_PATTERN . '<\/a>/',
            array(
                $this,
                '_cryptEmail'
            ),
            $text
        );
        
        // Returns the text
        return $text;
    }
}
