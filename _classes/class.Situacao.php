<?php

class Situacao {

    /**
     * Abriga as várias rotina referentes a concurso
     *
     * @author André Águia (Alat) - alataguia@gmail.com
     * 
     * @var private $idConcurso integer null O id do concurso
     */
    private $idSituacao = null;

##############################################################

    public function __construct($idSituacao = null) {
        /**
         * Inicia a Classe somente
         * 
         * @param $idContrato integer null O id do concurso
         * 
         * @syntax $concurso = new Concurso([$idConcurso]);
         */
        $this->idSituacao = $idSituacao;
    }

##############################################################

    public function getDados($idSituacao = null) {

        /**
         * Informa os dados da base de dados
         * 
         * @param $idConcurso integer null O id do concurso
         * 
         * @syntax $concurso->get_dados([$idConcurso]);
         */
        # Joga o valor informado para a variável da classe
        if (!vazio($idSituacao)) {
            $this->idSituacao = $idSituacao;
        }

        # Conecta ao Banco de Dados
        $contratos = new Contratos();

        # Verifica se foi informado
        if (vazio($this->idSituacao)) {
            alert("É necessário informar o id da Situação.");
            return;
        }

        # Pega os dados
        $select = 'SELECT * 
                     FROM tbsituacao
                    WHERE idSituacao = ' . $this->idSituacao;

        $row = $contratos->select($select, false);

        # Retorno
        return $row;
    }

    #####################################################################################

    public function getSituacaoAtualEAlerta($idContrato) {

        # Conecta ao Banco de Dados
        $contratos = new Contratos();

        # Verifica se foi informado
        if (vazio($idContrato)) {
            alert("É necessário informar o id do Contrato.");
            return;
        }

        # Pega os dados
        $select = "SELECT situacao
                     FROM tbsituacao
                    WHERE idContrato = {$idContrato}
                 ORDER BY idSituacao desc";

        $row = $contratos->select($select, false);

        # Retorno
        if (!vazio($row)) {
            echo $row[0];
        }
        
        hr("geral1");

        # Exibe alertas (se tiver)
        $alerta = new AlertaContrato($idContrato);
    }

    #####################################################################################

    public function getSituacaoAtual($idContrato) {

        # Conecta ao Banco de Dados
        $contratos = new Contratos();

        # Verifica se foi informado
        if (vazio($idContrato)) {
            alert("É necessário informar o id do Contrato.");
            return;
        }

        # Pega os dados
        $select = "SELECT situacao
                     FROM tbsituacao
                    WHERE idContrato = {$idContrato}
                 ORDER BY idSituacao desc";

        $row = $contratos->select($select, false);

        # Retorno
        if (!vazio($row)) {
            echo $row[0];
        }
    }

    #####################################################################################

    function exibeSituacaoAtual($idContrato, $idUsuario) {

        # Conecta ao Banco de Dados
        $contratos = new Contratos();

        # Verifica se foi informado
        if (vazio($idContrato)) {
            alert("É necessário informar o id do Contrato.");
            return;
        }

        # Pega os dados
        $select = "SELECT *
                     FROM tbsituacao
                    WHERE idContrato = {$idContrato}
                 ORDER BY idSituacao desc";

        $situacao = $contratos->select($select, false);

        # Limita a tela
        $grid = new Grid();
        $grid->abreColuna(12);

        tituloTable("Situação Atual");
        
        $painel = new Callout("warning");
        $painel->abre();

        $grid = new Grid();
        $grid->abreColuna(6);

        $grid->fechaColuna();
        $grid->abreColuna(6);

        if (!empty($situacao)) {
            p(date_to_php($situacao['data']), "situacaoData");
        }

        $grid->fechaColuna();
        $grid->abreColuna(12);

        if (!empty($situacao)) {
            br();
            p($situacao['situacao'], "situacaoAtual", "left important");
            br();
        } else {
            br();
            p("Nenhuma situação cadastrada", "situacaoAtual", "left important");
            br();
        }

        $grid->fechaColuna();
        $grid->fechaGrid();

        # Editar
        if (Verifica::acesso($idUsuario, [1, 9])) {

            $div = new Div("divEdita1");
            $div->abre();

            $div = new Div("divEdita2");
            $div->abre();

            # Editar
            $botaoEditar = new Link("Editar", "cadastroSituacao.php");
            $botaoEditar->set_class('tiny button secondary');
            $botaoEditar->set_title('Editar situação');
            $botaoEditar->show();

            $div->fecha();

            $div->fecha();
        }
        $painel->fecha();

        $grid->fechaColuna();
        $grid->fechaGrid();
    }

    #####################################################################################

    public function getSituacao($idSituacao) {

        # Conecta ao Banco de Dados
        $contratos = new Contratos();

        # Verifica se foi informado
        if (vazio($idSituacao)) {
            alert("É necessário informar o id do Situacao.");
            return;
        }

        # Pega os dados
        $select = "SELECT situacao
                     FROM tbsituacao
                    WHERE idSituacao = {$idSituacao}";

        $row = $contratos->select($select, false);

        # Retorno
        if (vazio($row)) {
            return "---";
        } else {
            return "<pre>{$row[0]}</pre>";
        }
    }

    #####################################################################################
}
