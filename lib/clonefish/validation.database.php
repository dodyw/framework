<?php

/**
 * Clonefish form generator class 
 * (c) phpformclass.com, Dots Amazing
 * All rights reserved.
 * 
 * @copyright  2010 Dots Amazing
 * @link       http://phpformclass.com
 * @package    clonefish
 * @subpackage validation
 */

/* 
 * Validation
 * @package clonefish
 * @subpackage validationTypes
 */
class databaseValidation extends validation {
 
  var $sql;
  var $condition = 'and';
  var $minrows = false;
  var $maxrows = false;
  var $value   = false;
  var $field   = false;
  var $help;
  var $helpmaxrows;
  var $helpminrows;
  var $helpvalue;

  // settings coming from the settings array

  var $form;         // form 
  
  // -------------------------------------------------------------------------
  function isValid() {

    $results = Array();

    if ( $this->checkDependencyPHP() ) {

    if ( strlen( $this->sql ) ) {

      if ( !is_object( $this->form->db ) )
        die(
          sprintf(
            CF_ERR_CONFIG_DATABASE_VALIDATION_DB_MISSING,
            $this->element->getName()
          )
        );

      // templating the sql

      preg_match_all(
        '/<FORM\.(.+)>/Ums', 
        $this->sql, 
        $templatevars, 
        PREG_SET_ORDER 
      );

      $sql = $this->sql;
      foreach ( $templatevars as $match )
        $sql = str_replace( 
          $match[ 0 ],
          $this->form->db->quote( $this->form->getValue( $match[ 1 ], 1 ) ),
          $sql
        );

      $rs = $this->form->db->execute(
        str_replace( '<VALUE>',
          $this->form->db->quote( $this->element->getValue( 0 ) ),
          $sql
        )
      );

      if ( !$rs )
        die( sprintf( CF_ERR_DATABASE_VALIDATION_DB_ERROR, $this->element->getName(), $this->form->db->errormsg() ) );
      else {

        $valid = true;
        $helps = Array();

        if ( ( $this->field !== false ) && ( $this->value !== false ) ) {

          $result = $rs->fields[ $this->field ] == $this->value;

          if ( $this->condition == 'and' )
            $valid = $valid && $result;
          else
            $valid = $valid || $result;

          if ( strlen( $this->helpvalue ) )
            $helps[] = $this->helpvalue;
        }
        else
          if ( 
               ( $this->field !== false ) || 
               ( $this->value !== false ) 
             ) 
            die( sprintf( CF_ERR_DATABASE_VALIDATION_VALUE_OR_FIELD_MISSING, $this->element->getName() ) );

        if ( $this->minrows !== false ) {

          $result = ( $rs->RecordCount() >= $this->minrows );

          if ( $this->condition == 'and' ) 
            $valid = $valid && $result;
          else
            $valid = $valid || $result;

          if ( strlen( $this->helpminrows ) ) 
            $helps[] = $this->helpminrows;
        }

        if ( $this->maxrows !== false ) {
          $result = ( $rs->RecordCount() <= $this->maxrows );

          if ( $this->condition == 'and' ) 
            $valid = $valid && $result;
          else
            $valid = $valid || $result;

          if ( strlen( $this->helpmaxrows ) )
            $helps[] = $this->helpmaxrows;

        }

        if ( !count( $helps ) && strlen( $this->help ) ) 
          $helps[] = $this->help;

        if ( !$valid ) {

          if ( !count( $helps ) )
            $helps[] =
              sprintf( 
                $this->selecthelp( $this->element, CF_STR_DATABASE_INPUT_INVALID ),
                $this->element->getDisplayName()
              );

          foreach ( $helps as $message ) {
            $results[] = $message;
            $this->element->addMessage( $message );
          }

        }
      }
    }
    else
      die(
        sprintf(
          CF_ERR_CONFIG_DATABASE_VALIDATION_SQL_MISSING,
          $this->element->getName()
        )
      );

    }

    return $results;

  } 

}

?>