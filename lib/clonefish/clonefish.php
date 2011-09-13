<?php

/**
 * Clonefish form generator class
 * (c) phpformclass.com, Dots Amazing
 * All rights reserved.
 * 
 * @copyright 2010 Dots Amazing
 * @version   v2.2, 2010-10-21
 * @link      http://phpformclass.com
 * @package   clonefish
 */

/**
 * Clonefish class - the main class representing a form instance
 * @package clonefish
 */
class clonefish {

  /**
   * <form name="...">
   * @var    string 
   * @access public 
   */
  var $name;

  /**
   * <form id="..."> - takes form name as default, may
   * be overriden to avoid conflicts.
   *
   * @var    string
   * @access public  
   */
  var $id;

  /**
   * <form action="...">
   * @var    string 
   * @access public  
   */
  var $action;

  /**
   * <form method="...">
   * @var    string 
   * @access public  
   */
  var $method;

  /**
   * Codepage for proper string validation: 'utf-8', for example.
   * @var    string
   * @access public  
   */
  var $codepage;

  /**
   * The name of the chosen multibyte support, an array index of $multibytesetup.
   * @var    string 
   * @access public 
   */
  var $multibytesupport = 'multibyteutf8';

  /**
   * This array defines the multibyte supporting functions for
   * different installations and codepage combinations.
   * @var    array  
   * @access public 
   */
  var $multibytesetup   = Array(
    'none' => Array( // Single byte charsets only! Affected by setlocale.
      'strlen'   => 'strlen( "%s" )',
      'regexp'   => 'preg_match( "%s", "%s" )', 
    ),
    'multibyteutf8' => Array( // Best bet for utf-8, uses preg!
      'strlen'   => 'mb_strlen( \'%s\' )',
      'regexp'   => 'preg_match( \'%su\', \'%s\' )',     // utf-8 only
      'encoding' => 'mb_internal_encoding( \'%1$s\' ) && mb_regex_encoding( \'%1$s\' )'
    ),
    'multibyte' => Array( // Even for multibyte charsets, uses ereg
      'strlen'   => 'mb_strlen( \'%s\' )',
      'regexp'   => 'mb_ereg( \'%s\', \'%s\', $reg )',          // ereg itself is deprecated
      'encoding' => 'mb_internal_encoding( \'%1$s\' ) && mb_regex_encoding( \'%1$s\' )'
    ),
    'multibyteci' => Array( // Even for multibyte charsets, uses case insensitive ereg
      'strlen'   => 'mb_strlen( \'%s\' )',
      'regexp'   => 'mb_eregi( \'%s\', \'%s\', $reg )',        // ereg itself is deprecated
      'encoding' => 'mb_internal_encoding( \'%1$s\' ) && mb_regex_encoding( \'%1$s\' )'
    ),
    'iconv' => Array( // Single byte charsets only, if mb_* support is missing
      'strlen'   => 'iconv_strlen( \'%s\' )',
      'regexp'   => 'preg_match( \'%s\', \'%s\' )',
      'encoding' => 'iconv_set_encoding( \'internal_encoding\', \'%s\' )'
    ),
    'iconvutf8' => Array( // UTF-8 only, if mb_* support is missing
      'strlen'   => 'iconv_strlen( \'%s\' )',
      'regexp'   => 'preg_match( \'%su\', \'%s\' )',
      'encoding' => 'iconv_set_encoding( \'internal_encoding\', \'%s\' )'
    )
  );

  /**
   * This array holds the element objects.
   * @var    array  
   * @access private
   */
  var $elements = Array();

  /**
   * Path (URL) to clonefish.js file including the filename.
   * @var    string 
   * @access public 
   */
  var $jspath  = 'clonefish/clonefish.js';

  /**
   * Enable or disable JavaScript in the form.
   * @var    integer 
   * @access public  
   */
  var $js      = 1;

  // ERROR MESSAGE SETTINGS FOR CLIENT SIDE ERRORS
  /**
   * Turn JavaScript alert form messages on/off.
   * @var    integer 
   * @access public  
   */
  var $jsalert = 1;

  /**
   * Customize JS alert form message container, where %s is the placeholder
   * of the messages.
   * @var    string 
   * @access public 
   */
  var $jsalertmessagecontainerlayout = '%s';

  /**
   * Customize JS alert form message item rows, where %s is the placeholder
   * of the message.
   * @var    string 
   * @access public 
   */
  var $jsalertmessagelayout          = '- %s\n';

  /**
   * Turn JavaScript HTML inline error messages on/off.
   * @var    integer 
   * @access public  
   */
  var $jshtml  = 1;

  /**
   * JS HTML inline error message container string, where %s is the 
   * placeholder of the concatenated messages.
   * @var    string 
   * @access public 
   */
  var $jshtmlmessagecontainerlayout = '%s';

  /**
   * JS HTML inline error message item row, where %s is the placeholder of
   * the message. Multiple error messages are concatenated and the
   * result is inserted into jshtmlmessagecontainerlayout container above.
   * @var    string 
   * @access public 
   */
  var $jshtmlmessagelayout          = '%s<br />';

  // ERROR MESSAGE SETTINGS FOR SERVER SIDE ERRORS

  /**
   * This array holds the error messages of the form after a 
   * $clonefish->validate() or $clonefish->validateElement() call. 
   * @var    array  
   * @access public 
   */
  var $messages        = Array();

  /**
   * Holds separate HTML error message strings after a $clonefish->validate() or 
   * $clonefish->validateElement() call. The messagelayout property is used
   * to render the HTML format.
   *
   * @var    array  
   * @access public 
   */
  var $messageoutput   = Array();

  /**
   * Enable or disable error message output
   * @var    boolean
   * @access public  
   */
  var $outputmessages  = true;

  /**
   * String displayed above error messages. Set to CF_STR_FORM_ERRORS
   * by the contructor (CF_STR_FORM_ERRORS is defined in messages_XX.php)
   * @var    string 
   * @access public 
   */
  var $messageprefix   = '';

  /**
   * The HTML container for error messages rendered above the form.
   * @var    string 
   * @access public 
   */
  var $messagecontainerlayout = "<ul>%s</ul>\n";

  /**
   * The HTML container for a single error message. Multiple instances
   * are concatenated and the result is inserted into messagecontainerlayout.
   * @var    string 
   * @access public 
   */
  var $messagelayout          = "<li>%s</li>\n";

  /**
   * A string displayed below error messages
   * @var    string 
   * @access public 
   */
  var $messagepostfix  = '';

  /**
   * Display error icon or not (find example use of error icon in the
   * tabular form layout)
   * @var    boolean
   * @access public  
   */
  var $showerroricon   = true;

  /**
   * Inline error style used when form validation failed on the server side
   * @var    string 
   * @access public 
   */
  var $errorstyle      = ' style="background-color: #e95724; color: black; " ';

  /**
   * Error icon IMG tag used in layout rows where element validation failed
   * @var    string 
   * @access public 
   */
  var $erroricon       = '<img src="images/error.gif" />';

  // FORM LAYOUT SETTINGS

  /**
   * String displayed above the form
   * @var    string 
   * @access public 
   */
  var $prefix          = '';

  /**
   * String displayed below the form
   * @var    string 
   * @access public 
   */
  var $postfix         = ''; 

  /**
   * Opening form tag. 
   * Placeholders are: %target%, %id%, %name%, %action%, 
   * %onsubmit% and %method%. All placeholders except onsubmit get
   * replaced with their respective attributes (eg. %target% will contain
   * $clonefish->target). %onsubmit% is rendered as onsubmit="return ..." 
   * by Clonefish when $clonefish->js is set to true. This call is needed
   * to provide proper form handling and fallback for browsers with disabled 
   * JavaScript.
   * @var    string 
   * @access public 
   */
  var $formopenlayout  = "<form enctype=\"multipart/form-data\" target=\"%target%\" id=\"%id%\" name=\"%name%\" action=\"%action%\" %onsubmit% method=\"%method%\">\n";

  /**
   * Closing form tag.
   * @var    string 
   * @access public 
   */
  var $formcloselayout = "</form>\n";

  /**
   * <form target="...">
   * @var    string 
   * @access public 
   */
  var $target = "_self";

  /**
   * JavaScript code executed right before the form gets submitted.
   * Will not execute when the browser has JavaScript turned off.
   * @var    string 
   * @access public 
   */
  var $onbeforesubmit  = '';

  /**
   * The label of the submit button.
   * @var    string 
   * @access public 
   */
  var $submit = 'OK';

  /**
   * Setting to disable displaying the submit button. 
   * When set to true, the entire buttonrow is not displayed.
   * @var    boolean 
   * @access public  
   */
  var $nosubmit = false;

  // ELEMENT LAYOUT SETTINGS

  /**
   * This array holds the HTML snippets used to render the form elements. 
   * @var    array  
   * @access public 
   */
  var $layouts = 

    Array(

      'tabular' => 
        Array(
          'container'  => "<table cellpadding=\"5\" cellspacing=\"0\" border=\"0\">\n%s\n</table>\n",
          'element'    => "<tr %errorstyle%><td width=\"120\" align=\"right\"><label for=\"%id%\">%displayname%</label></td><td width=\"15\">%erroricon%</td><td>%prefix%%element%%postfix%%errordiv%</td></tr>\n",
          'errordiv'   => '<div id="%divid%" style="display: none; visibility: hidden; padding: 2px 5px 2px 5px; background-color: #d03030; color: white;"></div>',
          'buttonrow'  => '<tr><td colspan="2"></td><td>%s</td></tr>',
          'button'     => '<input type="submit" value="%s" />',
        ),

      'rowbyrow' => 
        Array(
          'container'  => '%s',
          'element'    => "<label for=\"%id%\">%displayname%</label> %erroricon%<br />\n%prefix%%element%%postfix%%errordiv%\n<br /><br />\n",
          'errordiv'   => '<div id="%divid%" style="display: none; visibility: hidden; padding: 2px 5px 2px 5px; background-color: #d03030; color: white;"></div>',
          'buttonrow'  => '%s',
          'button'     => '<input type="submit" value="%s" />',
        ),

    )
  ;

  // MISCELLANEOUS

  /**
   * The identifier the selected layout - an array index of $clonefish->layouts.
   * @var    string 
   * @access public 
   */
  var $layout         = 'tabular';

  /**
   * When true, Clonefish cleans up empty label tags.
   * (When an element displayname is empty, HTML code 
   * like <label for="xxx"></label> may be generated because of
   * the use of layout template strings.)
   * @var    boolean 
   * @access public  
   */
  var $layoutcleanup  = true; 

  /**
   * A reference to the database wrapper. Should be set through Clonefish constructor.
   * @var    object 
   * @access public 
   */
  var $db;

  /**
   * Database type. Should be set through Clonefish constructor.
   * Determines filename and classname too.
   * To add a new wrapper, create a file named dbwrapper_DBTYPE.php and
   * a class within named DBWrapperDBTYPE.
   * @var    string 
   * @access public
   */
  var $dbtype;

  /**
   * A callback function which may be used to alter values in loaded config files.
   * The configfilter callback is useful when there's a need to
   * have dynamic values in .ini files. 
   *
   * Usage:
   * - Use any kind of placeholders in your .ini files 
   *   like <input type="submit" value="l('okbuttonlabel')" />
   *   or <img src="{STATIC_DOMAIN}/images/button.png" />)
   * - specify a callback function which takes a single string parameter 
   *   and replaces placeholders to the appropriate replacement values, 
   *   for example: 
   *   <pre>
   *     function configFilter( $value ) { 
   *       $trans = Array( 
   *         '{STATIC_DOMAIN}' => 'http://static.example.com' 
   *       );
   *       return strtr( $value, $trans );
   *     }
   *   </pre>
   *
   * @var    string
   * @access public  
   */
  var $configfilter;

  /**
   * Set this flag to true to invalidate the form.
   * This flag may be used to invalidate the form when some
   * circumstances outside the scope of Clonefish makes the 
   * form invalid and form submission is unwanted. 
   * For example
   * when a form includes dynamic (database-backed) elements, but
   * the required database connection can't be established, you may 
   * invalidate the form this way.
   * This property shouldn't be used as a replacement for 
   *
   * @var    boolean
   * @access public  
   */
  var $invalidated = false;

  /**
   * Clonefish constructor to create form object.
   * 
   * @param  string  $name   &lt;form name="..."&gt;
   * @param  string  $action &lt;form action="..."&gt;
   * @param  string  $method &lt;form method="..."&gt;
   * @param  mixed   $db     Database resource/object (optional)
   * @param  string  $dbtype Database wrapper matching $db (optional)
   * @param  mixed   $refid  Reference ID of form to use in dynamic selects for example
   * @return void    
   * @access public  
   */
  function clonefish( $name, $action, $method, $db = null, $dbtype = 'mysql', $refid = null ) {

    $this->name   = $name;
    $this->id     = $name;
    $this->action = $action;
    $this->method = $method;
    $this->refid  = $refid;
    $this->dbtype = $dbtype;

    $this->messageprefix = CF_STR_FORM_ERRORS;

    if ( !defined( 'CLONEFISH_DIR' ) )  

  /**
   * Clonefish filesystem path for includes.
   */
      define('CLONEFISH_DIR', dirname( __FILE__ ) . DIRECTORY_SEPARATOR );

    include_once( CLONEFISH_DIR . 'constants.php');
    include_once( CLONEFISH_DIR . 'element.php');
    include_once( CLONEFISH_DIR . 'validation.php');

    if ( $this->dbtype ) {

      // we should have a wrapper object available
      if ( file_exists( CLONEFISH_DIR . 'dbwrapper_' . strtolower( $this->dbtype ) . '.php' ) ) {
        include_once( CLONEFISH_DIR . 'dbwrapper_' . strtolower( $this->dbtype ) . '.php');
        $classname    = 'DBWrapper' . $this->dbtype;
        $this->db     = new $classname( $db );
      }
      else
        die( sprintf( CF_ERR_DBWRAPPER_DBTYPE_UNKNOWN, $this->dbtype ) );

    }

  }

  /**
   * Adds input elements to form through a configuration array
   * and also an array holding submitted values.
   * 
   * @param  array   $elements      Configuration array
   * @param  mixed   $values        Array of submitted values (eg. $_POST, $_GET) if any, false otherwise (default)
   * @param  boolean $slashes_added Required if $values is used. Specifies whether $values are magic quoted. Learn about and use get_magic_quotes_gpc() here if values are not treated otherwise.
   * @return void    
   * @access public  
   */
  function addElements( $elements, $values = false, $slashes_added = null ) {

    if ( ( $values !== false ) && ( $slashes_added === null ) )
      die( sprintf( CF_ERR_MISSING_SLASHES_ADDED_PARAMETER, 'addElements()' ) );

    foreach ( $elements as $key => $value ) {

      if ( !isset( $value['type'] ) ) 
        die( sprintf( CF_ERR_CONFIG_TYPE_MISSING, $key ) );

      if ( 
           isset( $value['validation'] ) &&
           !empty( $values['validation'] ) &&
           !is_array( current( $value['validation'] ) )
         )
        die( 
          sprintf( CF_ERR_CONFIG_VALIDATION_NOT_AN_ARRAY_OF_ARRAYS, $key ) 
        );

      $elementtype = strtolower( $value['type'] );

      $classfile = 
        CLONEFISH_DIR . 'element.' . $elementtype . '.php';

      if ( file_exists( $classfile ) ) {
        include_once( $classfile );
        $element = new $value['type']( $key, $value, $this->db, $this->refid );
        $element->form = &$this;
      }
      else
        die( sprintf( CF_ERR_CONFIG_TYPE_UNSUPPORTED, $key, $value['type'], $classfile ) );

      if (
           is_array( $values ) &&
           count( $values ) &&
           !$element->readonly
         ) {

        // array hack: associative arrays

        if (
             preg_match( '/^([^\[\]]+)((\[([^\[\]]*)\])+)$/', $element->name, $parts ) &&
             preg_match_all( '/\[([^\[\]]+)\]/', $element->name, $indexes ) 
           ) {

          if ( isset( $values[ $parts[1] ] ) ) {

            $arrayvalue = $values[ $parts[1] ];
            // remove empty [] from the end of the name
            $parts[2] = preg_replace('/(\[\])+$/', '', $parts[2] );
            // add quotes: [name] => ['name']
            $parts[2] = preg_replace('/\[(.*\D.*)\]/U', '[\'\1\']', $parts[2] );

            // avoid indexes being not numeric, but string in fact,
            // for example: 00000002
            if (
                 (
                   strlen( trim( $parts[2], '[]' ) ) !=
                   strlen( (int) trim( $parts[2], '[]' ) )
                 ) &&
                 !preg_match( '/^\'.+\'$/', trim( $parts[2], '[]' ) )
               ) {
              // add quotes: [name] => ['name']
              $parts[2] = preg_replace('/\[(.*)\]/U', '[\'\1\']', $parts[2] );
            }

            $code = '$found = @$arrayvalue' . $parts[2] . ';';
            eval( $code );

            if ( $found !== false )
              $element->setValue( $found, $slashes_added );

          }

        }

        // array hack: [] arrays
        elseif ( preg_match( '/^(.+)\[\]$/', $element->name, $parts ) ) {
          if ( is_array( @$values[ $parts[1] ] ) )
            $element->setValue( $values[ $parts[1] ], $slashes_added );
        }
        elseif ( isset( $values[ $element->getName() ] ) ) {

          // normal case: we've received a value

          switch ( $elementtype ) {

            case 'mapmarker':

              if (
                   !isset( $values[ $element->getName() . 'lat' ] ) &&
                   !isset( $values[ $element->getName() . 'lng' ] )
                 )
                // only the value itself was given: it must be
                // a date in compiled format, like when 
                // a form is created from a database record
                $element->setValue(
                  $values[ $element->getName() ], $slashes_added 
                );
              else {
                $element->setValue(
                  @$values[ $element->getName() . 'lat' ] .
                  $element->glue .
                  @$values[ $element->getName() . 'lng' ],
                  $slashes_added
                );
              }
              break;

            case 'selectdate':

            // selectdate parts needs to be 'compiled' into a date.
            // we cannot trust the attached hidden field, as it's
            // created by JS, which can be disabled

            if (
                 !isset( $values[ $element->getName() . 'year' ] ) &&
                 !isset( $values[ $element->getName() . 'month' ] ) &&
                 !isset( $values[ $element->getName() . 'day' ] ) &&
                 !isset( $values[ $element->getName() . 'hour' ] ) &&
                 !isset( $values[ $element->getName() . 'min' ] ) &&
                 !isset( $values[ $element->getName() . 'sec' ] )
               ) {
              // only the value itself was given: it must be
              // a date in compiled format, like when 
              // a form is created from a database record
              $element->setValue(
                $values[ $element->getName() ], $slashes_added 
              );
            }
            else
              // also received at least one helper part:
              // then we have a form submitted at hand, so
              // we need to compile the date (if there's
              // no JS support, we will receive only an empty hidden
              // field, that's why we cannot trust that)

              $element->createStoredFormat( 
                @$values[ $element->getName() . 'year'  ],
                @$values[ $element->getName() . 'month' ],
                @$values[ $element->getName() . 'day'   ],
                @$values[ $element->getName() . 'hour'  ],
                @$values[ $element->getName() . 'min'   ],
                @$values[ $element->getName() . 'sec'   ],
                $slashes_added 
              );

              break;

            default:
              // all the other inputs
              $element->setValue( $values[ $element->getName() ], $slashes_added );
              break;

          }
        }
        else {
          // checkboxes are missing from $_POST/$_GET if 
          // they're unchecked
          if ( $elementtype == 'inputcheckbox' )
            $element->setValue( $element->offvalue, 0 );
          if ( $elementtype == 'inputcheckboxdynamic' )
            $element->setValue( Array(), 0 );
        }
      }
      else
        if ( isset( $value['value'] ) )
          $element->setValue( $value['value'], 0 );

      $this->addElement( $element );
      unset( $element );

    }

  }

  /**
   * Adds an element to the form.
   * 
   * @param  object $object A clonefish element object
   * @return void    
   * @access public  
   */
  function addElement( $object ) {

    $this->elements[] = &$object;

  }

  /**
   * Creates JavaScript code block string.
   * 
   * @return string  JavaScript code block including script open/close tags.
   * @access public 
   */
  function getValidationJSCode() {
    
    $code     = '';
    $ajaxcode = '';
    $message  = '';

    foreach ( $this->elements as $key => $object ) {

      $validationsettings = $this->elements[ $key ]->getValidationSettings();
      $ajaxcode .= $this->elements[ $key ]->getAJAXValidation();

      foreach ( $validationsettings as $vskey => $validationparameters ) {

        if ( ( $vskey === 'anddepend' ) || ( $vskey === 'ordepend' ) )
          continue;

        if ( !is_array( $validationparameters ) ) 
          die( sprintf( CF_ERR_CONFIG_VALIDATION_NOT_AN_ARRAY_OF_ARRAYS, $object->getName() ) );

        $validationparameters['form'] = &$this;
        if ( !isset( $validationparameters['type'] ) ) 
          die( sprintf( CF_ERR_CONFIG_VALIDATION_TYPE_MISSING, $object->getName() ) );

        // inherit dependancy parameters to each validation
        if ( isset( $validationsettings['anddepend'] ) )
          $validationparameters['anddepend'] = $validationsettings['anddepend'];

        if ( isset( $validationsettings['ordepend'] ) )
          $validationparameters['ordepend'] = $validationsettings['ordepend'];
        
        switch ( $validationparameters['type'] ) {
        
          case 'required':

            $validator = &$this->_getRequiredClassFor( 
              $this->elements[ $key ], $validationparameters
            );
            
            $code     .= $validator->getJSCode();
            break;

          default:

            $classfile = 
              CLONEFISH_DIR . 'validation.' . strtolower( $validationparameters['type'] ) . '.php';
            
            if ( file_exists( $classfile ) ) {
              include_once( $classfile );
              $class     = $validationparameters['type'] . 'Validation';
              $validator = new $class( $validationparameters, $this->elements[ $key ] );
              $code     .= $validator->getJSCode();
            }
            else
              die( sprintf( CF_ERR_JSVALIDATOR_UNSUPPORTED, $object->getName(), $validationparameters['type'] ) ); 
            
            break;

        }

      }

    }

    $code             = preg_replace('/^(.*)$/Umsi', '    \\1', $code );
    $javascript_trans = Array(
      "\n" => '\n',
      "'"  => "\\'",
      "\t" => '\t',
    );

    return
      "<!--\n" .
      "\n" .
      "function check_" . $this->name . "() {\n" .
      "\n" .
      "  errors = new clonefishErrors();\n" .
      "  errors.useAlert = " . ( $this->jsalert ? 'true' : 'false' ) . ";\n" .
      "  errors.useHTML  = " . ( $this->jshtml  ? 'true' : 'false' ) . ";\n" .
      ( $this->jshtml ?
        "  errors.messageContainerLayout      = '" . strtr( $this->jshtmlmessagecontainerlayout, $javascript_trans ) . "';\n" .
        "  errors.messageLayout               = '" . strtr( $this->jshtmlmessagelayout, $javascript_trans ) . "';\n"
      : "" ) .
      ( $this->jsalert ?
        "  errors.alertMessageContainerLayout = '" . strtr( $this->jsalertmessagecontainerlayout, $javascript_trans ) . "';\n" .
        "  errors.alertMessageLayout          = '" . strtr( $this->jsalertmessagelayout, $javascript_trans ) . "';\n" 
      : "" ) .
      "\n" .
      "  // validation code\n" .
      $code .
      "\n" .
      "  // show messages if needed\n" . 
      "  errors.go();\n" .
      "\n" .
      "  if ( !errors.empty )\n" .
      "    return false;\n" .
      "  else {\n" .
      (
        strlen( $this->onbeforesubmit ) ? 
          "    // onbeforesubmit code\n" .
          $this->onbeforesubmit 
        : 
          "" 
      ) .
      "    return true;\n" .
      "  }\n" .
      "\n" .
      "}\n" .
      ( strlen( $ajaxcode ) ?
        "\n" .
        "// AJAX request object setup\n" .
        "clonefishAjaxValidation['" . $this->name . "'] = new clonefishAjaxRequest({\n" .
        "  url:     '" . $this->ajaxurl . "',\n" .
        "  formid:  '" . $this->name . "'\n" .
        "});\n" .
        "\n" .
        "// Event handlers for AJAX validation\n" .
        "clonefishOnLoad( function() {\n" .
        "\n" .
        $ajaxcode . "\n" .
        "});\n" .
        "\n" 
        : "" 
      ) .
      "// -->\n"
    ;

  }

  /**
   * Return form components in an array to be used in template 
   * engines like Smarty.
   * 
   * @return array  An array of components: formopen, formclose, fields (array), submit, messageslayout, script
   * @access public 
   */
  function getVars() {

    $messages         = $this->messages;
    $return           = Array();
    $return['fields'] = Array();
    $javascripts      = Array();

    foreach ( $this->elements as $key => $object ) {

      $return['fields'][ $this->elements[ $key ]->getName() ] = 
        $this->elements[ $key ]->getHTMLRow( 
          $this->layout, 
          $this->errorstyle, 
          $this->showerroricon, 
          $this->erroricon
        );
      
      $messages = array_merge( 
        $messages, 
        $this->elements[ $key ]->getMessages() 
      );
    
      // form level scripts are only needed once per element type
      $javascripts[ $this->elements[ $key ]->type ] = 
        $this->elements[ $key ]->getScripts();
      
    }

    $fields = implode('', $return['fields'] );

    $replace = Array(
       '%id%'     => $this->id,
       '%name%'   => $this->name,
       '%action%' => $this->action,
       '%method%' => $this->method,
       '%target%' => $this->target,
       '%onsubmit%' => $this->js ? " onsubmit=\"return check_" . $this->name . "();\" " : "" 
    );

    $return['formopen']  = $this->prefix . strtr( $this->formopenlayout, $replace );
    $return['formclose'] = $this->formcloselayout . $this->postfix;
    $return['submit']    = 
          sprintf(
        $this->layouts[ $this->layout ]['buttonrow'], 
        sprintf( $this->layouts[ $this->layout ]['button'],
          $this->submit 
        )
      );

    $messages = array_unique( $messages ); 
    $return['messageslayout'] = '';
    $this->messageoutput = array_merge( $this->messageoutput, $messages );
    if ( $this->outputmessages && count( $this->messageoutput ) ) {
      $allmessages = '';
      foreach ( $this->messageoutput as $onemessage )
        $allmessages .= sprintf( $this->messagelayout, $onemessage );

      $return['messageslayout'] .= 
        $this->messageprefix . 
        sprintf( $this->messagecontainerlayout, $allmessages ) .
        $this->messagepostfix;
        
    }

    $return['messages'] = $this->messageoutput;

    $return['script'] =
      implode( '', $javascripts ) .
      '<script src="' . $this->jspath . '" type="text/javascript"></script>' .
      '<script type="text/javascript">' . "\n" . 
        $this->getValidationJSCode() . 
      '</script>';

    return $return;

  }

  /**
   * Renders entire HTML+JS code block for the form based on layout settings.
   * 
   * @return mixed  HTML code block including JavaScript.
   * @access public 
   */
  function getHTML() {

    // hidden fields are collected first
    // and are put before the form table
    // so it won't break the html code of the table

    $fields       = '';
    $hiddenfields = '';
    $messages = $this->messages;

    // the following foreach will
    //   1) collect field html data
    //   2) if a fieldset element is 
    //        found, closes previous fieldset (if there's any)
    //        and starts collecting fields into the current
    //        fieldset's content
    //   3) place the submit button inside the fieldset if
    //        the $fieldset_element->submit property is set to 1
    //   4) will only display the submit button below the form if
    //        it was not previously printed with a fieldset element

    $currentfieldset = false;
    $collect         = '';
    $postfix         = '';
    $submitbutton    =
      sprintf(
        $this->layouts[ $this->layout ]['buttonrow'], 
        sprintf( $this->layouts[ $this->layout ]['button'],
          $this->submit 
        )
      )
    ;
    
    $submitprinted = 0;
    $fieldsetcount = null;
    $javascripts   = Array();

    foreach ( $this->elements as $key => $object ) {

      if ( strtolower( $this->elements[ $key ]->type ) != 'inputhidden' ) {

        if ( ( $fieldsetcount !== null ) && ( $fieldsetcount >= 0 ) )
           $fieldsetcount--;

        if ( is_object( $currentfieldset ) && ( $fieldsetcount === -1 ) ) {

          // the field counter for this fieldset is now empty,
          // let's flush the current fieldset

          $postfix = $currentfieldset->submit ? $submitbutton : '';

          $fields .=
            sprintf(
              $currentfieldset->getHTMLRow( $this->layout, $this->errorstyle, $this->showerroricon, $this->erroricon ),
              sprintf(
                $this->layouts[ $this->layout ]['container'] . "\n",
                $collect . $postfix
              )
            );
          $currentfieldset = false;
          $fieldsetcount   = false;
          $collect         = '';
          $postfix         = '';

        }

        if ( $this->elements[ $key ]->type == 'fieldset' ) {

          $fieldsetcount =
            $this->elements[ $key ]->value > 0 ?
              $this->elements[ $key ]->value : false
          ;

          if ( is_object( $currentfieldset ) ) {

            // we've found a fieldset that's running

            if ( $currentfieldset->submit ) {
              $postfix = $submitbutton;
              $submitprinted = 1;
            }
            else
              $postfix = '';

            // let's flush the running fieldset 

            $fields .=
              sprintf(
                $currentfieldset->getHTMLRow( 
                  $this->layout, $this->errorstyle, $this->showerroricon, $this->erroricon 
                ),
                sprintf(
                  $this->layouts[ $this->layout ]['container'] . "\n",
                  $collect . $postfix 
                )
              );
          }
          else
            // we've found a fieldset after some fields: flush
            // current fields 
            if ( strlen( $collect ) )
            $fields .= 
              sprintf(
                $this->layouts[ $this->layout ]['container'] . "\n",
                $collect . $postfix 
              )
            ;
          
          // we're starting the current (or new) fieldset
          $currentfieldset = $this->elements[ $key ];
          $collect = '';

        }
        else
          // not a fieldset: store
          $collect .= $this->elements[ $key ]->getHTMLRow( $this->layout, $this->errorstyle, $this->showerroricon, $this->erroricon );
      }
      else
        $hiddenfields .= $this->elements[ $key ]->getHTMLRow( $this->layout, $this->errorstyle, $this->showerroricon, $this->erroricon );

      $javascripts[ $this->elements[ $key ]->type ] = 
        $this->elements[ $key ]->getScripts();

      $messages =
        array_merge( $messages, $this->elements[ $key ]->getMessages() );
    }

    // we've finished: flush fieldset

    if ( is_object( $currentfieldset ) ) {

      $postfix = '';
      if ( $currentfieldset->submit ) {
        $postfix = $submitbutton;
        $submitprinted = 1;
      }

      $fields .= 
        sprintf( 
          $currentfieldset->getHTMLRow( $this->layout, $this->errorstyle, $this->showerroricon, $this->erroricon ),
          sprintf(
            $this->layouts[ $this->layout ]['container'],
            $collect . $postfix
          ) 
        );

      $collect = '';

    }
      
    if ( strlen( $collect ) || !$submitprinted ) 
      $fields .= 
        sprintf(
          $this->layouts[ $this->layout ]['container'] . "\n",
          $collect .
            ( $submitprinted || $this->nosubmit ? '' : $submitbutton )
        )
      ;

    $messages = array_unique( $messages );
    $validationcode = $this->getValidationJSCode();

    $replace = Array(
       '%id%'       => $this->id,
       '%name%'     => $this->name,
       '%action%'   => $this->action,
       '%target%'   => $this->target,
       '%method%'   => $this->method,
       '%onsubmit%' => $this->js ? " onsubmit=\"return check_" . $this->name . "();\" " : "" 
    );

    // form open tag
    $out = $this->prefix . strtr( $this->formopenlayout, $replace );

    // messages
    $this->messageoutput = array_merge( $this->messageoutput, $messages );
    if ( $this->outputmessages && count( $this->messageoutput ) ) {
      $allmessages = '';
      foreach ( $this->messageoutput as $onemessage )
        $allmessages .= sprintf( $this->messagelayout, $onemessage );

      $out .= 
        $this->messageprefix . 
        sprintf( $this->messagecontainerlayout, $allmessages ) .
        $this->messagepostfix;
        
    }

    // fields and closing tag
    $out .= 
      implode( '', $javascripts ) .
      $hiddenfields . 
      $fields .
      $this->formcloselayout . 
      $this->postfix .
      "\n"
    ;

    return
      ( $this->js ?
      "<script src=\"" . $this->jspath . "\" type=\"text/javascript\"></script>\n" .
      "<script type=\"text/javascript\">" . $validationcode . "</script>\n" 
      : ""
      ) .
      $out;

  }

  /**
   * Returns a cross-browser JavaScript reference string to the form
   * in the DOM.
   * 
   * @return string
   * @access public 
   */
  function getJSName() {
    return 'document.forms["'. $this->name .'"]';
  }
  
  /**
   * Returns the name of the form.
   * 
   * @return string
   * @access public 
   */
  function getName() {
    return $this->name;
  }

  /**
   * Removes an element from the form.
   * 
   * @param  string  $field Name of the element
   * @return boolean True if the element was found and removed, false otherwise.
   * @access public  
   */
  function removeElement( $field ) {
  
    foreach ( $this->elements as $key => $object ) {
      if ( $this->elements[ $key ]->getRealName() === $field ) {
        unset( $this->elements[ $key ] );
        return true;
      }
    }

    return false;
    
  }  

  // -------------------------------------------------------------------------


  /**
   * Searches and returns a form element.
   * 
   * @param  string $field Element name
   * @return mixed  Element object on success, false otherwise
   * @access public  
   */
  function &getElementByName( $field ) {
  
    foreach ( $this->elements as $key => $object ) {
      if ( $this->elements[ $key ]->getRealName() === $field ) {
        $object = &$this->elements[ $key ];
        return $object;
      }
    }

    $notfound = false;
    return $notfound;

  }  

  // -------------------------------------------------------------------------


  /**
   * Add an error message to the form
   * 
   * @param  string $message Error message
   * @return void    
   * @access public  
   */
  function addMessage( $message ) {

    if ( !in_array( $message, $this->messages ) )
      $this->messages[] = $message;

  }  

  // -------------------------------------------------------------------------


  /**
   * Get error messages as an array. To be used after validation.
   * 
   * @return array  Error messages
   * @access public 
   */
  function getMessages() {

    $messages = $this->messages;

    foreach ( $this->elements as $key => $object )

      $messages =
        array_merge( $messages, $this->elements[ $key ]->getMessages() );

    return $messages;

  }

  // -------------------------------------------------------------------------


  /**
   * Get the form element values as an array.
   * 
   * @param  boolean $addSlashes  Whether or not quoting needed
   * @param  boolean $asArray     Return multidimensional array structures (eg. options['a'][]) as arrays. Optional, false by default.
   * @return array   An array of values.
   * @access public  
   */
  function getElementValues( $addSlashes, $asArray = false ) {

    $values = Array();
    $arrays = Array();

    foreach ( $this->elements as $key => $object ) {
      if ( 
           !in_array( $this->elements[ $key ]->type, 
              Array( 'fieldset', 'text', 'template' ) 
           )
         )
        if (
             $asArray &&
             preg_match_all( '/^([^\[]+)(\[.*\])+$/', $this->elements[ $key ]->getName(), $results )
           ) {

          $results[2][0] = preg_replace( '/\[([^\[\]]*)\]/', '[\'\\1\']', $results[2][0] );
          $cmd = '
             $arrays[\'' . $results[1][0] . '\']' . $results[2][0] . ' = 
              $this->elements[ $key ]->getValue( $addSlashes );
            ';

          eval( $cmd );

        }
        else
        $values[ $this->elements[ $key ]->getName() ] =
          $this->elements[ $key ]->getValue( $addSlashes );
    }

    if ( $asArray )
      foreach ( $arrays as $key => $array ) 
        $values[ $key ] = $array;

    return $values;

  }

  /**
   * Invalidate the form.
   * 
   * @return void
   * @access public 
   */
  function invalidate() {
    $this->invalidated = 1;
  }

  /**
   * Validate all elements in the form.
   * 
   * @return boolean  True on successful validation, false otherwise.
   * @access public 
   */
  function validate() {

    $valid   = Array();

    foreach ( $this->elements as $key => $object ) {

      // never use $object here because as that's only a 
      // _copy_ of the object

      $valid = array_merge(
        $valid,
        $this->validateElement( null, $this->elements[ $key ] )
      );

    }

    return $this->invalidated ? false : count( $valid ) == 0;

  }

  /**
   * Validate a single element.
   * 
   * @param  mixed   $name     Element name or null (optional if $element is used)
   * @param  object  $element  Element object or null (optional if $name is used)
   * @return array An array of error messages related to the element. An empty array if there are no errors.
   * @access public  
   */
  function validateElement( $name = null, &$element = null ) {

    if ( !is_object( $element ) )
      $element =& $this->getElementByName( $name );

    if ( $element->validated )
      return $element->validationarray;

    $element->validating = true;

    $valid = Array();
    $validationsettings = $element->getValidationSettings();

    foreach ( $validationsettings as $vskey => $validationparameters ) {

      if ( 
           ( $vskey === 'anddepend' ) || 
           ( $vskey === 'ordepend' )
         )
        continue;

      $validationparameters['form'] = &$this;

      if ( isset( $validationsettings['anddepend'] ) )
        $validationparameters['anddepend'] = $validationsettings['anddepend'];

      if ( isset( $validationsettings['ordepend'] ) )
        $validationparameters['ordepend'] = $validationsettings['ordepend'];

        switch ( $validationparameters['type'] ) {

          case 'required':
            $validator = $this->_getRequiredClassFor( 
              $element, $validationparameters
            );
            
            break;

          default:

            $classfile = 
              CLONEFISH_DIR . 'validation.' . strtolower( $validationparameters['type'] ) . '.php';

            if ( file_exists( $classfile ) ) {
              include_once( $classfile );
              $class = $validationparameters['type'] . 'Validation';
            $validator = new $class( $validationparameters, $element );
            }
            else
              die( sprintf( CF_ERR_PHPVALIDATOR_UNSUPPORTED, $this->elements[ $key ]->name, $validationparameters['type'] ) ); 

            break;

        }

        $valid = array_merge( $valid, $validator->isValid() );

      }

    $element->validating      = false;
    $element->validationarray = $valid;
    $element->valid           = count( $valid ) == 0;
    $element->validated       = true;

    return $valid;

  }

  // -------------------------------------------------------------------------


  /**
   * A helper function which maps 'required' validation to specific 
   * files and classes.
   * 
   * @param  object  &$element             An element object
   * @param  unknown $validationparameters Validation configuration for the element
   * @return object  Returns a validator object.
   * @access private
   */
  function &_getRequiredClassFor( &$element, $validationparameters ) {

    $fallbacks = 
      Array(
        'inputradiodynamic' => 'inputradio',
        'selectdynamic'     => 'select',
        'selectfile'        => 'select',
        'inputhidden'       => 'inputtext',
        'fckeditorarea2'        => 'fckeditorarea2',
        'fckeditorarea2_bbcode' => 'fckeditorarea2',
        'inputpassword'     => 'inputtext', 
        'textarea'          => 'inputtext',
      );

    $elementtype = strtolower( $element->getType() );
    if ( isset( $fallbacks[ $elementtype ] ) ) 
      $elementtype = $fallbacks[ $elementtype ];

    $classfile   = 'validation.required.' . $elementtype . '.php';

    if ( file_exists( CLONEFISH_DIR . $classfile ) ) {
      include_once( $classfile );
      $class     = $elementtype . 'Required';
      $validator = new $class( $validationparameters, $element, $this );
      return $validator;

    }
    else 
      die( sprintf( CF_ERR_REQUIRE_UNSUPPORTED, $element->getName(), $element->getType() ) );

  }

  // -------------------------------------------------------------------------


  /**
   * Returns the value of an element.
   * 
   * @param  string  $elementname  Element name
   * @param  string  $addSlashes   Whether or not slashes should be added.
   * @return mixed   Returns the element value, or false if the element cannot be found.
   * @access public  
   */
  function getValue( $elementname, $addSlashes ) {
    
    $element = $this->getElementByName( $elementname );

    if ( is_object( $element ) )
      return $element->getValue( $addSlashes );
    else
      return false;
 
  }

  // -------------------------------------------------------------------------


  /**
   * Sets the value for an element
   * 
   * @param  string  $elementname      Element name
   * @param  string  $value            Element value
   * @param  boolean $magic_quotes_gpc Whether or not the element has quotes already added
   * @return mixed   Return false if the element doesn't exist or setting a value failed (eg. an inputRadio setValue fails if a value is to be set which is not present in the 'values' array)
   * @access public  
   */
  function setValue( $elementname, $value, $magic_quotes_gpc ) {
    
    $element = $this->getElementByName( $elementname );

    if ( is_object( $element ) )
      return $element->setValue( $value, $magic_quotes_gpc );
    else
      return false;
 
  }

  // ----------------------------------------------------------------------------


  /**
   * Load .ini configuration. Handles multi.dimensional.array="value" format
   * and multiline settings (tab or space indented) too.
   * 
   * @param  string $filename Filename to load.
   * @return void    
   * @access public  
   */
  function loadConfig( $filename ) {

    $f        = fopen( $filename, 'r' );

    $settings = Array();
    $row      = fgets( $f, 4096 );
    $continue = $row !== false;

    while ( $continue ) {

      $thisrow = fgets( $f, 4096 );

      if (
           ( ( 
               strlen( trim( $thisrow ) ) &&
               !in_array(
                 substr( $thisrow, 0, 1 ),
                 Array( ' ', "\t" ) 
               )
             ) ) ||
           $thisrow === false
         ) {

        $row = rtrim( $row, "\n\r" );

        if ( substr( $row, 0, 1 ) != ';' ) {

          $parts = explode( 
            '=', 
            $this->_configfilter( $this->configfilter, $row ), 
            2 
          );
          
          $parts[ 0 ] = trim( $parts[ 0 ] );
          
          if ( strlen( $parts[ 0 ] ) ) {
  
            // instead of trim(), we use preg_match + substr
            // to ensure symmetrical trimming
            while ( preg_match('/^\"(.+)\"$/', $parts[1] ) )
              $parts[1] = substr( $parts[1], 1, -1 );

            $parts[ 1 ] = 
              preg_replace(
                '/\\\([nrt"])/e', "\"\\\\$1\"", $parts[ 1 ]
              );

            if ( strpos( $parts[ 0 ], '.' ) !== false ) {

              // array setting
              $nameparts = explode('.', $parts[ 0 ] );
              $settings  = $this->_createArray( $nameparts, $settings, 0, $parts[ 1 ] );

            }
            else {
              $settings[ $parts[ 0 ] ] = $parts[ 1 ];
            }

          }

        }

        $continue = false;
        $row = '';

      }

      if ( $thisrow !== false )
        $continue = true;

      if ( strlen( trim( $thisrow, "\n" ) ) ) 
        $row .= $thisrow;

    }

    foreach ( $settings as $key => $value ) 
      if ( is_array( $value ) ) {
        $this->$key =  
          $this->_betterArrayMerge(
            $this->$key, 
            $settings[ $key ] 
          );
      }
      else
        $this->$key = $value;

  }

  /**
   * Helper function which makes sure that values are overwritten recursively
   * in arrays.
   * 
   * @param  array   $array1 First array to merge
   * @param  array   $array2 Second array to merge
   * @return array   Return description (if any) ...
   * @access private 
   */
  function _betterArrayMerge( $array1, $array2 ) {

    foreach ( $array1 as $key => $value ) 
      if ( isset( $array2[ $key ] ) ) {
        if ( is_array( $array2[ $key ] ) )
          $array1[ $key ] = $this->_betterArrayMerge( $array1[ $key ], $array2[ $key ] );
        else
          if ( !is_array( $array1[ $key ] ) )
            $array1[ $key ] = $array2[ $key ];
      }

    return $array1;

  }

  /**
   * Creates multidimensional arrays to help loading
   * configuration with loadConfig.
   * 
   * @param  array   $nameparts  Setting name exploded
   * @param  array   $settings   Current settings array
   * @param  int     $counter    Recursion level counter
   * @param  string  $leafvalue  The actual setting value
   * @return array   Returns an array with proper multidimensional structure.
   * @access private 
   */
  function _createArray( $nameparts, $settings, $counter, $leafvalue ) {

    if ( !isset( $settings[ $nameparts[ $counter ] ] ) ) {
      if ( $counter == ( count( $nameparts ) - 1 ) )
        $settings[ $nameparts[ $counter ] ]  = $leafvalue;
      else
        $settings[ $nameparts[ $counter ] ]  = 
          $this->_createArray( 
            $nameparts, 
            Array(), 
            $counter + 1, 
            $leafvalue );
    } 
    else
      if ( $counter == ( count( $nameparts ) - 1 ) )
        $settings[ $nameparts[ $counter ] ]  = $leafvalue;
      else
        $settings[ $nameparts[ $counter ] ]  = 
          $this->_createArray( 
            $nameparts, 
            $settings[ $nameparts[ $counter ] ], 
            $counter + 1, 
            $leafvalue );
    
    return $settings;
    
  }

  /**
   * Run configuration filter function if needed when using loadConfig.
   * 
   * @param  string  $configfilter Filter function name
   * @param  string  $value        Configuration setting value loaded from .ini file
   * @return string  Filtered on unfiltered value
   * @access private 
   */
  function _configFilter( $configfilter, $value ) {

    if ( $configfilter )
      return $configfilter( $value );
    else
      return $value;
   
  }

  /**
   * Helps normalizing paths to avoid missing/extra trailing slashes.
   * 
   * @param  string  $path Path to normalize
   * @return string  Normalized path
   * @access private 
   */
  function _normalizePath( $path ) {

    if ( substr( $path, strlen( $path ) - 1, 1 ) != '/' )
      return $path . '/';
    else
      return $path;

  }

  /**
   * A static helper function to help adding a new element 
   * into a configuration array before another element.
   * 
   * @param  array   $array   
   * @param  string  $index   Index of existing element 
   * @param  unknown $newItem Configuration array of new element
   * @param  unknown $newKey  Configuration key of new element
   * @return array   Configuration array including new element
   * @access public  
   */
  function insertIntoArrayBefore( $array, $index, $newItem, $newKey = null ) {

    $result = Array();

    foreach ( $array as $key => $item ) {
      if ( $key == $index ) {
        if ( $newKey !== null )
          $result[ $newKey ] = $newItem;
        else
          $result[] = $newItem;
      }
      $result[ $key ] = $item;
    }

    return $result;

  }

  // -------------------------------------------------------------------------


  /**
   * Helper function for multibyte string features: checks if
   * a function is supported.
   * 
   * @param  string  $function Function name
   * @return boolean True if the function is supported. Die()s with an errormessage otherwise.
   * @access private 
   */
  function _functionSupported( $function ) {

    if ( !strlen( $this->codepage ) )
      die( CF_ERR_MBSUPPORT_CODEPAGE_MISSING );

    elseif ( !array_key_exists( $this->multibytesupport, $this->multibytesetup ) )
      die(
        sprintf(
          CF_ERR_MBSUPPORT_INVALID_PARAMETER,
          $this->multibytesupport,
          "'" . implode("', '", array_keys( $this->multibytesetup ) ) . "'"
        )
      );

    elseif ( !in_array( $function, Array( 'encoding', 'strlen', 'regexp' ) ) )
      die(
        sprintf(
          CF_ERR_MBSUPPORT_FUNCTION_UNIMPLEMENTED, 
          $function
        )
      );

    else
      
      return true;

  }

  // -------------------------------------------------------------------------


  /**
   * Execute (eval) multibyte function calls
   * 
   * @param  string $function  Function to execute
   * @param  string $value     Value to pass to function
   * @param  string $parameter Optional parameter to pass
   * @return string
   * @access private 
   */
  function _handleString( $function, $value, $parameter = null ) {

    if ( $this->_functionSupported( $function ) ) {

      $setup = $this->multibytesetup[ $this->multibytesupport ];

      if ( isset( $setup['encoding'] ) ) {
        $cmd = sprintf( $setup[ 'encoding' ], $this->codepage );
        eval( '$encodingvalue = ' . $cmd . ';' );

        if ( !$encodingvalue )
          die(
            sprintf( CF_ERR_MBSUPPORT_CODEPAGE_UNSUPPORTED,
              $this->codepage,
              $this->multibytesupport,
              $cmd
            )
          );

      }

      $value = addslashes( $value );

      switch ( $function ) {

        case 'regexp':
          $parameter = addslashes( $parameter );
          $cmd       = sprintf( $setup['regexp'], $parameter, $value );
          break;
        case 'strlen':
          $cmd = sprintf( $setup['strlen'], $value );
          break;

      }

      eval( '$value = ' . $cmd . ';' );
      return $value;

    }

  }

}

?>