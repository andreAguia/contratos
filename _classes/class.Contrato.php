<?php

class Contrato {

    /**
     * Abriga as várias rotina referentes a concurso
     *
     * @author André Águia (Alat) - alataguia@gmail.com
     *
     * @var private $idConcurso integer null O id do concurso
     */
##############################################################

    public function __construct($idContrato = null) {
        /**
         * Inicia a Classe somente
         *
         * @param $idContrato integer null O id do concurso
         *
         * @syntax $concurso = new Concurso([$idConcurso]);
         */
        $this->idContrato = $idContrato;
    }

##############################################################

    public function getDados($idContrato = null) {

        /**
         * Informa os dados da base de dados
         *
         * @param $idConcurso integer null O id do concurso
         *
         * @syntax $concurso->get_dados([$idConcurso]);
         */
        # Verifica se foi informado
        if (vazio($idContrato)) {
            alert("É necessário informar o id do Contrato.");
            return;
        }

        # Conecta ao Banco de Dados
        $contratos = new Contratos();

        # Pega os dados
        $select = "SELECT *,
                          IF(tipoPrazo = 2,
                          SUBDATE(ADDDATE(dtInicial, INTERVAL prazo MONTH), INTERVAL 1 DAY),
                          ADDDATE(dtInicial, INTERVAL prazo-1 DAY)) as dtFinal
                     FROM tbcontrato
                    WHERE idContrato = {$idContrato}";

        $row = $contratos->select($select, false);

        # Retorno
        return $row;
    }

    ##############################################################

    public function getProcessoAdm($idContrato = null, $br = true) {

        /**
         * Informa os dados da base de dados
         *
         * @param $idConcurso integer null O id do concurso
         *
         * @syntax $concurso->get_dados([$idConcurso]);
         */
        # Verifica se foi informado
        if (vazio($idContrato)) {
            alert("É necessário informar o id do Contrato.");
            return;
        }

        $conteudo = $this->getDados($idContrato);

        $processo = null;

        # Verifica se tem somente um processo
        if ((empty($conteudo["processoSei"])) xor (empty($conteudo["processo"]))) {
            if (empty($conteudo["processoSei"])) {
                $processo = $conteudo["processo"];
            } else {
                $processo = "SEI - {$conteudo["processoSei"]}";
            }
        }

        # Verifica se tem os dois
        if ((!empty($conteudo["processoSei"])) and (!empty($conteudo["processo"]))) {
            if ($br) {
                $processo = "SEI - {$conteudo["processoSei"]} <br/> {$conteudo["processo"]}";
            } else {
                $processo = "SEI - {$conteudo["processoSei"]}  {$conteudo["processo"]}";
            }
        }
        return $processo;
    }

    ##############################################################

    public function getProcesso($idContrato = null, $br = true) {

        /**
         * Informa os dados da base de dados
         *
         * @param $idConcurso integer null O id do concurso
         *
         * @syntax $concurso->get_dados([$idConcurso]);
         */
        # Verifica se foi informado
        if (vazio($idContrato)) {
            alert("É necessário informar o id do Contrato.");
            return;
        }

        $conteudo = $this->getDados($idContrato);

        $processo = null;

        # Verifica se tem somente um processo
        if ((empty($conteudo["processoSei"])) xor (empty($conteudo["processo"]))) {
            if (empty($conteudo["processoSei"])) {
                $processo = $conteudo["processo"];
            } else {
                $processo = "SEI - {$conteudo["processoSei"]}";
            }
        }

        # Verifica se tem os dois
        if ((!empty($conteudo["processoSei"])) and (!empty($conteudo["processo"]))) {
            if ($br) {
                $processo = "SEI - {$conteudo["processoSei"]} <br/> {$conteudo["processo"]}";
            } else {
                $processo = "SEI - {$conteudo["processoSei"]}  {$conteudo["processo"]}";
            }
        }

        # Verifica se tem processo de execução
        if (!empty($conteudo["processoExecucao"])) {
            if ($br) {
                $processo .= "<br/>Exec: SEI - {$conteudo["processoExecucao"]}";
            } else {
                $processo .= " Exec: SEI - {$conteudo["processoExecucao"]}";
            }
        }

        return $processo;
    }

    #####################################################################################

    public function exibeProcessoExecucao($idContrato = null) {

        /**
         * Informa os dados da base de dados
         *
         * @param $idConcurso integer null O id do concurso
         *
         * @syntax $concurso->get_dados([$idConcurso]);
         */
        # Verifica se foi informado
        if (vazio($idContrato)) {
            alert("É necessário informar o id do Contrato.");
            return;
        }

        $conteudo = $this->getDados($idContrato);

        $processo = null;

        # Verifica se tem processo de execução
        if (empty($conteudo["processoExecucao"])) {
            return null;
        } else {
            return "SEI - {$conteudo["processoExecucao"]}";
        }
    }

    #####################################################################################

    public function exibeNumeroContrato($idContrato) {
        # Verifica se foi informado
        if (vazio($idContrato)) {
            alert("É necessário informar o id do Contrato.");
            return;
        }

        $conteudo = $this->getDados($idContrato);

        $modalidade = new Modalidade();

        p($conteudo["numero"], "contratoNumero");
        if (!empty($conteudo['siafe'])) {
            p("Siafe: {$conteudo['siafe']}", "pVigencia");
        }

        if (!empty($conteudo['rubrica'])) {
            p("Rubrica:: {$conteudo['rubrica']}", "pVigencia");
        }

        p($this->exibeModalidade($idContrato), "pVigencia");

        $status = $this->getStatus($idContrato);

        if ($status == "Ativo") {
            $stilo = "statusAtivo";
        } elseif ($status == "Pendente") {
            $stilo = "statusPendente";
        } else {
            $stilo = "statusEncerrado";
        }
        p($status, "$stilo");

        if ($conteudo["maoDeObra"]) {
            p("Mão de Obra Alocada", "pVigencia");
        }
    }

    #####################################################################################

    public function exibeNumeroContratoRel($idContrato) {
        # Verifica se foi informado
        if (vazio($idContrato)) {
            alert("É necessário informar o id do Contrato.");
            return;
        }

        $conteudo = $this->getDados($idContrato);

        $modalidade = new Modalidade();

        p($conteudo["numero"], "contratoNumero");
        if (!empty($conteudo['siafe'])) {
            p("Siafe: {$conteudo['siafe']}", "pVigencia");
        }

        if (!empty($conteudo['rubrica'])) {
            p("Rubrica: {$conteudo['rubrica']}", "pVigencia");
        }

        p($this->exibeModalidade($idContrato), "pVigencia");

//        $status = $this->getStatus($idContrato);
//
//        if ($status == "Ativo") {
//            $stilo = "statusAtivo";
//        } elseif ($status == "Pendente") {
//            $stilo = "statusPendente";
//        } else {
//            $stilo = "statusEncerrado";
//        }
//        p($status, "$stilo");

        if ($conteudo["maoDeObra"]) {
            p("Mão de Obra Alocada", "pVigencia");
        }
    }

    #####################################################################################

    public function exibeNumeroContratoSimples($idContrato) {
        # Verifica se foi informado
        if (vazio($idContrato)) {
            alert("É necessário informar o id do Contrato.");
            return;
        }

        $conteudo = $this->getDados($idContrato);

        p($conteudo["numero"], "contratoNumero");
    }

    #####################################################################################

    public function exibeNumeroSiafeRelatorio($idContrato) {
        # Verifica se foi informado
        if (vazio($idContrato)) {
            alert("É necessário informar o id do Contrato.");
            return;
        }

        $conteudo = $this->getDados($idContrato);

        p($conteudo["numero"], "contratoNumero");
        if (!empty($conteudo['siafe'])) {
            p("Siafe: {$conteudo['siafe']}", "pVigencia");
        }

        if ($conteudo["maoDeObra"]) {
            p("Mão de Obra Alocada", "pVigencia");
        }
    }

    ###########################################################

    public function exibeStatus($idContrato) {
        # Verifica se foi informado
        if (vazio($idContrato)) {
            alert("É necessário informar o id do Contrato.");
            return;
        }

        $status = $this->getStatus($idContrato);

        if ($status == "Ativo") {
            $painel = new Callout("success");
            $stilo = "statusAtivo";
        } elseif ($status == "Pendente") {
            $painel = new Callout("alert");
            $stilo = "statusPendente";
        } else {
            $painel = new Callout("secondary");
            $stilo = "statusEncerrado";
        }
        $painel->abre();

        p("Status:", "contratoLabelCallout2");
        p($status, "$stilo");

        $painel->fecha();
    }

    ###########################################################

    /**
     * Método exibeDadosConcurso
     * fornece os dados de uma vaga em forma de tabela
     *
     * @param    string $idContrato O id da vaga
     */
    public function exibeDadosContrato($idContrato, $idUsuario) {

        # Verifica se foi informado
        if (vazio($idContrato)) {
            alert("É necessário informar o id do Contrato.");
            return;
        }
        $conteudo = $this->getDados($idContrato);

        $select = "SELECT idContrato,
                          idContrato,
                          idContrato,
                          dtAssinatura,
                          idContrato,
                          idContrato,
                          idContrato
                     FROM tbcontrato
                    WHERE idContrato = {$idContrato}";

        $contratos = new Contratos();
        $row = $contratos->select($select);

        $tabela = new Tabela();
        $tabela->set_titulo("Contrato {$conteudo["numero"]}");
        $tabela->set_label(array("Tipo", "Objeto", "Publicação", "Assinatura", "Duração", "Garantia", "Valor"));
        $tabela->set_align(array("center", "left", "center", "center", "center", "center", "right"));
        $tabela->set_width(array(15, 25, 10, 10, 10, 15, 15));
        $tabela->set_classe(array("Contrato", "Contrato", "Contrato", null, "Contrato", "Contrato", "Contrato"));
        $tabela->set_metodo(array("exibeModalidade", "exibeObjeto", "getPublicacao", null, "getPeriodo", "getGarantia", "exibeValor"));
        $tabela->set_funcao(array(null, null, null, "date_to_php"));
        $tabela->set_conteudo($row);
        $tabela->set_totalRegistro(false);

        if (Verifica::acesso($idUsuario, 9)) {
            $tabela->set_editar('cadastroContrato.php?fase=editar&id=' . $idContrato);
            $tabela->set_idCampo('idContrato');
            $tabela->set_metodo(array("exibeModalidade", "exibeObjeto", "exibePublicacao", null, "getPeriodo", "getGarantia", "exibeValor"));
        }
        $tabela->show();
    }

    ###########################################################

    /**
     * Método exibeDadosConcurso
     * fornece os dados de uma vaga em forma de tabela
     *
     * @param    string $idContrato O id da vaga
     */
    public function exibeDadosContratoRel($idContrato) {

        # Verifica se foi informado
        if (vazio($idContrato)) {
            alert("É necessário informar o id do Contrato.");
            return;
        }
        $conteudo = $this->getDados($idContrato);

        $select = "SELECT idContrato,
                          dtAssinatura,
                          idContrato,
                          idContrato
                     FROM tbcontrato
                    WHERE idContrato = {$idContrato}";

        $contratos = new Contratos();
        $row = $contratos->select($select);

        tituloRelatorio("Contrato {$conteudo["numero"]}");

        # Monta o Relatório
        $relatorio = new Relatorio();
        $relatorio->set_label(array("Publicação", "Assinatura", "Duração", "Valor"));
        $relatorio->set_align(array("center", "center", "center", "center", "center", "right"));
        #$relatorio->set_width(array(15, 25, 10, 10, 10, 15, 15));
        $relatorio->set_classe(array("Contrato", null, "Contrato", "Contrato"));
        $relatorio->set_metodo(array("getPublicacaoRel", null, "getPeriodo", "exibeValor"));
        $relatorio->set_funcao(array(null, "date_to_php"));
        $relatorio->set_conteudo($row);

        $relatorio->set_subTotal(false);
        $relatorio->set_totalRegistro(false);
        $relatorio->set_dataImpressao(false);
        $relatorio->set_cabecalhoRelatorio(false);
        $relatorio->set_menuRelatorio(false);
        $relatorio->set_log(false);
        $relatorio->show();
    }

    ###########################################################

    public function exibeResumoDados($idContrato) {
        # Verifica se foi informado
        if (vazio($idContrato)) {
            alert("É necessário informar o id do Contrato.");
            return;
        }

        $select = "SELECT idContrato, 
                          idContrato, 
                          idContrato,
                          idEmpresa, 
                          idContrato
                    FROM tbcontrato
                   WHERE idContrato = {$idContrato}";

        $contratos = new Contratos();
        $row = $contratos->select($select);

        $empresa = new Empresa();

        # Limita o tamanho da tela
        $grid = new Grid();
        $grid->abreColuna(12);

        $formatacaoCondicional = array(array(
                'coluna' => 3,
                'valor' => $empresa->getEmpresaCnpj($row[0][3]),
                'operador' => '=',
                'id' => 'listaDados'));

        # Monta a tabela
        $tabela = new Tabela();
        $tabela->set_conteudo($row);
        $tabela->set_label(["Contrato", "Processo", "Objeto", "Empresa", "Vigência"]);
        $tabela->set_width([15, 20, 25, 25, 15]);
        #$tabela->set_funcao($function);
        $tabela->set_classe(["Contrato", "Contrato", "Contrato", "Empresa", "Contrato"]);
        $tabela->set_metodo(["exibeNumeroContrato", "getProcesso", "exibeObjeto", "getEmpresaCnpj", "exibeTempoEVigencia"]);
        $tabela->set_totalRegistro(false);
        $tabela->set_formatacaoCondicional($formatacaoCondicional);

        $tabela->show();
    }

    ###########################################################

    public function exibeResumoDadosRel($idContrato) {
        # Verifica se foi informado
        if (vazio($idContrato)) {
            alert("É necessário informar o id do Contrato.");
            return;
        }

        $select = "SELECT idContrato, 
                          idContrato,
                          idEmpresa, 
                          idContrato
                    FROM tbcontrato
                   WHERE idContrato = {$idContrato}";

        $contratos = new Contratos();
        $row = $contratos->select($select);

        $empresa = new Empresa();

        # Limita o tamanho da tela
        $grid = new Grid();
        $grid->abreColuna(12);

        # Monta o Relatório
        $relatorio = new Relatorio();
        $relatorio->set_conteudo($row);
        $relatorio->set_label(["Processo", "Objeto", "Empresa", "Vigência"]);
        $relatorio->set_width([30, 30, 30, 10]);
        #$relatorio->set_funcao($function);
        $relatorio->set_classe(["Contrato", "Contrato", "Empresa", "Contrato"]);
        $relatorio->set_metodo(["getProcessoAdm", "exibeObjetoRel", "getEmpresaCnpj", "exibeVigencia"]);
        $relatorio->set_totalRegistro(false);

        $relatorio->set_subTotal(false);
        $relatorio->set_totalRegistro(false);
        $relatorio->set_dataImpressao(false);
        $relatorio->set_cabecalhoRelatorio(false);
        $relatorio->set_menuRelatorio(false);
        $relatorio->set_log(false);
        $relatorio->show();
    }

    ###########################################################

    public function exibeTempoEVigencia($idContrato) {

        # Pega os dados
        $dados = $this->getDados($idContrato);

        # Verifica se tem data inicial
        if (empty($dados["dtInicial"])) {

            return null;
        } else {

            # Vigência
            $vigencia = $this->getVigencia($idContrato);

            # Verifica se a data já passou ou quantos dias faltam pra ela
            if (!jaPassou($vigencia)) {
                $diferenca = abs(dataDif($vigencia));
            }

            # Tempo Total
            $tempo = $this->getTempoTotal($idContrato);

            #var_dump($tempo);
            # Verifica se já passou de 60 meses
            if (!empty($tempo["meses"])) {
                if ($tempo["meses"] >= 60) {
                    p("{$tempo["meses"]} Meses", "pTempoTotal60");
                } else {
                    p("{$tempo["meses"]} Meses", "pTempoTotal");
                }
            }

            if (!empty($tempo["dias"])) {
                if ($tempo["dias"] >= 1800) {
                    p("{$tempo["dias"]} dias", "pTempoTotal60");
                } else {
                    p("{$tempo["dias"]} dias", "pTempoTotal");
                }
            }

            p($vigencia, "pVigencia");

            # Exibe o tempo que falta                        
            if (!jaPassou($vigencia)) {
                p("Faltam {$diferenca} dias", "pVigencia");
            } else {
                p("Já Passou !", "pVigenciaJaPassou");
            }
        }
    }

    ###########################################################

    public function exibeVigencia($idContrato) {

        # Pega os dados
        $dados = $this->getDados($idContrato);

        # Verifica se tem data inicial
        if (empty($dados["dtInicial"])) {

            return null;
        } else {

            # Vigência
            $vigencia = $this->getVigencia($idContrato);

            # Verifica se a data já passou ou quantos dias faltam pra ela
            if (!jaPassou($vigencia)) {
                $diferenca = abs(dataDif($vigencia));
            }

            # Tempo Total
            $tempo = $this->getTempoTotal($idContrato);

            #var_dump($tempo);
            # Verifica se já passou de 60 meses
            if (!empty($tempo["meses"])) {
                if ($tempo["meses"] >= 60) {
                    p("{$tempo["meses"]} Meses", "pTempoTotal60");
                } else {
                    p("{$tempo["meses"]} Meses", "pTempoTotal");
                }
            }

            if (!empty($tempo["dias"])) {
                if ($tempo["dias"] >= 1800) {
                    p("{$tempo["dias"]} dias", "pTempoTotal60");
                } else {
                    p("{$tempo["dias"]} dias", "pTempoTotal");
                }
            }

            p($vigencia, "pVigencia");
        }
    }

    ###########################################################

    public function exibeDuracao($idContrato) {

        # Pega os dados
        $dados = $this->getDados($idContrato);

        # Verifica se tem data inicial
        if (empty($dados["dtInicial"])) {

            return null;
        } else {
            # Monta os valores
            $dtInicial = date_to_php($dados["dtInicial"]);
            $dtFinal = $this->getVigencia($idContrato);
            $tempo = $this->getTempoTotal($idContrato);
            p("{$dtInicial} - {$dtFinal}", "pVigencia");

            # Verifica se já passou de 60 meses
            if ($tempo["meses"] >= 60) {
                p("{$tempo["meses"]} Meses", "pTempoTotal60");
            } else {
                p("{$tempo["meses"]} Meses", "pTempoTotal");
            }
        }
    }

    ###########################################################

    public function exibePeriodo($idContrato) {

        # Pega os dados
        $dados = $this->getDados($idContrato);

        # Verifica se tem data inicial
        if (empty($dados["dtInicial"])) {

            return null;
        } else {
            # Monta os valores
            $dtInicial = date_to_php($dados["dtInicial"]);
            $dtFinal = $this->getVigencia($idContrato);
            p("{$dtInicial} - {$dtFinal}", "pVigencia");
        }
    }

    ###########################################################

    public function exibePrazo($idContrato) {

        # Pega os dados
        $dados = $this->getDados($idContrato);

        # Verifica se tem data inicial
        if (empty($dados["dtInicial"])) {

            return null;
        } else {
            $tempo = $this->getTempoTotal($idContrato);
            # Verifica se já passou de 60 meses
            if ($tempo["meses"] >= 60) {
                p("{$tempo["meses"]} Meses", "pTempoTotal60");
            } else {
                p("{$tempo["meses"]} Meses", "pTempoTotal");
            }
        }
    }

    ###########################################################
    /*
     * Retorna array com os meses e dias do tempo do contrato
     */

    public function getTempoTotal($idContrato) {
        # Verifica se foi informado
        if (vazio($idContrato)) {
            alert("É necessário informar o id do Contrato.");
            return;
        }

        # Inicia as variáveis de retorno
        $prazoDias = 0;
        $prazoMeses = 0;

        # Pega o tempo do contrato
        $conteudo = $this->getDados($idContrato);
        $prazo = $conteudo["prazo"];
        $tipoPrazo = $conteudo["tipoPrazo"];

        if ($tipoPrazo == 2) {
            $prazoMeses += $prazo;
        } else {
            $prazoDias += $prazo;
        }

        # Verifica se tem aditivo e pega os tempo de cada um deles
        $aditivo = new Aditivo();
        if ($aditivo->temAditivo($idContrato)) {
            $arrayAditivo = $aditivo->getAditivosContrato($idContrato);

            # Percorre o array
            foreach ($arrayAditivo as $itemAditivo) {
                if (!empty($itemAditivo["prazo"])) {
                    if ($itemAditivo["tipoPrazo"] == 2) {
                        $prazoMeses += $itemAditivo["prazo"];
                    } else {
                        $prazoDias += $itemAditivo["prazo"];
                    }
                }
            }
        }

        $retorno = [
            "dias" => $prazoDias,
            "meses" => $prazoMeses,
        ];

        return $retorno;
    }

    ###########################################################

    public function getDtFinal($idContrato) {

        # Verifica se foi informado o id
        if (vazio($idContrato)) {
            alert("É necessário informar o id do Contrato.");
            return;
        }

        # Pega os dados
        $conteudo = $this->getDados($idContrato);

        # Retorna a data Final
        return date_to_php($conteudo["dtFinal"]);
    }

    ###########################################################

    public function getDtInicial($idContrato) {

        # Verifica se foi informado o id
        if (vazio($idContrato)) {
            alert("É necessário informar o id do Contrato.");
            return;
        }

        # Pega os dados
        $conteudo = $this->getDados($idContrato);

        # Retorna a data Final
        return date_to_php($conteudo["dtInicial"]);
    }

    ##############################################################
    /*
     * Informa a vigência geral do contrato
     */

    public function getVigencia($idContrato) {

        # Verifica se foi informado o id
        if (vazio($idContrato)) {
            alert("É necessário informar o id do Contrato.");
            return;
        }

        # Define a vigencia com a data final do contrato inicial
        $vigencia = $this->getDtFinal($idContrato);

        # Verifica se tem aditivo e pega os tempo de cada um deles
        $aditivo = new Aditivo();
        if ($aditivo->temAditivo($idContrato)) {
            $arrayAditivo = $aditivo->getAditivosContrato($idContrato);

            # Percorre o array
            foreach ($arrayAditivo as $itemAditivo) {

                # Pega a vigência deste aditivo
                $dtFinal = $aditivo->getDtFinal($itemAditivo["idAditivo"]);

                # Descarta de for nula e atualiza dtFinal total se não for
                if (!empty($dtFinal)) {
                    $vigencia = $dtFinal;
                }
            }
        }

        return $vigencia;
    }

    ###########################################################

    public function getNumero($idContrato) {

        # Verifica se foi informado o id
        if (!vazio($idContrato)) {
            $this->idContrato = $idContrato;
        }

        $conteudo = $this->getDados($idContrato);

        return $conteudo["numero"];
    }

    ##############################################################

    public function getStatus($idContrato = null) {

        # Verifica se foi informado
        if (vazio($idContrato)) {
            alert("É necessário informar o id do Contrato.");
            return;
        }

        # Conecta ao Banco de Dados
        $contratos = new Contratos();

        $conteudo = $this->getDados($idContrato);
        $idStatus = $conteudo["idStatus"];

        # monta o select
        $select = "SELECT status
                     FROM tbstatus
                    WHERE idStatus = {$idStatus}";

        $row = $contratos->select($select, false);

        return $row["status"];
    }

    ##############################################################

    public function exibeModalidade($idContrato = null) {

        # Verifica se foi informado
        if (vazio($idContrato)) {
            alert("É necessário informar o id do Contrato.");
            return;
        }

        # Conecta ao Banco de Dados
        $contratos = new Contratos();
        $modalidade = new Modalidade();

        $conteudo = $this->getDados($idContrato);
        $idModalidade = $conteudo["idModalidade"];

        $return = $modalidade->getModalidade($idModalidade);

        # Verifica se é pregão e se tem o número do pregão
        if ($idModalidade == 2) {
            if (!empty($conteudo["numPregao"])) {
                $return .= " " . str_pad($conteudo["numPregao"], 3, "0", STR_PAD_LEFT);
            }
        }
        return $return;
    }

    #####################################################################################

    public function getNovoNumeroContrato() {
        # Conecta ao Banco de Dados
        $contratos = new Contratos();

        # Seleciona todos os contratos que tem o número com o caractere /
        $select = "SELECT numero
                     FROM tbcontrato
                    WHERE INSTR(numero,'/')
                      AND SUBSTRING(numero,INSTR(numero,'/')+1) = year(now())
                 ORDER BY SUBSTRING(numero,1,INSTR(numero,'/')-1) desc LIMIT 1";

        $numero = $contratos->select($select, false);

        if (empty($numero["numero"])) {
            $retorno = "001/" . date('Y');
        } else {
            # Verifica se tem / em numero
            $posicao = mb_strpos($numero["numero"], "/");

            if ($posicao !== false) {
                $itens = explode("/", $numero["numero"]);
                $itens[0]++;
                $itens[0] = str_pad($itens[0], 3, '0', STR_PAD_LEFT);

                # Verifica se o ano do ultimo lançamento é o ano atual
                if ($itens[1] == date('Y')) {
                    $retorno = "$itens[0]/" . date('Y');
                } else {
                    $retorno = "001/" . date('Y');
                }
            } else {
                $retorno = null;
            }
        }

        return $retorno;
    }

    #####################################################################################

    public function exibeValorTotalPainel($idContrato = null, $detalhe = false) {
        # Verifica se foi informado
        if (vazio($idContrato)) {
            alert("É necessário informar o id do Contrato.");
            return;
        }

        # exibe o resultado
        $painel = new Callout("secondary");
        $painel->abre();

        titulo("Valor Total");

        $valorTotal = $this->getValorTotal($idContrato);

        if ($valorTotal >= 0) {
            p("R$ " . formataMoeda($valorTotal), "pvalorTotalPositivo");
        } else {
            p("R$ " . formataMoeda($valorTotal), "pvalorTotalNegativo");
        }

        ####
        # Exibe o detalhe (somente para a rotina de pagamento - controle de saldo)
        if ($detalhe) {

            # Exibe um resumo dos aditivos
            # Pega os dados do contrato
            $conteudo = $this->getDados($idContrato);

            # Pega os valores dos aditivos
            $contratos = new Contratos();
            $select = "SELECT idAditivo, obs FROM tbaditivo WHERE idContrato = {$idContrato} ORDER BY dtAssinatura";
            $row = $contratos->select($select);
            $numAditivos = $contratos->count($select);

            # Verifica se tem algum aditivo
            if ($numAditivos > 0) {

                $link = new Link("Detalhes");
                $link->set_id("porNatureza");
                $link->set_onClick("abreFechaDivId('divDetalhes');");
                $link->set_title("Detalha os pagamanto por termos aditivos");
                $link->show();

                $div = new Div("divDetalhes");
                $div->abre();

                br();
                $array2[] = [$idContrato];

                # tabela
                $tabela = new Tabela();
                $tabela->set_conteudo($array2);
                $tabela->set_label(array("Contrato"));
                $tabela->set_align(array("center"));

                $tabela->set_classe(array("Contrato"));
                $tabela->set_metodo(array("exibeValor"));

                $tabela->set_totalRegistro(false);
                $tabela->show();

                foreach ($row as $item) {
                    $array3[] = [$item["idAditivo"], $item["idAditivo"]];
                }

                # tabela
                $tabela = new Tabela();
                $tabela->set_conteudo($array3);
                $tabela->set_label(array("Termos", "Valor"));
                $tabela->set_align(array("left", "center"));

                $tabela->set_classe(array("Aditivo", "Aditivo"));
                $tabela->set_metodo(array("exibeTipoNumerado", "exibeValor"));

                $tabela->set_totalRegistro(false);
                $tabela->show();

                $div->fecha();
            }
        }
        $painel->fecha();
    }

    #####################################################################################

    public function exibeValorTotalRel($idContrato = null) {
        if (vazio($idContrato)) {
            alert("É necessário informar o id do Contrato.");
            return;
        }

        # exibe o resultado
        $painel = new Callout();
        $painel->abre();

        $valorTotal = $this->getValorTotal($idContrato);
        p("Valor Total:", "pvalorTotalTexto");
        p("R$ " . formataMoeda($valorTotal), "pvalorTotalValor");

        $painel->fecha();
    }

    #####################################################################################

    public function exibeValorTotal($idContrato = null) {
        # Verifica se foi informado
        if (vazio($idContrato)) {
            alert("É necessário informar o id do Contrato.");
            return;
        }

        $valorTotal = $this->getValorTotal($idContrato);

        if ($valorTotal >= 0) {
            p("R$ " . formataMoeda($valorTotal), "pvalorTotalPositivoTabela");
        } else {
            p("R$ " . formataMoeda($valorTotal), "pvalorTotalNegativoTabela");
        }
    }

    #####################################################################################

    public function exibeValor($idContrato = null) {
        # Verifica se foi informado
        if (vazio($idContrato)) {
            alert("É necessário informar o id do Contrato.");
            return;
        }
        # Pega os dados
        $conteudo = $this->getDados($idContrato);

        # Prepara a variável do Total
        $ValorTotal = 0;

        # Verifica se tem vinculado
        $aditivosVinculados = $this->getAditivosVinculados($idContrato);

        # Valor
        if (empty($conteudo["valor"])) {
            p("----", "p#pvalorNulo");
        } else {
            p("R$ " . formataMoeda($conteudo['valor']), "pvalorPositivo");
            $ValorTotal += $conteudo['valor'];
        }

        # Percorre os vinculados
        if (count($aditivosVinculados) > 0) {
            foreach ($aditivosVinculados as $item) {
                if (!empty($item["valor"])) {

                    if ($item["valorSinal"]) {
                        p("R$ -" . formataMoeda($item['valor']), "paditivoVinculadoValorNegativo");
                        $ValorTotal -= $item['valor'];
                    } else {
                        p("R$ " . formataMoeda($item['valor']), "paditivoVinculadoValorPositivo");
                        $ValorTotal += $item['valor'];
                    }
                }
            }
            hr("hrComissao");
            if ($ValorTotal >= 0) {
                p("R$ " . formataMoeda($ValorTotal), "pvalorPositivo");
            } else {
                p("R$ " . formataMoeda($ValorTotal), "pvalorNegativo");
            }
        }
    }

    ##########################################################
    /*
     * Informa a data de publicação mais a página ( se tiver) do Contrato
     */

    public function exibePublicacao($idContrato = null) {

        # Verifica se foi informado o id
        if (vazio($idContrato)) {
            alert("É necessário informar o id do Contrato.");
            return;
        }

        $conteudo = $this->getDados($idContrato);

        # Monta o arquivo
        $arquivo = PASTA_CONTRATOS . $idContrato . ".pdf";

        # Verifica se ele existe
        if (file_exists($arquivo)) {
            # Monta o link
            $link = new Link(null, $arquivo, "Exibe a Publicação");
            $link->set_imagem(PASTA_FIGURAS_GERAIS . "ver.png", 20, 20);
            $link->set_target("_blank");
            $link->show();
        } else {
            # Botão de Upload
            $botao = new BotaoGrafico();
            $botao->set_title('Faça upload do arquivo!');
            $botao->set_url("cadastroContrato.php?fase=upload&id={$idContrato}");
            $botao->set_imagem(PASTA_FIGURAS . 'upload.png', 20, 20);
            $botao->show();
        }

        # Publicação
        p(date_to_php($conteudo["dtPublicacao"]), "pAditivoPublicacao");

        if (!empty($conteudo["pgPublicacao"])) {
            p("pag: {$conteudo["pgPublicacao"]}", "pAditivoPag");
        }


        return;
    }

    ##########################################################
    /*
     * Informa a data de publicação mais a página ( se tiver) do Contrato
     */

    public function getPublicacao($idContrato = null) {

        # Verifica se foi informado o id
        if (vazio($idContrato)) {
            alert("É necessário informar o id do Contrato.");
            return;
        }

        $conteudo = $this->getDados($idContrato);

        # Monta o arquivo
        $arquivo = PASTA_CONTRATOS . $idContrato . ".pdf";

        # Verifica se ele existe
        if (file_exists($arquivo)) {
            # Monta o link
            $link = new Link(null, $arquivo, "Exibe a Publicação");
            $link->set_imagem(PASTA_FIGURAS_GERAIS . "ver.png", 20, 20);
            $link->set_target("_blank");
            $link->show();
        }

        # Publicação
        p(date_to_php($conteudo["dtPublicacao"]), "pAditivoPublicacao");

        if (!empty($conteudo["pgPublicacao"])) {
            p("pag: {$conteudo["pgPublicacao"]}", "pAditivoPag");
        }


        return;
    }

    ##########################################################
    /*
     * Informa a data de publicação mais a página ( se tiver) do Contrato
     */

    public function getPublicacaoRel($idContrato = null) {

        # Verifica se foi informado o id
        if (vazio($idContrato)) {
            alert("É necessário informar o id do Contrato.");
            return;
        }

        $conteudo = $this->getDados($idContrato);

        # Publicação
        p(date_to_php($conteudo["dtPublicacao"]), "pAditivoPublicacao");

        if (!empty($conteudo["pgPublicacao"])) {
            p("pag: {$conteudo["pgPublicacao"]}", "pAditivoPag");
        }


        return;
    }

    ##########################################################
    /*
     * Informa a data de publicação mais a página ( se tiver) do Contrato
     */

    public function getDtPublicacao($idContrato = null) {

        # Verifica se foi informado o id
        if (vazio($idContrato)) {
            alert("É necessário informar o id do Contrato.");
            return;
        }

        $conteudo = $this->getDados($idContrato);

        # Retorno
        if (is_null($conteudo["dtPublicacao"])) {
            $retorno = trataNulo($conteudo["dtPublicacao"]);
        } else {
            $retorno = date_to_php($conteudo["dtPublicacao"]) . "<br/>Pag.: " . trataNulo($conteudo["pgPublicacao"]);
        }

        return $retorno;
    }

    ###########################################################

    function getPeriodo($idContrato = null) {

        # Verifica se foi informado o id
        if (vazio($idContrato)) {
            alert("É necessário informar o id do Contrato.");
            return;
        }

        $conteudo = $this->getDados($idContrato);

        if (!empty($conteudo["dtInicial"])) {

            $dtInicial = date_to_php($conteudo["dtInicial"]);
            $prazo = $conteudo["prazo"];
            $tipoPrazo = $conteudo["tipoPrazo"];

            $tipo = null;
            $dtFinal = null;

            if ($tipoPrazo == 1) {
                $tipo = "Dias";
                $dtFinal = $this->getDtFinal($idContrato);
            } else {
                $tipo = "Meses";
                $dtFinal = $this->getDtFinal($idContrato);
            }
            $retorno = "{$dtInicial}<br/>{$prazo} {$tipo}<br/>$dtFinal";
        } else {
            $retorno = null;
        }

        return $retorno;
    }

    ###########################################################

    function getGarantia($idContrato = null) {
        # Verifica se foi informado o id
        if (vazio($idContrato)) {
            alert("É necessário informar o id do Contrato.");
            return;
        }

        $conteudo = $this->getDados($idContrato);

        # Garantia
        if (!empty($conteudo["valor"])) {
            if (!empty($conteudo["garantia"])) {
                $garantia = $conteudo['valor'] * ($conteudo['garantia'] / 100);
                $garantia = "R$ " . formataMoeda($garantia) . "<br/>({$conteudo['garantia']}%)";
            } else {
                $garantia = "---";
            }
        } else {
            $garantia = "---";
        }

        return $garantia;
    }

    ###########################################################

    function getValorTotal($idContrato = null) {
        # Verifica se foi informado o id
        if (vazio($idContrato)) {
            alert("É necessário informar o id do Contrato.");
            return;
        }

        # Define a variável de retorno
        $valorTotal = 0;

        # Pega os dados do contrato
        $conteudo = $this->getDados($idContrato);

        # Joga o valor do contrato no valor total
        if (!empty($conteudo["valor"])) {
            $valorTotal += $conteudo["valor"];
        }

        # Pega os valores dos aditivos
        $contratos = new Contratos();
        $select = "SELECT valor, valorSinal FROM tbaditivo WHERE idContrato = {$idContrato} ORDER BY dtAssinatura";
        $row = $contratos->select($select);
        $numAditivos = $contratos->count($select);

        # Verifica se tem algum aditivo
        if ($numAditivos > 0) {
            foreach ($row as $item) {
                if ($item["valorSinal"]) {
                    $valorTotal -= $item["valor"];
                } else {
                    $valorTotal += $item["valor"];
                }
            }
        }

        return $valorTotal;
    }

    ###########################################################

    function getValorUltimoAditivo($idContrato = null) {
        # Verifica se foi informado o id
        if (vazio($idContrato)) {
            alert("É necessário informar o id do Contrato.");
            return;
        }

        # Pega os dados do contrato
        $conteudo = $this->getDados($idContrato);

        # Pega os dados do último aditivo com valor
        $aditivo = new Aditivo();
        $ultAdtValor = $aditivo->getDadosUltimoAditivocomValor($idContrato);

        # Verifica se temos aditivo com valor
        if (empty($ultAdtValor["valor"])) {
            $valorTotal = $conteudo["valor"];
        } else {
            if ($ultAdtValor["valorSinal"]) {
                $valorTotal = "-{$ultAdtValor["valor"]}";
            } else {
                $valorTotal = $ultAdtValor["valor"];
            }
        }

        return $valorTotal;
    }

    ###########################################################

    function exibeValorUltimoAditivo($idContrato = null) {
        # Verifica se foi informado o id
        if (vazio($idContrato)) {
            alert("É necessário informar o id do Contrato.");
            return;
        }

        # Pega o valor
        $valor = $this->getValorUltimoAditivo($idContrato);

        if ($valor >= 0) {
            p("R$ " . formataMoeda($valor), "pvalorTotalPositivoTabela");
        } else {
            p("R$ " . formataMoeda($valor), "pvalorTotalNegativoTabela");
        }
    }

    ###########################################################


    /*
     * Informa todos os dados de um aditivo vinculado ao aditivo informado
     * 
     */

    public function getAditivosVinculados($idContrato = null) {
        # Verifica se foi informado o id
        if (vazio($idContrato)) {
            alert("É necessário informar o id do Contrato.");
            return;
        }

        # Conecta ao Banco de Dados
        $contratos = new Contratos();

        # Monta o select
        $select = "SELECT *,
                          IF(tipoPrazo = 2,
                          SUBDATE(ADDDATE(dtInicial, INTERVAL prazo MONTH), INTERVAL 1 DAY),
                          ADDDATE(dtInicial, INTERVAL prazo-1 DAY)) as dtFinal
                     FROM tbaditivo
                    WHERE idContrato = {$idContrato}
                      AND vinculado = 'contrato'
                 ORDER BY dtAssinatura";

        return $contratos->select($select);
    }

    ##########################################################################################

    public function exibeObjetoRelatorio($idContrato) {

        $dados = $this->getDados($idContrato);
        p($dados["objeto"], "pComissaoImpressao");
    }

    ##########################################################################################

    public function exibeObjeto($idContrato) {

        # Pega os dados
        $dados = $this->getDados($idContrato);

        # Exibe o objeto
        echo $dados["objeto"];

        # Verifica se tem observação, se tiver exibe uma figura com mouseover
        if (!empty($dados["obs"])) {
            echo "&nbsp;&nbsp;";
            toolTip("(Obs)", $dados["obs"]);
        }
    }

    ##########################################################################################

    public function exibeObjetoRel($idContrato) {

        # Pega os dados
        $dados = $this->getDados($idContrato);

        # Exibe o objeto
        echo $dados["objeto"];
    }

    ##########################################################################################
}
