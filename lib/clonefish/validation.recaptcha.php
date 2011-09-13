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
class recaptchaValidation extends validation {

  var $privkey;

  // -------------------------------------------------------------------------
  function isValid() {

    if ( !function_exists('recaptcha_check_answer') )
      die( sprintf(
        CF_ERR_RECAPTCHA_LIBRARY_MISSING,
        $this->element->getName() 
      ) );

    if ( !strlen( $this->privkey ) )
      die( sprintf(
        CF_ERR_RECAPTCHA_PRIVKEY_MISSING,
        $this->element->getName() 
      ) );

    $resp = recaptcha_check_answer(
      $this->privkey,
      @$_SERVER["REMOTE_ADDR"],
      @$_POST["recaptcha_challenge_field"],
      @$_POST["recaptcha_response_field"]
    );

    if ( $resp->is_valid )
      return Array();
    else {
      
      $message =
        sprintf(
          $this->selecthelp( 
            $this->element, 
            CF_STR_CAPTCHA_ERROR . 
            ( strlen( $resp->error ) &&
              $resp->error != 'incorrect-captcha-sol' ? 
              ' [reCaptcha: ' . $resp->error . ']' : ''
            )
          ), 
          $this->element->getDisplayName()
        );
      
      $this->element->addMessage( $message );
      return Array( $message );
    }

  }

}

?>