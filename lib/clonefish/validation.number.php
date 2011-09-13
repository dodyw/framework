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
class numberValidation extends validation {

  var $settings = Array();

  // settings coming from the settings array

  var $required = 1; // is it required?
  var $real = 0;  // can use real values
  var $minimum;   // minimum value
  var $maximum;   // maximum value

  // -------------------------------------------------------------------------
  function getJSCode() {

    $code       = '';
    $fieldvalue = $this->getJSField( $this->element ) . '.value';
    
    if ( $this->real === 'sci' ) {
      // the length is hard to track in some of these, so we just let the 
      // server handle it
      $regex = 
        '/(^-?0x[0-9a-fA-F]+$)|'. //hexadecimal notation
        '(^-?[0-9]+?[\\.]?(?=[0-9]+?[eE][+\\-]?(?=[0-9]+$)))|'. // a dot and only one dot followed by only one 'e'
        '(^-?[0-9]+?[\\.](?=[0-9]+$))|' . // a dot only followed by numbers
        '(^-?[0-9]+?[eE][+\\-]?(?=[0-9]+$))|' .  // scientific notation with a lookahead to watch out for multiple 'e's following one already present
        '(^-?[0-9]+$)/';
      $error = CF_STR_NUMBER_SCIENTIFICNOTATIONONLY;
    }
    elseif ( $this->real ) {
      $regex = '/^-?[0-9]+?[\\.](?=[0-9]+$)|^-?[0-9]+$/';
      $error = CF_STR_NUMBER_NUMBERSONLY;
    } 
    else {
      $regex = '/^-?[0-9]+$/';
      $error = CF_STR_NUMBER_INTEGERONLY;
    }
        
    $code .=
      'errors.addIf( \'' . $this->element->_getHTMLId() . '\', ' . 
        ( $this->required ? '' : '(' . $fieldvalue . '.length == 0 ) || ' ) .
        '( '. $regex . '.test(' . $fieldvalue . " ) ), \"" . 
      $this->_jsescape( sprintf(
        $this->selecthelp( $this->element, $error ),
        $this->element->getDisplayName()
      ) ). "\" );\n";

    // MINIMUM VALUE
    if ( is_numeric( $this->minimum ) && $this->real !== 'sci' )
      $code .=
        'errors.addIf( \'' . $this->element->_getHTMLId() . '\', ' .
        ( $this->required ? '' : '(' . $fieldvalue . '.length == 0 ) || ' ) .
        '( parseFloat( ' . $fieldvalue . ' ) >= ' . $this->minimum . " ), \"" . 
        $this->_jsescape( sprintf( 
          $this->selecthelp( $this->element, CF_STR_NUMBER_MINIMUM ), 
          $this->element->getDisplayName(), 
          $this->minimum 
        ) ). "\" );\n";

    // MAXIMUM VALUE
    if ( is_numeric( $this->maximum ) && $this->real !== 'sci' ) 
      $code .= 
        'errors.addIf( \'' . $this->element->_getHTMLId() . '\', ' . 
        ( $this->required ? '' : '(' . $fieldvalue . '.length == 0 ) || ' ) .
        '( parseFloat( ' . $fieldvalue . ' ) <= ' . $this->maximum . " ), \"" . 
        $this->_jsescape( sprintf( 
          $this->selecthelp( $this->element, CF_STR_NUMBER_MAXIMUM ), 
          $this->element->getDisplayName(), 
          $this->maximum 
        ) ) . "\" );\n";

    return $this->injectDependencyJS( $code );

  }

  // -------------------------------------------------------------------------
  function isValid() {

    $results = Array();

    if ( $this->checkDependencyPHP() ) {

     if ( !$this->required && !strlen( $this->element->getValue( 0 ) ) )
       return $results;

     if ( $this->real === 'sci' ) { // SCIENTIFIC NOTATION
         
       if ( !is_numeric( $this->element->getValue( 0 ) ) ) {
         $message = 
           sprintf(
             $this->selecthelp( $this->element, CF_STR_NUMBER_NUMBERSONLY ),
             $this->element->getDisplayName()
           );
         $results[] = $message;
         $this->element->addMessage( $message );
       }
         
     } 
     elseif ( $this->real ) { // ONLY NUMBERS
        
       if ( !preg_match('/^-?[0-9]+?[\\.](?=[0-9]+$)|^-?[0-9]+$/', $this->element->getValue( 0 ) ) ) {
         $message = 
           sprintf(
             $this->selecthelp( $this->element, CF_STR_NUMBER_NUMBERSONLY ),
             $this->element->getDisplayName()
           );
         $results[] = $message;
         $this->element->addMessage( $message );
       }
       
     } 
     else { // ONLY INTEGER VALUES
       
       if ( !preg_match('/^-?[0-9]+$/', $this->element->getValue( 0 ) ) ) {
         $message = 
           sprintf(
             $this->selecthelp( $this->element, CF_STR_NUMBER_INTEGERONLY ),
             $this->element->getDisplayName()
           );
         $results[] = $message;
         $this->element->addMessage( $message );
       }

     }     

     // MINIMUM VALUE

     if ( is_numeric( $this->minimum ) )
       if ( $this->element->getValue( 0 ) < $this->minimum ) {
         $message =
           sprintf( 
             $this->selecthelp( $this->element, CF_STR_NUMBER_MINIMUM ),
             $this->element->getDisplayName(), 
             $this->minimum 
           )
         ;
         $results[] = $message;
         $this->element->addMessage( $message );
       }

     // MAXIMUM VALUE

     if ( is_numeric( $this->maximum ) ) 
       if ( $this->element->getValue( 0 ) > $this->maximum ) {
         $message = 
           sprintf( 
             $this->selecthelp( $this->element, CF_STR_NUMBER_MAXIMUM ), 
             $this->element->getDisplayName(), 
             $this->maximum 
           )
         ;
         $results[] = $message;
         $this->element->addMessage( $message );
       }

    }

    return $results;

  }

} 

?>