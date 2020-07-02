<?php
/*
 * Rotina Extra de Validação
 * 
 */

# Só pode passar se todos estão preenchidos ou se todos estão em branco
$total = 0;
if (empty($campoValor[8])) {
    $total++;
}

if (empty($campoValor[9])) {
    $total++;
}

if (empty($campoValor[10])) {
    $total++;
}

if (($total > 0) AND ($total < 3)) {
    $msgErro .= 'A data inicial, prazo e tipo do prazo devem ser preenchidos todos ou nenhum!\n';
    $erro    = 1;
}
