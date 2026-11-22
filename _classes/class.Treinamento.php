<?php

class TReinamento {

    /**
     * Abriga as várias rotina referentes ao cadastro de treinamento de membros de comissão
     *
     * @author André Águia (Alat) - alataguia@gmail.com
     * 
     */
##############################################################

    public function __construct() {
        
    }

##############################################################

    public function get_numAlunos($id = null) {

        /*
         * Informa o número de alunos cadastrados nesse treinamento
         */

        # Verifica se foi informado
        if (empty($id)) {
            return null;
        } else {
            # Conecta ao Banco de Dados
            $contratos = new Contratos();

            # Pega os dados
            $select = "SELECT idTreinamentoServidor
                         FROM tbtreinamentoservidor
                        WHERE idTreinamento = {$id}";

            return $contratos->count($select);
        }
    }

    ############################################################

    public function listaTreinamentos($id = null) {

        # Variável de retorno
        $retorno = null;

        # Verifica se foi informado
        if (empty($id)) {
            return "---";
        } else {
            # Conecta ao Banco de Dados
            $contratos = new Contratos();

            # Pega os dados
            $select = "SELECT data, descricao
                         FROM tbtreinamento JOIN tbtreinamentoservidor USING (idTReinamento)
                        WHERE idServidor = {$id}";

            $dados = $contratos->select($select);

            foreach ($dados as $item) {
                $retorno .= p2(date_to_php($item[0]) . " - {$item[1]}", "p#p10left");
            }

            return $retorno;
        }
    }

    ############################################################
}
