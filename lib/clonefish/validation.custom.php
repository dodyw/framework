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
class customValidation extends validation {
 
  var $php;
  var $js;

  // -------------------------------------------------------------------------
  function getJSCode( ) {

    $code = '';
    $fieldvalue = $this->getJSField( $this->element ) . '.value';

    if ( strlen( $this->js ) ) {

      preg_match_all( 
        '/<FORM\.(.+)>/Ums',
        $this->js,
        $templatevars, 
        PREG_SET_ORDER 
      );

      $jstemplate = $this->js;
      foreach ( $templatevars as $match ) {

        $element = &$this->form->getElementByName( $match[ 1 ] );
        $replaceto = 
          $this->getJSField( $element ) . '.value';

        $jstemplate = str_replace(
            $match[ 0 ], $replaceto, $jstemplate
        );
      }

      $code .= 'errors.addIf( \'' . $this->element->name . '\', ' .
        $jstemplate . ", '" .
        $this->_jsescape( sprintf(
          $this->selecthelp( $this->element, CF_STR_CUSTOM_VALIDATION_FAILS ),
          $this->element->getDisplayName()
        ) ) .
        "' );\n";
    }

    return $this->injectDependencyJS( $code );

  }

  // -------------------------------------------------------------------------
  function isValid() {

    $results = Array();

    if ( $this->checkDependencyPHP() ) {

    if ( strlen( $this->php ) ) {

      $code = '$result = ' . $this->php . ';';

      $code = preg_replace(
        '/<FORM\.(.+)>/Ums',
        '$this->form->getValue( "\\1", 0 )',
        $code
      );

      eval( $code );

      if ( !$result ) {
        $message =
          sprintf(
            $this->selecthelp( $this->element, CF_STR_CUSTOM_VALIDATION_FAILS ),
            $this->element->getDisplayName()
          );
        $results[] = $message;
        $this->element->addMessage( $message );
      }

      }
    
    }

    return $results;

  }

}

?>