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
        $select = "SELECT *
                     FROM tbcontrato
                    WHERE idContrato = {$idContrato}";

        $row = $contratos->select($select, false);

        # Retorno
        return $row;
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
                $processo = "SEI - {$conteudo["processoSei"]} / {$conteudo["processo"]}";
            }
        }

        return $processo;
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
    public function exibeDadosContrato($idContrato) {

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
        $tabela->set_width(array(10, 20, 10, 10, 10, 15, 15, 10));
        $tabela->set_classe(array("Contrato", "Contrato", "Contrato", null, "Contrato", "Contrato", "Contrato"));
        $tabela->set_metodo(array("exibeModalidade", "exibeObjeto", "getPublicacao", null, "getPeriodo", "getGarantia", "exibeValor"));
        $tabela->set_funcao(array(null, null, null, "date_to_php"));
        $tabela->set_conteudo($row);
        $tabela->set_totalRegistro(false);

        $tabela->set_editar('cadastroContrato.php?fase=editar&id=' . $idContrato);
        $tabela->set_idCampo('idContrato');
        $tabela->show();
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
        $tabela->set_label(["Contrato", "Processo", "Objeto", "Empresa", "Vigência Total"]);
        $tabela->set_width([15, 20, 25, 25, 15]);
        #$tabela->set_funcao($function);
        $tabela->set_classe(["Contrato", "Contrato", "Contrato", "Empresa", "Contrato"]);
        $tabela->set_metodo(["exibeNumeroContrato", "getProcesso", "exibeObjeto", "getEmpresaCnpj", "exibeTempoEVigencia"]);
        $tabela->set_totalRegistro(false);
        $tabela->set_formatacaoCondicional($formatacaoCondicional);

        $tabela->show();
    }

    ###########################################################

    public function exibeTempoEVigencia($idContrato) {

        # Pega os dados
        $dados = $this->getDados($idContrato);

        # Verifica se tem data inicial
        if (empty($dados["dtInicial"])) {

            return null;
        } else {

            # Tempo Total
            $tempo = $this->getTempoTotal($idContrato);

            # Vigencia Total
            $vigencia = $this->getVigenciaTotal($idContrato);

            # Diferença em dias
            if (!jaPassou($vigencia)) {
                $diferenca = abs(dataDif($vigencia));
            }

            if ($tempo["meses"] >= 60) {
                p("{$tempo["meses"]} Meses", "pTempoTotal60");
            } else {
                p("{$tempo["meses"]} Meses", "pTempoTotal");
            }

            p($vigencia, "pVigencia");
            if (!jaPassou($vigencia)) {
                p("Faltam {$diferenca} dias", "pVigencia");
            } else {
                p("Já Passou !", "pVigenciaJaPassou");
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

    public function getVigencia($idContrato) {

        # Verifica se foi informado
        if (vazio($idContrato)) {
            alert("É necessário informar o id do Contrato.");
            return;
        }

        $conteudo = $this->getDados($idContrato);

        # Verifica se a data inicial foi preenchida
        if (!empty($conteudo["dtInicial"])) {

            $dtInicial = date_to_php($conteudo["dtInicial"]);
            $prazo = $conteudo["prazo"];
            $tipoPrazo = $conteudo["tipoPrazo"];

            $tipo = null;
            $dtVigencia = null;

            # trata pelo tipo de prazo
            if ($tipoPrazo == 1) {
                $dtVigencia = addDias($dtInicial, $prazo);
            } else {
                $dtVigencia = addMeses($dtInicial, $prazo);
                $dtVigencia = addDias($dtVigencia, -1, false);      // retira 1 dia
            }
        } else {
            $dtVigencia = null;
        }

        return $dtVigencia;
    }

    ###########################################################
    /*
     * Informa a vigência geral do contrato
     */

    public function getVigenciaTotal($idContrato) {

        # Verifica se foi informado o id
        if (vazio($idContrato)) {
            alert("É necessário informar o id do Contrato.");
            return;
        }

        # Define a vigencia total com a vigência do contrato inicial
        $vigenciaTotal = $this->getVigencia($idContrato);

        # Verifica se tem aditivo e pega os tempo de cada um deles
        $aditivo = new Aditivo();
        if ($aditivo->temAditivo($idContrato)) {
            $arrayAditivo = $aditivo->getAditivosContrato($idContrato);

            # Percorre o array
            foreach ($arrayAditivo as $itemAditivo) {

                # Pega a vigência deste aditivo
                $vigencia = $aditivo->getVigencia($itemAditivo["idAditivo"]);

                # Descarta de for nula e atualiza vigencia total se não for
                if (!empty($vigencia)) {
                    $vigenciaTotal = $vigencia;
                }
            }
        }

        return $vigenciaTotal;
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

    public function exibeValorTotal($idContrato = null) {
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
        $painel->fecha();
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
                $dtFinal = $this->getVigencia($idContrato);
            } else {
                $tipo = "Meses";
                $dtFinal = $this->getVigencia($idContrato);
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

    ##############################################################

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

        # monta o select
        $select = "SELECT *
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
            $div = new Div("divObs");
            $div->abre();
            $figura = new Imagem(PASTA_FIGURAS . 'obs.png', $dados["obs"], 20, 20);
            $figura->show();
            $div->fecha();
        }
    }

    ##########################################################################################
}
