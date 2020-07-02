<?php

class Contrato
{

    /**
     * Abriga as várias rotina referentes a concurso
     *
     * @author André Águia (Alat) - alataguia@gmail.com
     *
     * @var private $idConcurso integer null O id do concurso
     */
##############################################################

    public function __construct($idContrato = null)
    {
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

    public function getDados($idContrato = null)
    {

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

    public function getProcesso($idContrato = null, $br = true)
    {

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

    public function listaContratos()
    {

        # Conecta ao Banco de Dados
        $contratos = new Contratos();

        # monta o select
        $select = "SELECT CONCAT(numero,'<br/>',modalidade,'<br/>',status),
                          objeto,
                          idEmpresa,
                          idContrato,
                          idContrato,
                          idContrato,
                          idContrato
                     FROM tbcontrato JOIN tbmodalidade USING (idModalidade)
                                     JOIN tbstatus USING (idStatus)
                                     JOIN tbempresa USING (idEmpresa)
                  ORDER BY numero";

        $row = $contratos->select($select);

        # Monta a tabela
        $tabela = new Tabela();
        $tabela->set_titulo("Contratos");
        $tabela->set_label(array("Contrato", "Objeto", "Empresa", "Processo", "Prazo", "Situação"));
        $tabela->set_classe(array(null, null, "Empresa", "Contrato", "Contrato", "Situacao"));
        $tabela->set_metodo(array(null, null, "get_empresaCnpj", "get_processo", "get_periodo", "get_situacaoAtual"));
        $tabela->set_width(array(10, 20, 20, 20, 10, 20));
        $tabela->set_align(array("center", "left", "left", "left", "center", "left"));
        $tabela->set_idCampo('idContrato');

        if ($this->permiteEditar) {
            $tabela->set_editar('areaContrato.php?fase=');
            $tabela->set_nomeColunaEditar("Acessar");
            $tabela->set_editarBotao("ver.png");
        }

        $tabela->set_conteudo($row);
        $tabela->show();
    }

    ###########################################################

    public function exibeNumeroContrato($idContrato)
    {
        # Verifica se foi informado
        if (vazio($idContrato)) {
            alert("É necessário informar o id do Contrato.");
            return;
        }

        $conteudo = $this->getDados($idContrato);

        $modalidade = new Modalidade();

        p($conteudo["numero"], "contratoNumero");
        p($conteudo['siafe'], "pVigencia");
        p($modalidade->get_modalidade($conteudo["idModalidade"]), "pVigencia");

        $status = $this->getStatus($idContrato);

        if ($status == "Ativo") {
            $stilo = "statusAtivo";
        } elseif ($status == "Pendente") {
            $stilo = "statusPendente";
        } else {
            $stilo = "statusEncerrado";
        }
        p($status, "$stilo");
    }

    ###########################################################

    public function exibeStatus($idContrato)
    {
        # Verifica se foi informado
        if (vazio($idContrato)) {
            alert("É necessário informar o id do Contrato.");
            return;
        }

        $status = $this->getStatus($idContrato);

        if ($status == "Ativo") {
            $painel = new Callout("success");
            $stilo  = "statusAtivo";
        } elseif ($status == "Pendente") {
            $painel = new Callout("alert");
            $stilo  = "statusPendente";
        } else {
            $painel = new Callout("secondary");
            $stilo  = "statusEncerrado";
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
    public function exibeDadosContrato($idContrato)
    {

        # Verifica se foi informado
        if (vazio($idContrato)) {
            alert("É necessário informar o id do Contrato.");
            return;
        }
        $conteudo = $this->getDados($idContrato);

        $select = "SELECT idContrato,
                          objeto,
                          idContrato,
                          dtAssinatura,
                          idContrato,
                          idContrato
                     FROM tbcontrato
                    WHERE idContrato = {$idContrato}";

        $contratos = new Contratos();
        $row       = $contratos->select($select);

        $tabela = new Tabela();
        $tabela->set_titulo("Contrato {$conteudo["numero"]}");
        $tabela->set_label(array("Tipo", "Objeto", "Publicação", "Assinatura", "Duração", "Garantia"));
        $tabela->set_align(array("center", "left", "center"));
        $tabela->set_width(array(15, 23, 15, 12, 15, 20));
        $tabela->set_classe(array("Contrato", null, "Contrato", null, "Contrato", "Contrato"));
        $tabela->set_metodo(array("exibeModalidade", null, "getPublicacao", null, "getPeriodo", "getGarantia"));
        $tabela->set_funcao(array(null, null, null, "date_to_php"));
        $tabela->set_conteudo($row);
        $tabela->set_totalRegistro(false);
        $tabela->show();
    }

    ###########################################################

    /**
     * Método exibeDadosConcurso
     * fornece os dados de uma vaga em forma de tabela
     *
     * @param    string $idContrato O id da vaga
     */
    public function exibeResumoDados($idContrato)
    {
        # Verifica se foi informado
        if (vazio($idContrato)) {
            alert("É necessário informar o id do Contrato.");
            return;
        }

        $select = "SELECT idContrato, 
                          idContrato, 
                          objeto,
                          idEmpresa, 
                          idContrato
                    FROM tbcontrato
                   WHERE idContrato = {$idContrato}";

        $contratos = new Contratos();
        $row       = $contratos->select($select);

        # Limita o tamanho da tela
        $grid = new Grid();
        $grid->abreColuna(12);

        $formatacaoCondicional = array(array(
                'coluna'   => 2,
                'valor'    => $row[0][2],
                'operador' => '=',
                'id'       => 'listaDados'));

        # Monta a tabela
        $tabela = new Tabela();
        $tabela->set_conteudo($row);
        $tabela->set_label(["Contrato", "Processo", "Objeto", "Empresa", "Vigência"]);
        $tabela->set_width([10, 20, 25, 25, 10]);
        #$tabela->set_funcao($function);
        $tabela->set_classe(["Contrato", "Contrato", null, "Empresa", "Contrato"]);
        $tabela->set_metodo(["exibeNumeroContrato", "getProcesso", null, "getEmpresaCnpj", "exibeTempoEVigencia"]);
        $tabela->set_totalRegistro(false);
        $tabela->set_formatacaoCondicional($formatacaoCondicional);

        $tabela->show();
    }

    ###########################################################

    public function exibeTempoEVigencia($idContrato)
    {
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
            p("({$diferenca} dias)", "pVigencia");
        }
    }

    ###########################################################
    /*
     * Retorna array com os meses e dias do tempo do contrato
     */

    public function getTempoTotal($idContrato)
    {
        # Verifica se foi informado
        if (vazio($idContrato)) {
            alert("É necessário informar o id do Contrato.");
            return;
        }

        # Inicia as variáveis de retorno
        $prazoDias  = 0;
        $prazoMeses = 0;

        # Pega o tempo do contrato
        $conteudo  = $this->getDados($idContrato);
        $prazo     = $conteudo["prazo"];
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
            "dias"  => $prazoDias,
            "meses" => $prazoMeses,
        ];

        return $retorno;
    }

    ###########################################################

    public function getVigencia($idContrato)
    {

        # Verifica se foi informado
        if (vazio($idContrato)) {
            alert("É necessário informar o id do Contrato.");
            return;
        }

        $conteudo = $this->getDados($idContrato);

        # Verifica se a data inicial foi preenchida
        if (!empty($conteudo["dtInicial"]) AND!empty($conteudo["prazo"]) AND!empty($conteudo["tipoPrazo"])) {

            $dtInicial = date_to_php($conteudo["dtInicial"]);
            $prazo     = $conteudo["prazo"];
            $tipoPrazo = $conteudo["tipoPrazo"];

            $tipo       = null;
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

    public function getVigenciaTotal($idContrato)
    {

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

    public function getNumero($idContrato)
    {

        # Verifica se foi informado o id
        if (!vazio($idContrato)) {
            $this->idContrato = $idContrato;
        }

        $conteudo = $this->getDados($idContrato);

        return $conteudo["numero"];
    }

    ##############################################################

    public function getStatus($idContrato = null)
    {

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

    public function exibeModalidade($idContrato = null)
    {

        # Verifica se foi informado
        if (vazio($idContrato)) {
            alert("É necessário informar o id do Contrato.");
            return;
        }

        # Conecta ao Banco de Dados
        $contratos = new Contratos();

        $conteudo     = $this->getDados($idContrato);
        $idModalidade = $conteudo["idModalidade"];

        # monta o select
        $select = "SELECT modalidade
                     FROM tbmodalidade
                    WHERE idModalidade = {$idModalidade}";

        $row = $contratos->select($select, false);
        echo $row["modalidade"];

        if ($conteudo["maoDeObra"]) {
            hr("hrComissao");
            echo "Mão de Obra Alocada";
        }

        return;
    }

    #####################################################################################

    public function getNovoNumeroProcesso()
    {
        # Conecta ao Banco de Dados
        $contratos = new Contratos();

        $select = "SELECT numero
                     FROM tbcontrato
                    WHERE INSTR(numero,'/')
                 ORDER BY numero desc LIMIT 1";

        $numero = $contratos->select($select, false);

        if (empty($numero["numero"])) {
            $retorno = "001/" . date('Y');
        } else {
            # Verifica se tem / em numero
            $posicao = mb_strpos($numero["numero"], "/");

            if ($posicao !== false) {
                $itens    = explode("/", $numero["numero"]);
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

    public function exibeValorTotal($idContrato = null)
    {
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

        # Pega o valor do contrato
        $conteudo = $this->getDados($idContrato);

        # Contrato
        $tabela = new Tabela();
        #$tabela->set_titulo("Aditivos");
        $tabela->set_label(array("Descrição", "Valor"));
        $tabela->set_align(array("left", "right"));
        $tabela->set_width(array(45, 55));
        $tabela->set_conteudo([["Contrato", formataMoeda($conteudo["valor"])]]);
        $tabela->set_totalRegistro(false);
        $tabela->show();

        # Valores do aditivo
        $contratos   = new Contratos();
        $select      = "SELECT idAditivo, idAditivo FROM tbaditivo WHERE idContrato = {$idContrato} ORDER BY dtAssinatura";
        $row         = $contratos->select($select);
        $numAditivos = $contratos->count($select);

        /*
         * Exibe a tabela de aditivos e valores
         */

        if ($numAditivos > 0) {

            # Monta a tabela
            $tabela = new Tabela();
            #$tabela->set_titulo("Aditivos");
            $tabela->set_label(array("", ""));
            $tabela->set_align(array("left", "right"));
            $tabela->set_width(array(45, 55));
            $tabela->set_classe(array("Aditivo", "Aditivo"));
            $tabela->set_metodo(array("getTipoNumerado", "getValor"));
            $tabela->set_conteudo($row);
            $tabela->show();
        }

        $painel->fecha();
    }

    ###########################################################
    /*
     * Informa a data de publicação mais a página ( se tiver) do Contrato
     */

    public function getPublicacao($idContrato = null)
    {

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

    function getPeriodo($idContrato = null)
    {
        # Verifica se foi informado o id
        if (vazio($idContrato)) {
            alert("É necessário informar o id do Contrato.");
            return;
        }

        $conteudo = $this->getDados($idContrato);

        if (!empty($conteudo["dtInicial"])) {

            $dtInicial = date_to_php($conteudo["dtInicial"]);
            $prazo     = $conteudo["prazo"];
            $tipoPrazo = $conteudo["tipoPrazo"];

            $tipo    = null;
            $dtFinal = null;

            if ($tipoPrazo == 1) {
                $tipo    = "Dias";
                $dtFinal = $this->getVigencia($idContrato);
            } else {
                $tipo    = "Meses";
                $dtFinal = $this->getVigencia($idContrato);
            }
            $retorno = "{$dtInicial}<br/>{$prazo} {$tipo}<br/>$dtFinal";
        } else {
            $retorno = null;
        }

        return $retorno;
    }

    ###########################################################

    function getGarantia($idContrato = null)
    {
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

    function getValorTotal($idContrato = null)
    {
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
        $contratos   = new Contratos();
        $select      = "SELECT valor, valorSinal FROM tbaditivo WHERE idContrato = {$idContrato} ORDER BY dtAssinatura";
        $row         = $contratos->select($select);
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

    #####################################################################################
}
