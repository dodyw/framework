<?php

/**
 * Clonefish form generator class 
 * (c) phpformclass.com, Dots Amazing
 * All rights reserved.
 * 
 * @copyright  2010 Dots Amazing
 * @version    v2.2, 2010-10-21
 * @link       http://phpformclass.com
 * @package    clonefish
 * @subpackage validation
 */

/* 
 * Validation root
 * @package clonefish
 * @subpackage validationTypes
 */
class validation {

  var $element;
  var $help;
  var $settings = Array();
  var $anddepend = Array();
  var $ordepend  = Array();

  // -------------------------------------------------------------------------
  function validation( $settings, &$element ) {

    if ( is_object( $element ) )
      $this->element = &$element;

    if ( is_array( $settings ) ) 
      $this->settings = $settings;

    $this->parseSettings();

  }

  // -------------------------------------------------------------------------
  function parseSettings() {

    foreach ( $this->settings as $key => $value ) {
      $this->$key = $value;
    }

  } 

  // -------------------------------------------------------------------------
  function isValid() {
    // to be implemented by descendants
  }

  // -------------------------------------------------------------------------
  function getJSCode() {
    // to be implemented by descendants
  }

  // -------------------------------------------------------------------------
  function selectHelp( &$element, $defaulthelp ) {

    if ( strlen( $this->help ) )
      return $this->help;
    else
      if ( strlen( $element->getHelp() ) )
        return $element->getHelp();
      else
        return $defaulthelp;

  }

  // -------------------------------------------------------------------------
  function getJSField( &$element ) {

    // no '.value' here, since not all input objects support this
    // attribute (eg for selects we'll need select[select.selectedIndex].value)
    return $this->form->getJSName() . '["' . $element->getName() . '"]';

  }

  // -------------------------------------------------------------------------
  // escaping for JavaScript error messages.

  function _jsescape( $string ) {

    $trans = Array(
      '"' => '\"',
      "'" => "\'",
      '\\' => '\\\\',
    );

    return strtr( strip_tags( $string ), $trans );

  }

  // -------------------------------------------------------------------------
  function injectDependencyJS( $jscode ) {

    if ( count( $this->anddepend ) )
      return $this->processJSDependencyItems( $this->anddepend, '&&', $jscode );
    if ( count( $this->ordepend ) )
      return $this->processJSDependencyItems( $this->ordepend, '||', $jscode );

    return $jscode;

  }

  // -------------------------------------------------------------------------
  function processJSDependencyItems( $items, $glue, $jscode ) {

    if ( !strlen( $jscode ) )
      return '';
    else {

      $out = Array();

      if ( !is_array( $items ) )
        die( sprintf( CF_ERR_DEPEND_VALIDATION_NOT_ARRAY_OF_ARRAYS, $this->element->name ) );

      foreach ( $items as $item ) {

        if ( !is_array( $item ) )
          die( sprintf( CF_ERR_DEPEND_VALIDATION_NOT_ARRAY_OF_ARRAYS, $this->element->name ) );

        if ( !array_key_exists( 'js', $item ) )
          die( sprintf( CF_ERR_DEPEND_VALIDATION_JS_KEY_MISSING, $this->element->name ) );

        if ( !array_key_exists( 'php', $item ) )
          die( sprintf( CF_ERR_DEPEND_VALIDATION_PHP_KEY_MISSING, $this->element->name ) );

        preg_match_all( '/(\<FORM.([^>]+)\>)/msU', $item['js'], $results );

        foreach ( $results[1] as $key => $result ) {

          $element = $this->form->getElementByName( $results[2][ $key ] );
          
          // the extra () is used to help creating expressions easily like:
          //    <FORM.element>.length > 0 
          // without the ()'s the above expression would return the
          // length of the function instead of the length of the returned
          // value.

          $getValueJS = 
            "( clonefishGetFieldValue( " .
              "'" . $this->form->name . "', " .
              "'" . $element->name . "', " .
              "'" . $element->type . "' " .
            ") )"
          ;

          $item['js'] = str_replace( $result, $getValueJS, $item['js'] );

        }
      
        $out[] = $item['js'];
      
      }

      if ( count( $out ) )
        return
          "/* " . $this->element->name . " validation is depending on other field(s) */\n" .
          'if ( ( ' . implode(' ) ' . $glue . ' ( ', $out ) . " ) ) {\n  " . 
          $jscode .
          "}\n"
          ;
      else
        return $jscode;

    }

  }

  // -------------------------------------------------------------------------
  function checkDependencyPHP() {

    if ( count( $this->anddepend ) )
      return $this->processPHPDependencyItems( $this->anddepend, '&&' );
    if ( count( $this->ordepend ) )
      return $this->processPHPDependencyItems( $this->ordepend, '||' );

    // no dependency: process validations
    return true;

  }

  // -------------------------------------------------------------------------
  function processPHPDependencyItems( $items, $glue ) {

    switch ( $glue ) {
      case '||': $out = false; break;
      case '&&': $out = true;  break;
      default: die( 'CF validation::processPHPDependencyItems() glue unsupported: ' . $glue );
    }

    if ( !is_array( $items ) )
      die( sprintf( CF_ERR_DEPEND_VALIDATION_NOT_ARRAY_OF_ARRAYS, $this->element->name ) );

    foreach ( $items as $item ) {

      if ( !is_array( $item ) )
        die( sprintf( CF_ERR_DEPEND_VALIDATION_NOT_ARRAY_OF_ARRAYS, $this->element->name ) );

      if ( !array_key_exists( 'php', $item ) )
        die( sprintf( CF_ERR_DEPEND_VALIDATION_PHP_KEY_MISSING, $this->element->name ) );

      preg_match_all( '/(\<FORM.([^\>]+)\>)/msU', $item['php'], $results );

      $elements = Array();

      foreach ( $results[2] as $key => $result ) {

        $elements[ $results[2][ $key ] ] =& $this->form->getElementByName( $results[2][ $key ] );
        if ( !is_object( $elements[ $results[2][ $key ] ] ) ) {
          die( sprintf( 
            CF_ERR_DEPEND_VALIDATION_ELEMENT_MISSING, 
            $this->element->name, htmlspecialchars( $results[ 1 ][ $key ] )
          ) );
        }

        $item['php'] = str_replace( 
          $results[1][ $key ], 
          '$elements[ \'' . $results[2][ $key ] . '\' ]->getValue( 0 )', 
          $item['php'] 
        );

      }

      eval( '$result = ' . $item['php'] . ' ? true : false;' );
      
      switch ( $glue ) {
        case '&&': $out = $out && $result; break;
        case '||': $out = $out || $result; break;
        default: die( 'CF validation::processPHPDependencyItems() glue unsupported: ' . $glue );
      }
    
    }

    return $out;

  }

} 

?>