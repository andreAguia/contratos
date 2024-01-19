<?php

class MembroExterno {

    /**
     * Abriga as várias rotina referentes ao cadastro de membro externo
     *
     * @author André Águia (Alat) - alataguia@gmail.com
     *
     * @var private $idConcurso integer null O id do concurso
     */
    private $idStatus = null;

    ##############################################################

    public function get_dados($id = null) {

        /**
         * Informa os dados da base de dados
         *
         * @param $idConcurso integer null O id do concurso
         *
         * @syntax $concurso->get_dados([$idConcurso]);
         */
        # valida o id
        if (empty($id)) {
            return null;
        }

        # Conecta ao Banco de Dados
        $contratos = new Contratos();

        # Pega os dados
        $select = "SELECT *
                     FROM tbmembroexterno
                    WHERE idMembroExterno = {$id}";

        return $contratos->select($select, false);
    }

    ##############################################################

    public function get_nome($id = null) {

        /**
         * Informa o nome do membro externo
         */
        # valida o id
        if (empty($id)) {
            return null;
        } else {

            # Pega os dados
            $dados = $this->get_dados($id);
            return $dados['nome'];
        }
    }

    ##############################################################
}
