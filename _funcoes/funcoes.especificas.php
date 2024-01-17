<?php

/**
 * Função que retorna os dados de um contrato
 */
function get_DadosContrato($idContrato) {
    $contrato = new Contrato();
    $contrato->exibeResumoDados($idContrato);
}

##########################################################

function get_DadosContratoPagamento($idContrato) {
    $contrato = new Contrato();
    # Exibe os dados do contrato
    $contrato->exibeResumoDados($idContrato);

    # Exibe a observação do saldo (quando houver)
    $contrato->exibeObsSaldo($idContrato);

    $grid = new Grid();
    $grid->abreColuna(3);

    # Exibe o valor    
    $contrato->exibeValorTotalPainel($idContrato, true);
    #$contrato->exibeValorPorAnoPainel($idContrato, true);

    $grid->fechaColuna();
    $grid->abreColuna(3);

    # Exibe o valor Liquidado
    $pagamento = new Pagamento();
    $pagamento->exibeValorLiquidado($idContrato);

    $grid->fechaColuna();
    $grid->abreColuna(3);

    # Exibe o Saldo
    $pagamento->exibeValorSaldo($idContrato);

    $grid->fechaColuna();
    $grid->abreColuna(3);

    # Exibe o Pgto Ideal
    $pagamento->exibePgtoIdeal($idContrato);

    $grid->fechaColuna();
    $grid->fechaGrid();
}

##########################################################

function get_DadosContratoReceita($idContrato) {
    $contrato = new Contrato();
    # Exibe os dados do contrato
    $contrato->exibeResumoDados($idContrato);

    # Exibe a observação do saldo (quando houver)
    $contrato->exibeObsSaldo($idContrato);
}

##########################################################

function ressaltaSaiu($tipo) {

    if ($tipo == "Saiu") {
        label("Saiu", "alert");
    } else {
        return $tipo;
    }
}

##########################################################
/**
 * Função que exibe um subtitulo na ficha cadastral
 * 
 */

function tituloRelatorio($texto) {
    $div = new Div("tituloFichaCadastral");
    $div->abre();
    echo $texto;
    $div->fecha();
}

##########################################################