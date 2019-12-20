<?php

function debug($conteudo = '', $break = false) {

    echo "<pre>";
    print_r($conteudo);
    echo "</pre>";
    if($break) {
        die();
    }

}

?>