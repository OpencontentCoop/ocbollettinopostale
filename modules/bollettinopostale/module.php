<?php
$Module = array( 'name' => 'bollettinopostale', 'variable_params' => false );

$ViewList = array();
$ViewList['stampa'] = array(
    'script' => 'stampa.php',
    'params' => array( 'orderid', 'debug' ),
    'unordered_params' => array() );

?>