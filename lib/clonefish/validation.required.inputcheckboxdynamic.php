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
class inputcheckboxDynamicRequired extends validation {

  var $minimum = 1;
  var $maximum;

  // -------------------------------------------------------------------------
  function getJSCode( ) {

    $code = '';

    // We're building a JavaScript expression here to
    // get the number of checkboxes checked.
    //
    // As there are various limits regarding the number
    // of operands in a JS expression in the browsers, 
    // we're building "safe" expressions containing
    // max 100 operands.
    $check = Array();
    $block = 0;

    foreach ( $this->element->values as $key => $value ) {
      if ( !isset( $check[ $block ] ) )
        $check[ $block ] = Array();
      $check[ $block ][] = $this->getJSField( $this->element, $key ) . '.checked';
      if ( count( $check[ $block ] ) == 100 )
        $block++;
    }

    if ( count( $check ) ) {

      foreach ( $check as $key => $oneblock ) {

        if ( $key == 0 )
          $code .= 'checked = ';
        else
          $code .= 'checked = checked + ';

        $code .= implode( " +\n\t\t\t", $oneblock ) . ";\n";

      }

    }
    else
      $code .= "checked = 0;\n";

    if ( $this->minimum )
      $code .=
        'errors.addIf( \'' . $this->element->_getHTMLId() . '\', ' .
        'checked >= ' . $this->minimum . ', "' .
          $this->_jsescape( sprintf(
            $this->selecthelp( $this->element, CF_STR_REQUIRED_MINIMUM_CHECKBOXES ), 
            $this->minimum, 
            $this->element->getDisplayName() 
          ) ) . '" );' . "\n"
        ;

    if ( $this->maximum )
      $code .=
        'errors.addIf( \'' . $this->element->_getHTMLId() . '\', ' .
        'checked <= ' . $this->maximum . ', "' .
          $this->_jsescape( sprintf(
            $this->selecthelp( $this->element, CF_STR_REQUIRED_MAXIMUM_CHECKBOXES ), 
            $this->maximum, 
            $this->element->getDisplayName() 
          ) ) . '" );' . "\n"
        ;

    return $this->injectDependencyJS( $code );

  }

  // -------------------------------------------------------------------------
  function isValid() {

    $results = Array();

    if ( $this->checkDependencyPHP() ) {

    if ( $this->minimum &&
         ( count( $this->element->getValue( 0 ) ) < $this->minimum ) 
       ) {
      $message =
        sprintf(
          $this->selecthelp( $this->element, CF_STR_REQUIRED_MINIMUM_CHECKBOXES ),
          $this->minimum,
          $this->element->getDisplayName()
        );
      $results[] = $message;
      $this->element->addMessage( $message );
    }

    if ( $this->maximum &&
         ( count( $this->element->getValue( 0 ) ) > $this->maximum )
       ) {
      $message =
        sprintf(
          $this->selecthelp( $this->element, CF_STR_REQUIRED_MAXIMUM_CHECKBOXES ),
          $this->maximum,
          $this->element->getDisplayName()
        );
      $results[] = $message;
      $this->element->addMessage( $message );
      }

    }

    return $results;

  }

  // -------------------------------------------------------------------------
  function getJSField( &$element, $key ) {

    return $this->form->getJSName() . '["' . $element->getName() . '[' . $key . ']"]';

  }

} 

?>