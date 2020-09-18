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
function get_DadosContratoPagamento($idContrato)
{   
    $contrato = new Contrato();
    $contrato->exibeResumoDados($idContrato);
    
    $grid = new Grid();
    $grid->abreColuna(3);

    # Exibe o valor    
    $contrato->exibeValorTotalPainel($idContrato);

    $grid->fechaColuna();
    $grid->abreColuna(3);

    # Exibe o valor
    $pagamento = new Pagamento();
    $pagamento->exibeValorLiquidado($idContrato);

    $grid->fechaColuna();
    $grid->abreColuna(3);

    # Exibe o valor
    $pagamento->exibeValorSaldo($idContrato);

    $grid->fechaColuna();
    $grid->abreColuna(3);

    # Exibe o valor
    $pagamento->exibePgtoIdeal($idContrato);

    $grid->fechaColuna();
    $grid->fechaGrid();
}

##########################################################
function ressaltaSaiu($tipo){
    
    if($tipo == "Saiu"){
        label("Saiu","alert");
    }else{
        return $tipo;
    }
    
}