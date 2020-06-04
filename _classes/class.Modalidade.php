<?php

class Modalidade {

    /**
     * Abriga as várias rotina referentes a concurso
     *
     * @author André Águia (Alat) - alataguia@gmail.com
     * 
     * @var private $idConcurso integer null O id do concurso
     */
    private $idModalidade = null;

##############################################################

    public function __construct($idModalidade = null) {
        /**
         * Inicia a Classe somente
         * 
         * @param $idContrato integer null O id do concurso
         * 
         * @syntax $concurso = new Concurso([$idConcurso]);
         */
        $this->idModalidade = $idModalidade;
    }

##############################################################

    public function get_dados($idModalidade = null) {

        /**
         * Informa os dados da base de dados
         * 
         * @param $idConcurso integer null O id do concurso
         * 
         * @syntax $concurso->get_dados([$idConcurso]);
         */
        # Joga o valor informado para a variável da classe
        if (!vazio($idModalidade)) {
            $this->idModalidade = $idModalidade;
        }

        # Conecta ao Banco de Dados
        $contratos = new Contratos();

        # Verifica se foi informado
        if (vazio($this->idModalidade)) {
            alert("É necessário informar o id da Modalidade.");
            return;
        }

        # Pega os dados
        $select = 'SELECT * 
                     FROM tbmodalidade
                    WHERE idModalidade = ' . $this->idModalidade;

        $row = $contratos->select($select, false);

        # Retorno
        return $row;
    }

}
