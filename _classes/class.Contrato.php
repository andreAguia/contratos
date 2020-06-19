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
    private $idContrato    = null;
    private $permiteEditar = true;
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

    public function get_dados($idContrato = null)
    {

        /**
         * Informa os dados da base de dados
         *
         * @param $idConcurso integer null O id do concurso
         *
         * @syntax $concurso->get_dados([$idConcurso]);
         */
        # Joga o valor informado para a variável da classe
        if (!vazio($idContrato)) {
            $this->idContrato = $idContrato;
        }

        # Conecta ao Banco de Dados
        $contratos = new Contratos();

        # Verifica se foi informado
        if (vazio($this->idContrato)) {
            alert("É necessário informar o id do Contrato.");
            return;
        }

        # Pega os dados
        $select = 'SELECT *
                     FROM tbcontrato
                    WHERE idContrato = ' . $this->idContrato;

        $row = $contratos->select($select, false);

        # Retorno
        return $row;
    }

    ##############################################################

    public function get_processo($idContrato = null, $br = true)
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

        $conteudo = $this->get_dados($idContrato);

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
        # Joga o valor informado para a variável da classe
        if (!vazio($idContrato)) {
            $this->idContrato = $idContrato;
        }

        $conteudo = $this->get_dados($idContrato);

        $painel = new Callout("primary");
        $painel->abre();

        $numero       = $conteudo["numero"];
        $idModalidade = $conteudo["idModalidade"];
        $status       = $this->get_status($idContrato);

        $mm         = new Modalidade();
        $mmDados    = $mm->get_dados($idModalidade);
        $modalidade = $mmDados['modalidade'];

        p($numero, "contratoNumero");
        p($modalidade, "contratoItem");
        br();
        p("Contrato {$status}", "status{$status}");

        $painel->fecha();
    }

    ###########################################################

    public function exibeStatus($idContrato)
    {

        $status = $this->get_status($idContrato);

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

        # Joga o valor informado para a variável da classe
        if (!vazio($idContrato)) {
            $this->idContrato = $idContrato;
        }

        $conteudo = $this->get_dados($idContrato);

        $painel = new Callout("secondary");
        $painel->abre();

        # Pega os valores
        $modalidade = $this->get_modalidade($idContrato);
        $status     = $this->get_status($idContrato);
        $processo   = $this->get_processo($idContrato, false);
        $assinatura = date_to_php($conteudo["dtAssinatura"]);
        $obs        = $conteudo["obs"];

        # Prazo
        $prazo     = $conteudo["prazo"];
        $tipoPrazo = $conteudo["tipoPrazo"];
        $inicio    = date_to_php($conteudo["dtInicial"]);

        if ($tipoPrazo == 1) {
            $prazo2   = " dias";
            $vigencia = addDias($inicio, $prazo);
            $prazo    .= $prazo2;
            $vigencia .= " ({$prazo})";
        } elseif ($tipoPrazo == 2) {
            $prazo2   = " meses";
            $vigencia = addMeses($inicio, $prazo);
            $prazo    .= $prazo2;
            $vigencia .= " ({$prazo})";
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

        $div = new Div("divEdita1");
        $div->abre();

        # Editar
        $div = new Div("divEdita2");
        $div->abre();

        # Editar
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
     * @param    string $idVaga O id da vaga
     */
    public function exibeResumoDados($idContrato)
    {

        $conteudo  = $this->get_dados($idContrato);
        $numero    = $conteudo["numero"];
        $objeto    = $conteudo["objeto"];
        $idEmpresa = $conteudo["idEmpresa"];
        $processo  = $this->get_processo($idContrato);

        $bdempresa = new Empresa();
        $dados     = $bdempresa->get_dados($idEmpresa);
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

    public function get_periodo($idContrato)
    {

        # Joga o valor informado para a variável da classe
        if (!vazio($idContrato)) {
            $this->idContrato = $idContrato;
        }

        $conteudo = $this->get_dados($idContrato);

        $dtInicial = date_to_php($conteudo["dtInicial"]);
        $prazo     = $conteudo["prazo"];
        $tipoPrazo = $conteudo["tipoPrazo"];

        $tipo    = null;
        $dtFinal = null;

        if ($tipoPrazo == 1) {
            $tipo    = "Dias";
            $dtFinal = addDias($dtInicial, $prazo);
        } else {
            $tipo    = "Meses";
            $dtFinal = addMeses($dtInicial, $prazo);
        }
        $retorno = "{$dtInicial}<br/>{$prazo} {$tipo}<br/>$dtFinal";

        return $retorno;
    }

    ###########################################################

    public function get_numero($idContrato)
    {

        # Joga o valor informado para a variável da classe
        if (!vazio($idContrato)) {
            $this->idContrato = $idContrato;
        }

        $conteudo = $this->get_dados($idContrato);

        return $conteudo["numero"];
    }

    ##############################################################

    public function get_status($idContrato = null)
    {

        # Verifica se foi informado
        if (vazio($idContrato)) {
            alert("É necessário informar o id do Contrato.");
            return;
        }

        # Conecta ao Banco de Dados
        $contratos = new Contratos();

        $conteudo = $this->get_dados($idContrato);
        $idStatus = $conteudo["idStatus"];

        # monta o select
        $select = "SELECT status
                     FROM tbstatus
                    WHERE idStatus = {$idStatus}";

        $row = $contratos->select($select, false);

        return $row["status"];
    }

    ##############################################################

    public function get_modalidade($idContrato = null)
    {

        # Verifica se foi informado
        if (vazio($idContrato)) {
            alert("É necessário informar o id do Contrato.");
            return;
        }

        # Conecta ao Banco de Dados
        $contratos = new Contratos();

        $conteudo     = $this->get_dados($idContrato);
        $idModalidade = $conteudo["idModalidade"];

        # monta o select
        $select = "SELECT modalidade
                     FROM tbmodalidade
                    WHERE idModalidade = {$idModalidade}";

        $row = $contratos->select($select, false);

        return $row["modalidade"];
    }

    #####################################################################################

    public function get_novoNumero()
    {
        # Conecta ao Banco de Dados
        $contratos = new Contratos();

        $select = "SELECT numero
                     FROM tbcontrato
                 ORDER BY numero desc LIMIT 1";

        $numero = $contratos->select($select, false);

        if (empty($numero["numero"])) {
            $retorno = "001/" . date('Y');
        } else {
            $itens    = explode("/", $numero["numero"]);
            $itens[0]++;
            $itens[0] = str_pad($itens[0], 3, '0', STR_PAD_LEFT);
            $retorno  = "$itens[0]/$itens[1]";
        }

        return $retorno;
    }

    #####################################################################################

    public function exibeValorTotal($idContrato = null)
    {
        # Pega o valor do contrato
        $conteudo = $this->get_dados($idContrato);

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
