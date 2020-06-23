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

        $numero       = $conteudo["numero"];
        $idModalidade = $conteudo["idModalidade"];
        $status       = $this->getStatus($idContrato);

        $mm         = new Modalidade();
        $mmDados    = $mm->get_dados($idModalidade);
        $modalidade = $mmDados['modalidade'];

        p($numero, "contratoNumero");
        p($modalidade, "contratoItem");
        p($status, "status{$status}");
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

        p("Status:", "contratoLabelCallout");
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

        $painel = new Callout("secondary");
        $painel->abre();

        # Pega os valores
        $modalidade = $this->getModalidade($idContrato);
        $status     = $this->getStatus($idContrato);
        $processo   = $this->getProcesso($idContrato, false);
        $assinatura = date_to_php($conteudo["dtAssinatura"]);
        $obs        = $conteudo["obs"];

        # Prazo
        $prazo     = $conteudo["prazo"];
        $tipoPrazo = $conteudo["tipoPrazo"];
        $inicio    = date_to_php($conteudo["dtInicial"]);
        $vigencia  = $this->getVigencia($idContrato);

        if ($tipoPrazo == 1) {
            $vigencia = "{$vigencia} ({$prazo} dias)";
        } elseif ($tipoPrazo == 2) {
            $vigencia = "{$vigencia} ({$prazo} meses)";
        }

        # Publicação
        $dtPublicacao = $conteudo["dtPublicacao"];
        $publicacao   = date_to_php($dtPublicacao);
        if (!empty($conteudo["pgPublicacao"])) {
            $publicacao .= " pag: {$conteudo["pgPublicacao"]}";
        }

        # Valor
        if (!empty($conteudo["valor"])) {
            $valor = "R$ " . formataMoeda($conteudo['valor']);
        }

        # Garantia
        if (!empty($conteudo["valor"])) {
            if (!empty($conteudo["garantia"])) {
                $garantia = $conteudo['valor'] * ($conteudo['garantia'] / 100);
                $garantia = "R$ " . formataMoeda($garantia) . " ({$conteudo['garantia']}%)";
            }
        }

        $dtAssinatura = $conteudo["dtAssinatura"];

        # Monta o array de exibição
        $dados = [
            ["siafe", 4],
            ["modalidade", 4],
            ["publicacao", 4, "Publicação DOERJ"],
            ["inicio", 4, "Início"],
            ["vigencia", 4, "Vigência"],
            ["assinatura", 4, "Assinatura"],
            ["garantia", 4],
        ];

        # Rotina de exibição
        $grid = new Grid();
        $grid->abreColuna(12);

        titulo("Contrato");
        br();

        $grid->fechaColuna();

        foreach ($dados as $item) {

            # Monta a variável para usar o $$
            $pp = $item[0];

            # label
            if (empty($item[2])) {
                $label = plm($pp);
            } else {
                $label = $item[2];
            }

            # Verifica se tem variável com esse nome
            if (empty($$pp)) { // Se não tem variável com esse nome
                if (empty($conteudo[$pp])) { // Se não tiver no array de conteúdo do bd
                    $dado = "---"; // Exibe tracinho
                } else { // Se tiver conteúdo do bd exibe ele
                    $dado = $conteudo[$pp];
                }
            } else { // Se tiver variável exibe ela
                $dado = $$pp;
            }

            $grid->abreColuna($item[1]);
            p("{$label}:", "contratoLabel");
            p($dado, "contratoConteudo");
            $grid->fechaColuna();
        }
        $grid->fechaGrid();

        # Editar    
        $div = new Div("divEdita1");
        $div->abre();

        $div = new Div("divEdita2");
        $div->abre();

        $botaoEditar = new Link("Editar", "cadastroContrato.php?fase=editar&id={$idContrato}");
        $botaoEditar->set_class('tiny button secondary');
        $botaoEditar->set_title('Editar contrato');
        $botaoEditar->show();

        $div->fecha();
        $div->fecha();

        $painel->fecha();
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

        $conteudo  = $this->getDados($idContrato);
        $numero    = "<p id='contratoNumero'>{$conteudo["numero"]}</p>";
        $objeto    = $conteudo["objeto"];
        $idEmpresa = $conteudo["idEmpresa"];
        $processo  = $this->getProcesso($idContrato);

        $bdempresa = new Empresa();
        $dados     = $bdempresa->getDados($idEmpresa);
        $empresa   = $dados["razaoSocial"];
        $cnpj      = $dados["cnpj"];

        # Limita o tamanho da tela
        $grid = new Grid();
        $grid->abreColuna(12);

        # Monta os dados
        $label = ["Contrato", "Processo", "Objeto", "Empresa"];
        $item  = [[$numero, $processo, $objeto, "{$empresa}<br/>{$cnpj}"]];

        $formatacaoCondicional = array(array('coluna'   => 0,
                'valor'    => $numero,
                'operador' => '=',
                'id'       => 'listaDados'));

        # Monta a tabela
        $tabela = new Tabela();
        $tabela->set_conteudo($item);
        $tabela->set_label($label);
        #$tabela->set_funcao($function);
        #$tabela->set_classe($classe);
        #$tabela->set_metodo($metodo);
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
                if ($itemAditivo["tipoPrazo"] == 2) {
                    $prazoMeses += $itemAditivo["prazo"];
                } else {
                    $prazoDias += $itemAditivo["prazo"];
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

        return $dtVigencia;
    }

    ###########################################################
    /*
     * Informa a vigência geral do contrato
     */

    public function getVigenciaTotal($idContrato)
    {

        # Verifica se foi informado
        if (vazio($idContrato)) {
            alert("É necessário informar o id do Contrato.");
            return;
        }

        # Verifica se tem aditivo e pega os tempo de cada um deles
        $aditivo = new Aditivo();
        if ($aditivo->temAditivo($idContrato)) {
            $dados = $aditivo->getDadosUltimoAditivo($idContrato);

            return $aditivo->getVigencia($dados["idAditivo"]);
        } else {
            return $this->getVigencia($idContrato);
        }
    }

    ###########################################################

    public function getNumero($idContrato)
    {

        # Joga o valor informado para a variável da classe
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

    public function getModalidade($idContrato = null)
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

        return $row["modalidade"];
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
                if($itens[1] == date('Y')){
                    $retorno  = "$itens[0]/".date('Y');
                }else{
                    $retorno  = "001/".date('Y');
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
        # Pega o valor do contrato
        $conteudo = $this->getDados($idContrato);

        # Inicia as variáveis
        $valorTotal       = 0;
        $valoresTabela[]  = null;
        $contadorAditivos = 0;

        $valorTotal       += $conteudo["valor"];
        $valoresTabelas[] = ["Contrato", "R$ " . formataMoeda($conteudo["valor"])];

        # Valores do aditivo
        $contratos   = new Contratos();
        $select      = "SELECT valor FROM tbaditivo WHERE idContrato = {$idContrato} ORDER BY dtAssinatura";
        $row         = $contratos->select($select);
        $numAditivos = $contratos->count($select);

        # Percorre os valores somando-os
        foreach ($row as $item) {
            $contadorAditivos++;
            if (!empty($item[0])) {
                $valorTotal       += $item[0];
                $valoresTabelas[] = ["Aditivo {$contadorAditivos}", "R$ " . formataMoeda($item[0])];
            }
        }

        # exibe o resultado
        $painel = new Callout("secondary");
        $painel->abre();

        titulo("Valor Total");
        br();

        p("R$ " . formataMoeda($valorTotal), "p12", "center");

        /*
         * Exibe a tabela de aditivos e valores
         */

        if ($numAditivos > 0) {

            # Monta a tabela
            $tabela = new Tabela();
            #$tabela->set_titulo("Aditivos");
            $tabela->set_label(array("Descrição", "Valor"));
            $tabela->set_align(array("left", "right"));
            $tabela->set_width(array(60, 40));
            #$tabela->set_classe(array("Comissao", "Comissao"));
            #$tabela->set_metodo(array("get_nomeMembro", "get_tipo"));
            #$tabela->set_numeroOrdem(true);
            $tabela->set_conteudo($valoresTabelas);
            $tabela->show();
        }

        $painel->fecha();
    }

#####################################################################################
}
