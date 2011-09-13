<?php

/**
 * Clonefish database wrapper (connector) for AdoDB library
 *
 * Clonefish uses this class the following way:
 *
 * $result = $wrapper->execute( 'SELECT * FROM books');
 *
 * if ( $result ) {
 *   while ( !$result->EOF ) {
 *     print_r( $result->fields );
 *     $result->moveNext();
 *   }
 * else
 *   die( $wrapper->errorMsg() );
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

/**
 * A database wrapper for AdoDB database library.
 *
 * Note1: We only need this class because we have to make sure that the
 * fetch mode is set to associative, then restore the fetch mode
 * after executing the query.
 *
 * Note2: We do not need a result class for result objects in this case, 
 * since CloneFish internally uses AdoDB-style result sets.
 *
 * @package     clonefish
 * @subpackage  databaseWrappers
 *
 */

class DBWrapperAdoDB {

  /** 
   * Result object
   * @var object
   */
  var $rs;

  /** 
   * Database link object
   * @var object
   */
  var $link;

  /** 
   * Constructor: check if the resource identifier is a valid connection
   *
   * @param object AdoDB connection object
   */
  function DBWrapperAdoDB( &$db ) {

    if ( is_subclass_of( $db, 'adoconnection' ) )
      $this->db = &$db;
    else
      die( CF_ERR_DBWRAPPER_NOT_ADODB );

  }

  /** 
   * Database-specific and php.ini-independent quoting method.
   *
   * The addslashes() function is php.ini-dependent:
   * when magic_quotes_sybase is ON, it will only replace ' to '' and
   * nothing else, so your application will not remain fully portable.   
   * 
   * We need to be sure that we add the appropriate slashes.
   *
   * @param  string String to quote
   * @return string Quoted string
   */
  function quote( $string ) {

    return $this->db->quote( $string );

  }

  /**
   * Executing the query
   *
   * Executes the query, and returns a result object on success.
   * In case of a failure, it returns false (the application will then
   * call the errorMsg() method)
   *
   * @param string SQL query string
   * @return object|bool Result object on success, false on failure
   */
  function execute( $sql ) {

    // for AdoDB, we have to save and restore the fetch
    // mode, and set the needed associative fetch mode.
    // AdoDB has two ways for setting fetch mode, we
    // should restore both of them.

    if ( $this->db->fetchMode ) {
      $global = 0;
      $old_adodb_mode = $this->db->fetchMode;
      $this->db->SetFetchMode(ADODB_FETCH_ASSOC);
    }
    else {
      $global = 1;
      $old_adodb_mode = $GLOBALS['ADODB_FETCH_MODE'];
      $GLOBALS['ADODB_FETCH_MODE'] = ADODB_FETCH_ASSOC;
    }

    $result = $this->db->query( $sql );

    if ( $global )
      $GLOBALS['ADODB_FETCH_MODE'] = $old_adodb_mode;
    else
      $this->db->SetFetchMode( $old_adodb_mode );

    return $result;

  }

  /**
   * Returns the last error message
   *
   * @return string The last error message
   */
  function errorMsg() {
    return 
      $this->db->errorMsg();
  }

}

?>