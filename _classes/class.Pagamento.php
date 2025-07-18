<?php

class Pagamento {

    public function getDados($idPagamento = null) {
        # Conecta ao Banco de Dados
        $contratos = new Contratos();

        # Verifica se foi informado
        if (vazio($idPagamento)) {
            alert("É necessário informar o id do Pagamento.");
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
        if (empty($idContrato)) {
            alert("É necessário informar o id do Contrato.");
            return;
        }

        # Inicia a variável de retorno
        $valorTotal = 0;

        # Inicia as Classes
        $contratos = new Contratos();

        # Pega os valores dos pgtos que afetam o saldo
        if (is_null($idNatureza)) {
            $select = "SELECT valor,
                              tipo 
                         FROM tbpagamento LEFT JOIN tbnatureza USING (idNatureza) 
                        WHERE afetaSaldo = 's'
                          AND idContrato = {$idContrato}";
        } else {
            if ($idNatureza == 0) {
                $select = "SELECT valor,
                                  tipo 
                             FROM tbpagamento LEFT JOIN tbnatureza USING (idNatureza) 
                            WHERE afetaSaldo = 's' 
                              AND idContrato = {$idContrato} 
                              AND idNatureza IS NULL";
            } else {
                $select = "SELECT valor,
                                  tipo 
                             FROM tbpagamento LEFT JOIN tbnatureza USING (idNatureza) 
                            WHERE afetaSaldo = 's' 
                              AND idContrato = {$idContrato} 
                              AND idNatureza = {$idNatureza}";
            }
        }

        $row = $contratos->select($select);
        $numPgtos = $contratos->count($select);

        # Verifica se tem algum aditivo
        if ($numPgtos > 0) {
            foreach ($row as $item) {
                if ($item["tipo"] == 2) {
                    # Diminui Quando é estorno
                    $valorTotal -= $item["valor"];
                } elseif ($item["tipo"] == 1) {
                    # Aumenta quando é pgto
                    $valorTotal += $item["valor"];
                }
            }
        }

        return $valorTotal;
    }

    ###########################################################

    public function getValorSaldo($idContrato = null) {
        # Verifica se foi informado o id
        if (empty($idContrato)) {
            return null;
        }

        $contrato = new Contrato();
        $valorTotal = $contrato->getValorTotal($idContrato);
        $valorLiquidado = $this->getValorLiquidado($idContrato);

        # Verifica se tem Sra
        if ($this->temSra($idContrato)) {
            $sra = $this->getSra($idContrato);
            return $valorTotal - $valorLiquidado - $sra;
        } else {
            return $valorTotal - $valorLiquidado;
        }
    }

    ###########################################################

    public function exibeValorLiquidado($idContrato = null) {
        # Verifica se foi informado
        if (empty($idContrato)) {
            alert("É necessário informar o id do Contrato.");
            return;
        }

        # Exibe o titulo
        tituloTable("Valor Liquidado");

        $painel = new Callout("secondary");
        $painel->abre();

        $valorTotal = $this->getValorLiquidado($idContrato);

        p(formataMoeda2($valorTotal), "pvalorTotalPositivo");

        # Verifica se teve pagamentos
        if ($this->getNumPgto($idContrato) > 0) {

            $link = new Link("Detalhes");
            $link->set_id("porNatureza");
            $link->set_onClick("abreFechaDivId('divNatureza');");
            $link->set_title("Detalha os Pagamantos");
            $link->show();

            $div = new Div("divNatureza");
            $div->abre();
            br();

            $this->exibeValorLiquidadoPorNatureza($idContrato);
            $this->exibeValorLiquidadoPorAno($idContrato);

            # Verifica se tem algum pagamento sem afetar saldo
            if ($this->temPgtaSemSaldo($idContrato)) {

                # Exibe o titulo
                tituloTable("Pagamentos que<br/> Não Afetam o Saldo");

                $painel2 = new Callout("secondary");
                $painel2->abre();

                $this->exibeValorSemSaldoPorNatureza($idContrato);
                $this->exibeValorSemSaldoPorAno($idContrato);

                $painel2->fecha();
            }
            $div->fecha();
        }
        $painel->fecha();
    }

    ###########################################################

    public function exibeValorLiquidadoAnoRelatorio($texto) {
        # Separa os valores
        $pedaco = explode("&", $texto);

        $idContrato = $pedaco[0];
        $anoReferencia = $pedaco[1];

        # Verifica se foi informado
        if (empty($idContrato)) {
            return null;
        }

        return formataMoeda2($this->getValorLiquidadoPorAno($idContrato, $anoReferencia));
    }

    ############################################################

    public function exibeValorSaldo($idContrato = null) {
        # Verifica se foi informado
        if (vazio($idContrato)) {
            alert("É necessário informar o id do Contrato.");
            return;
        }

        # exibe o resultado
        tituloTable("Saldo");

        $painel = new Callout("secondary");
        $painel->abre();

        $valorSaldo = $this->getValorSaldo($idContrato);

        if ($valorSaldo >= 0) {
            p(formataMoeda2($valorSaldo), "pvalorTotalPositivo");
        } else {
            p(formataMoeda2($valorSaldo), "pvalorTotalNegativo");
        }

        # Verifica se ter SRA Se tiver exibe informação
        if ($this->temSra($idContrato)) {
            $link = new Link("Considerando o SRA", "#", "O Saldo foi reduzido pelo Saldo Residual Anulado");
            $link->set_id("porNatureza");
            $link->show();
        }

        $painel->fecha();
    }

    ############################################################

    public function exibeValorSaldoRelatorio($idContrato = null) {
        # Verifica se foi informado
        if (empty($idContrato)) {
            return null;
        }

        return formataMoeda2($this->getValorSaldo($idContrato));
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

        # Verifica se tem algum mês pago. Se não tem o valor é false
        # Pega o ano
        if (!$ultimoMesPago) {
            $anoPago = year($dtInicial);
        } else {
            $anoPago = $ultimoMesPago["anoReferencia"];
        }

        # Pega o mês
        if (!$ultimoMesPago) {
            $mesPago = month($dtInicial);
        } else {
            $mesPago = $ultimoMesPago["mesReferencia"];
        }

        # Pega a vigencia (Data de término do contrato)
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
            # Quando não temos menos de 1 mês para a vigência
            # Dai temos 2 situações:
            # Se temos saldo ainda
            if ($saldo > 0) {
                return [$saldo, $saldo, "1)<br/>(Contrato terminando e ainda existe saldo!!"];
            } else {
                return [null, null, null];
            }
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

        # Caso não tiver nenhum mês retorna false (comportamento normal do pdo)
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
        tituloTable("Parcela Mensal Ideal");

        $painel = new Callout("success");
        $painel->abre();

        $valorTotal = $this->getPgtoIdeal($idContrato);

        p(formataMoeda2($valorTotal[0]), "pvalorTotalPositivo");
        p("(" . formataMoeda2($valorTotal[1]) . " / {$valorTotal[2]})", "pPagamentos");
        $painel->fecha();
    }

    ###########################################################

    public function exibeValorLiquidadoPorNatureza($idContrato = null) {
        # Verifica se foi informado
        if (empty($idContrato)) {
            alert("É necessário informar o id do Contrato.");
            return;
        }

        # Conecta ao Banco de Dados
        $contratos = new Contratos();

        # Monta o select 
        $select = "SELECT distinct idNatureza, natureza
                     FROM tbpagamento LEFT JOIN tbnatureza USING (idNatureza)
                    WHERE idContrato = {$idContrato} and idNatureza IS NOT NULL
                      AND afetaSaldo = 's'
                 ORDER BY natureza desc ";

        $row = $contratos->select($select);
        $numPgtos = $contratos->count($select);

        # Somatório
        $somatorio = 0;

        # Percorre o array
        if ($numPgtos > 0) {
            foreach ($row as $item) {
                $resultado[] = [$item["natureza"], $this->getValorLiquidado($idContrato, $item["idNatureza"])];
                $somatorio += $this->getValorLiquidado($idContrato, $item["idNatureza"]);
            }

            $resultado[] = ["Total", $somatorio];
        } else {
            $resultado[] = [null, null];
        }

        # Exemplo de tabela simples
        $tabela = new Tabela();
        $tabela->set_titulo("Por Natureza");
        $tabela->set_conteudo($resultado);
        $tabela->set_label(["Natureza", "Valor"]);
        $tabela->set_width([40, 60]);
        $tabela->set_funcao([null, "formataMoeda2"]);
        $tabela->set_totalRegistro(false);
        $tabela->set_formatacaoCondicional([
            [
                'coluna' => 0,
                'valor' => "Total",
                'operador' => '=',
                'id' => 'resumoTotal'
        ]]);
        $tabela->show();
    }

    ###########################################################

    public function exibeValorLiquidadoPorAno($idContrato = null) {
        # Verifica se foi informado
        if (empty($idContrato)) {
            alert("É necessário informar o id do Contrato.");
            return;
        }

        # Conecta ao Banco de Dados
        $contratos = new Contratos();

        # Monta o select        
        $select = "SELECT DISTINCT anoReferencia
                     FROM tbpagamento LEFT JOIN tbnatureza USING (idNatureza) 
                    WHERE afetaSaldo = 's' 
                      AND idContrato = {$idContrato}
                 ORDER BY anoReferencia desc";

        $row = $contratos->select($select);
        $numPgtos = $contratos->count($select);

        # Somatório
        $somatorio = 0;

        # Percorre o array
        if ($numPgtos > 0) {
            foreach ($row as $item) {
                $resultado[] = [$item["anoReferencia"], $this->getValorLiquidadoPorAno($idContrato, $item["anoReferencia"])];
                $somatorio += $this->getValorLiquidadoPorAno($idContrato, $item["anoReferencia"]);
            }

            $resultado[] = ["Total", $somatorio];
        } else {
            $resultado[] = [null, null];
        }

        # Exemplo de tabela simples
        $tabela = new Tabela();
        $tabela->set_titulo("Por Ano");
        $tabela->set_conteudo($resultado);
        $tabela->set_label(["Ano", "Valor"]);
        $tabela->set_width([40, 60]);
        $tabela->set_funcao([null, "formataMoeda2"]);
        $tabela->set_totalRegistro(false);
        $tabela->set_formatacaoCondicional([
            [
                'coluna' => 0,
                'valor' => "Total",
                'operador' => '=',
                'id' => 'resumoTotal'
        ]]);
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

    public function getNumPgto($idContrato = null) {
        # Verifica se foi informado
        if (empty($idContrato)) {
            return null;
        }

        # Conecta ao Banco de Dados
        $contratos = new Contratos();

        # Monta o select
        $select = "SELECT idContrato
                     FROM tbpagamento
                    WHERE idContrato = {$idContrato}";

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
            if ($conteudo["tipo"] == 2) {
                p("- " . formataMoeda2($conteudo['valor']), "pvalorNegativo");
            } elseif ($conteudo["tipo"] == 1) {
                p(formataMoeda2($conteudo['valor']), "pvalorPositivo");
            } else {
                p(formataMoeda2($conteudo['valor']), "pvalorTipo3");
            }
        }
    }

    ###########################################################
    /*
     * Verifica se tem algum SRA nos lançamentos
     * SRA - Saldo Residual Anulado
     */

    public function temSra($idContrato = null) {

        # Verifica se foi informado
        if (vazio($idContrato)) {
            alert("É necessário informar o id do Contrato.");
            return;
        }

        # Conecta ao Banco de Dados
        $contratos = new Contratos();

        # Monta o select
        $select = "SELECT idPagamento
                     FROM tbpagamento 
                    WHERE idContrato = {$idContrato}
                      AND tipo = 3";

        $soma = $contratos->count($select);

        if ($soma > 0) {
            return true;
        } else {
            return false;
        }
    }

    ############################################################

    private function getSra($idContrato = null) {
        # Verifica se foi informado o id
        if (empty($idContrato)) {
            alert("É necessário informar o id do Contrato.");
            return;
        }

        # Pega os valores dos pgtos
        $contratos = new Contratos();
        $select = "SELECT SUM(valor) as total FROM tbpagamento WHERE idContrato = {$idContrato} AND tipo = 3";
        $row = $contratos->select($select, false);

        return $row['total'];
    }

    ############################################################

    public function exibeTipo($tipo = null) {

        switch ($tipo) {
            case 1:
                p("Pgto", "pvalorPositivo", null, "Pagamento normal da nota fiscal");
                break;

            case 2:
                p("Estorno", "pvalorNegativo", null, "Estorno de pagamento");
                break;

            case 3:
                p("SRA", "pvalorTipo3", null, "Saldo Residual Anulado");
                break;
        }
    }

    ############################################################

    public function exibeSaldosPgtoRel($idContrato = null) {

        if (empty($idContrato)) {
            alert("É necessário informar o id do Contrato.");
            return;
        }

        $contrato = new Contrato();

        # Pega os Valores
        $valorTotal = formataMoeda2($contrato->getValorTotal($idContrato));
        $valorLiquidado = formataMoeda2($this->getValorLiquidado($idContrato));
        $saldo = formataMoeda2($this->getValorSaldo($idContrato));

        $row = [["<br/>{$valorTotal}<br/>.", $valorLiquidado, $saldo]];

        # Monta o Relatório
        $relatorio = new Relatorio();
        $relatorio->set_tituloTabela('<br/>Valores');
        $relatorio->set_label(["Valor Total", "Valor Liquidado", "Saldo"]);
        $relatorio->set_conteudo($row);

        $relatorio->set_subTotal(false);
        $relatorio->set_totalRegistro(false);
        $relatorio->set_dataImpressao(false);
        $relatorio->set_cabecalhoRelatorio(false);
        $relatorio->set_menuRelatorio(false);
        $relatorio->set_log(false);
        $relatorio->show();
    }

    ############################################################

    private function getValorLiquidadoPorAno($idContrato = null, $anoReferencia = null) {

        # Inicia a variável de retorno
        $valorTotal = 0;

        # Pega os valores dos pgtos
        $contratos = new Contratos();

        if (is_null($anoReferencia)) {
            return $this->getValorLiquidado($idContrato);
        } else {
            $select = "SELECT valor, 
                              tipo 
                         FROM tbpagamento LEFT JOIN tbnatureza USING (idNatureza) 
                        WHERE idContrato = {$idContrato} 
                          AND afetaSaldo = 's'
                          AND anoReferencia = {$anoReferencia}";

            $row = $contratos->select($select);
            $numPgtos = $contratos->count($select);

            # Verifica se tem algum aditivo
            if ($numPgtos > 0) {
                foreach ($row as $item) {
                    if ($item["tipo"] == 2) {
                        $valorTotal -= $item["valor"];  // Diminui Quando é estorno
                    } elseif ($item["tipo"] == 1) {
                        $valorTotal += $item["valor"];  // /aumenta quando é pgto
                    }   // Desconsidera o SRA
                }
            }
            return $valorTotal;
        }
    }

    ############################################################

    private function getValorSemSaldoPorAno($idContrato = null, $anoReferencia = null) {

        # Inicia a variável de retorno
        $valorTotal = 0;

        # Pega os valores dos pgtos
        $contratos = new Contratos();

        if (is_null($anoReferencia)) {
            return $this->getValorLiquidado($idContrato);
        } else {
            $select = "SELECT valor, 
                              tipo 
                         FROM tbpagamento LEFT JOIN tbnatureza USING (idNatureza) 
                        WHERE idContrato = {$idContrato} 
                          AND afetaSaldo = 'n'
                          AND anoReferencia = {$anoReferencia}";

            $row = $contratos->select($select);
            $numPgtos = $contratos->count($select);

            # Verifica se tem algum aditivo
            if ($numPgtos > 0) {
                foreach ($row as $item) {
                    if ($item["tipo"] == 2) {
                        $valorTotal -= $item["valor"];  // Diminui Quando é estorno
                    } elseif ($item["tipo"] == 1) {
                        $valorTotal += $item["valor"];  // /aumenta quando é pgto
                    }   // Desconsidera o SRA
                }
            }
            return $valorTotal;
        }
    }

    ###########################################################

    public function exibeValorSemSaldoPorAno($idContrato = null) {
        # Verifica se foi informado
        if (empty($idContrato)) {
            alert("É necessário informar o id do Contrato.");
            return;
        }

        # Conecta ao Banco de Dados
        $contratos = new Contratos();

        # Monta o select        
        $select = "SELECT DISTINCT anoReferencia
                     FROM tbpagamento LEFT JOIN tbnatureza USING (idNatureza) 
                    WHERE afetaSaldo = 'n' 
                      AND idContrato = {$idContrato}
                 ORDER BY anoReferencia desc";

        $row = $contratos->select($select);
        $numPgtos = $contratos->count($select);

        # Somatório
        $somatorio = 0;

        # Percorre o array
        if ($numPgtos > 0) {
            foreach ($row as $item) {
                $resultado[] = [$item["anoReferencia"], $this->getValorSemSaldoPorAno($idContrato, $item["anoReferencia"])];
                $somatorio += $this->getValorSemSaldoPorAno($idContrato, $item["anoReferencia"]);
            }

            $resultado[] = ["Total", $somatorio];
        } else {
            $resultado[] = [null, null];
        }

        # Exemplo de tabela simples
        $tabela = new Tabela();
        $tabela->set_titulo("Por Ano");
        $tabela->set_conteudo($resultado);
        $tabela->set_label(["Ano", "Valor"]);
        $tabela->set_width([40, 60]);
        $tabela->set_funcao([null, "formataMoeda2"]);
        $tabela->set_totalRegistro(false);
        $tabela->set_formatacaoCondicional([
            [
                'coluna' => 0,
                'valor' => "Total",
                'operador' => '=',
                'id' => 'resumoTotal'
        ]]);
        $tabela->show();
    }

    ###########################################################    

    private function getValorSemSaldoPorNatureza($idContrato = null, $idNatureza = null) {

        # Verifica se foi informado o id
        if (empty($idContrato)) {
            alert("É necessário informar o id do Contrato.");
            return;
        }

        # Inicia a variável de retorno
        $valorTotal = 0;

        # Inicia as Classes
        $contratos = new Contratos();

        # Pega os valores dos pgtos que afetam o saldo
        if (is_null($idNatureza)) {
            $select = "SELECT valor,
                              tipo 
                         FROM tbpagamento LEFT JOIN tbnatureza USING (idNatureza) 
                        WHERE afetaSaldo = 'n'
                          AND idContrato = {$idContrato}";
        } else {
            if ($idNatureza == 0) {
                $select = "SELECT valor,
                                  tipo 
                             FROM tbpagamento LEFT JOIN tbnatureza USING (idNatureza) 
                            WHERE afetaSaldo = 'n' 
                              AND idContrato = {$idContrato} 
                              AND idNatureza IS NULL";
            } else {
                $select = "SELECT valor,
                                  tipo 
                             FROM tbpagamento LEFT JOIN tbnatureza USING (idNatureza) 
                            WHERE afetaSaldo = 'n' 
                              AND idContrato = {$idContrato} 
                              AND idNatureza = {$idNatureza}";
            }
        }

        $row = $contratos->select($select);
        $numPgtos = $contratos->count($select);

        # Verifica se tem algum aditivo
        if ($numPgtos > 0) {
            foreach ($row as $item) {
                if ($item["tipo"] == 2) {
                    # Diminui Quando é estorno
                    $valorTotal -= $item["valor"];
                } elseif ($item["tipo"] == 1) {
                    # Aumenta quando é pgto
                    $valorTotal += $item["valor"];
                }
            }
        }

        return $valorTotal;
    }

    ###########################################################

    public function exibeValorSemSaldoPorNatureza($idContrato = null) {
        # Verifica se foi informado
        if (empty($idContrato)) {
            alert("É necessário informar o id do Contrato.");
            return;
        }

        # Conecta ao Banco de Dados
        $contratos = new Contratos();

        # Monta o select 
        $select = "SELECT distinct idNatureza, natureza
                     FROM tbpagamento LEFT JOIN tbnatureza USING (idNatureza)
                    WHERE idContrato = {$idContrato} and idNatureza IS NOT NULL
                      AND afetaSaldo = 'n'
                 ORDER BY natureza desc ";

        $row = $contratos->select($select);
        $numPgtos = $contratos->count($select);

        # Somatório
        $somatorio = 0;

        # Percorre o array
        if ($numPgtos > 0) {
            foreach ($row as $item) {
                $resultado[] = [$item["natureza"], $this->getValorSemSaldoPorNatureza($idContrato, $item["idNatureza"])];
                $somatorio += $this->getValorSemSaldoPorNatureza($idContrato, $item["idNatureza"]);
            }

            $resultado[] = ["Total", $somatorio];
        } else {
            $resultado[] = [null, null];
        }

        # Exemplo de tabela simples
        $tabela = new Tabela();
        $tabela->set_titulo("Por Natureza");
        $tabela->set_conteudo($resultado);
        $tabela->set_label(["Natureza", "Valor"]);
        $tabela->set_width([40, 60]);
        $tabela->set_funcao([null, "formataMoeda2"]);
        $tabela->set_totalRegistro(false);
        $tabela->set_formatacaoCondicional([
            [
                'coluna' => 0,
                'valor' => "Total",
                'operador' => '=',
                'id' => 'resumoTotal'
        ]]);
        $tabela->show();
    }

    ###########################################################
    /*
     * Verifica se tem algum Pagamento que não é considerado no saldo liquidado
     */

    public function temPgtaSemSaldo($idContrato = null) {

        # Verifica se foi informado
        if (vazio($idContrato)) {
            alert("É necessário informar o id do Contrato.");
            return;
        }

        # Conecta ao Banco de Dados
        $contratos = new Contratos();

        # Monta o select
        $select = "SELECT idPagamento
                     FROM tbpagamento LEFT JOIN tbnatureza USING (idNatureza)
                    WHERE idContrato = {$idContrato}
                      AND afetaSaldo = 'n'";

        $soma = $contratos->count($select);

        if ($soma > 0) {
            return true;
        } else {
            return false;
        }
    }

    ############################################################
}
