<?php

/**
 * Clonefish database wrapper (connector) for PHP PDO library
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
 * A database wrapper for PDO database library.
 *
 * @package     clonefish
 * @subpackage  databaseWrappers
 *
 */
class DBWrapperPDO {

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
   * Current query SQL
   * @var string
   */
  var $sql;

  /** 
   * Constructor: check if the object is a valid connection object
   *
   * @param object PDO connection object
   */
  function DBWrapperPDO( &$db ) {

    if ( is_object( $db ) && ( get_class( $db ) == 'PDO' ) )
      $this->link = &$db;
    else
      die( CF_ERR_DBWRAPPER_NOT_PDO );
  
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

    return $this->link->quote( $string );

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

    $result    = $this->link->query( $sql );
    $this->sql = $sql;

    if ( $result ) {
      $rsobject = new DBWrapperPDOResult( $result, $this );
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

    $errorInfo = $this->link->errorInfo();
    return
      'SQLSTATE: ' . $errorInfo[0] . ', ' .
      'driver specific message: '.
        '"' . $errorInfo[1] . ' ' . $errorInfo[2] . '"'
    ;
  
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
class DBWrapperPDOResult {

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
   * Constructor: holds a PDO result object
   *
   * @param object PDO result object
   * @param object PDO database object
   */
  function DBWrapperPDOResult( &$result, &$dbwrapper ) {
    $this->result    = &$result;
    $this->dbwrapper = &$dbwrapper;
  }

  /**
   * Returns the number of rows of the current result set
   *
   * The minrows and maxrows options for database validation rely on        
   * the RecordCount() method of the database wrapper result object.        
   * Unfortunately counting rows for a SELECT statement is not supported    
   * by several RDBMSes. While some database abstraction layers             
   * (like AdoDB) provide rowcount emulation in such cases, there's no such 
   * feature in PDO, so the PDO wrapper runs another query in these cases   
   * as it's suggested in the PDO manual.                                   
   *                                                                        
   * To maintain portability, you may have to modify this method to support 
   * your RDBMS of choice.                                                  
   *
   * @return int the record count 
   */
  function RecordCount() {
  
    $rs = $this->dbwrapper->execute("
      SELECT count(*) AS counter
      FROM
        ( 
          " . $this->dbwrapper->sql . "
        ) AS subquery
    ");
    return $rs->fields['counter'];
  
  }

  /**
   * Moves the internal pointer to the next result in the result set
   */
  function moveNext() {

    $row = $this->result->fetch( PDO::FETCH_ASSOC );
    $this->EOF    = $row === false;
    $this->fields = $row;

  }

}

?>