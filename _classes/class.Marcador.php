<?php

class Marcador {

    public function get_dados($idMarcador = null) {
        # Conecta ao Banco de Dados
        $contratos = new Contratos();

        # Verifica se foi informado o id
        if (empty($idMarcador)) {
            alert("É necessário informar o id do Marcador.");
            return;
        }

        # Pega os dados
        $select = "SELECT *
                     FROM tbmarcador
                    WHERE idMarcador = {$idMarcador}";

        $row = $contratos->select($select, false);

        # Retorno
        return $row;
    }

##############################################################

    public function get_numContratos($idMarcador = null) {

        # Conecta ao Banco de Dados
        $contratos = new Contratos();

        # Verifica se foi informado o id
        if (empty($idMarcador)) {
            alert("É necessário informar o id do Marcador.");
            return;
        }

        # Pega os dados
        $select = "SELECT idContrato
                     FROM tbcontrato
                    WHERE marcador1 = {$idMarcador}
                       OR marcador2 = {$idMarcador}
                       OR marcador3 = {$idMarcador}
                       OR marcador4 = {$idMarcador}
                       OR marcador5 = {$idMarcador}
                       OR marcador6 = {$idMarcador}";

        $numero = $contratos->count($select, false);

        # Retorno
        return $numero;
    }

##############################################################

    public function exibe_marcador($idMarcador = null) {
        # Verifica se foi informado o id
        if (empty($idMarcador)) {
            return null;
        } else {
            # Pega os dados do Marcador
            $dados = $this->get_dados($idMarcador);

            etiqueta($dados["marcador"], $dados["obs"], $dados["cor"]);
        }
    }

##############################################################

    /*
     * Exibe os marcadorees de um contrato
     */

    public function exibe_marcadoresContratoHorizontal($idContrato = null, $idUsuario = null) {

        # Verifica se foi informado o id
        if (empty($idContrato)) {
            return null;
        } else {

            # Limita o tamanho da tela
            $grid = new Grid();

            # Pega os dados do Contrato
            $contrato = new Contrato();
            $dados = array_filter($contrato->getDados($idContrato));
            $numValores = count($dados);

            $tamColuna = 12 / $numValores;

            for ($i = 1; $i <= $numValores; $i++) {
                if (!empty($dados["marcador{$i}"])) {
                    $grid->abreColuna($tamColuna);
                    $this->exibe_marcador($dados["marcador{$i}"]);
                    $grid->fechaColuna();
                }
            }


            $grid->fechaGrid();
        }
    }

    ##############################################################

    /*
     * Exibe os marcadorees de um contrato
     */

    public function exibe_marcadores($idContrato = null) {

        # Verifica se foi informado o id
        if (empty($idContrato)) {
            return null;
        } else {

            # Pega os dados do Contrato
            $contrato = new Contrato();
            $dados = array_filter($contrato->getDados($idContrato));
            $numValores = count($dados);

            $tamColuna = 12 / $numValores;

            for ($i = 1; $i <= $numValores; $i++) {
                if (!empty($dados["marcador{$i}"])) {
                    $this->exibe_marcador($dados["marcador{$i}"]);
                }
            }
        }
    }
    
    ##############################################################
}
    