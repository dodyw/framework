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
class fckEditorArea2Required extends validation {

  // -------------------------------------------------------------------------
  function getJSCode( ) {

    $code = '';
    $fieldvalue = $this->getJSField( $this->element ) . '.value';

    $code .= 
        'FCKValue = FCKeditorAPI.GetInstance(\'' . $this->element->_getHTMLId() . '\').GetXHTML( true );' . "\n" .
        'errors.addIf( \'' . $this->element->_getHTMLId() . '\', FCKValue.match(/[\s]*/m) != FCKValue, "' . 
        $this->_jsescape( sprintf(
          $this->selecthelp( $this->element, CF_STR_REQUIRED_TEXT ),
          $this->element->getDisplayName()
        ) ) .
      '" );' . "\n"
      ;

    return $this->injectDependencyJS( $code );

  }

  // -------------------------------------------------------------------------
  function isValid() {

    $results = Array();

    if ( $this->checkDependencyPHP() ) {

    if ( !strlen( trim( $this->element->getValue( 0 ) ) ) ) {
      $message = 
        sprintf(
          $this->selecthelp( $this->element, CF_STR_REQUIRED_TEXT ),
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