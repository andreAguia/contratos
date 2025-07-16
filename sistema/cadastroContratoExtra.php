<?php

/*
 * Faz com que após ser incluído o sistema possa
 * ser redirecionado  
 */

# Pega o idpncp
$idPncp = $campoValor[27];

# Verifica se está preenchido
if(!empty($idPncp)){
    $pncp = new Pncp();
    $campoValor[26] = $pncp->transformaIdToLink($idPncp);
}