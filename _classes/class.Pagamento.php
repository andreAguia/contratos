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
        if (empty($conteudo["mesReferencia"]) OR empty($conteudo["anoReferencia"])) {
            return "---";
        } else {
            return get_nomeMes($conteudo["mesReferencia"]) . " / " . $conteudo["anoReferencia"];
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

    ###########################################################

    public function getPgtoIdeal($idContrato = null) {
        # Verifica se foi informado
        if (vazio($idContrato)) {
            alert("É necessário informar o id do Contrato.");
            return;
        }

        # Acessa a classe do contrato
        $contrato = new Contrato();

        # Inicia as parcelas com os meses
        $tempo = $contrato->getTempoTotal($idContrato);
        $parcelas = $tempo["meses"];

        # Pega o saldo
        $saldo = $this->getValorSaldo($idContrato);

        # Pega o ultimo mes pago
        $ultimoMesPago = $this->getUltimoMesPago($idContrato);
        $anoPago = $ultimoMesPago["anoReferencia"];
        $mesPago = $ultimoMesPago["mesReferencia"];

        # Pega a vigencia
        $vigencia = $contrato->getVigencia($idContrato);
        $anoVigencia = year($vigencia);
        $mesVigencia = month($vigencia);
        $diaVigencia = day($vigencia);

        # Diferença
        $anoDif = $anoVigencia - $anoPago;
        
        # Verifica se e o mesmo ano
        if ($anoDif == 0) {
            $parcelas = $mesVigencia - $mesPago;
        } elseif ($anoDif == 1) {
            $parcelas = (12 - $mesPago) + $mesVigencia;
        } elseif ($anoDif > 1) {
            $parcelas = (12 - $mesPago) + $mesVigencia + (12 * ($anoDif - 1));
        }

        # Desconsidera o ultimo mes caso o dia da vigencia for menor que 20
        if ($diaVigencia < 20) {
            $parcelas--;
        }

        # Retorna o saldo / pelas parcelas
        if ($parcelas > 0) {
            return [
                $saldo / $parcelas,
                $saldo,
                $parcelas
            ];
        }else{
            return null;
        }
    }

    ###########################################################

    public function getUltimoMesPago($idContrato = null) {
        # Verifica se foi informado
        if (vazio($idContrato)) {
            alert("É necessário informar o id do Contrato.");
            return;
        }

        # Conecta ao Banco de Dados
        $contratos = new Contratos();

        # Monta o select
        $select = "SELECT anoReferencia,
                          mesReferencia
                     FROM tbpagamento
                    WHERE idContrato = {$idContrato}
                    ORDER BY anoReferencia desc, mesReferencia desc LIMIT 1";

        $soma = $contratos->select($select, false);
        return $soma;
    }

    ###########################################################

    public function exibePgtoIdeal($idContrato = null) {
        # Verifica se foi informado
        if (vazio($idContrato)) {
            alert("É necessário informar o id do Contrato.");
            return;
        }

        # exibe o resultado
        $painel = new Callout("success");
        $painel->abre();

        titulo("Parcela Mensal Ideal");

        $valorTotal = $this->getPgtoIdeal($idContrato);

        p("R$ " . formataMoeda($valorTotal[0]), "pvalorTotalPositivo");
        p("(R$ " . formataMoeda($valorTotal[1]) . " / {$valorTotal[2]})", "pPagamentos");
        $painel->fecha();
    }

    ############################################################
}
