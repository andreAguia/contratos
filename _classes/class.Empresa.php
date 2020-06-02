<?php

class Empresa {

    /**
     * Abriga as várias rotina referentes a concurso
     *
     * @author André Águia (Alat) - alataguia@gmail.com
     * 
     * @var private $idConcurso integer NULL O id do concurso
     */
    private $idEmpresa = null;
    private $permiteEditar = true;

##############################################################

    public function __construct($idEmpresa = NULL) {
        /**
         * Inicia a Classe somente
         * 
         * @param $idContrato integer NULL O id do concurso
         * 
         * @syntax $concurso = new Concurso([$idConcurso]);
         */
        $this->idEmpresa = $idEmpresa;
    }

##############################################################

    public function get_dados($idEmpresa = NULL) {

        /**
         * Informa os dados da base de dados
         * 
         * @param $idConcurso integer NULL O id do concurso
         * 
         * @syntax $concurso->get_dados([$idConcurso]);
         */
        # Joga o valor informado para a variável da classe
        if (!vazio($idEmpresa)) {
            $this->idEmpresa = $idEmpresa;
        }

        # Conecta ao Banco de Dados
        $contratos = new Contratos();

        # Verifica se foi informado
        if (vazio($this->idEmpresa)) {
            alert("É necessário informar o id da Empresa.");
            return;
        }

        # Pega os dados
        $select = 'SELECT * 
                     FROM tbempresa
                    WHERE idEmpresa = ' . $this->idEmpresa;

        $row = $contratos->select($select, FALSE);

        # Retorno
        return $row;
    }

    #####################################################################################

    public function get_razaoSocial($idEmpresa) {

        # Joga o valor informado para a variável da classe
        if (!vazio($idEmpresa)) {
            $this->idEmpresa = $idEmpresa;
        }

        # Conecta ao Banco de Dados
        $contratos = new Contratos();

        # Verifica se foi informado
        if (vazio($this->idEmpresa)) {
            alert("É necessário informar o id da Empresa.");
            return;
        }

        # Pega os dados
        $select = 'SELECT razaoSocial 
                     FROM tbempresa
                    WHERE idEmpresa = ' . $this->idEmpresa;

        $row = $contratos->select($select, FALSE);

        # Retorno
        return $row[0];
    }

}
