<?php

class Pagamento {

    public function getDados($idPagamento = null) {
        # Conecta ao Banco de Dados
        $contratos = new Contratos();

        # Verifica se foi informado
        if (vazio($idPagamento)) {
            alert("É necessário informar o id dp Pagamento.");
            return;
        }

        # Pega os dados
        $select = 'SELECT *
                     FROM tbpagamento
                    WHERE idPagamento = ' . $idPagamento;

        $row = $contratos->select($select, false);

        # Retorno
        return $row;
    }

#####################################################################################

    public function exibeReferencia($idPagamento) {
        # Verifica se foi informado
        if (vazio($idPagamento)) {
            alert("É necessário informar o id.");
            return;
        }

        # Conecta ao Banco de Dados
        $contratos = new Contratos();

        # Pega os dados
        $conteudo = $this->getDados($idPagamento);
        
        # Verifica se foi informado a referência
        if(empty($conteudo["mesReferencia"]) OR empty($conteudo["anoReferencia"])){
            return "---";
        }else{
            return get_nomeMes($conteudo["mesReferencia"])."/".$conteudo["anoReferencia"];
        }
    }

    ############################################################

    private function getValorLiquidado($idContrato = null) {
        # Verifica se foi informado o id
        if (vazio($idContrato)) {
            alert("É necessário informar o id do Contrato.");
            return;
        }
        
        # Conecta ao Banco de Dados
        $contratos = new Contratos();

        # Monta o select
        $select = "SELECT SUM(valor)
                     FROM tbpagamento
                    WHERE idContrato = {$idContrato}";

        $soma = $contratos->select($select, false);
        return $soma[0];
    }

    ###########################################################

    private function getValorSaldo($idContrato = null) {
        # Verifica se foi informado o id
        if (vazio($idContrato)) {
            alert("É necessário informar o id do Contrato.");
            return;
        }
        
        $contrato = new Contrato();
        $valorTotal = $contrato->getValorTotal($idContrato);
        $valorLiquidado = $this->getValorLiquidado($idContrato);
        return $valorTotal - $valorLiquidado;
    }

    ###########################################################


    public function exibeValorLiquidado($idContrato = null) {
        # Verifica se foi informado
        if (vazio($idContrato)) {
            alert("É necessário informar o id do Contrato.");
            return;
        }

        # exibe o resultado
        $painel = new Callout("secondary");
        $painel->abre();

        titulo("Valor Liquidado");

        $valorTotal = $this->getValorLiquidado($idContrato);
        
        p("R$ " . formataMoeda($valorTotal), "pvalorTotalPositivo");
        $painel->fecha();
    }

    ############################################################

    public function exibeValorSaldo($idContrato = null) {
        # Verifica se foi informado
        if (vazio($idContrato)) {
            alert("É necessário informar o id do Contrato.");
            return;
        }

        # exibe o resultado
        $painel = new Callout("secondary");
        $painel->abre();

        titulo("Saldo");

        $valorSaldo = $this->getValorSaldo($idContrato);

        if ($valorSaldo >= 0) {
            p("R$ " . formataMoeda($valorSaldo), "pvalorTotalPositivo");
        } else {
            p("R$ " . formataMoeda($valorSaldo), "pvalorTotalNegativo");
        }
        $painel->fecha();
    }

    #####################################################################################
}
