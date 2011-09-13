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

include_once( CLONEFISH_DIR . 'element.inputradio.php');

/* 
 * Element
 * @package clonefish
 * @subpackage elements
 */
class inputRadioDynamic extends inputRadio {

  // --------------------------------------------------------------------------
  function inputRadioDynamic( $key, $configvalues, &$db ) {

    // call parent constructor
    $parent_class_name = get_parent_class( $this );
    $this->$parent_class_name( $key, $configvalues );

    $this->db = &$db;

    if ( is_object( $this->db ) ) {

      $results = $this->db->execute( $this->sql );
      if ( $results ) {
        while ( !$results->EOF ) {
          $value = each( $results->fields );
          $label = each( $results->fields );
          $this->values[ $value[ 'value' ] ] = $label[ 'value' ];
          $results->moveNext();
        }
      }
      else
        die( sprintf( CF_ERR_DB_ERROR, $this->name, $this->db->errormsg() ) );
    }
    else
      die( sprintf( CF_ERR_MISSING_DB_OBJECT, $this->name ) );

  }

}

?>