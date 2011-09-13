<?php

/**
 * Language string constants - English
 *
 * Clonefish form generator class
 * (c) phpformclass.com, Dots Amazing
 * All rights reserved.
 * 
 * @copyright 2010 Dots Amazing
 * @version   v2.2, 2010-10-21
 * @link      http://phpformclass.com
 * @package   clonefish
 *
 */

// element.inputfile.php
define('CF_STR_FILE_UPLOADS_NOT_ALLOWED', 'File uploads are disabled on this webserver' );
define('CF_STR_IMAGE_DELETE','Delete image');

// form.php
define('CF_STR_FORM_ERRORS', 'Please correct the following entries:');

// validation.date.php
define('CF_STR_DATE_FORMAT',  "The date/time is incorrect in the '%s' field! Please use the following format: %s");
define('CF_STR_DATE_OVER_MINIMUM', "The minimum date/time is '%s' in the '%s' field!" );
define('CF_STR_DATE_OVER_MAXIMUM', "The maximum date/time is '%s' in the '%s' field!" );
define('CF_STR_DATE_OVERFLOW',     "Due to system limits the date/time in the '%s' field cannot be more than 03:14:07 UTC on Tuesday, 19 Jan 2038!" );
define('CF_STR_DATE_OVER_LESSFIELD',      "The date/time in '%s' must be less than '%s'!" );
define('CF_STR_DATE_OVER_LESSEQFIELD',    "The date/time in '%s' must be less or equal than '%s'!" );
define('CF_STR_DATE_OVER_GREATERFIELD',   "The date/time in '%s' must be greater than '%s'!" );
define('CF_STR_DATE_OVER_GREATEREQFIELD', "The date/time in '%s' must be greater or equal than '%s'!" );

// validation.file.php
define('CF_STR_FILE_REQUIRED',      "You have to choose a '%s' file");
define('CF_STR_FILE_TYPES_ALLOWED', "The type of the '%s' file should be %s");
define('CF_STR_FILE_MINIMUM',       "The '%s' file must be at least %s bytes large in size");
define('CF_STR_FILE_MAXIMUM',       "The '%s' file must not be larger than %s bytes in size");
define('CF_STR_FILE_OR',            " or ");

// validation.number.php
define('CF_STR_NUMBER_MINIMUM',     "The value of the '%s' field must be at least %s");
define('CF_STR_NUMBER_MAXIMUM',     "The value of the '%s' field must be less or equal than %s");
define('CF_STR_NUMBER_NUMBERSONLY', "The value of the '%s' field must be a number");
define('CF_STR_NUMBER_INTEGERONLY', "The value of the '%s' field must be a whole number");
define('CF_STR_NUMBER_SCIENTIFICNOTATIONONLY', "The value of the '%s' field must be a decimal or hexadecimal number or in scientific notation." );

// validation.string.php
define('CF_STR_STRING_MINIMUM',    "The '%s' field must be at least %s characters long");
define('CF_STR_STRING_MAXIMUM',    "The '%s' field must not be longer than %s characters");
define('CF_STR_STRING_REGEXP',     "The format of the '%s' field is incorrect! (Use %s)");
define('CF_STR_STRING_REGEXP_NOT', "The format of the '%s' field is incorrect! (Use %s)");
define('CF_STR_STRING_NOT_EQUAL', "The values of the '%s' and the '%s' fields must be equal");
define('CF_STR_STRING_NOT_DIFFERENT', "The values of the '%s' and the '%s' fields must be different!");

// validation.required.inputcheckbox.php
define('CF_STR_REQUIRED_CHECKBOX',      "You have to check the '%s' checkbox");

// validation.required.inputcheckboxdynamic.php
define('CF_STR_REQUIRED_MINIMUM_CHECKBOXES',      "You have to check at least %s '%s' checkbox(es)");
define('CF_STR_REQUIRED_MAXIMUM_CHECKBOXES',      "You may only check at most %s '%s' checkbox(es)");

// validation.required.inputradio.php
define('CF_STR_REQUIRED_RADIO',      "You have to choose a(n) '%s'");

// validation.required.inputtext.php
define('CF_STR_REQUIRED_TEXT',      "The '%s' field is required");

// validation.required.select.php
define('CF_STR_REQUIRED_SELECT',      "You have to select a(n) '%s'");

// validation.custom.php
define('CF_STR_CUSTOM_VALIDATION_FAILS', "The contents of '%s' is invalid!" );

// validation.database.php
define('CF_STR_DATABASE_INPUT_INVALID',  "The contents of '%s' field is invalid!");

// validation.captcha.php, validation.recaptcha.php
define('CF_STR_CAPTCHA_ERROR',     "The characters in the '%s' field didn't match the characters in the picture");

// validation.email.php
define('CF_STR_EMAIL_VALIDATION_FAILED', "The e-mail address in the '%s' field has problems: %s" );

// general form configuration/runtime errors
define('CF_ERR_MISSING_SLASHES_ADDED_PARAMETER', 'CLONEFISH: specify the "slashes added" parameter for %s');
define('CF_ERR_CONFIG_TYPE_MISSING',                      'CLONEFISH: "%s" input: "type" setting is missing from the configuration array' );
define('CF_ERR_CONFIG_TYPE_UNSUPPORTED',                  'CLONEFISH: "%s" input: "%s" input type is not supported by the form class, "%s" file not found' );
define('CF_ERR_CONFIG_VALIDATION_NOT_AN_ARRAY',           'CLONEFISH: "%s" input: the "validation" setting must be an array, eg.: <PRE>\'validation\' => Array( Array( \'type\' => \'required\' ) )</PRE>' );
define('CF_ERR_CONFIG_VALIDATION_NOT_AN_ARRAY_OF_ARRAYS', 'CLONEFISH: "%s" input: the "validation" array must contain arrays, eg.: <PRE>\'validation\' => Array( Array( \'type\' => \'required\' ) )</PRE>' );
define('CF_ERR_CONFIG_VALIDATION_TYPE_MISSING',           'CLONEFISH: "%s" input: the "type" setting is missing from an array of the "validation" array, eg.: <PRE>\'validation\' => Array( Array( \'type\' => \'required\' ) )</PRE>');
define('CF_ERR_VALUE_ARRAY_REQUIRED',                     'CLONEFISH: "%s" input: if set, the "values" setting must contain an array for this input, eg.: <PRE>\'values\' => Array( 0 => \'no\', 1 => \'yes\' )</PRE>');
define('CF_ERR_CONFIG_SINGLE_VALUE_REQUIRED',             'CLONEFISH: "%s" input: if set, the "value" setting must not contain nem an array, but single values, eg.: <PRE>\'value\'  => 142</PRE>');
define('CF_ERR_CONFIG_DATABASE_VALIDATION_SQL_MISSING',   'CLONEFISH: "%s" input (database validation): the "sql" setting (used to validate input) is missing' );
define('CF_ERR_CONFIG_DATABASE_VALIDATION_DB_MISSING',    'CLONEFISH: "%s" input (database validation): Clonefish constructor call is missing the database type setting (and/or the database connection object for AdoDB, PearDB)' );
define('CF_ERR_JSVALIDATOR_UNSUPPORTED',                  'CLONEFISH: "%s" input: unsupported JavaScript validation: "%s"' );
define('CF_ERR_PHPVALIDATOR_UNSUPPORTED',                 'CLONEFISH: "%s" input: unsupported PHP validation: "%s"' );
define('CF_ERR_REQUIRE_UNSUPPORTED',                      'CLONEFISH: "%s" input: the form class does not support this class for require: "%s"');
define('CF_ERR_FILE_VALIDATION_UNSUPPORTED',              'CLONEFISH: "%s" input (file upload input): "%s" filetype is not supported for require' );
define('CF_ERR_MISSING_DB_OBJECT',                        'CLONEFISH: "%s" input (dynamic input): database inaccessible (possibly no database link/object was passed to the form object)' );
define('CF_ERR_DATABASE_VALIDATION_DB_ERROR',             'CLONEFISH: "%s" input (database validation): database error: "%s"' );
define('CF_ERR_DATABASE_VALIDATION_VALUE_OR_FIELD_MISSING','CLONEFISH: "%s" input (database validation): either "field" or "value" setting is missing while the other one is defined' );
define('CF_ERR_DIRECTORY_INACCESSIBLE',                   'CLONEFISH: "%s" input (selectfile): the "%s" directory does not exist, or cannot be accessed (or the "directory" array element is not defined in the input configuration)');
define('CF_ERR_SELECTFILE_ORDER_UNKNOWN',                 'CLONEFISH: "%s" input (selectfile): ordering by "%s" is unimplemented' );
define('CF_ERR_DB_ERROR',                                 'CLONEFISH: "%s" input (dynamic input): database error: "%s"' );
define('CF_ERR_TEMPLATE_MISSING_ELEMENT',                 'CLONEFISH: "%s" input (template): missing element: "%s"' );
define('CF_ERR_CONFIG_AJAX_VALIDATION_FORMID_MISSING',    'CLONEFISH: "%s" input (ajax validation): "formid" setting needs to be used which identifies the form in the Clonefish AJAX connector script' );
define('CF_ERR_CONFIG_EMAIL_VALIDATION_DNS_MISSING',      'CLONEFISH: "%s" input (email validation): "dns" setting (an array of DNS IP addresses) is missing for domain checking/probe' );
define('CF_ERR_DEPEND_VALIDATION_NOT_ARRAY_OF_ARRAYS',    'CLONEFISH: "%s" input anddepend/ordepend validation: this setting must be an array of arrays' );
define('CF_ERR_DEPEND_VALIDATION_JS_KEY_MISSING',         'CLONEFISH: "%s" input anddepend/ordepend validation: "js" setting (a JS expression) is missing' );
define('CF_ERR_DEPEND_VALIDATION_PHP_KEY_MISSING',        'CLONEFISH: "%s" input anddepend/ordepend validation: "php" setting (a PHP expression) is missing' );
define('CF_ERR_DEPEND_VALIDATION_ELEMENT_MISSING',        'CLONEFISH: "%s" input anddepend/ordepend validation: the element referred to as "%s" cannot be found' );
define('CF_ERR_MBSUPPORT_CODEPAGE_MISSING',               'CLONEFISH: clonefish->codepage must be defined to use multibyte string length or regular expression' );
define('CF_ERR_MBSUPPORT_CODEPAGE_UNSUPPORTED',           'CLONEFISH: setting codepage to "%s" failed, multibytesupport="%s", function call: %s ');
define('CF_ERR_MBSUPPORT_INVALID_PARAMETER',              'CLONEFISH: value of clonefish->multibytesupport ("%s") is invalid. Use one of %s' );
define('CF_ERR_MBSUPPORT_FUNCTION_UNIMPLEMENTED',         'CLONEFISH: function handler call to "%s" is unimplemented' );
define('CF_ERR_STRING_FIELD_NOT_FOUND',                   'CLONEFISH: referenced field "%s" (string validation, "%s" => "%1$s") not found when tried to validate element "%s"');
define('CF_ERR_RECAPTCHA_LIBRARY_MISSING',                'CLONEFISH: the reCAPTCHA library is missing, please include it before calling $clonefish->validate(). You can find the library here http://code.google.com/p/recaptcha/downloads/list?q=label:phplib-Latest');
define('CF_ERR_RECAPTCHA_PUBKEY_MISSING',                 'CLONEFISH: the reCAPTCHA public API key is missing at element "%s", please get one from http://recaptcha.net');
define('CF_ERR_RECAPTCHA_PRIVKEY_MISSING',                'CLONEFISH: the reCAPTCHA private API key is missing from validation settings of "%s", please get one from http://recaptcha.net');
define('CF_ERR_DATE_COMPARE_VALIDATION_MISSING',          'CLONEFISH: date validation is required for "%s" to be comparable to "%s".');
define('CF_ERR_FCK_JSPATH_NOT_SET',                       'CLONEFISH: path (URL) to FCK Editor directory should be set for "%s" element using \'jspath\'');
define('CF_ERR_FCK_INCLUDEPATH_NOT_SET',                  'CLONEFISH: filesystem path to FCKEditor directory (fckeditor.php) should be set for "%s" element using \'includepath\'');
define('CF_ERR_MAPMARKER_APIVERSION_UNIMPLEMENTED',       'CLONEFISH: Google Maps API version "%s" is unimplemented at element "%s"');

// dbwrapper.php errors
define('CF_ERR_DBWRAPPER_DBTYPE_UNKNOWN', "CLONEFISH: database connection type '%s' is unknown" );
define('CF_ERR_DBWRAPPER_NOT_MYSQL',      "CLONEFISH: database variable passed to CloneFish object is not a resource or is not a MySQL link resource" );
define('CF_ERR_DBWRAPPER_NOT_ADODB',      "CLONEFISH: database variable passed to CloneFish object is not an AdoDB connection object" );
define('CF_ERR_DBWRAPPER_NOT_PEARDB',     "CLONEFISH: database variable passed to CloneFish object is not a PearDB connection object" );
define('CF_ERR_DBWRAPPER_NOT_MDB2',       "CLONEFISH: database variable passed to CloneFish object is not a MDB2 connection object" );
define('CF_ERR_DBWRAPPER_NOT_PDO',        "CLONEFISH: database variable passed to CloneFish object is not a PDO connection object" );

define('CF_STR_MONTH_01', 'January');
define('CF_STR_MONTH_02', 'February');
define('CF_STR_MONTH_03', 'March');
define('CF_STR_MONTH_04', 'April');
define('CF_STR_MONTH_05', 'May');
define('CF_STR_MONTH_06', 'June');
define('CF_STR_MONTH_07', 'July');
define('CF_STR_MONTH_08', 'August');
define('CF_STR_MONTH_09', 'September');
define('CF_STR_MONTH_10', 'October');
define('CF_STR_MONTH_11', 'November');
define('CF_STR_MONTH_12', 'December');

?>