<?php

class Status
{

    /**
     * Abriga as várias rotina referentes a concurso
     *
     * @author André Águia (Alat) - alataguia@gmail.com
     *
     * @var private $idConcurso integer null O id do concurso
     */
    private $idStatus = null;

##############################################################

    public function __construct($idStatus = null)
    {
        /**
         * Inicia a Classe somente
         *
         * @param $idContrato integer null O id do concurso
         *
         * @syntax $concurso = new Concurso([$idConcurso]);
         */
        $this->idStatus = $idStatus;
    }

##############################################################

    public function get_dados($idStatus = null)
    {

        /**
         * Informa os dados da base de dados
         *
         * @param $idConcurso integer null O id do concurso
         *
         * @syntax $concurso->get_dados([$idConcurso]);
         */
        # Joga o valor informado para a variável da classe
        if (!vazio($idStatus)) {
            $this->idStatus = $idStatus;
        }

        # Conecta ao Banco de Dados
        $contratos = new Contratos();

        # Verifica se foi informado
        if (vazio($this->idStatus)) {
            alert("É necessário informar o id do Status.");
            return;
        }

        # Pega os dados
        $select = 'SELECT *
                     FROM tbstatus
                    WHERE idStatus = ' . $this->idStatus;

        $row = $contratos->select($select, false);

        # Retorno
        return $row;
    }

##############################################################

    public function get_numContratos($idStatus = null)
    {
        # Joga o valor informado para a variável da classe
        if (!vazio($idStatus)) {
            $this->idStatus = $idStatus;
        }

        # Conecta ao Banco de Dados
        $contratos = new Contratos();

        # Verifica se foi informado
        if (vazio($this->idStatus)) {
            alert("É necessário informar o id do Status.");
            return;
        }

        # Pega os dados
        $select = 'SELECT idStatus
                 FROM tbcontrato
                WHERE idStatus = ' . $this->idStatus;

        $numero = $contratos->count($select, false);

        # Retorno
        return $numero;
    }
}
