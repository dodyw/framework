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
class selectDate extends element {

  var $value;

  var $layout = '%Y %M %D';
    // we use % here to avoid problems with Y, M and D letters
    // in the layout string.
    // eg. if you'd like to use a select like
    //   Year: [    ] Month: [    ] Day: [    ]
    // you should specify:
    //   $layout = 'Year: %Y Month: %M Day: %D'

  var $format = '%Y-%M-%D';
    // format is used to specify the 'compiled' result of
    // the selects returned by getValue

  var $padding      = true;
    // store month, day, with two digits,
    // (01..12, 01..31 ), year with 4 digits (leading zeroes) or not.
    // Affects only compiled result ($this->format), 
    // does not affect display.

  var $showpaddinghours = false;
    // Show zeropadded hours (00..23) or not (default: false).
    // Compiled ('stored') format is always padded disregarding this attribute.
  var $showpaddingmins  = true;
    // Show zeropadded minutes (00..59) or not (default: true).
    // Compiled ('stored') format is always padded disregarding this attribute.
  var $showpaddingsecs  = true;
    // Show zeropadded seconds (00..59) or not (default: true).
    // Compiled ('stored') format is always padded disregarding this attribute.

  var $null         = Array( '' => '' );
    // if $null is not false, but an array, it is used
    // for enabling empty dates. You may set it like:
    // Array( '' => '-- choose --' ) and combine
    // the element with a date validation.

  var $yearfrom  = false; // if ===false, will be set to current year in constructor
  var $yearuntil = 1900;  // if ===false, will be set to current year in constructor

  // if $yearfrom is larger than $yearuntil, you'll get a 
  // decrementing list of years, which is more natural to the users
  
  var $months    = Array( 
    1 => CF_STR_MONTH_01,  2 => CF_STR_MONTH_02,  3 => CF_STR_MONTH_03,  4 => CF_STR_MONTH_04, 
    5 => CF_STR_MONTH_05,  6 => CF_STR_MONTH_06,  7 => CF_STR_MONTH_07,  8 => CF_STR_MONTH_08, 
    9 => CF_STR_MONTH_09, 10 => CF_STR_MONTH_10, 11 => CF_STR_MONTH_11, 12 => CF_STR_MONTH_12
  );
  // you'll find the months defined in messages_XX.php 
  // You can still override the month array by
  // the 'months' setting of your element.

  var $onbeforechange; 
  var $onafterchange; 
  // should you need to update anything when the dropdowns change,
  // just include your JS code here (trailing ";" required)

  // private variables
  var $year      = null;
  var $month     = null;
  var $day       = null;
  var $hour      = null;
  var $min       = null;
  var $sec       = null;

  // --------------------------------------------------------------------------
  function selectDate( $name, $configvalues ) {

    $this->name = $name;

    foreach ( $configvalues as $key => $value )
      if ( $key != 'value' )
        $this->$key = $value;

    if ( $this->yearfrom === false )
      $this->yearfrom = date("Y");

    if ( $this->yearuntil === false )
      $this->yearuntil = date("Y");

    if ( isset( $configvalues['value'] ) ) 
      $this->setValue( $configvalues['value'], 0 );

  }

  // --------------------------------------------------------------------------
  function setValue( $value, $magic_quotes_gpc ) {

    // besides the date value, we also maintain
    // private variables - $year, $month, $day -
    // which are needed to reload the selects
    // with selected values

    $value = $this->_prepareInput( $value, $magic_quotes_gpc );

    // $this->value = $value;
    //
    // we don't set value unless it's a date than can 
    // be processed by strtotime. setting value is done
    // by $this->createStoredFormat()

    if ( class_exists( 'DateTime' ) ) {

      // above PHP 5.2, we have the DateTime object
      // also supporting historical dates, and better
      // date support
       
      if ( preg_match( '/^([^\d]|0)+$/', $value ) )
        // 0000-00-00 type dates are convenient 
        // database values instead of NULLs, which are
        // converted to Nov 30, -0001 by PHP, so we
        // need to have a workaround here instead.
        // It will never break date compatibility, as it's
        // a wrong date format (zero month and day).
        $time = false;
      else {
        
        $time = new DateTime( $value );

        if ( is_object( $time ) ) {
          $year    = $time->format("Y");
          $month   = $time->format("m");
          $day     = $time->format("d"); 
          $hour    = $time->format("H");
          $min     = $time->format("i");
          $sec     = $time->format("s");
        }
      }

    }
    else {

      $time = strtotime( $value );

      if ( $time ) {  
        $year      = date("Y", $time );
        $month     = date("m", $time );
        $day       = date("d", $time );
        $hour      = date("H", $time );
        $min       = date("i", $time );
        $sec       = date("s", $time );
      }

    }

    if ( $time ) {

      // we now have valid time parts, but
      // are the values between yearfrom/yearuntil?
      return $this->createStoredFormat(
        $year, $month, $day, $hour, $min, $sec, 0
      );
  
    }
    else
      return false;

  }

  // --------------------------------------------------------------------------
  function getHTML() {

    $selects = Array();

    $years     = $this->_createrange( $this->yearfrom, $this->yearuntil );
    $months    = $this->months;
    $days      = $this->_createrange( 1, 31 );
    $hours     = 
      $this->showpaddinghours ?
        $this->_createrange( 0, 23, '0', 2 ) :
        $this->_createrange( 0, 23 );
    $mins     =
      $this->showpaddingmins ? 
        $this->_createrange( 0, 59, '0', 2 ) : 
        $this->_createrange( 0, 59 );
    $secs     = 
      $this->showpaddingsecs ? 
        $this->_createrange( 0, 59, '0', 2 ) : 
        $this->_createrange( 0, 59 );

    if ( is_array( $this->null ) && count( $this->null ) ) {
      $years     = $this->null + $years;
      $months    = $this->null + $months;
      $days      = $this->null + $days;
      $hours     = $this->null + $hours;
      $mins      = $this->null + $mins;
      $secs      = $this->null + $secs;
    }

    $parts['%Y']  = $this->makeSelect( $this->_getHTMLId() . 'year',  $years,  $this->year );
    $parts['%M']  = $this->makeSelect( $this->_getHTMLId() . 'month', $months, $this->month );
    $parts['%D']  = $this->makeSelect( $this->_getHTMLId() . 'day',   $days,   $this->day );

    $parts['%h']  = $this->makeSelect( $this->_getHTMLId() . 'hour',  $hours,  $this->hour );
    $parts['%m']  = $this->makeSelect( $this->_getHTMLId() . 'min',   $mins,   $this->min );
    $parts['%s']  = $this->makeSelect( $this->_getHTMLId() . 'sec',   $secs,   $this->sec );

    // $parts['%T']  = $this->makeInput( $this->_getHTMLId() . 'timeshort', $this->timeshort, 5 );

    $out = strtr( $this->layout, $parts );

    // the hidden input is a helper field containing
    // the current date in a 'compiled' form (according to
    // 'format' setting)

    return
      '<input type="hidden" ' . $this->html .
        ' id="' . $this->_getHTMLId() . '"' .
        ' value="' . $this->value . '"' .
        ' name="' . $this->name . '" />' . "\n" .
      $out . "\n"
    ;

  }

  // -------------------------------------------------------------------------
  function makeInput( $name, $value, $length ) {

    return
      '<input name="' . $name . '" id="' . $name . '" ' .
        'maxlength="' . $length . '" size="' . $length . '" type="text" ' .
        'value="' . htmlspecialchars( $value ) . '" ' .
        'onchange="' . $this->onbeforechange . 'clonefishSelectDateStoredFormat( ' .
          'document.forms[\'' . $this->form->name . '\'], ' .
          '\'' . $this->_getHTMLId() . '\', ' .
          '\'' . $this->format . '\', ' .
          '\'' . $this->padding . '\' ' .
          ');' . $this->onafterchange .
        "\" />\n";

  }

  // -------------------------------------------------------------------------
  function makeSelect( $name, $options, $value ) {

    $out = '';
    foreach ( $options as $key => $avalue ) {

      if ( (string)$key == (string)$value )
        $out .= '<option selected="selected" value="' . 
          htmlspecialchars( $key ) . 
        '">';
      else
        $out .= '<option value="' . htmlspecialchars( $key ) . '">';

      $out .= htmlspecialchars( $avalue ) . "</option>\n";

    }

    return
      "<select " .
        "onchange=\"" . $this->onbeforechange . "clonefishSelectDateStoredFormat( " .
          "document.forms['" . $this->form->name . "'], " .
          "'" . $this->_getHTMLId() . "', ".
          "'" . $this->format . "', ".
          "'" . $this->padding . "' ".
          ");" . $this->onafterchange .
        "\" name=\"" . $name . "\">\n" . $out . "</select>\n";

  }

  // -------------------------------------------------------------------------
  function createStoredFormat( $year, $month, $day, $hour, $min, $sec, $magic_quotes_gpc ) {

    // used by $clonefish->addElements(): the values of the received
    // date part selects are compiled also on the server side
    // to support server-side validation (we cannot rely purely on JS)
    //
    // also used by $this->setValue(): when a formatted date is
    // received, it's being split using strtotime(), and 
    // the element value is set to the stored format, this
    // way we can always match with the format settings, even
    // when unnecessary date elements (eg. seconds) are
    // passed from a database query as a value.

    $year      = $this->_prepareInput( $year,  $magic_quotes_gpc );
    $month     = $this->_prepareInput( $month, $magic_quotes_gpc );
    $day       = $this->_prepareInput( $day,   $magic_quotes_gpc );
    $hour      = $this->_prepareInput( $hour,  $magic_quotes_gpc );
    $min       = $this->_prepareInput( $min,   $magic_quotes_gpc );
    $sec       = $this->_prepareInput( $sec,   $magic_quotes_gpc );

    $partMap = Array(
      '%Y' => $year,
      '%M' => $month,
      '%D' => $day,
      '%h' => $hour,
      '%m' => $min,
      '%s' => $sec
    );

    $out           = '';
    $isYearPresent = false;

    for ( $i = 0; $i < strlen( $this->format ); $i++ ) {

      $part = substr( $this->format, $i, 2 );

      switch ( $part ) {

        case '%Y':
        case '%M':
        case '%D':
        case '%h':
        case '%m':
        case '%s':

          if ( $part == '%Y' )
            $isYearPresent = true;

          // padding the appropriate date part 
          // if it has length and padding is needed
          if ( 
               strlen( $partMap[ $part ] ) &&
               ( $this->padding || in_array( $part, Array( '%h', '%m', '%s' ) ) )
             ) {

            $padLength = 1;
            if ( $this->padding || in_array( $part, Array( '%h', '%m', '%s' ) ) )
              $padLength = $part == '%Y' ? 4 : 2;

            $value = str_pad( $partMap[ $part ], $padLength, '0', STR_PAD_LEFT );
            $out  .= $value;

            // padded values back to properties
            switch ( $part ) {
              case '%h': $hour = $value; break;
              case '%m': $min  = $value; break;
              case '%s': $sec  = $value; break;
            }

          }
          else
            $out .= $partMap[ $part ];

          $i++; // skip another char in format string

          break;

        default:
          $out .= substr( $this->format, $i, 1 );
          break;
      }

    }

    $reverse = $this->yearfrom > $this->yearuntil;

    if (
         $isYearPresent && 
         (
           (
             ( $this->yearfrom !== false ) &&
             (
               ( !$reverse && ( $year < $this->yearfrom ) ) ||
               (  $reverse && ( $year > $this->yearfrom ) )
             )
           )
           ||
           (
             ( $this->yearuntil !== false ) &&
             (
               ( !$reverse && ( $year > $this->yearuntil ) ) ||
               (  $reverse && ( $year < $this->yearuntil ) )
             )
           )
         )
       ) {
      // invalid date passed, out of year range
      return false;
    }
    else {

      $this->value     = $out;

      $this->year      = $year;
      $this->month     = $month;
      $this->day       = $day;
      $this->hour      = $hour;
      $this->min       = $min;
      $this->sec       = $sec;

      return true;

    }

  }

  // -------------------------------------------------------------------------
  function _createRange( $from, $until, $padchar = false, $padlength = false ) {

    $range = Array();

    for (
          $i = $from;
          $from < $until ? $i <= $until : $i >= $until;
          $from < $until ? $i++ : $i--
        ) {

      if ( is_string( $padchar ) && $padlength ) {
        $padded           = (string) str_pad( $i, $padlength, $padchar, STR_PAD_LEFT );
        $range[ $padded ] = $padded;
      }
      else
        $range[ $i ] = $i;
    }

    return $range;

  }

}

?>