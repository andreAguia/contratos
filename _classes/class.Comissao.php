<?php

class Comissao {

    /**
     * Abriga as várias rotina referentes a comissao
     *
     * @author André Águia (Alat) - alataguia@gmail.com
     * 
     * @var private $idComissao integer NULL O id do concurso
     */
    private $idComissao = null;

##############################################################

    public function __construct($idComissao = NULL) {
        /**
         * Inicia a Classe somente
         * 
         * @param $idContrato integer NULL O id do concurso
         * 
         * @syntax $concurso = new Concurso([$idConcurso]);
         */
        $this->idComissao = $idComissao;
    }

##############################################################

    public function get_dados($idComissao = NULL) {

        /**
         * Informa os dados da base de dados
         * 
         * @param $idConcurso integer NULL O id do concurso
         * 
         * @syntax $concurso->get_dados([$idConcurso]);
         */
        # Joga o valor informado para a variável da classe
        if (!vazio($idComissao)) {
            $this->idComissao = $idComissao;
        }

        # Conecta ao Banco de Dados
        $contratos = new Contratos();

        # Verifica se foi informado
        if (vazio($this->idComissao)) {
            alert("É necessário informar o id.");
            return;
        }

        # Pega os dados
        $select = 'SELECT * 
                     FROM tbcomissao
                    WHERE idComissao = ' . $this->idComissao;

        $row = $contratos->select($select, FALSE);

        # Retorno
        return $row;
    }

    #####################################################################################

    public function get_portariaEntrada($idComissao) {

        # Joga o valor informado para a variável da classe
        if (!vazio($idComissao)) {
            $this->idComissao = $idComissao;
        }

        # Conecta ao Banco de Dados
        $contratos = new Contratos();

        # Verifica se foi informado
        if (vazio($this->idComissao)) {
            alert("É necessário informar o id.");
            return;
        }

        $return = null;

        # Pega os dados
        $select = "SELECT portariaEntrada,
                          dtPortariaEntrada,
                          dtPublicacaoEntrada
                     FROM tbcomissao
                    WHERE idComissao = {$idComissao}";

        $row = $contratos->select($select, FALSE);

        # Trata o retorno
        if (empty($row["portariaEntrada"])) {
            return "---";
        } else {
            $return = "Portaria n° {$row["portariaEntrada"]}";

            if (!empty($row["dtPortariaEntrada"])) {
                $return .= " de " . date_to_php($row["dtPortariaEntrada"]);
            }

            if (!empty($row["dtPublicacaoEntrada"])) {
                $return .= "<br/>publicada no DOERJ de " . date_to_php($row["dtPublicacaoEntrada"]);
            }
        }

        # Retorno
        return $return;
    }

    #####################################################################################

    public function get_tipo($tipo) {

        # Verifica se foi informado
        if (vazio($tipo)) {
            return "---";
        } else {
            switch ($tipo) {
                case 1 :
                    return "Presidente";
                    break;

                case 2 :
                    return "Membro";
                    break;

                case 3 :
                    return "Suplente";
                    break;
            }
        }
    }

    #####################################################################################

    public function listaComissao($idContrato) {

        # Conecta ao Banco de Dados
        $contratos = new Contratos();

        # monta o select
        $select = "SELECT tipo,
                          idServidor
                     FROM tbcomissao
                    WHERE idContrato = {$idContrato}
                 ORDER BY tipo";

        $row = $contratos->select($select);

        # Monta a tabela
        $tabela = new Tabela();
        $tabela->set_titulo("Comissão de Fiscalização");
        $tabela->set_label(array("Tipo", "Nome"));
        $tabela->set_align(array("center", "left"));
        $tabela->set_width(array(30, 70));
        $tabela->set_classe(array("Comissao", "pessoal"));
        $tabela->set_metodo(array("get_tipo", "get_nome"));
        $tabela->set_numeroOrdem(true);
        $tabela->set_conteudo($row);
        $tabela->show();
    }

    ###########################################################
}
