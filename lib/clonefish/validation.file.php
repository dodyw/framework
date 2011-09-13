<?php

/**
 * Clonefish form generator class 
 * (c) phpformclass.com, Dots Amazing
 * All rights reserved.
 * 
 * @copyright  2010 Dots Amazing
 * @link       http://phpformclass.com
 * @package    clonefish
 * @subpackage validation
 */

/* 
 * Validation
 * @package clonefish
 * @subpackage validationTypes
 */
class fileValidation extends validation {
 
  var $settings = Array();

  // settings coming from the settings array

  var $minimum;              // minimum size in bytes
  var $maximum;              // maximum size in bytes
  var $required   = 1;       // uploading a file is required
  var $types      = Array(); // filetypes allowed
  var $extensions = Array(); // extensions allowed
  
  var $imagecreatecheck = 0; // check gif,jpeg,png,wbmp using imagecreate*()
                             // to get a proper result if it can be
                             // handled by gd2 later

  var $_channels = Array(); // allowed channels for a JPEG file
  var $_types    = Array(); // filetypes - PHP constants rendered by the class
  var $_jpgchannels = Array();
  
    // only RGB channels: 'jpgrgb' setting,
    // only CMYK channels: 'jpgcmyk' setting,
    // both channels are set with 'jpg'

  // -------------------------------------------------------------------------
  function fileValidation( &$settings, &$element ) {

    // call parent constructor
    $parent_class_name = get_parent_class( $this );
    $this->$parent_class_name( $settings, $element );

    $inisize = ini_get('upload_max_filesize');
    if ( preg_match( '/^([0-9]+)M$/i', $inisize, $results ) ) 
      $inisize = $results[ 1 ]  * 1024 * 1024;

    if ( 
         !is_numeric( $this->maximum ) || 
         ( $this->maximum > $inisize ) 
       ) 
      $maximum = $inisize;

    foreach ( $this->types as $type ) {

      switch ( $type ) {
        case 'gif':  $this->_types[] = IMAGETYPE_GIF; break;
        case 'jpg':
          $this->_types[]     = IMAGETYPE_JPEG;
          $this->_jpgchannels = Array( 3, 4 ); 
          // by default, both CMYK+RGB is allowed
          break;
        case 'jpgrgb':
          $this->_types[]       = IMAGETYPE_JPEG; 
          $this->_jpgchannels[] = 3;
          // adding RGB channel
          break;
        case 'jpgcmyk':  
          $this->_types[]       = IMAGETYPE_JPEG; 
          $this->_jpgchannels[] = 4;
          // adding CMYK channel 
          break;
        case 'png':  $this->_types[] = IMAGETYPE_PNG; break;
        case 'bmp':  
          $this->_types[] = IMAGETYPE_BMP; 
          $this->_types[] = IMAGETYPE_WBMP; 
          break;
        case 'swf':  
          $this->_types[] = IMAGETYPE_SWF; 
          $this->_types[] = IMAGETYPE_SWC;  
          break;
        default: 
          die( sprintf( CF_ERR_FILE_VALIDATION_UNSUPPORTED, $this->element->getName(), $type ) );
          break;
      }

    }

  }
  
  // -------------------------------------------------------------------------
  function validateExtension( $filename ) {
    
    if ( empty( $this->extensions ) )
      return true; // no extensions to validate, so we pass
    
    if ( ( $pos = strrpos( $filename, '.' ) ) !== false ) {
      
      $extension = strtolower( substr( $filename, $pos + 1 ) );
      foreach( $this->extensions as $v ) {
        
        $v = strtolower( $v );
        if ( $extension == $v )
          return true;
        
      }
      
    }
    
    return false;
    
  }

  // -------------------------------------------------------------------------
  function isValid() {

    $results = Array();

    if ( $this->checkDependencyPHP() ) {

    $name = $this->element->getName();

    if ( isset( $_FILES[ $name ] ) ) {

      if ( 
           !isset( $_FILES[ $name ] ) ||
           ( $_FILES[ $name ]['tmp_name'] == 'none' ) || 
           ( $_FILES[ $name ]['size'] == '0' ) 
         ) 
        $file['error'] = UPLOAD_ERR_NO_FILE;
      else
        $file = $_FILES[ $name ];

      switch ( $file['error'] ) {

        case UPLOAD_ERR_INI_SIZE:
          break;

        case UPLOAD_ERR_PARTIAL:
          break;

        case UPLOAD_ERR_NO_FILE:

          if ( $this->required && !$this->element->getValue( 0 ) ) {

            $message = sprintf( 
              $this->selecthelp( $this->element, CF_STR_FILE_REQUIRED ), 
              $this->element->getDisplayName()
            );
            $results[] = $message;
            $this->element->addMessage( $message );

          }
            
          break;

        case UPLOAD_ERR_OK:
        
          // EXTENSIONS
          
          if ( !$this->validateExtension( $file['name'] ) ) {
            
            $message = 
              sprintf(
                $this->selecthelp( $this->element, CF_STR_FILE_EXTENSIONS_ALLOWED ),
                $this->element->getDisplayName(),
                implode( CF_STR_FILE_OR, $this->extensions )
              );
            $results[] = $message;
            $this->element->addMessage( $message );
            
          }

          // TYPES

          if ( count( $this->_types ) ) {
            $dimension = getimagesize( $file['tmp_name'] );

              $function = '';

              if ( $this->imagecreatecheck ) {

                switch ( $dimension[ 2 ] ) {
                  case IMAGETYPE_GIF:  $function = 'imagecreatefromgif';  break;
                  case IMAGETYPE_JPEG: $function = 'imagecreatefromjpeg'; break;
                  case IMAGETYPE_PNG:  $function = 'imagecreatefrompng';  break;
                  case IMAGETYPE_WBMP: $function = 'imagecreatefromwbmp'; break;
                }

              }

              if (
                 !is_array( $dimension ) ||
                 !in_array( $dimension[ 2 ], $this->_types ) ||
                 (
                   in_array( $dimension[ 2 ], $this->_types ) &&
                   ( $dimension[ 2 ] == IMAGETYPE_JPEG ) &&
                   !in_array( $dimension['channels'], $this->_jpgchannels )
                 ) ||
                 (
                   $this->imagecreatecheck &&
                   strlen( $function ) &&
                   !@$function( $file['tmp_name'] ) 
                 )
               ) {
              $message = 
                sprintf(
                  $this->selecthelp( $this->element, CF_STR_FILE_TYPES_ALLOWED ),
                  $this->element->getDisplayName(),
                  implode( CF_STR_FILE_OR, $this->types)
                );
              $results[] = $message;
              $this->element->addMessage( $message );
               
            }

          }

          // MINIMUM LENGTH

          if ( is_numeric( $this->minimum ) ) {
            if ( filesize( $file['tmp_name'] ) < $this->minimum ) {
              $message = 
                sprintf( 
                  $this->selecthelp( $this->element, CF_STR_FILE_MINIMUM ), 
                  $this->element->getDisplayName(), 
                  $this->minimum 
                );
              $results[] = $message;
              $this->element->addMessage( $message );
            }
          }

          // MAXIMUM LENGTH

          if ( is_numeric( $this->maximum ) ) {

            if ( filesize( $file['tmp_name'] ) > $this->maximum ) {
              $message = 
                sprintf( 
                  $this->selecthelp( $this->element, CF_STR_FILE_MAXIMUM ), 
                  $this->element->getDisplayName(), 
                  $this->maximum 
                );
              $results[] = $message;
              $this->element->addMessage( $message );
            }

          }

          break;

      }

    }
    else {
      // $_FILES[ $name ] was not set

      if ( $this->required && !$this->element->getValue( 0 ) ) {
        $message = sprintf( 
          $this->selecthelp( $this->element, CF_STR_FILE_REQUIRED ), 
          $this->element->getDisplayName()
        );
        $results[] = $message;
        $this->element->addMessage( $message );

      }

    }      

    // load value for validated and not validated inputs
    if ( !count( $results ) && $this->element->binaryvalue )
      $this->element->_readContents();

    }

    return $results;

  }

  // -------------------------------------------------------------------------
  function getJSCode( ) {

    $code       = '';
    $fieldvalue = $this->getJSField( $this->element ) . '.value';

    // FILENAME LENGTH

    /* *******

    // type check - not working in some browsers unfortunately

    if ( count( $this->types ) ) {

      $types = implode('|', $this->types );
      $code .= 
        'errors.addIf( \'' . $this->element->_getHTMLId() . '\', ( ' . $fieldvalue . 
        '.match(/^.*'.$types.'$/) == ' . $fieldvalue . 
        ', "' . 
        $this->_jsescape( sprintf( 
          $this->selecthelp( $this->element, CF_STR_FILE_TYPES_ALLOWED ), 
          $this->element->getDisplayName(), 
          implode(', ', $this->types )
        ) ). "\" ) );\n";

    }

    ******* */

    if ( $this->required && !$this->element->getValue( 0 ) )
        $code .=
          'errors.addIf( \'' . $this->element->_getHTMLId() . '\', ( ' . $fieldvalue . '.length == 0, "' . 
          $this->_jsescape( sprintf( 
            $this->selecthelp( $this->element, CF_STR_FILE_REQUIRED ), 
            $this->element->getDisplayName()
          ) ). "\" ) );\n";

    return $this->injectDependencyJS( $code );

  }

} 

?>