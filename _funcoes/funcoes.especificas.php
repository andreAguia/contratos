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