<?php

/**
 * Clonefish form generator class 
 * (c) phpformclass.com, Dots Amazing
 * All rights reserved.
 * 
 * @copyright  2010 Dots Amazing
 * @link       http://phpformclass.com
 * @package    clonefish
 * @subpackage elements
 */

/* 
 * Element
 * @package clonefish
 * @subpackage elements
 */
class reCaptcha extends element {

  var $pubkey; // API public key

  // -------------------------------------------------------------------------   
  function getHTML() {
    
    if ( !function_exists('recaptcha_get_html') )
      die( sprintf(
        CF_ERR_RECAPTCHA_LIBRARY_MISSING,
        $this->getName()
      ) );

    if ( !$this->pubkey )
      die( sprintf(
        CF_ERR_RECAPTCHA_PUBKEY_MISSING,
        $this->getName()
      ) );

    return recaptcha_get_html( $this->pubkey );
  
  }

}

?>