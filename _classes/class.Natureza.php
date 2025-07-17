<?php

class Natureza {

    /**
     * Abriga as várias rotina referentes ao cadastro de natureza da despesa
     *
     * @author André Águia (Alat) - alataguia@gmail.com
     * 
     */
##############################################################

    public function __construct() {
        
    }

##############################################################

    public function get_nomeNatureza($id = null) {


        # Verifica se foi informado
        if (empty($id)) {
            alert("É necessário informar o id da Natureza.");
            return;
        } else {
            # Conecta ao Banco de Dados
            $contratos = new Contratos();

            # Pega os dados
            $select = "SELECT natureza
                     FROM tbnatureza
                    WHERE idNatureza = {$id}";

            return $contratos->select($select, false)[0];
        }
    }

    ############################################################

    public function exibeNatureza($id = null) {

        # Verifica se foi informado
        if (empty($id)) {
            return null;
        } else {
            # Conecta ao Banco de Dados
            $contratos = new Contratos();

            # Pega os dados
            $select = "SELECT natureza, afetaSaldo
                         FROM tbnatureza
                        WHERE idNatureza = {$id}";
            
            $dados = $contratos->select($select, false);
            
            echo $dados[0];
            
            if($dados[1] == "n"){
                label("Não Afeta o Saldo");
            }
        }
    }

    ############################################################
}
