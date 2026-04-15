<?php
include('./NSSuite.php');
foreach (glob('./Requisicoes/*/*.php') as $filename) { 
    require_once($filename); 
} 

// Para testes de metodos::.
?>
