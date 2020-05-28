<?php

class Contrato {

    /**
     * Abriga as várias rotina referentes a concurso
     *
     * @author André Águia (Alat) - alataguia@gmail.com
     * 
     * @var private $idConcurso integer NULL O id do concurso
     */
    private $idContrato = null;
    private $permiteEditar = true;

##############################################################

    public function __construct($idContrato = NULL) {
        /**
         * Inicia a Classe somente
         * 
         * @param $idContrato integer NULL O id do concurso
         * 
         * @syntax $concurso = new Concurso([$idConcurso]);
         */
        $this->idContrato = $idContrato;
    }

##############################################################

    public function get_dados($idContrato = NULL) {

        /**
         * Informa os dados da base de dados
         * 
         * @param $idConcurso integer NULL O id do concurso
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

        $row = $contratos->select($select, FALSE);

        # Retorno
        return $row;
    }

    #####################################################################################

    public function listaContratos() {

        # Conecta ao Banco de Dados
        $contratos = new Contratos();

        # monta o select
        $select = "SELECT status,
                          CONCAT(numero,'<br/>',tbcontrato.nome,'<br/>',modalidade),
                          CONCAT(tbempresa.razaoSocial,'<br/>',cnpj),
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
        $tabela->set_label(array("Status", "Número", "Empresa", "Prazo", "Situação"));
        $tabela->set_classe(array(NULL, NULL, NULL, "Contrato", "Situacao"));
        $tabela->set_metodo(array(NULL, NULL, NULL, "exibeDatasTabela", "get_situacaoAtual"));
        #$tabela->set_funcao(array(NULL,NULL,NULL,NULL,"date_to_php"));
        $tabela->set_align(array("center", "center", "left", "center", "left"));
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

    /**
     * Método exibeDadosConcurso
     * fornece os dados de uma vaga em forma de tabela
     * 
     * @param	string $idVaga O id da vaga
     */
    function exibeDadosContrato1($idContrato) {

        # Joga o valor informado para a variável da classe
        if (!vazio($idContrato)) {
            $this->idContrato = $idContrato;
        }

        $conteudo = $this->get_dados($idContrato);

        $painel = new Callout("primary");
        $painel->abre();

        $numero = $conteudo["numero"];
        $nome = $conteudo["nome"];
        $idModalidade = $conteudo["idModalidade"];

        $mm = new Modalidade();
        $mmDados = $mm->get_dados($idModalidade);
        $modalidade = $mmDados['modalidade'];

        p($numero, "contratoNumero");
        p($nome, "contratoItem");
        p($modalidade, "contratoItem");

        $painel->fecha();
    }

    ###########################################################

    /**
     * Método exibeDadosConcurso
     * fornece os dados de uma vaga em forma de tabela
     * 
     * @param	string $idVaga O id da vaga
     */
    function exibeDadosContrato2($idContrato) {

        # Joga o valor informado para a variável da classe
        if (!vazio($idContrato)) {
            $this->idContrato = $idContrato;
        }

        $conteudo = $this->get_dados($idContrato);

        #$painel = new Callout("secondary");
        #$painel->abre();

        # Pega os valores
        $bdModalidade = new Modalidade();
        $idModalidade = $conteudo["idModalidade"];
        $dadosModalidade = $bdModalidade->get_dados($idModalidade);
        $modalidade = $dadosModalidade["modalidade"];
        $dtPublicacao = $conteudo["dtPublicacao"];
        $publicacao = date_to_php($dtPublicacao);
        $dtAssinatura = $conteudo["dtAssinatura"];
        $assinatura = date_to_php($dtAssinatura);
        $dtInicial = $conteudo["dtInicial"];
        $inicio = date_to_php($dtInicial);
        $prazo1 = $conteudo["prazo"];
        $tipoPrazo = $conteudo["tipoPrazo"];

        if ($tipoPrazo == 1) {
            $prazo2 = " dias";
        } elseif ($tipoPrazo == 2) {
            $prazo2 = " meses";
        }

        $prazo = "{$prazo1} {$prazo2}";

        $bdempresa = new Empresa();
        $idEmpresa = $conteudo["idEmpresa"];
        $empresa = $bdempresa->get_razaoSocial($idEmpresa);
        $conteudo2 = $bdempresa->get_dados($idEmpresa);
        $cnpj = $conteudo2["cnpj"];

        # Monta o array de exibição
        $dados = [
            ["numero", 3],
            ["siafe", 3],
            ["nome", 3],
            ["modalidade", 3],
            ["empresa", 8],
            ["cnpj", 4],
            ["objeto", 12],
            ["publicacao", 2, "Publicação DOERJ"],
            ["pgPublicacao", 2, "Página"],
            ["assinatura", 2, "Assinatura"],
            ["processoSei", 3, "Processo Sei"],
            ["processo", 3, "Processo Físico"],
            ["inicio", 2, "Início"],
            ["prazo", 2],
            ["obs", 8]
        ];

        # Rotina de exibição
        # Limita a tela
        $grid = new Grid();
        $grid->abreColuna(12);

        #tituloTable("Dados do Contrato:");
        #br();

        $grid->fechaColuna();

        foreach ($dados as $item) {

            # Monta a variável para usar o $$
            $pp = $item[0];

            # Monta a coluna
            $grid->abreColuna($item[1]);

            # Exibe o label colocando a primeira letra em maúsculas
            if (empty($item[2])) {
                p(plm($pp) . ":", "contratoLabel");
            } else {
                p($item[2] . ":", "contratoLabel");
            }


            # Verifica se tem variável com esse nome
            if (empty($$pp)) {
                if (empty($conteudo[$pp])) {
                    p("---", "contratoConteudo");
                } else {
                    p($conteudo[$pp], "contratoConteudo");
                }
            } else {
                p($$pp, "contratoConteudo");
            }

            $grid->fechaColuna();
        }
        $grid->fechaGrid();

        #$painel->fecha();
    }

    ###########################################################

    /**
     * Método exibeDadosConcurso
     * fornece os dados de uma vaga em forma de tabela
     * 
     * @param	string $idVaga O id da vaga
     */
    function exibeDatasTabela($idContrato) {

        # Joga o valor informado para a variável da classe
        if (!vazio($idContrato)) {
            $this->idContrato = $idContrato;
        }

        $conteudo = $this->get_dados($idContrato);

        $dtInicial = date_to_php($conteudo["dtInicial"]);
        $prazo = $conteudo["prazo"];
        $tipoPrazo = $conteudo["tipoPrazo"];

        $tipo = null;
        $dtFinal = null;

        if ($tipoPrazo == 1) {
            $tipo = "Dias";
            $dtFinal = addDias($dtInicial, $prazo);
        } else {
            $tipo = "Meses";
            $dtFinal = addMeses($dtInicial, $prazo);
        }
        $retorno = "{$dtInicial}<br/>{$prazo} {$tipo}<br/>$dtFinal";

        return $retorno;
    }

    ###########################################################
}
