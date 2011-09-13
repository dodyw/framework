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

/* 
 * Element
 * @package clonefish
 * @subpackage elements
 */
class inputCheckboxDynamic extends element {

  var $onvalue  = 1;
  var $offvalue = 0;
  var $value  = Array();
  var $values = Array();
  var $layout = "%s";
  var $itemlayout = "%indent% %checkbox% %label%<br />\n";
  var $sql;
  var $divide;
  var $divider;
  var $valuesql;

  var $levelprefix = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
  var $levels = Array();

  // -------------------------------------------------------------------------
  function inputCheckboxDynamic( $name, $configvalues, &$db, $id ) {

    $this->name = $name;
    foreach ( $configvalues as $key => $value )
      $this->$key = $value;

    $this->db = &$db;

    if ( strlen( $this->sql ) )

    if ( is_object( $this->db ) ) {

      if ( !isset( $this->treeparent ) ) {

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
      else {

        // tree-type select
        $this->buildTree( $this->treestart, 0);

      }

      if ( $this->valuesql ) {

        $results = $this->db->execute( sprintf( $this->valuesql, $id ) );

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

  // -------------------------------------------------------------------------
  function getHTML() {

    $i   = mt_rand( 1000, 10000 );
    $out = '';
    $counter = 0;

    foreach ( $this->values as $key => $value ) {

      $i++;

      $label    = '<label for="checkbox' . $i . '">' . $value . "</label>";
      $checked  = in_array( $key, $this->value );

      $checkbox = '<input '.
          $this->html . ' ' .
          'id="checkbox' . $i . '" ' .
          'type="checkbox" '.
          'name="' . $this->getName() . '[' . htmlspecialchars( $key ) . ']" ' .
          ( $checked ? 'checked="checked" ' : '' ) .
          'value="' . htmlspecialchars( $key ) . '" />';

      $replace = Array( 
        "%checkbox%" => $checkbox, 
        "%indent%"   => 
          isset( $this->levels[ $key ] ) ? 
          str_repeat( $this->levelprefix, $this->levels[ $key ] )
          : '',
        "%label%"    => $label
      );

      if ( $this->divide && $counter && ( ( $counter % $this->divide ) == 0 ) )
        $out .= $this->divider;

      $out .= strtr( $this->itemlayout, $replace );
      $counter++;

    }

    return sprintf( $this->layout, $out );

  }                   

  // -------------------------------------------------------------------------
  function setValue( $values, $magic_quotes_gpc ) {

    // if there is no 'onvalue' defined, browsers send 'on' as default
    // value

    $this->value = Array();

    foreach ( $values as $key => $value )
      $this->value[] = $this->_prepareInput( $value, $magic_quotes_gpc );

    return true;

  }

  // -------------------------------------------------------------------------
  function getJSfield() {

    die('getJSField unimplementable for inputCheckboxDynamic');

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

        $this->values[ $value[ 'value' ] ] = $label[ 'value' ];
        $this->levels[ $value[ 'value' ] ] = $level;

        // get children
        $this->buildTree( $results->fields[ $this->treeid ], $level + 1 );

        $results->moveNext();

      }

    else
      die( sprintf( CF_ERR_DB_ERROR, $this->name, $this->db->errormsg() ) );

  }

}

?>