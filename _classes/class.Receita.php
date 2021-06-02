<?php

class Receita {

    public function getDados($idReceita = null) {
        # Conecta ao Banco de Dados
        $contratos = new Contratos();

        # Verifica se foi informado
        if (vazio($idReceita)) {
            alert("É necessário informar o id da receita.");
            return;
        }

        # Pega os dados
        $select = 'SELECT *
                     FROM tbreceita
                    WHERE idReceita = ' . $idReceita;

        $row = $contratos->select($select, false, true);

        # Retorno
        return $row;
    }

#####################################################################################

    public function exibeReferencia($idReceita) {
        # Verifica se foi informado
        if (vazio($idReceita)) {
            alert("É necessário informar o id.");
            return;
        }

        # Conecta ao Banco de Dados
        $contratos = new Contratos();

        # Pega os dados
        $conteudo = $this->getDados($idReceita);

        # Verifica se foi informado a referência
        if (empty($conteudo["mesReferencia"]) OR empty($conteudo["anoReferencia"])) {
            return "---";
        } else {
            return get_nomeMes($conteudo["mesReferencia"]) . " / " . $conteudo["anoReferencia"];
        }
    }

    ############################################################
    
    public function exibeValor($idReceita = null) {

        # Verifica se foi informado o id
        if (vazio($idReceita)) {
            alert("É necessário informar o id do Pgto.");
            return;
        }

        # Pega os dados
        $conteudo = $this->getDados($idReceita);

        # Valor
        if (empty($conteudo["valor"])) {
            p("----", "p#pvalorNulo");
        } else {
            p(formataMoeda2($conteudo['valor']), "pvalorPositivo");
        }
    }

    ############################################################
    
    public function exibeValorEnergia($idReceita = null) {

        # Verifica se foi informado o id
        if (vazio($idReceita)) {
            alert("É necessário informar o id do Pgto.");
            return;
        }

        # Pega os dados
        $conteudo = $this->getDados($idReceita);

        # Valor
        if (empty($conteudo["energiaEletrica"])) {
            p("----", "p#pvalorNulo");
        } else {
            p(formataMoeda2($conteudo['energiaEletrica']), "pvalorPositivo");
        }
    }
    ###########################################################
}
