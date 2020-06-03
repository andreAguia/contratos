<?php

/**
 * Função que retorna os dados de um contrato
 */
function get_DadosContrato($idContrato)
{
    $contrato = new Contrato();
    $contrato->exibeResumoDados($idContrato);
}

##########################################################
/**
 * Função que retorna uma tabela com os dados do servidor
 * 
 * Obs esta função só existe para ser usada na classe modelo
 */
function get_DadosServidor($idServidor)
{
    Grh::listaDadosServidor($idServidor);
}

##########################################################