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
 * @subpackage elements
 */

/* 
 * Element root
 * @package clonefish
 * @subpackage elements
 */
class element {

  /**
   * Element name (label) displayed
   * @var    string
   * @access public  
   */
   var $displayname;

  /**
   * Error messages after validation
   * @var    array  
   * @access public 
   */
   var $errormessages = Array();

  /**
   * Element name (eg. &lt;input name="..."&gt;)
   * @var    unknown 
   * @access public  
   */
   var $name;

  /**
   * Element value 
   * @var    mixed
   * @access public  
   */
   var $value;

  /**
   * Element row layout to override form row layout if needed.
   * @var    string
   * @access public  
   */
   var $rowlayout;


  /**
   * Validation settings
   * @var    array  
   * @access public 
   */
   var $validation = Array();

  /**
   * Inline HTML code to inject into element HTML code (eg. style="...")
   * @var    string 
   * @access public  
   */
   var $html;

  /**
   * String to show right before the element widget
   * @var    string 
   * @access public  
   */
   var $prefix;

  /**
   * String to show right after the element widget
   * @var    string 
   * @access public  
   */
   var $postfix;

  /**
   * HTML (DOM) ID for element to override default ID, which equals element name. Use this setting to avoid conflicts in a page.
   * @var    string
   * @access public  
   */
   var $htmlid;

  /**
   * Generic element level help message if validation fails
   * @var    string
   * @access public  
   */
   var $help;

  /**
   * Whether or not element is displayed. Used when an element needs to be used in a template element.
   * @var    boolean 
   * @access public  
   */
   var $display = true;

  /**
   * Whether or not the element is read only
   * @var    boolean 
   * @access public  
   */
   var $readonly = false;

  /**
   * Message container layout for messages
   * @var    string 
   * @access public 
   */
   var $messagecontainerlayout = "%s<br />";

  /**
   * Message item layout
   * @var    string 
   * @access public 
   */
   var $messagelayout          = "%s<br />\n";

  /**
   * Flag used internally to avoid recursive validation loops
   * @var    boolean 
   * @access private
   */
   var $validating = false;  


  /**
   * Defines whether the element was already validated
   * @var    boolean 
   * @access public  
   */
   var $validated  = false;

  /**
   * Internal message cache used to avoid revalidating
   * @var    array  
   * @access private
   */
   var $validationarray = Array();

  /**
   * Defines whether the element is valid or not
   * @var    unknown 
   * @access public  
   */
   var $valid = null;
   
   var $ajax = false;
   var $ajaxevent = 'change';

  /**
   * Constructor
   * 
   * @param  string  $name         Element name
   * @param  array   $configvalues Element configuration
   * @return void    
   * @access public  
   */
  function element( $name, $configvalues ) {

    $this->name           = $name;
    foreach ( $configvalues as $key => $value ) 
      $this->$key = $value;

    if ( !is_array( $this->validation ) )
      die( sprintf( CF_ERR_CONFIG_VALIDATION_IS_NOT_AN_ARRAY_OF_ARRAYS, $this->name ) ); 

  }
  
  /**
   * Returns validation configuration. Die()s if it's not an array.
   * 
   * @return array
   * @access public 
   */
  function getValidationSettings() {

    return $this->validation;

  }

  /**
   * Renders and returns HTML row of an element.
   * 
   * @param  string  $layout        Form element row layout (HTML with placeholders)
   * @param  string  $errorstyle    Form error HTML style="..." declaration
   * @param  boolean $showerroricon Whether or not to show error icon
   * @param  string  $erroricon     Error icon HTML string
   * @return string
   * @access public  
   */
  function getHTMLRow( $layout, $errorstyle, $showerroricon, $erroricon ) {

    $errormessages = '';

    if ( count( $errormessages ) ) {

      foreach ( $this->errormessages as $message ) 
        $errormessages .= sprintf( $this->messagelayout, $message );

      $errormessages = sprintf( 
        $this->messagecontainerlayout, $errormessages 
      );

    } 

    // The template element might need to know if it contains
    // erronous elements - getHTML() sets $this->_hasErrors 
    // which is then used in next step
    $replace = Array(
      '%element%' => $this->getHTML(),
    );

    $replace = $replace + 
      Array(
        '%displayname%' => $this->displayname,
        '%errorstyle%' => ( count( $this->errormessages ) || @$this->_hasErrors ? $errorstyle : '' ),
        '%erroricon%' => ( $showerroricon && count( $this->errormessages ) ? $erroricon : '' ),
        '%prefix%' => $this->prefix,
        '%message%' => $errormessages,
        '%postfix%' => $this->postfix,
        '%errordiv%' => 
          $this->form->jshtml == true ?
          strtr( 
            $this->form->layouts[ $layout ]['errordiv'],
            Array( '%divid%' => 'cf_error' . $this->_getHTMLId() )
          ) : '',
        '%id%' => $this->_getHTMLId(),
      );

    $layoutused = $this->form->layouts[ $layout ]['element'];
    if ( $this->rowlayout )
      $layoutused = $this->rowlayout;
    if ( strtolower( $this->type ) == 'inputradio' )
      $layoutused = preg_replace('/<label.*>(.*)<\/label>/Uims', '\\1', $layoutused );

    if ( $this->display ) {
      $out = strtr( 
        $layoutused,
        $replace
      );
     
      // remove unnecessary label tag pairs
      if ( $this->form->layoutcleanup )
        $out = preg_replace('/<label[^>]*>\s*<\/label>/Uims', '', $out );

    }
    else
      $out = '';

    return $out;

  }

  /**
   * Abstract method to be implemented by descendants. Returns 
   * element HTML code.
   * 
   * @return string
   * @access public 
   */
  function getHTML() {
  }

  /**
   * Returns class name of the element.
   * 
   * @return string
   * @access public  
   */
  function getType() {
    return trim( get_class( $this ) );
  }

  /**
   * Returns element help string
   * 
   * @return string
   * @access public  
   */
  function getHelp() {
    return $this->help;
  }

  /**
   * Returns element name without trailing []
   * 
   * getName() is used by the clonefish class to find the
   * appropriate index in the incoming value array - that's why
   * we don't need the trailing [] in the name (an element
   * with a name like 'categories[]' will become an
   * array when the form is submitted: $_POST['categories']
   * 
   * @return integer Return description (if any) ...
   * @access public  
   */
  function getName() {

    if ( substr( $this->name, strlen( $this->name ) - 2 , 2 ) == '[]' ) {
      return substr( $this->name, 0, strlen( $this->name ) - 2 );
    }
    else
      return $this->name;
  }

  /**
   * Returns element name
   * 
   * @return string
   * @access public  
   */
  function getRealName() {
    return $this->name;
  }
  
  /**
   * Returns displayed name (label)
   * 
   * @return string
   * @access public  
   */
  function getDisplayName() {
    return $this->displayname;
  }

  /**
   * Returns element value with or without slashes added
   * 
   * @param  boolean $addSlashes True if slashes are needed, false otherwise
   * @return mixed
   * @access public  
   */
  function getValue( $addSlashes ) {

    return $this->_prepareOutput( $this->value, $addSlashes );

  }

  /**
   * Sets element value. Returns true on success, false on error (implemented by descendants).
   * 
   * @param  mixed   $value         Element value to set
   * @param  boolean $slashes_added Whether or not slashes are already added
   * @return boolean
   * @access public  
   */
  function setValue( $value, $slashes_added ) {

    // if the second parameter is true, we have to strip the slashes

    if ( $slashes_added )
      $value = $this->_prepareInput( $value, $slashes_added );

    $this->value = $value; 

    return true;

  }

  /**
   * Add error message to element if it's not already there
   * 
   * @param  string  $message   Error message to add
   * @return void    
   * @access public  
   */
  function addMessage( $message ) {

    if ( !in_array( $message, $this->errormessages ) ) 
      $this->errormessages[] = $message;

  }

  /**
   * Returns whether or not a string or an array value is empty.
   * 
   * @return boolean
   * @access public 
   */
  function isEmpty() {
    
    if ( is_string( $this->value ) ) 
      return strlen( $this->value ) == 0;
    
    if ( is_array( $this->value ) ) 
      return count( $this->value ) == 0;

    // returns empty value to force
    // reimplementing in child classes
    // for other element types 
    return true;

  }

  /**
   * Returns error messages in an array
   * 
   * @return array
   * @access public 
   */
  function getMessages() {
    return $this->errormessages;
  }

  /**
   * Returns HTML DOM ID of an element
   * 
   * @return string
   * @access private 
   */
  function _getHTMLId() {
    if ( strlen( $this->htmlid ) )
      return $this->htmlid;
    else
      return $this->name;
  }

  /**
   * Prepares value output: adds slashes when needed.
   * 
   * @param  mixed   $array        Scalar or array value to prepare
   * @param  boolean $addSlashes   Whether or not slashes should be added
   * @return mixed
   * @access private 
   */
  function _prepareOutput( $array, $addSlashes ) {

    if ( !$addSlashes ) 
      // no need to add slashes for output
      return $array;

    if ( is_array( $array ) ) {
      foreach ( $array as $key => $value )
        if ( !is_array( $value ) )
          $array[ $key ] = addslashes( $value );
        else
          $array[ $key ] = $this->_prepareOutput( $value, $addSlashes );
      }
    else
      $array = addslashes( $array );

    return $array;

  }

  /**
   * Prepares values when setting an element value, removes slashes when added.
   * 
   * @param  mixed   $array            Scalar or array value to prepare
   * @param  boolean $magic_quotes_gpc Whether or not slashes are already added
   * @return mixed
   * @access private 
   */
  function _prepareInput( $array, $magic_quotes_gpc ) {

    if ( !$magic_quotes_gpc )
      // no need to strip slashes for input
      return $array;
    
    if ( is_array( $array ) ) {
      foreach ( $array as $key => $value )
        if ( !is_array( $value ) ) 
          $array[ $key ] = stripslashes( $value );
        else
          $array[ $key ] = $this->_prepareInput( $value, $magic_quotes_gpc );
      }
    else
      $array = stripslashes( $array );

    return $array;

  }

  /**
   * Abstract method to be implemented by descendants. Returns HTML blocks including JavaScript needed for an element like mapMarker.
   * 
   * @return string
   * @access public 
   */
  function getScripts() {
     
    return '';
   
  }

  /**
   * Find and return settings a specific type of an element validation, false otherwise.
   * 
   * @param  string  $type    Validation type to find
   * @return mixed   Validation settings if validation was found, false otherwise
   * @access public  
   */
  function getValidation( $type ) {

    foreach ( $this->validation as $validation )
      if ( $validation['type'] == $type )
        return $validation;

    return false;

  }
  
  function getAJAXValidation() {
    
    if ( $this->ajax ) {
      
      $javascript_trans = Array(
        "\n" => '\n',
        "'"  => "\\'",
        "\t" => '\t',
      );
      
      $ajaxcode =
        "  clonefishAjaxValidation['" . $this->form->name . "'].registerEventHandler(\n" .
        "    '" . $this->ajaxevent . "', " .
             "function() { this.startRequest('" . $this->name . "', '" . $this->_getHTMLId() . "'); }, " .
             "'" . $this->_getHTMLId() . "'\n" .
        "  );\n" .
        "  clonefishAjaxValidation['" . $this->form->name . "'].registerEventHandler(\n" .
        "    'success', " .
             "function( data, xhr ) { check_" . $this->form->name . "( data, '" . $this->name . "', '" . $this->_getHTMLId() . "' ); }, " .
             "'" . $this->_getHTMLId() . "'\n" .
        "  );\n"
      ;
      
    } 
    else 
      $ajaxcode = '';
    
    return $ajaxcode;
    
  }

}

?>
