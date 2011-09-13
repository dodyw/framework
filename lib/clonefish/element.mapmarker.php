<?php

/**
 * A location (lat/long) element based on the addressChooser component
 * ( http://addresschooser.maptimize.com )
 *
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
class mapMarker extends element {

  /**
   * Google API version - required, defaults to 3 for Google Maps API version 3
   * @var    int
   * @access public 
   */
  var $apiversion = 3;
  
  /**
   * Google API key - required if apiversion is < 3
   * @var    string
   * @access public 
   */
  var $key;

  /**
   * Width of Google Maps component in pixels
   * @var    integer
   * @access public 
   */
  var $width  = 350;

  /**
   * Height of Google Maps component in pixels
   * @var    integer
   * @access public 
   */
  var $height = 350;

  /**
   * Path to ./addresschooser.js and ./proxy/googlemap.js (URI to a directory)
   * @var 
   * @access public  
   */
  var $jspath = '/js/';


  /**
   * Use true to turn on autocomplete feature.
   * @var    boolean 
   * @access public  
   */
  var $autocomplete = true;

  /**
   * Path to ./prototype.js (URI to a directory)
   * Defaults to Google AJAX JS API. Required for autocompletion feature.
   * DO NOT load prototype two times in one page. If your page already
   * loads prototype, set this path to null.
   *
   * @var    string 
   * @access public 
   */
  var $prototypepath     = 'http://ajax.googleapis.com/ajax/libs/prototype/1.6.0.2/';

  /**
   * Path to scriptaculous ./controls.js and ./effects.js (URI to a directory)
   * Defaults to Google AJAX JS API. Required for autocompletion feature.
   * @var    string
   * @access public
   */
  var $scriptaculouspath = 'http://ajax.googleapis.com/ajax/libs/scriptaculous/1.8.2/';

  /**
   * Label for latitude field when JS is not available
   * @var    string
   * @access public
   */
  var $latitude  = 'Latitude:';

  /**
   * Label for longitude field when JS is not available
   * @var    string 
   * @access public 
   */
  var $longitude = 'Longitude:';

  /**
   * String displayed when JS is not available
   * @var    string 
   * @access public 
   */
  var $jshelp    = 'Turn on JavaScript in your browser to use Google Maps instead of typing coordinates!';

  /**
   * Layout string for HTML code of the element. 
   * Available placeholders:
   *
   * %id%           DOM ID of the element ('htmlid' setting)
   * %name%         Element name
   * %latitude%     'latitude' setting
   * %longitude%    'longitude' setting
   * %jshelp%       'jshelp' setting
   * %html%         'html' setting
   * %width%        'width' setting
   * %height%       'height' setting
   * 
   * If you need the autosuggest feature
   *
   * @var    string 
   * @access public 
   */
  var $layout;

  /**
   * Layout string for HTML code of the element fallback (when 
   * JavaScript is not available). 
   * Use the same placeholders as for $layout.
   *
   * @var    string 
   * @access public 
   */
  var $fallbacklayout;

  /**
   * Glue string used to separate latitude and longitude numbers
   * in concatenated element value (eg. 42.12312938###34.124812498)
   *
   * @var    string
   * @access public
   */
  var $glue = '###';

  /*
   * If you want, you can create other form fields using Clonefish
   * to get an entire address broken into separate fields.
   * When these elements are created, you can refer to them here:
   * in this case the mapMarker element will use these elements
   * to find the location using the Google API, which can then
   * be corrected using drag and drop.
   *
   * If you don't care about the address itself,
   * just leave all these fields on default. 
   *
   * If a single field is enough for you, use the 'street' setting below.
   *
   */

  var $country = 'cf_Country'; // setting defaults to non-existing
  var $state   = 'cf_State';   // DOM ids to avoid unwanted default
  var $city    = 'cf_City';    // behaviour
  var $zip     = 'cf_Zip';
  
  /**
   * The street field holds the DOM ID of the text input field 
   * used to search for a location. If this settings is null,
   * a 'throw away' textfield will be generated right above 
   * the Google Maps container. The value of the generated
   * textfield is not returned by the getValue methods.
   *
   * @var    string
   * @access public
   */
  var $street;

  var $autofocus = true;

  // -------------------------------------------------------------------------   

  /**
   * Returns form level script tags for form rendering
   * 
   * @return string  HTML code including script tags
   * @access public 
   */
  function getScripts() {

    $scripts =
      (
        $this->autocomplete ? 
          ( $this->prototypepath === null ? '' :
            '<script src="' . $this->form->_normalizePath( $this->prototypepath ) . 'prototype.js" type="text/javascript"></script>' . "\n"
          ) .
          ( $this->scriptaculouspath === null ? '' :
            '<script src="' . $this->form->_normalizePath( $this->scriptaculouspath ) . 'effects.js" type="text/javascript"></script>' . "\n" .
            '<script src="' . $this->form->_normalizePath( $this->scriptaculouspath ) . 'controls.js" type="text/javascript"></script>' . "\n"
          )
        : ''
      );
    
    switch ( $this->apiversion ) {
    
      case 3:
        $scripts .=
          '<script src="http://maps.google.com/maps/api/js?sensor=false" type="text/javascript"></script>' . "\n" .
          '<script src="' . $this->form->_normalizePath( $this->jspath ) . 'proxy/googlemap_v3.js" type="text/javascript"></script>' . "\n"
        ;
        break;
      case 2:
        $scripts .=
          '<script src="http://maps.google.com/maps?file=api&v=2&sensor=false&key=' . $this->key . '" type="text/javascript"></script>' . "\n" .
          '<script src="' . $this->form->_normalizePath( $this->jspath ) . 'proxy/googlemap_v2.js" type="text/javascript"></script>' . "\n"
        ;
        break;
      default:
        die( sprintf(
          CF_ERR_MAPMARKER_APIVERSION_UNIMPLEMENTED,
          $this->apiversion,
          $this->getName()
        ) );
        break;
     
    }

    $scripts .= '<script src="' . $this->form->_normalizePath( $this->jspath ) . 'addresschooser.js" type="text/javascript"></script>' . "\n";

    return $scripts;

  }

  // -------------------------------------------------------------------------   
  /**
   * Returns element level HTML code for form rendering
   * 
   * @return string  Element HTML code
   * @access public 
   */
  function getHTML() {

    $fallbacklayout = 
       '<div id="%id%fallbackfields">

         <label for="%id%latfield">%latitude%</label>
         <input type="text" name="%name%lat" id="%id%latfield" /><br />

         <label for="%id%lngfield">%longitude%</label>
         <input type="text" name="%name%lng" id="%id%lngfield" />

         <p>%jshelp%</p>

        </div>';

    $layout =
      '<div id="%id%container" style="display: none">' .
        (
          $this->street != null ? '' :
          '<input %html% type="text" name="%name%helper" id="%id%helperfield" autocomplete="off" />'
        ) .
        (
          $this->autocomplete ? 
          '<div id="%id%suggests" class="cfac_auto_complete" style="display: none;"></div>' 
          : ''
        ) .

        '<div id="%id%map_container">' .
        '  <div style="width: %width%px; height: %height%px; " id="%id%map"></div>' .
        '</div>' .

      '</div>'
    ;
    
    if ( $this->layout )
      $layout = $this->layout;

    if ( $this->fallbacklayout )
      $fallbacklayout = $this->fallbacklayout;

    $trans = Array(
      '%id%'        => $this->_getHTMLId(),
      '%name%'      => $this->name,
      '%latitude%'  => $this->latitude,
      '%longitude%' => $this->longitude,
      '%jshelp%'    => $this->jshelp,
      '%html%'      => $this->html,
      '%width%'     => $this->width,
      '%height%'    => $this->height
    );

    return

      '<input type="hidden" name="' . $this->name . '" id="' . $this->_getHTMLId() . '" value="' . $this->value . '" />' .

      strtr( $fallbacklayout, $trans ) .
      strtr( $layout, $trans ) .

      '<script type="text/javascript">' . "\n" .
      'clonefish_setup_addressChooser( "' . $this->_getHTMLId() . '", 
        {
          country: "' . $this->country . '",
          state:"' . $this->state . '",
          city: "' . $this->city . '",
          zip: "' . $this->zip . '",
          street: "' .
          (
            $this->street == null ?
              $this->_getHTMLId() . 'helperfield' :
              $this->street
          ) . '",
          autocomplete: ' . ( $this->autocomplete ? 'true' : 'false' ) . ',
          autofocus: ' . ( $this->autofocus ? 'true' : 'false' ) . ',
          glue: "' . $this->glue . '"
        }
      );' . "\n" .
      '</script>' . "\n"

    ;

  }

  // --------------------------------------------------------------------------
  /**
   * Sets element value
   * 
   * @return boolean  True if value was successfully set
   * @access public 
   */
  function setValue( $value, $magic_quotes_gpc ) {

    // besides the element value (coords glued together), we also maintain
    // private variables - $lat, $lng -
    // which are needed to reload the lat/lng fields

    $value = $this->_prepareInput( $value, $magic_quotes_gpc );

    // set valid coordinates only,
    // simulate 'overflow' for invalid values

    $parts = explode( $this->glue, $value );
    if (
         ( count( $parts ) == 2 ) &&
         is_numeric( $parts[0] ) && 
         is_numeric( $parts[1] ) &&
         ( $parts[0] >= -90 ) &&     // latitude:  -90deg .. 90deg
         ( $parts[0] <= 90 ) &&      // longitude: -180deg .. 180deg
         ( $parts[1] >= -180 ) &&
         ( $parts[1] <= 180 )
       ) {
      $this->value = $value;
      return true;
    }
    else
      $this->value = null;
      return false;

  }

  // -------------------------------------------------------------------------
  /**
   * Get lat/long values in an array
   * 
   * @return array    An array with indexes 'lat' and 'lng'
   * @access public 
   */
  function getValueArray( $magic_quotes_gpc ) {

    $value = Array(
      'lat' => 0,
      'lng' => 0
    ); 

    $parts = explode( $this->glue, $this->value );
    
    if ( count( $parts ) == 2 )
      $value = Array(
        'lat' => $parts[0],
        'lng' => $parts[1]
      ); 

    return $this->_prepareOutput( $value, $magic_quotes_gpc );

  }

}

?>