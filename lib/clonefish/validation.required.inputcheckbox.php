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
class inputcheckboxRequired extends validation {

  // -------------------------------------------------------------------------
  function getJSCode( ) {

    $code = '';

    $code .= 
      'errors.addIf( \'' . $this->element->_getHTMLId() . '\', ' . 
        $this->getJSField( $this->element ) .
        '.checked, "' . 
        $this->_jsescape( sprintf( 
          $this->selecthelp( $this->element, CF_STR_REQUIRED_CHECKBOX ), 
          $this->element->getDisplayName() 
        ) ) . '" );' . "\n"
      ;

    return $this->injectDependencyJS( $code );

  }

  // -------------------------------------------------------------------------
  function isValid() {

    $results = Array();

    if ( $this->checkDependencyPHP() ) {
    
    if ( $this->element->getValue( 0 ) != $this->element->onvalue ) {
      $message =
        sprintf(
          $this->selecthelp( $this->element, CF_STR_REQUIRED_CHECKBOX ),
          $this->element->getDisplayName()
        );
      $results[] = $message;
      $this->element->addMessage( $message );
      }

    }

    return $results;

  }

} 

?>