<?php

# $Id$

/**
 * Terminal module for Drupal
 * 
 * This module allows you to run shell commands on the server. This may
 * be useful for changing files permissions, creating symbolic links,
 * uncompressing archives, etc.
 * 
 * @author          Jean-David Gadina <macmade@eosgarden.com>
 * @copyright       Copyright &copy; 2008
 * @version         0.1
 */
class shell extends Oop_Drupal_ModuleBase implements Oop_Drupal_MenuItem_Interface
{
    /**
     * The permissions array
     */
    protected $_perms = array(
        'access shell admin',
        'access shell admin/shell'
    );
    
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
    public function __construct( $modPath )
    {
        parent::__construct( $modPath );
        
        if( isset( $this->_reqVars[ 'ajaxCall' ] ) ) {
            
            $this->_processCommand();
            exit();
        }
    }
    
    /**
     * 
     */
    protected function _processCommand()
    {
        // Gets the execution function
        $execCommand = ( isset( $this->_modVars[ 'exec_command' ] ) ) ? $this->_modVars[ 'exec_command' ] : 'proc_open';
        
        // Sets the current working directory
        $this->_cwd  = ( isset( $this->_reqVars[ 'cwd' ] ) ) ? $this->_reqVars[ 'cwd' ] : self::$_classManager->getDrupalPath();
        
        // Sets the new line character
        self::$_NL   = chr( 13 ) . chr( 10 );
        
        // Gets the command
        $cmd         = $this->_reqVars[ 'command' ];
        
        // Gets multiple commands
        $commands    = explode( ' && ', $cmd );
        
        $allowed     = ( isset( $this->_modVars[ 'allow_commands' ] ) ) ? $this->_modVars[ 'allow_commands' ] : '';
        $disallowed  = ( isset( $this->_modVars[ 'disallow_commands' ] ) ) ? $this->_modVars[ 'disallow_commands' ] : '';
        
        $disallow    = array_flip( explode( ',', preg_replace( '/,\s+/', ',', $disallowed ) ) );
        $allow       = ( $allowed ) ? array_flip( explode( ',', preg_replace( '/,\s+/', ',', $allowed ) ) ) : array();
        
        // Error state
        $error       = false;
        
        // Process each command
        foreach( $commands as $command ) {
            
            // Gets the command without the arguments
            $cmdOnly = ( strstr( $command, ' ' ) ) ? substr( $command, 0, strpos( $command, ' ' ) ) : $command;
            
            if( isset( $disallow[ $cmdOnly ] )
                || ( count( $allow ) && !isset( $allow[ $cmdOnly ] ) )
            ) {
                
                print sprintf( $this->_lang->commandNotAllowed, $cmdOnly );
                $error = true;
                break;
            }
        }
        
        if( !$error ) {
            
            // Checks the execution function
            switch( $execCommand ) {
                
                case 'exec':
                    $this->_exec( $commands );
                    break;
                
                case 'proc_open':
                    
                    $this->_procOpen( $commands );
                    break;
                
                case 'system':
                    $this->_system( $commands );
                    break;
                
                case 'passthru':
                    $this->_passthru( $commands );
                    break;
                
                case 'popen':
                    $this->_pOpen( $commands );
                    break;
                
                case 'shell_exec':
                    $this->_shellExec( $commands );
                    break;
            }
        }
        
        // Prints the current working directory and exit
        print self::$_NL . $this->_cwd;
    }
    
    /**
     * 
     */
    protected function _handleCwd( $command )
    {
        // Command is 'cd'
        if( preg_match( '/^\s*cd\s*$/', $command ) ) {
            
            // Home is Drupal site
            $this->_cwd = self::$_classManager->getDrupalPath();
        }
        
        // Change directory command
        if( preg_match( '/\s*cd ([^\s]+)\s*/', $command, $matches ) ) {
            
            // DIrectory to change
            $dir = $matches[ 1 ];
            
            // Checks for an absolute path
            if( substr( $dir, 0, 1 ) == '/' ) {
                
                // Sets the current directory
                $this->_cwd = $matches[ 1 ];
                
            } else {
                
                // Sets the current directory
                $this->_cwd = $this->_cwd . $matches[ 1 ];
            }
        }
        
        // Adds a trailing slash if necessary
        if( substr( $this->_cwd, strlen( $this->_cwd ) - 1, 1 ) != '/' ) {
            
            $this->_cwd .= '/';
        }
        
        // Normalize the path
        $this->_cwd = preg_replace( '/\/\/+/', '/', $this->_cwd );
        $this->_cwd = str_replace( '/./', '/', $this->_cwd );
        
        // Get path parts
        $cwdParts = explode( '/', $this->_cwd );
        $cwd      = array();
        
        // Process each part of the path
        foreach( $cwdParts as $key => $value  ) {
            
            // Previous directory
            if( $value == '..' ) {
                
                // Removes last directory
                array_pop( $cwd );
                
            } else {
                
                // Stores current directory
                $cwd[] = $value;
            }
        }
        
        // Rebuilds the path
        $this->_cwd = implode( '/', $cwd );
        
        // Stores the CWD in the session
        $this->_storeSessionVar( 'cwd', $this->_cwd );
    }
    
    /**
     * 
     */
    protected function _exec( array $commands )
    {
        // Storage
        $return = array();
        
        // Process each command
        foreach( $commands as $command ) {
            
            // Support for cd commands
            $this->_handleCwd( $command );
            
            // Change current working directory
            if( !@file_exists( $this->_cwd ) || !@is_readable( $this->_cwd ) ) {
                
                // Directory cannot be changed. Reset to home (Drupal site root)
                $this->_cwd = self::$_classManager->getDrupalPath();
                print sprintf( $this->_lang->noChdir, $this->_cwd );
                print self::$_NL . $this->_cwd;
                
                // Stores the working directory in session data
                $this->_storeSessionVar( 'cwd', $this->_cwd );
                
                // Aborts the script
                exit();
            }
            
            // Changes the working directory
            chdir( $this->_cwd );
            
            // Tries to execute command
            if( substr( $command, 0, 3 ) == 'cd ' || $command == 'cd' ) {
                
                // Change current working directory
                if( @chdir( $this->_cwd ) ) {
                    
                    continue;
                }
                
                // Directory cannot be changed
                print self::$_NL . $this->_cwd;
                exit();
                
            } elseif( @exec( $command, $return ) ) {
                
                // Display the command result
                print implode( self::$_NL, $return );
                
            } else {
                
                // Command cannot be executed
                print self::$_NL . $this->_cwd;
                exit();
            }
        }
    }
    
    /**
     * 
     */
    protected function _procOpen( array $commands )
    {
        // Storage
        $return = '';
        $error  = '';
        
        // Process pipes
        $descriptorSpec = array(
            0 => array( 'pipe', 'r' ),
            1 => array( 'pipe', 'w' ),
            2 => array( 'pipe', 'w' )
        );
        
        // Process each command
        foreach( $commands as $command ) {
            
            // Support for cd commands
            $this->_handleCwd( $command );
            
            // Do not process cd commands
            if( substr( $command, 0, 3 ) != 'cd ' && $command != 'cd' ) {
                
                // Open process
                $process = proc_open(
                    $command,
                    $descriptorSpec,
                    $pipes,
                    $this->_cwd,
                    $_ENV
                );
                
                // Checks the process
                if( is_resource( $process ) ) {
                    
                    // Process pipes
                    $stdin  = $pipes[0];
                    $stdout = $pipes[1];
                    $stderr = $pipes[2];
                    
                    // Process and stores the result
                    while( !feof( $stdout ) ) {
                        
                        $return .= fgets( $stdout );
                    }
    
                    // Process and stores errors
                    while( !feof( $stderr ) ) {
                        
                        $error .= fgets( $stderr );
                    }
                    
                    // Close process pipes
                    fclose( $stdin );
                    fclose( $stdout );
                    fclose( $stderr );
                    
                    // Close the process
                    proc_close( $process );
                    
                    // Checks for errors
                    if( empty( $error ) ) {
                        
                        // Display results
                        print preg_replace( '/(\r\n|\r|\n)/', self::$_NL, $return );
                        
                    } else {
                        
                        // Display errors, current working directory and exit
                        print $error;
                        print self::$_NL . $this->_cwd;
                    }
                }
            }
        }
    }
    
    /**
     * 
     */
    protected function _system( array $commands )
    {
        // Storage
        $return = '';
        
        // Process each command
        foreach( $commands as $command ) {
            
            // Support for cd commands
            $this->_handleCwd( $command );
            
            // Change current working directory
            if( !@file_exists( $this->_cwd ) || !@is_readable( $this->_cwd ) ) {
                
                // Directory cannot be changed. Reset to home (Drupal site root)
                $this->_cwd = self::$_classManager->getDrupalPath();
                print sprintf( $this->_lang->noChdir, $this->_cwd );
                print self::$_NL . $this->_cwd;
                
                // Stores the working directory in session data
                $this->_storeSessionVar( 'cwd', $this->_cwd );
                
                // Aborts the script
                exit();
            }
            
            // Changes the working directory
            chdir( $this->_cwd );
            
            // Tries to execute command
            if( substr( $command, 0, 3 ) == 'cd ' || $command == 'cd' ) {
                
                // Change current working directory
                if( @chdir( $this->_cwd ) ) {
                    
                    continue;
                }
                
                // Directory cannot be changed
                print self::$_NL . $this->_cwd;
                exit();
                
            } elseif( @system( $command, $return ) ) {
                
                continue;
                
            } else {
                
                // Command cannot be executed
                print self::$_NL . $this->_cwd;
                exit();
            }
        }
    }
    
    /**
     * 
     */
    protected function _passthru( array $commands )
    {
        $this->_system( $commands );
    }
    
    /**
     * 
     */
    protected function _pOpen( array $commands )
    {
        // Storage
        $return = '';
        
        // Process each command
        foreach( $commands as $command ) {
            
            // Support for cd commands
            $this->_handleCwd( $command );
            
            // Change current working directory
            if( !@file_exists( $this->_cwd ) || !@is_readable( $this->_cwd ) ) {
                
                // Directory cannot be changed. Reset to home (Drupal site root)
                $this->_cwd = self::$_classManager->getDrupalPath();
                print sprintf( $this->_lang->noChdir, $this->_cwd );
                print self::$_NL . $this->_cwd;
                
                // Stores the working directory in session data
                $this->_storeSessionVar( 'cwd', $this->_cwd );
                
                // Aborts the script
                exit();
            }
            
            // Changes the working directory
            chdir( $this->_cwd );
            
            // Tries to execute command
            if( substr( $command, 0, 3 ) == 'cd ' || $command == 'cd' ) {
                
                // Change current working directory
                if( @chdir( $this->_cwd ) ) {
                    
                    continue;
                }
                
                // Directory cannot be changed
                print self::$_NL . $this->_cwd;
                exit();
                
            } else {
                
                // Open process
                $process = popen(
                    $command,
                    'r'
                );
                
                // Checks the process
                if( is_resource( $process ) ) {
                    
                    // Process and stores the result
                    while( !feof( $process ) ) {
                        
                        $return .= fgets( $process );
                    }
                    
                    // Close the process
                    pclose( $process );
                    
                    // Display results
                    print preg_replace( '/(\r\n|\r|\n)/', self::$_NL, $return );
                    
                } else {
                    
                    // Command cannot be executed
                    print self::$_NL . $this->_cwd;
                    exit();
                }
            }
        }
    }
    
    /**
     * 
     */
    protected function _shellExec( array $commands )
    {
        $this->_system( $commands );
    }
    
    /**
     * 
     */
    public function show( Oop_Xhtml_Tag $content )
    {
        $this->_includeModuleCss();
        $this->_includeModuleScript();
        
        $this->_history         = ( isset( $this->_modVars[ 'history' ] ) )      ? $this->_modVars[ 'history' ]       : true;
        $this->_fontSize        = ( isset( $this->_modVars[ 'font_size' ] ) )    ? $this->_modVars[ 'font_size' ]    : 10;
        $this->_execCommand     = ( isset( $this->_modVars[ 'exec_command' ] ) ) ? $this->_modVars[ 'exec_command' ] : 'proc_open';
        $this->_backgroundColor = ( isset( $this->_modVars[ 'background' ] ) )   ? $this->_modVars[ 'background' ]   : '#000000';
        $this->_foregroundColor = ( isset( $this->_modVars[ 'foreground' ] ) )   ? $this->_modVars[ 'foreground' ]   : '#FFFFFF';
        $this->_promptColor     = ( isset( $this->_modVars[ 'prompt' ] ) )       ? $this->_modVars[ 'prompt' ]       : '#00FF00';
        
        // Sets the current working directory
        $this->_cwd = ( isset( $this->_reqVars[ 'cwd' ] ) ) ? $this->_reqVars[ 'cwd' ] : self::$_classManager->getDrupalPath();
                
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
        $script                = $content->script;
        $script[ 'type' ]      = 'text/javascript';
        $script[ 'charset' ]   = 'utf-8';
        
        $script->addTextData(
            'oopManager.getInstance().getModule( \'shell\' ).init();'
          . 'oopManager.getInstance().getModule( \'shell\' ).setPrompt( \'' . $this->_prompt . '\' );'
          . 'oopManager.getInstance().getModule( \'shell\' ).setAjaxUrl( \'/' . self::$_request->q . '\' );'
        );
        
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
                     . 'px;';
                    
        $promptStyle = 'background-color: '
                     . $this->_backgroundColor
                     . '; color: '
                     . $this->_promptColor
                     . '; font-size: '
                     . $this->_fontSize
                    . 'px;';
        
        $shell[ 'style' ]       = $shellStyle;
        $promptInput[ 'style' ] = $shellStyle;
        $prompt[ 'style' ]      = $promptStyle;
    }
    
    /**
     * Adds items to the Drupal menu
     * 
     * @param   array   An array in which to place the menu items, passed by reference. It may contains existing menu items, for instance if an administration settings form exists
     * @return  NULL
     */
     public function addMenuItems( array &$items )
    {
        $items[ 'admin/shell' ] = array(
            'title'            => $this->_lang->menuTitle,
            'page callback'    => 'shell_show',
            'access arguments' => array( 'access shell admin/shell' ),
        );
    }
    
    /**
     * 
     */
    public function validateAdminForm( $form, &$formState )
    {
        $fontSize = $formState[ 'values' ][ 'shell_font_size' ];
        
        if( !is_numeric( $fontSize ) ) {
            
            form_set_error( 'shell_font_size', $this->_lang->notNumeric );
        }
    }
}
