<?php

/*
 * Rotina Extra de Validação
 * 
 */

/*
 * Rotina desabilitada a pedido de Kátia para permitir
 * aditivos com data inicial mas sem prazo.
 */

# Se a data inicial estiver em branco o prazo e o tipo também tem que estar
if (empty($campoValor[9])) {
    $campoValor[10] = null; // prazo
    $campoValor[11] = null; // tipo
}

# Se foi preenchido o prazo o tipo não pode estar em branco
if (!empty($campoValor[10]) AND empty($campoValor[11])) {
    $msgErro .= 'Com o prazo preenchido, Deve-se informar se é por mês ou por dia';
    $erro = 1;
}

# Não pode prazo negativo
if ($campoValor[10] < 0) {
    $msgErro .= 'O Prazo não pode ser negativo.';
    $erro = 1;
}


// A Rotina abaixo foi retirara a pedido d Kátia 
// para poder cadastrar aditivos sem data inicial
//# Só pode passar se todos estão preenchidos ou se todos estão em branco
//$total = 0;
//if (empty($campoValor[9])) {
//    $total++;
//}
//
//if (empty($campoValor[10])) {
//    $total++;
//}
//
//if (empty($campoValor[11])) {
//    $total++;
//}
//
//if (($total > 0) AND ($total < 3)) {
//    $msgErro .= 'A data inicial, prazo e tipo do prazo devem ser preenchidos todos ou nenhum!\n';
//    $erro    = 1;
//}
