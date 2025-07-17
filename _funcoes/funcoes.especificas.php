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

    # Inicia as Classes
    $contrato = new Contrato();
    $pagamento = new Pagamento();

    /*
     *  Exibe os dados do contrato
     */
    #$contrato->exibeResumoDados($idContrato);

    /*
     *  Exibe a observação do saldo (quando houver)
     */
    $contrato->exibeObsSaldo($idContrato);

    $grid = new Grid();

    /*
     * Exibe o Valor Total do Contrato
     */
    $grid->abreColuna(3);
    $contrato->exibeValorTotalPainel($idContrato, true);
    $grid->fechaColuna();

    /*
     * Exibe o Valor Liquidado
     */
    $grid->abreColuna(3);
    $pagamento->exibeValorLiquidado($idContrato);
    $grid->fechaColuna();

    /*
     * Exibe o Saldo 
     */
    $grid->abreColuna(3);
    $pagamento->exibeValorSaldo($idContrato);
    $grid->fechaColuna();

    /*
     * Exibe o Pgto Ideal
     */

    $grid->abreColuna(3);
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