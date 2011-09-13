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

include_once('element.select.php');

/* 
 * Element
 * @package clonefish
 * @subpackage elements
 */
class selectFile extends select {

  var $directory;          // path to directory to list

  var $tree          = 1;  // if 1, directory will be read recursively
  var $includedirs   = 0;  // if 1, directories will be included in the select
  var $includefiles  = 1;  // if 1, files will be included in the select

  var $exclude       = Array(); // specific filenames to exclude
  var $excludere;               // complete regexp string (used with preg_match) to specify files to exclude, eg '/\.xls$/i'
  var $includere;               // complete regexp string (used with preg_match) to specify files to include, eg '/\.xls$/i'

  var $fullpath      = 0;  // if 1, full directory paths will be displayed 
                           // in the select

  var $trailingslash = 1;  // if 1, the directory names will end with a '/'

  var $fullpathvalue = 0;  // if 1, the values will contain full path
                           // even if 'fullpath' is set to 0

  var $format    = '%1$s (%2$d bytes)'; // format string for file entries
  var $formatdir = '%1$s%';               // format string for directory entries
  var $order;              // file order: name, namedesc, size, sizedesc. Default: unordered (as provided by filesystem)

  // private vars
  var $_defaultexcludes = Array( '.', '..' );
  // mustn't be removed, makes the script run into an infinite cycle

  // --------------------------------------------------------------------------
  function selectFile( $key, $configvalues ) {

    // call parent constructor
    $parent_class_name = get_parent_class( $this );
    $this->$parent_class_name( $key, $configvalues );

    if ( is_dir( $this->directory ) ) {
      while ( substr( $this->directory, -1, 1 ) == '/' ) 
        $this->directory = substr( $this->directory,
          0, strlen( $this->directory ) - 1 
        );
      $this->buildTree( $this->directory, 0 );
    }
    else
      die( sprintf( CF_ERR_DIRECTORY_INACCESSIBLE, $this->name, $this->directory ) );

  }

  // --------------------------------------------------------------------------
  function buildTree( $directory, $level ) {

    $dir        = opendir( $directory );
    $dirverbose = $directory;
    
    if ( !$this->fullpath ) 
      $dirverbose = substr( $directory, strlen( $this->directory ) + 1 ) ;
    
    if ( strlen( $dirverbose ) ) 
      $dirverbose .= '/';

    $currentdir = Array();

    while ( $file = readdir( $dir ) ) {

      $include = 
        ( !in_array( $file, array_merge( $this->exclude, $this->_defaultexcludes ) ) )
        &&
        (
          !strlen( $this->excludere ) 
          ||
          ( strlen( $this->excludere ) && !preg_match( $this->excludere, $file ) )
        )
        &&
        (
          !strlen( $this->includere ) 
          ||
          ( strlen( $this->includere ) && preg_match( $this->includere, $file ) )
        )
      ;

      $is_dir  = is_dir( $directory . '/' . $file );

      if ( $include ) {

        if (
             ( !$is_dir && $this->includefiles ) || 
             ( $is_dir && $this->includedirs ) 
           ) {
          if ( $this->fullpathvalue )
            $value = $directory . '/' . $file;
          else
            $value = $dirverbose . $file;

          $currentdir[] =
            Array(
              'name'   => $dirverbose . $file . ( $is_dir && $this->trailingslash ? '/' : '' ),
              'value'  => $value,
              'size'   => 0,
              'is_dir' => $is_dir
            );

          // get filesize only when needed, this way we can avoid
          // unnecessary filesystem access
          if (
               in_array( $this->order, Array( 'size', 'sizedesc' ) ) ||
               strpos( $this->format, '2$' )
             )
            $currentdir[ count( $currentdir ) - 1 ]['size'] = 
              filesize( $directory . '/' . $file );

        }

      }

      // get children
      if (
           $is_dir && 
           $this->tree &&
           !in_array( $file, array_merge( $this->exclude, $this->_defaultexcludes ) )
         )
        $this->buildTree( $directory . '/' . $file, $level + 1 );

    }

    if ( count( $currentdir ) ) {

      if ( strlen( $this->order ) )
        switch ( $this->order ) {
          case 'name':     usort( $currentdir, Array( 'selectFile', 'sortEntriesByName' ) ); break;
          case 'namedesc': usort( $currentdir, Array( 'selectFile', 'sortEntriesByNameDesc' ) ); break;
          case 'size':     usort( $currentdir, Array( 'selectFile', 'sortEntriesBySize' ) ); break;
          case 'sizedesc': usort( $currentdir, Array( 'selectFile', 'sortEntriesBySizeDesc' ) ); break;
          default:
            die( sprintf( CF_ERR_SELECTFILE_ORDER_UNKNOWN, $this->name, $this->order ) );
            break;
        }

      foreach ( $currentdir as $entry )
        $this->values[ $entry['value'] ] = $this->formatName( $entry );

    }

  }

  function formatName( $item ) {

    return
      sprintf(
        $item['is_dir'] ? $this->formatdir : $this->format,
        $item['name'],
        @$item['size']
      );

  }

  // usort callback function configuration won't allow parameters:
  // wrapper methods to the rescue
  static function sortEntriesBySize( $a, $b ) {
    return selectFile::sortEntriesCore( $a, $b, 'size', '' );
  }

  static function sortEntriesBySizeDesc( $a, $b ) {
    return selectFile::sortEntriesCore( $a, $b, 'size', 'desc' );
  }

  static function sortEntriesByName( $a, $b ) {
    return selectFile::sortEntriesCore( $a, $b, 'name', '' );
  }

  static function sortEntriesByNameDesc( $a, $b ) {
    return selectFile::sortEntriesCore( $a, $b, 'name', 'desc' );
  }

  static function sortEntriesCore( $a, $b, $attribute, $desc ) {

    if ( !isset( $a[ $attribute ] ) ||
         !isset( $b[ $attribute ] ) ||
         ( $a[ $attribute ] == $b[ $attribute ] )
       )
         return 0;

    $result = $a[ $attribute ] < $b[ $attribute ] ? -1 : 1;

    if ( $desc == 'desc' )
      $result = -1 * $result;

    return $result;

  }

}

?>