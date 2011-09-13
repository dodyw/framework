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
class inputCheckbox extends element {

  var $onvalue  = 1;
  var $offvalue = 0;
  var $value;

  // -------------------------------------------------------------------------
  function inputCheckbox( $name, $configvalues ) {

    $this->name = $name;
    foreach ( $configvalues as $key => $value )
      $this->$key = $value;

  }

  // -------------------------------------------------------------------------
  function getHTML() {

    return
      '<input ' .
        'type="checkbox" ' .
        'name="' . $this->name . '" ' .
        'id="' . $this->_getHTMLId() . '" ' .
        'value="' . htmlspecialchars( $this->onvalue ) . '"' .
        (
          $this->getValue( 0 ) == $this->onvalue ? ' checked="checked" '
          :
            ''
        ).
        ' ' . $this->html .
      ' />' . "\n";

  }                   

  // -------------------------------------------------------------------------
  function setValue( $value, $magic_quotes_gpc ) {

    // if there is no 'onvalue' defined, browsers send 'on' as default
    // value

    if (
         ( $this->_prepareInput( $value, $magic_quotes_gpc ) == $this->onvalue )
         ||
         ( $value === 'on' ) 
       ) {
      $this->value = $this->onvalue;
    }
    else {
      $this->value = $this->offvalue;
    }

    return true;

  }

  // -------------------------------------------------------------------------
  function getValue( $magic_quotes_gpc ) {

    if ( $this->value == $this->onvalue ) 
      $value = $this->onvalue;
    else
      $value = $this->offvalue;

    return $this->_prepareOutput( $value, $magic_quotes_gpc );

  }

}

?>