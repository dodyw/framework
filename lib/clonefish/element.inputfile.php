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
class inputFile extends element {

  var $thumbnail;
  var $text;

  var $href = '';

  var $value; 
    // $value is only filled when the $binaryvalue property is
    // set to true. 
  var $binaryvalue = false;

  var $delete;

  var $deletelayout; // set in constructor
  var $linklayout      = '<a target="_blank" href="%href%">%thumbortext%</a><br />';
  var $thumbnaillayout = '<img src="%thumbnail%" border=0 alt="%text%" />';

  // --------------------------------------------------------------------------
  function inputFile( $name, $configvalues ) {

    // 1) without validation: 
    //     a) defined value
    //     b) form submission value from file
    //
    // 2) with validation:
    //     a) defined value in constructor and 
    //        defined value for invalid input
    //     b) form submission value upon valid input

    if ( !ini_get('file_uploads') )
      die( CF_ERR_FILE_UPLOADS_NOT_ALLOWED );

    $this->deletelayout =
      '<br /><input type="button" value="' . CF_STR_IMAGE_DELETE . '" onclick="location.href=\'%delete%\';" />';

    // call parent constructor
    // may override the value set by submission
    $parent_class_name = get_parent_class( $this );
    $this->$parent_class_name(
      $name, $configvalues
    );

  }

  // --------------------------------------------------------------------------
  function getHTML() {

    $file = '';

    if ( strlen( $this->href ) ) {

      $trans = Array(
        '%thumbnail%' => $this->thumbnail,
        '%href%'      => $this->href,
      );

      if ( $this->text )
        $trans['%text%'] = htmlspecialchars( $this->text );
      else
        $trans['%text%'] = basename( $this->href );
      
      if ( $this->thumbnail ) {
        // with thumbnail
        $trans['%thumbortext%'] = strtr( $this->thumbnaillayout, $trans );
        $file = strtr( $this->linklayout, $trans );
      }
      else {
        // without thumbnail
        $trans['%thumbortext%'] = $trans['%text%'];
        $file = strtr( $this->linklayout, $trans );
      }

    }

    $file .= 
      '<input ' .
      'type="file" ' .
      'name="' . $this->name . '" ' .
      'id="' . $this->_getHTMLId() . '" ' .
      $this->html . 
      ' />';

    if ( strlen( $this->href ) && $this->delete ) 
      $file .= strtr( 
        $this->deletelayout, 
        Array( '%delete%' => $this->delete )
      );

    return $file;

  }

  // --------------------------------------------------------------------------
  function _readContents() {

    if ( $this->binaryvalue &&
         isset( $_FILES[ $this->name ] )
       ) {

      if ( file_exists( $_FILES[ $this->name ]['tmp_name'] ) ) 
        if ( function_exists( 'file_get_contents' ) )
          $this->value =
            file_get_contents( $_FILES[ $this->name ]['tmp_name'] );
        else
          $this->value =
            implode('', file( $_FILES[ $this->name ]['tmp_name'] ) );

    }

  }

}

?>