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
class FCKEditorArea2 extends element {

  var $width  = 700;
  var $height = 300;
  var $includepath; // filesystem path to directory of fckeditor.php wrapper, trailing slash required
  var $jspath;      // base URI to fckeditor
  var $configpath;  // path to custom FCK configuration JS file (including filename)

  // -------------------------------------------------------------------------
  function FCKEditorArea2( $name, $configvalues ) {
    
    Element::Element( $name, $configvalues );

    if ( !strlen( $this->jspath ) )
      if ( !isset( $GLOBALS['FCKeditorBasePath'] ) )
        die( sprintf(
          CF_ERR_FCK_JSPATH_NOT_SET,
          $this->getName() 
        ) );
      else
        $this->jspath = $GLOBALS['FCKeditorBasePath'];

  }

  // -------------------------------------------------------------------------
  function getHTML() {

    // FCKEditor 2.x
    if ( !file_exists( $this->includepath . 'fckeditor.php' ) )
      die( sprintf(
        CF_ERR_FCK_INCLUDEPATH_NOT_SET,
        $this->getName() 
      ) );

    include_once( $this->includepath . 'fckeditor.php');

    $oFCKeditor           = new FCKeditor( $this->name );
    $oFCKeditor->BasePath = $this->jspath;

    if ( $this->configpath )
      $oFCKeditor->Config['CustomConfigurationsPath'] =
        $this->configpath;

    $oFCKeditor->Value    = $this->value;
    $oFCKeditor->Width    = $this->width;
    $oFCKeditor->Height   = $this->height;

    return 
      $oFCKeditor->CreateHTML() ;

  }

}

?>