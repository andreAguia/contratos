<?php

/*
 * Rotina Extra de Validação
 * 
 */


# Impede que os dois processos sejam preenchidos 
if (!empty($campoValor[0]) AND !empty($campoValor[1])) {
    $erro = 1;
    $msgErro .= 'Somente um processo deve ser preenchido!\n';
}

# Impede que os dois processos estejam em branco preenchidos 
if (empty($campoValor[0]) AND empty($campoValor[1])) {
    $erro = 1;
    $msgErro .= 'Pelo menos um processo deve ser preenchido!\n';
}