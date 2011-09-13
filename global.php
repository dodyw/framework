<?php  
  include 'config.php';

  define('PATH',dirname(__FILE__).'/');
  session_start();

  /**
   * connect to database
   */  
  
  include(PATH.'lib/adodb/adodb.inc.php');
  
  $app->db = ADONewConnection('mysql');  
  $app->db->debug = false;
  $app->db->Connect(DB_HOST, DB_USER, DB_PWD, DB_NAME);
  $ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
    
  function &CountExecs(, , ) {
    global ;
  
    if (!is_array(inputarray)) ++;
    elseif (is_array(reset()))  += sizeof();
    else ++;
  
    return ;
  }  
  
  $app->db->fnExecute = 'CountExecs';
?>