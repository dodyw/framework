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

include_once( CLONEFISH_DIR . 'element.select.php');

/* 
 * Element
 * @package clonefish
 * @subpackage elements
 */
class selectDynamic extends select {

  var $db;
  var $valuesql;
  var $levelprefix = '&nbsp;&nbsp;';
  var $refid;

  // --------------------------------------------------------------------------
  function getHTML() {

    if ( isset( $this->treeparent ) ) {
      $out = parent::getHTML();
      // we have to get back the htmlentities()-converted
      // levelprefix
      return str_replace(
        htmlspecialchars( $this->levelprefix ), 
        $this->levelprefix, 
        $out 
      );
    }
    else
      return parent::getHTML();

  }

  // --------------------------------------------------------------------------
  function selectDynamic( $key, $configvalues, &$db, $refid ) {

    // Passing a reference ID (refid) is the supported way of 
    // passing some kind of record ID to Clonefish in the 
    // form class contructor, which is then used with 'valuesql'.
    if ( $refid !== null )
      $this->refid = $refid;

    // However, you can still pass 'refid' in the element's
    // config array: the parent constructor below
    // will make it an object property too.

    // call parent constructor
    $parent_class_name = get_parent_class( $this );
    $this->$parent_class_name( $key, $configvalues );
    
    $this->db = &$db;

    $this->getValuesFromSQL();

  }

  // --------------------------------------------------------------------------
  function getValuesFromSQL() {

    if ( is_object( $this->db ) ) {

      if ( !isset( $this->treeparent ) ) {

        // plain one-level select

        // make sure switching to associative mode

        $results = $this->db->execute( $this->sql );

        if ( $results ) {
          while ( !$results->EOF ) {
            @reset( $results->fields );
            $value = each( $results->fields );
            $label = each( $results->fields );
            $this->values[ $value[ 1 ] ] = $label[ 1 ];
            $results->moveNext();
          }
        }
        else
          die( sprintf( CF_ERR_DB_ERROR, $this->name, $this->db->errormsg() ) );
      }
      else {

        // tree-type select
        $this->buildTree( $this->treestart, 0);

      }

      if ( $this->valuesql ) {

        $results = $this->db->execute( sprintf( $this->valuesql, $this->refid ) );

        if ( $results ) {
          while ( !$results->EOF ) {
            $value = each( $results->fields );
            $this->value[] = $results->fields[ $value[ 'key' ] ];
            $results->moveNext();
          }
        }
        else
          die( sprintf( CF_ERR_DB_ERROR, $this->name, $this->db->errormsg() ) );
      }

    }
    else
      die( sprintf( CF_ERR_MISSING_DB_OBJECT, $this->name ) );

  }

  // --------------------------------------------------------------------------
  function buildTree( $parentid, $level ) {

    $sql_where =
        $this->treeparent . " = " . $this->db->quote( $parentid, 0 ) . " " ;

    // if ordering is needed, we have to use some replacement stuff

    if ( strpos( $this->sql, '%s' ) !== false )
      $sql_to_run = sprintf( $this->sql, $sql_where );
    else
      $sql_to_run = $this->sql . ' WHERE ' . $sql_where;

    $results = $this->db->execute( $sql_to_run );

    if ( is_object( $results ) )
      while ( !$results->EOF ) {

        $value = each( $results->fields );
        $label = each( $results->fields );

        $this->values[ $value[ 'value' ] ] = 
          str_repeat( $this->levelprefix, $level ) . $label[ 'value' ];

        // get children
        $this->buildTree( $results->fields[ $this->treeid ], $level + 1 );

        $results->moveNext();

      }

    else
      die( sprintf( CF_ERR_DB_ERROR, $this->name, $this->db->errormsg() ) );

  }

}

?>