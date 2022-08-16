<?php

/*
 * Rotina Extra de Validação
 * 
 */


# Pega a data de Nascimento
$dataAno = year(date_to_php($campoValor[0]));
$referenciaAno = $campoValor[3];

if ($dataAno <> $referenciaAno) {
    $erro = 1;
    $msgErro .= 'O ano do lançamento deverá ser o mesmo do ano de referência!\n';
}