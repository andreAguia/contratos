<?php

class Modalidade
{

    /**
     * Abriga as várias rotina referentes a concurso
     *
     * @author André Águia (Alat) - alataguia@gmail.com
     *
     * @var private $idConcurso integer null O id do concurso
     */
##############################################################

    public function __construct()
    {
        ##somente para constar
    }

##############################################################

    public function getDados($idModalidade = null)
    {
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

##############################################################

    public function getNumContratos($idModalidade = null)
    {

        # Conecta ao Banco de Dados
        $contratos = new Contratos();

        # Verifica se foi informado
        if (vazio($idModalidade)) {
            alert("É necessário informar o id da Modalidade.");
            return;
        }

        # Pega os dados
        $select = "SELECT idContrato
                     FROM tbcontrato
                    WHERE idModalidade = {$idModalidade}";

        $numero = $contratos->count($select, false);

        # Retorno
        return $numero;
    }

##############################################################

    public function getModalidade($idModalidade = null)
    {

        # Conecta ao Banco de Dados
        $contratos = new Contratos();

        # Verifica se foi informado
        if (vazio($idModalidade)) {
            alert("É necessário informar o id da Modalidade.");
            return;
        }

        # Pega os dados
        $select = "SELECT modalidade
                     FROM tbmodalidade
                    WHERE idModalidade = {$idModalidade}";

        $row = $contratos->select($select, false);

        # Retorno        
        return $row["modalidade"];
    }

}
