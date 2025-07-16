<?php

class Pncp {

    /**
     * Abriga as várias rotina referentes ao cadastro do Pncp
     *
     * @author André Águia (Alat) - alataguia@gmail.com
     */
    
    ##############################################################

    public function transformaIdToLink($id = null) {

        /**
         * Transforma um id para o link
         *
         * @param $id integer null O id 
         *
         * @syntax $pncp->transformaIdToLink([$id]);
         */
        # valida o id
        if (empty($id)) {
            return null;
        }

        # Divide pelo -
        $parte = explode("-", $id);
        
        # Separa o ano
        $item = explode("/",$parte[2]);
        
        
        # Define o link
        return "https://pncp.gov.br/app/editais/{$parte[0]}/{$item[1]}/{$item[0]}";        
    }

    ##############################################################
}
