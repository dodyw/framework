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
class textarea extends element {

  // -------------------------------------------------------------------------
  function getHTML() {
    return 
      '<textarea ' . 
          'id="' . $this->_getHTMLId() . '" ' . 
          'name="' . $this->name . '" ' .
          $this->html .'>' .
        htmlspecialchars( $this->value ) . 
      '</textarea>';
  }

}

?>