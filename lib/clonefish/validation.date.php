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
class dateValidation extends validation {
 
  var $settings = Array();

  // settings coming from the settings array
  var $format = 'YYYY-MM-DD';
  var $required = 1;
  var $minimum;
  var $maximum;
  var $lessthan;      // value < a field's value
  var $greaterthan;   // value > a field's value
  var $lesseqthan;    // value <= than a field's value
  var $greatereqthan; // value >= than a field's value

  // -------------------------------------------------------------------------
  function getJSCode( ) {

    $code = '';
    $fieldvalue = $this->getJSField( $this->element ) . '.value';

    $code .= 'errors.addIf( \'' . $this->element->_getHTMLId() . '\', ' .
      ( $this->required ? '' : '(' . $fieldvalue . '.length == 0 ) || ' ) .
      'clonefishValidDateTime( ' .
        $fieldvalue . ', ' .
        '"' . $this->format . '", ' .
        ( $this->minimum       ? $this->minimum       : 'null' ) . ', ' .
        ( $this->maximum       ? $this->maximum       : 'null' ) . ', ' .
        $this->addJSParametersFor( 'lessthan' )      . ', ' .
        $this->addJSParametersFor( 'lesseqthan' )    . ', ' .
        $this->addJSParametersFor( 'greaterthan' )   . ', ' .
        $this->addJSParametersFor( 'greatereqthan' ) .
      ') ' .
      ", \"" .

      $this->_jsescape( sprintf(
        $this->selecthelp( $this->element, CF_STR_DATE_FORMAT ),
        $this->element->getDisplayName(),
        $this->format
      ) ) . "\" );\n";

    return $this->injectDependencyJS( $code );

  }

  // -------------------------------------------------------------------------
  function addJSParametersFor( $attribute ) {

    if ( $this->$attribute ) {

      $element = $this->form->getElementByName( $this->$attribute );
      if (
           ( $validation = $element->getValidation('date') ) &&
           isset( $validation['format'] )
         ) {
        $field = $this->getJSField( $element ) . '.value';
        return $field . ', "' . $validation['format'] . '"';
      }
      else
        die(
          sprintf(
            CF_ERR_DATE_COMPARE_VALIDATION_MISSING,
            $element->getName(),
            $this->element->getName()
          )
        );

    }
    else
      return 'null, null';

  }

  // -------------------------------------------------------------------------
  function isValid() {

    $this->results = Array();

    if ( $this->checkDependencyPHP() ) {

    if ( !$this->required && !strlen( $this->element->getValue( 0 ) ) )
      return $this->results;

    $formatcompiled = preg_quote( $this->format, '/' );

    $replace = Array(
      'YYYY' => '([0-9]{4})',
      'YY'   => '([0-9]{2})',
      'MM'   => '([0-9]{2})',
      'DD'   => '([0-9]{2})',
      'hh'   => '([0-9]{2})',
      'mm'   => '([0-9]{2})',
      'ss'   => '([0-9]{2})',
      'M'    => '([0-9]{1,2})',
      'D'    => '([0-9]{1,2})',
      'h'    => '([0-9]{1,2})',
      'm'    => '([0-9]{1,2})',
      's'    => '([0-9]{1,2})',
    );

    $formatcompiled = strtr(
      $formatcompiled,
      $replace
    );

    if ( !preg_match(
           '/^' . $formatcompiled . '$/Ums', 
           $this->element->getValue( 0 ),
           $rxresults
         )
       ) {

      $message = 
        sprintf(
          $this->selecthelp( $this->element, CF_STR_DATE_FORMAT ),
          $this->element->getDisplayName(),
          $this->format
        );
      $this->results[] = $message;
      $this->element->addMessage( $message );
      return $this->results;

    }

    // syntax is ok, check semantically

    // find elements first

    $indexes = Array(
      'year'  => strpos( $this->format, 'YYYY' ),
      'month' => strpos( $this->format, 'M'    ),
      'days'  => strpos( $this->format, 'D'    ),
      'hour'  => strpos( $this->format, 'h'    ),
      'min'   => strpos( $this->format, 'm'    ),
      'sec'   => strpos( $this->format, 's'    ),
    );

    asort( $indexes );

    $counter    = 1;
    $rxindex = Array();

    foreach ( $indexes as $key => $value ) {

      if ( $value !== false ) {
        $rxindex[ $key ] = $counter;
        $counter++;
      }
      else
        $rxindex[ $key ] = false;

    }

    $year  = $rxindex['year']  !== false ? $rxresults[ $rxindex['year']  ] : false;
    $month = $rxindex['month'] !== false ? $rxresults[ $rxindex['month'] ] : false;
    $days  = $rxindex['days']  !== false ? $rxresults[ $rxindex['days']  ] : false;
    $hour  = $rxindex['hour']  !== false ? $rxresults[ $rxindex['hour']  ] : false;
    $min   = $rxindex['min']   !== false ? $rxresults[ $rxindex['min']   ] : false;
    $sec   = $rxindex['sec']   !== false ? $rxresults[ $rxindex['sec']   ] : false;

    $daysOfMonth = Array( 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31 );
    if ( ( $year !== false ) && ( ( $year % 4 ) == 0 ) )
      $daysOfMonth[ 1 ] = 29;

    if (
         ( ( $month !== false ) && ( ( $month < 1 ) || ( $month > 12 ) ) ) ||
         ( ( $days !== false ) &&  ( ( $days <= 0 ) || ( $days > 31 ) ) ) ||
         ( 
           $month &&
           ( $days > $daysOfMonth[ $month - 1 ] )
         ) ||
         ( $hour !== false ) && ( ( $hour < 0 ) || ( $hour > 23 ) ) ||
         ( $min !== false ) && ( ( $min < 0 ) || ( $min > 59 ) ) ||
         ( $sec !== false ) && ( ( $sec < 0 ) || ( $sec > 59 ) )
       ) {
      $message = 
        sprintf(
          $this->selecthelp( $this->element, CF_STR_DATE_FORMAT ),
          $this->element->getDisplayName(),
          $this->format
        );
      $this->results[] = $message;
      $this->element->addMessage( $message );
    }

    if ( class_exists( 'DateTime' ) ) {

      // above PHP 5.2, we have the DateTime object
      // also supporting historical dates, and better
      // date support
       
      $timeObject = new DateTime();
      $timeObject->setDate( $year, $month, $days );
      $timeObject->setTime( $hour, $min, $sec );
      $currenttime = $timeObject->format("U");
      // Unix timestamp (DateTime::getTimestamp() is
      // only available above PHP 5.3.0, this one's >= 5.2.0

    }
    else {

      $currenttime = mktime( $hour, $min, $sec, $month, $days, $year );
      if ( $currenttime == false ) {
        $message = 
          sprintf(
            $this->selecthelp( $this->element, CF_STR_DATE_OVERFLOW ),
            $this->element->getDisplayName()
          );
        $this->results[] = $message;
        $this->element->addMessage( $message );
      }
      
    }

    $this->element->_currenttime = $currenttime;

    if ( $this->minimum && ( $currenttime < $this->minimum ) ) {
      $message = 
        sprintf(
          $this->selecthelp( $this->element, CF_STR_DATE_OVER_MINIMUM ),
          date("Y-m-d H:i:s", $this->minimum ),
          $this->element->getDisplayName()
        );
      $this->results[] = $message;
      $this->element->addMessage( $message );
    }

    if ( $this->maximum && ( $currenttime > $this->maximum ) ) {
      $message = 
        sprintf(
          $this->selecthelp( $this->element, CF_STR_DATE_OVER_MAXIMUM ),
          date("Y-m-d H:i:s", $this->minimum ),
          $this->element->getDisplayName()
        );
      $this->results[] = $message;
      $this->element->addMessage( $message );
    }

    if ( $this->lessthan )
      $this->compare( 'lessthan' );

    if ( $this->lesseqthan ) 
      $this->compare( 'lesseqthan' );

    if ( $this->greaterthan ) 
      $this->compare( 'greaterthan' );

    if ( $this->greatereqthan ) 
      $this->compare( 'greatereqthan' );

    }

    return $this->results;

  }

  // -------------------------------------------------------------------------
  function compare( $attribute ) {

    if ( !$this->$attribute )
      return;

    $otherelement =& $this->form->getElementByName( $this->$attribute );

    if ( $otherelement->validating ){
      // we're avoiding recursive loops when elements are
      // cross-validated
      return;
    }

    if ( !$otherelement->validated )
      $this->form->validateElement( null, $otherelement );

    if ( !isset( $otherelement->_currenttime ) )
      die(
        sprintf(
          CF_ERR_DATE_COMPARE_VALIDATION_MISSING,
          $otherelement->getName(),
          $this->element->getName()
        )
      );

    if ( $otherelement->validated && $otherelement->valid ) {

      switch ( $attribute ) {
        case 'lessthan':
          $valid     = $this->element->_currenttime < $otherelement->_currenttime;
          $opmessage = CF_STR_DATE_OVER_LESSFIELD;
          break;
        case 'lesseqthan':
          $valid     = $this->element->_currenttime <= $otherelement->_currenttime;
          $opmessage = CF_STR_DATE_OVER_LESSEQFIELD;
          break;
        case 'greaterthan':
          $valid     = $this->element->_currenttime > $otherelement->_currenttime;
          $opmessage = CF_STR_DATE_OVER_GREATERFIELD;
          break;
        case 'greatereqthan':
          $valid     = $this->element->_currenttime >= $otherelement->_currenttime;
          $opmessage = CF_STR_DATE_OVER_GREATEREQFIELD;
          break;
      }

      if ( !$valid ) {
        $message =
          sprintf(
            $this->selecthelp( $this->element, $opmessage ),
            $this->element->getDisplayName(),
            $otherelement->getDisplayName()
          );
        $this->results[] = $message;
        $this->element->addMessage( $message );
      }

    }

  }

}

?>