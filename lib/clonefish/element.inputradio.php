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
class inputRadio extends element {

  var $values = Array();
  var $layout = "%s";
  var $itemlayout = "%radio% %label%\n";
  var $divider;
  var $divide;

  // --------------------------------------------------------------------------
  function inputRadio( $name, $configvalues ) {

    $this->name           = $name;

    foreach ( $configvalues as $key => $value )
      if ( $key != 'value' )
        $this->$key = $value;

    if ( !is_array( $this->values ) )
      die( sprintf( CF_ERR_VALUE_ARRAY_REQUIRED, $this->name ) );

    if ( isset( $configvalues['value'] ) ) 
      if ( !is_array( $configvalues['value'] ) )
        $this->setValue( $configvalues['value'], 0 );
      else
        die( sprintf( CF_ERR_CONFIG_SINGLE_VALUE_REQUIRED, $name ) );
    

  }

  // -------------------------------------------------------------------------
  function setValue( $value, $slashes_added ) {

    // if the second parameter is true, we have to strip the slashes

    if ( $slashes_added )
      $value = $this->_prepareInput( $value, $slashes_added );

    if ( array_key_exists( $value, $this->values ) ) {
      $this->value = $value;
      return true;
    }
    else
      return false;

  }

  // --------------------------------------------------------------------------
  function getHTML() {

    $options = '';
    $i       = mt_rand( 1000, 10000 );
    $counter = 0;

    foreach ( $this->values as $key => $value ) {
      $i++;
      $label = '<label for="radio' . $i . '">' . $value . "</label>";
      $radio = '<input '.
          $this->html . ' ' .
          'id="radio' . $i . '" ' .
          'type="radio" '.
          'name="' . $this->getName() . '" ' .
          'value="' . htmlspecialchars( $key ) . '" />';

      $replace = Array( 
        "%radio%" => $radio, 
        "%label%" => $label
      );

      $option = strtr( $this->itemlayout, $replace );

      if ( $this->divide && $counter && ( ( $counter % $this->divide ) == 0 ) )
        $options .= $this->divider;

      $options .= $option;
      $counter++;

    }

    if ( isset( $this->values[ $this->value ] ) )
      $options = str_replace(
        'value="' . htmlspecialchars( $this->value ) . '"',
        'checked="checked" value="' . htmlspecialchars( $this->value ) . '"',
        $options
      );

    return 
      sprintf( $this->layout, $options );

  }

}

?>