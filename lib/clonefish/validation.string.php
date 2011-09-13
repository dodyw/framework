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
class stringValidation extends validation {

  var $settings = Array();

  // settings coming from the settings array

  var $minimum;   // minimum length
  var $maximum;   // maximum length
  var $regexp;    // regular expression matching
  var $regexpnot; // regular expression not matching
  var $jsregexp;     // regular expression matching for JS
  var $jsregexpnot;  // regular expression not matching for JS
  var $phpregexp;    // regular expression matching for PHP
  var $phpregexpnot; // regular expression not matching for PHP

  var $equals;    // fieldname of equal field
  var $differs;   // fieldname of differing field
  var $form;      // form

  var $required = 1;

  // -------------------------------------------------------------------------
  function getJSCode( ) {

    $code = '';

    // EQUALS
    
    if ( strlen( $this->equals ) ) {
    
        $equalfield = &$this->form->getElementByName( $this->equals );
	
        if ( !is_object( $equalfield ) )
          die(
            sprintf(
              CF_ERR_STRING_FIELD_NOT_FOUND, 
              $this->equals,
              'equals',
              $this->element->getDisplayName()
            )
          );

        $code .= 
          'errors.addIf( \'' . $this->element->_getHTMLId() . '\', ' . 
          ( $this->required ? '' : '( ' . $this->getJSField( $this->element ) . '.length == 0 ) || ' ) . 
          $this->getJSField( $this->element ) . 
          ' == ' . $this->getJSField( $equalfield ) . ", \"" . 
          $this->_jsescape( sprintf( 
            $this->selecthelp( $this->element, CF_STR_STRING_NOT_EQUAL ), 
            $this->element->getDisplayName(),
	          $equalfield->getDisplayName()
          ) ). "\" );\n";
    
    }

    // DIFFERS
    
    if ( strlen( $this->differs ) ) {
    
        $differfield = &$this->form->getElementByName( $this->differs );
	
        if ( !is_object( $differfield ) )
          die(
            sprintf(
              CF_ERR_STRING_FIELD_NOT_FOUND, 
              $this->differs,
              'differs',
              $this->element->getDisplayName()
            )
          );

        $code .=
          'errors.addIf( \'' . $this->element->_getHTMLId() . '\', ' .
          ( $this->required ? '' : '( ' . $this->getJSField( $this->element ) . '.length == 0 ) || ' ) . 
          $this->getJSField( $this->element ) .
          ' != ' . $differfield->getRealName() . ".value, \"" .
          $this->_jsescape( sprintf(
            $this->selecthelp( $this->element, CF_STR_STRING_NOT_DIFFERENT ),
            $this->element->getDisplayName(),
	          $differfield->getDisplayName()
          ) ). "\" );\n";
    
    }

    // MINIMUM LENGTH

    if ( is_numeric( $this->minimum ) ) 
      if ( $this->form->_functionSupported('strlen') )
        $code .= 
          'errors.addIf( \'' . $this->element->_getHTMLId() . '\', ' . 
          ( $this->required ? '' : '( ' . $this->getJSField( $this->element ) . '.length == 0 ) || ' ) . 
          $this->getJSField( $this->element ) . 
          '.length >= ' . $this->minimum . ", \"" . 
          $this->_jsescape( sprintf( 
            $this->selecthelp( $this->element, CF_STR_STRING_MINIMUM ), 
            $this->element->getDisplayName(), 
            $this->minimum 
          ) ). "\" );\n";

    // MAXIMUM LENGTH 

    if ( is_numeric( $this->maximum ) ) 
      if ( $this->form->_functionSupported('strlen') )
        $code .= 
          'errors.addIf( \'' . $this->element->_getHTMLId() . '\', ' . 
          ( $this->required ? '' : '( ' . $this->getJSField( $this->element ) . '.length == 0 ) || ' ) . 
          $this->getJSField( $this->element ) . 
          '.length <= ' . $this->maximum . ", \"" . 
          $this->_jsescape( sprintf( 
            $this->selecthelp( $this->element, CF_STR_STRING_MAXIMUM ), 
            $this->element->getDisplayName(), 
            $this->maximum 
          ) ) . "\" );\n";

    // MATCHING REGULAR EXPRESSION

    if ( strlen( $this->regexp ) || strlen( $this->jsregexp ) ) {
  
      if ( $this->form->_functionSupported('regexp') ) {

        $regexp = $this->regexp;
        if ( strlen( $this->jsregexp ) ) 
          $regexp = $this->jsregexp;
        
        $code .= 
          'errors.addIf( \'' . $this->element->_getHTMLId() . '\', ' .
          ( $this->required ? '' : '( ' . $this->getJSField( $this->element ) . '.length == 0 ) || ' ) . 
          $this->getJSField( $this->element ) . ".search( " . $regexp . " ) != -1, \"" .
          $this->_jsescape( sprintf( 
            $this->selecthelp( $this->element, CF_STR_STRING_REGEXP ), 
            $this->element->getDisplayName(), 
            $regexp 
          ) ) . "\" );\n";
  
      }

    }

    // NOT MATCHING REGULAR EXPRESSION

    if ( strlen( $this->regexpnot ) || strlen( $this->jsregexpnot ) ) {

      if ( $this->form->_functionSupported('regexp') ) {

        $regexpnot = $this->regexpnot;
        if ( strlen( $this->jsregexpnot ) ) 
          $regexpnot = $this->jsregexpnot;

        $code .=
          'errors.addIf( \'' . $this->element->_getHTMLId() . '\', ' . 
          ( $this->required ? '' : '( ' . $this->getJSField( $this->element ) . '.length == 0 ) || ' ) . 
          $this->getJSField( $this->element ) . ".search( " . $regexpnot . " ) == -1, \"" .
          $this->_jsescape( sprintf( 
            $this->selecthelp( $this->element, CF_STR_STRING_REGEXP_NOT ), 
            $this->element->getDisplayName(), 
            $regexpnot
          ) ) . "\" );\n";

      }
    
    }

    return $this->injectDependencyJS( $code );

  }

  // -------------------------------------------------------------------------
  function isValid() {

    $results = Array();

    if ( $this->checkDependencyPHP() ) {

      // EQUALS

      if ( strlen( $this->equals ) ) {

        $equalfield = $this->form->getElementByName( $this->equals );

        if ( !is_object( $equalfield ) )
          die(
            sprintf(
              CF_ERR_STRING_FIELD_NOT_FOUND, 
              $this->equals,
              'equals',
              $this->element->getDisplayName()
            )
          );
        
        if ( 
             ( $this->required || strlen( $this->element->getValue( 0 ) ) ) &&
             ( $this->element->getValue( 0 ) != $equalfield->getValue( 0 ) )
           ) {
          $message = 
            sprintf( 
              $this->selecthelp( $this->element, CF_STR_STRING_NOT_EQUAL ), 
              $this->element->getDisplayName(), 
              $equalfield->getDisplayName()
            );
          $results[] = $message;
          $this->element->addMessage( $message );
        }
      }

      // DIFFERS

      if ( strlen( $this->differs ) ) {

        $differfield = $this->form->getElementByName( $this->differs );
        
        if ( !is_object( $differfield ) )
          die(
            sprintf(
              CF_ERR_STRING_FIELD_NOT_FOUND, 
              $this->differs,
              'differs',
              $this->element->getDisplayName()
            )
          );

        if ( 
             ( $this->required || strlen( $this->element->getValue( 0 ) ) ) &&
             ( $this->element->getValue( 0 ) == $differfield->getValue( 0 ) ) 
           ) {
          $message = 
            sprintf( 
              $this->selecthelp( $this->element, CF_STR_STRING_NOT_EQUAL ), 
              $this->element->getDisplayName(), 
              $differfield->getDisplayName()
            );
          $results[] = $message;
          $this->element->addMessage( $message );
        }
      }

      // MINIMUM LENGTH

      if ( 
           is_numeric( $this->minimum ) && 
           ( $this->form->_functionSupported('strlen') )
         ) {
        if ( 
             ( $this->required || strlen( $this->element->getValue( 0 ) ) ) &&
             ( $this->form->_handleString( 'strlen', $this->element->getValue( 0 ) ) < $this->minimum ) 
           ) {
          $message = 
            sprintf( 
              $this->selecthelp( $this->element, CF_STR_STRING_MINIMUM ), 
              $this->element->getDisplayName(), 
              $this->minimum 
            );
          $results[] = $message;
          $this->element->addMessage( $message );
        }
      }

      // MAXIMUM LENGTH

      if ( 
           is_numeric( $this->maximum ) && 
           ( $this->form->_functionSupported('strlen') )
         ) {
        if (
             ( $this->required || strlen( $this->element->getValue( 0 ) ) ) &&
             ( $this->form->_handleString( 'strlen', $this->element->getValue( 0 ) ) > $this->maximum ) 
           ) {

          $message = 
            sprintf( 
              $this->selecthelp( $this->element, CF_STR_STRING_MAXIMUM ), 
              $this->element->getDisplayName(), 
              $this->maximum 
            );
          $results[] = $message;
          $this->element->addMessage( $message );
        }
      }

      // MATCH REGEXP

      if ( strlen( $this->regexp ) || strlen( $this->phpregexp ) ) { 

        if ( $this->form->_functionSupported('regexp') ) {

          $regexp = $this->regexp;
          if ( strlen( $this->phpregexp ) ) 
            $regexp = $this->phpregexp;

          if ( 
               ( $this->required || strlen( $this->element->getValue( 0 ) ) ) &&
               !$this->form->_handleString( 'regexp', $this->element->getValue( 0 ), $regexp )
             ) {
            $message = 
              sprintf( 
                $this->selecthelp( $this->element, CF_STR_STRING_REGEXP), 
                $this->element->getDisplayName(), 
                $regexp
              );
            $results[] = $message;
            $this->element->addMessage( $message );
          }
        
        }

      }

      // DON'T MATCH REGEXP

      if ( strlen( $this->regexpnot ) || strlen( $this->phpregexpnot ) ) { 

        if ( $this->form->_functionSupported('regexp') ) {

          $regexpnot = $this->regexpnot;
          if ( strlen( $this->phpregexpnot ) ) 
            $regexpnot = $this->phpregexpnot;

          if (
               ( $this->required || strlen( $this->element->getValue( 0 ) ) ) &&
               $this->form->_handleString( 'regexp', $this->element->getValue( 0 ), $regexpnot )
             ) {
            $message = 
              sprintf( 
                $this->selecthelp( $this->element, CF_STR_STRING_REGEXP_NOT), 
                $this->element->getDisplayName(), 
                $regexpnot
              );
            $results[] = $message;
            $this->element->addMessage( $message );
          }

        }

      }

    }

    return $results;

  }

  // -------------------------------------------------------------------------
  function getJSField( &$element ) {

    return parent::getJSField( $element ) . '.value';

  }

}

?>