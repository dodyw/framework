<?php

/**
 * Clonefish database wrapper (connector) for PEAR MDB2 library
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
 * A database wrapper for PEAR MDB2 database library.
 *
 * @package     clonefish
 * @subpackage  databaseWrappers
 *
 */
class DBWrapperMDB2 {

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
   * Holds the last error message
   * @var string
   */
  var $resultError;

  /** 
   * Constructor: check if the object is a valid connection object
   *
   * @param object MDB2 connection object
   */
  function DBWrapperMDB2( &$db ) {

    if ( is_subclass_of( $db, 'MDB2_Driver_Common' ) ) {
      $this->link = &$db;
    }
    else
      die( CF_ERR_DBWRAPPER_NOT_MDB2 );
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

    $result   = $this->link->query( $sql );

    if ( !PEAR::isError( $result ) ) {
      $rsobject = new DBWrapperMDB2Result( $result, $this->link );
      $rsobject->moveNext();
      return $rsobject;
    }
    else {
      $this->resultError = $result->getMessage();
      return false;
    }

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
  function quote( $string, $quotes = false ) {
    return $this->link->quote( ( $quotes )? stripslashes( $string ) : $string, null, true, true );
  }

  /**
   * Returns the last error message
   *
   * @return string The last error message
   */
  function errorMsg() {
    return 
      $this->resultError;
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
class DBWrapperMDB2Result {

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
   * Result object
   * @var object
   */
  var $result = false;

  /**
   * Constructor: holds an MDB2 result object
   *
   * @param object MDB2 result object
   * @param object MDB2 database object
   */
  function DBWrapperMDB2Result( &$result, $link ) {
    $this->result = &$result;
  }

  /**
   * Returns the number of rows of the current result set
   * @return int the record count 
   */
  function RecordCount() {
    return $this->result->numRows();
  }

  /**
   * Moves the internal pointer to the next result in the result set
   */
  function moveNext() {

    $row = $this->result->fetchRow( MDB2_FETCHMODE_ASSOC );
    $this->EOF    = !is_array( $row );
    $this->fields = $row;

  }

}

?>