<?php

/**
 * Clonefish database wrapper (connector) for native MySQL connections
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
 * A database wrapper for PHP-native MySQL database functions.
 *
 * @package     clonefish
 * @subpackage  databaseWrappers
 *
 */

class DBWrapperMySQL {

  /** 
   * Result object reference
   * @var object
   */
  var $rs;    
  /** 
   * Database link resource
   * @var resource 
   */
  var $link;

  /** 
   * Constructor: check if the resource identifier is a valid connection
   *
   * @param resource MySQL link resources
   */
  function DBWrapperMySQL( &$db ) {
    if ( is_resource( $db ) )
      if ( 
           ( get_resource_type( $db ) == 'mysql link' ) ||
           ( get_resource_type( $db ) == 'mysql link persistent' )
         ) 
        $this->link = &$db;
      else
        die( CF_ERR_DBWRAPPER_NOT_MYSQL );
    else
      $this->link = false;
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

    return "'" . mysql_real_escape_string( $string ) . "'";

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
    if ( $this->link )
      $result = mysql_query( $sql, $this->link );
    else
      $result = mysql_query( $sql );

    if ( $result ) {
      $rsobject = new DBWrapperMySQLResult( $result, $this->link );
      $rsobject->moveNext();
      return $rsobject;
    }
    else
      return false;
  }

  /**
   * Returns the last error message
   *
   * @return string The last error message
   */
  function errorMsg() {
    return mysql_error( $this->link );
  }

}

/** 
 * Result object to help processing database wrapper query results.
 *
 * It is used to walk through the database query
 * results, holding the rows in an array (both associative and enumerated   
 * values). The EOF property of this object is used to check   
 * if there are no more result rows available.
 *
 * @package     clonefish
 * @subpackage  databaseWrappers
 *
 */
class DBWrapperMySQLResult {

  /* 
   * EOF - whether or not the result set is over last record
   * @var bool
   */
  var $EOF    = false;
  /* 
   * Current record fields
   * @var array
   */
  var $fields = Array();
  /*
   * Result resource
   * @var resource
   */
  var $result = false;

  /**
   * Constructor: holds a MySQL result resource
   *
   * @param resource MySQL result
   * @param resource MySQL database link resource
   */
  function DBWrapperMySQLResult( $result, $link ) {
    $this->result = $result;
  }

  /**
   * Returns the number of rows of the current result set
   * @return int the record count 
   */
  function RecordCount() {
    return mysql_num_rows( $this->result );
  }

  /**
   * Moves the internal pointer to the next result in the result set
   */
  function moveNext() {

    $row = mysql_fetch_array( $this->result, MYSQL_ASSOC );
    $this->EOF    = !is_array( $row );
    $this->fields = $row;

  }

}

?>