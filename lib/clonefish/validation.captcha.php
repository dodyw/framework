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
class captchaValidation extends validation {

  var $index; // $_SESSION[ $index ]
  var $form;  // form
  var $casesensitive = false;

  // -------------------------------------------------------------------------
  function isValid() {

    $results = Array();

    if ( !session_id() )
      session_start();

    if ( !$this->element->getValue( 0 ) ||
         ( 
           $this->handleCase( $this->element->getValue( 0 ) ) != 
           $this->handleCase( @$_SESSION[ $this->index ] ) 
         )
       ) {
      $message =
        sprintf(
          $this->selecthelp( $this->element, CF_STR_CAPTCHA_ERROR ), 
          $this->element->getDisplayName()
        );
      $results[] = $message;
      $this->element->addMessage( $message );
    }

    return $results;

  }

  // -------------------------------------------------------------------------
  function handleCase( $value ) {

    if ( $this->casesensitive )
      return $value;
    else
      return strtolower( $value );

  }

}

?>