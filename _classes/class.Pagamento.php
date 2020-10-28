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

        $row = $contratos->select($select, false, true);

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

    private function getValorLiquidado($idContrato = null, $idNatureza = null) {
        # Verifica se foi informado o id
        if (vazio($idContrato)) {
            alert("É necessário informar o id do Contrato.");
            return;
        }

        # Inicia a variável de retorno
        $valorTotal = 0;

        # Pega os valores dos pgtos
        $contratos = new Contratos();

        if (is_null($idNatureza)) {
            $select = "SELECT valor, valorNegativo FROM tbpagamento WHERE idContrato = {$idContrato}";
        } else {
            if ($idNatureza == 0) {
                $select = "SELECT valor, valorNegativo FROM tbpagamento WHERE idContrato = {$idContrato} and idNatureza IS NULL";
            } else {
                $select = "SELECT valor, valorNegativo FROM tbpagamento WHERE idContrato = {$idContrato} and idNatureza = {$idNatureza}";
            }
        }
        $row = $contratos->select($select);
        $numPgtos = $contratos->count($select);

        # Verifica se tem algum aditivo
        if ($numPgtos > 0) {
            foreach ($row as $item) {
                if ($item["valorNegativo"]) {
                    $valorTotal -= $item["valor"];
                } else {
                    $valorTotal += $item["valor"];
                }
            }
        }

        return $valorTotal;
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


        p(formataMoeda2($valorTotal), "pvalorTotalPositivo");

        if ($this->temNatureza($idContrato)) {
            $link = new Link("por Natureza", "?fase=porNatureza&id={$idContrato}", "Detalha os pagamanto pela natureza do gasto");
            $link->set_id("porNatureza");
            $link->show();
        }

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
            p(formataMoeda2($valorSaldo), "pvalorTotalPositivo");
        } else {
            p(formataMoeda2($valorSaldo), "pvalorTotalNegativo");
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

        # Pega a data Inicial
        $dtInicial = $contrato->getDtInicial($idContrato);

        # Se não tiver data Inicial não podemos calcular a pgto inicial
        if (empty($dtInicial)) {
            return [null, null, "0)<br/>(A data inicial do contrato não foi cadastrada"];
        }

        # Se o saldo for 0 ou menor que 0
        if ($saldo < 1) {
            return [null, null, "0)<br>(Não há saldo a ser pago!"];
        }

        if (!$ultimoMesPago) {
            $anoPago = year($dtInicial);
        } else {
            $anoPago = $ultimoMesPago["anoReferencia"];
        }

        if (!$ultimoMesPago) {
            $mesPago = month($dtInicial);
        } else {
            $mesPago = $ultimoMesPago["mesReferencia"];
        }

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
        } else {
            return [null, null, null];
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

        $ultimo = $contratos->select($select, false);
        return $ultimo;
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

        p(formataMoeda2($valorTotal[0]), "pvalorTotalPositivo");
        p("(" . formataMoeda2($valorTotal[1]) . " / {$valorTotal[2]})", "pPagamentos");
        $painel->fecha();
    }

    ###########################################################

    public function exibeValorLiquidadoPorNatureza($idContrato = null) {
        # Verifica se foi informado
        if (vazio($idContrato)) {
            alert("É necessário informar o id do Contrato.");
            return;
        }

        # Conecta ao Banco de Dados
        $contratos = new Contratos();

        # Monta o select
        $select = "SELECT distinct idNatureza, natureza
                     FROM tbpagamento LEFT JOIN tbnatureza USING (idNatureza)
                    WHERE idContrato = {$idContrato} and idNatureza IS NOT NULL
                 ORDER BY natureza desc ";

        $row = $contratos->select($select);
        $numPgtos = $contratos->count($select);
        $resultado[] = [null, $this->getValorLiquidado($idContrato, 0)];
        
        # Percorre o array
        if ($numPgtos > 0) {
            foreach ($row as $item) {
                $resultado[] = [$item["natureza"], $this->getValorLiquidado($idContrato, $item["idNatureza"])];
            }
        }

        # Exemplo de tabela simples
        $tabela = new Tabela();
        $tabela->set_titulo("Valor Liquidado Por Natureza");
        $tabela->set_conteudo($resultado);
        $tabela->set_label(array("Natureza", "Valor"));
        $tabela->set_width(array(70, 30));
        $tabela->set_align(array("left", "center"));
        $tabela->set_funcao(array(null, "formataMoeda2"));
        $tabela->set_rodape("Total: " . formataMoeda2($this->getValorLiquidado($idContrato)));
        $tabela->show();
    }

    ###########################################################

    public function temNatureza($idContrato = null) {
        # Verifica se foi informado
        if (vazio($idContrato)) {
            alert("É necessário informar o id do Contrato.");
            return;
        }

        # Conecta ao Banco de Dados
        $contratos = new Contratos();

        # Monta o select
        $select = "SELECT natureza
                     FROM tbpagamento LEFT JOIN tbnatureza USING (idNatureza)
                    WHERE idContrato = {$idContrato}
                      AND natureza is not null";

        $soma = $contratos->count($select);

        if ($soma > 0) {
            return true;
        } else {
            return false;
        }
    }

    ###########################################################

    public function getNumPgtoNatureza($idNatureza = null) {
        # Verifica se foi informado
        if (vazio($idNatureza)) {
            alert("É necessário informar o id.");
            return;
        }

        # Conecta ao Banco de Dados
        $contratos = new Contratos();

        # Monta o select
        $select = "SELECT idNatureza
                     FROM tbpagamento
                    WHERE idNatureza = {$idNatureza}";

        return $contratos->count($select);
    }

    ###########################################################

    public function exibeValor($idPagamento = null) {

        # Verifica se foi informado o id
        if (vazio($idPagamento)) {
            alert("É necessário informar o id do Pgto.");
            return;
        }

        # Pega os dados
        $conteudo = $this->getDados($idPagamento);

        # Valor
        if (empty($conteudo["valor"])) {
            p("----", "p#pvalorNulo");
        } else {
            if ($conteudo["valorNegativo"]) {
                p("- " . formataMoeda2($conteudo['valor']), "pvalorNegativo");
            } else {
                p(formataMoeda2($conteudo['valor']), "pvalorPositivo");
            }
        }
    }

    ##########################################################################################
}
