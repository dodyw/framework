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
class inputPassword extends element {

  // -------------------------------------------------------------------------
  function getHTML() {
    return 
      '<input ' .
        'type="password" ' .
        'name="' . $this->name . '" ' .
        'id="' . $this->_getHTMLId() . '" ' .
        'value="' . htmlspecialchars( $this->value ) . '" ' . 
        $this->html . 
      ' />';
  }

}

?>