<?php

class Tarefa
{

##############################################################
    public function __construct()
    {
        #
    }

##############################################################
    public function get_dados($idTarefa = null)
    {
        # Conecta ao Banco de Dados
        $contratos = new Contratos();

        # Verifica se foi informado
        if (vazio($this->idTarefa)) {
            alert("É necessário informar o id da Tarefa");
            return;
        }

        # Pega os dados
        $select = "SELECT *
                     FROM tbtarefa
                    WHERE idtarefa = {$idTarefa}";

        $row = $contratos->select($select, false);

        # Retorno
        return $row;
    }

    #####################################################################################
    public function get_tarefa($idTarefa)
    {

        # Conecta ao Banco de Dados
        $contratos = new Contratos();
        $contrato = new Contrato();

        # Verifica se foi informado
        if (vazio($idTarefa)) {
            alert("É necessário informar o id da tarefa.");
            return;
        }

        # Pega os dados
        $select = "SELECT data, idContrato, tarefa
                     FROM tbtarefa
                    WHERE idTarefa = {$idTarefa}";

        $row = $contratos->select($select, false);

        $numContrato = $contrato->get_numero($row[1]);

        $link = new Link($row[2], "#");
        $link->show();
        br();

        # Data
        span(date_to_php($row[0]), "projeto", null, "Data da Tarefa");

        # Contrato

        echo "&nbsp&nbsp&nbsp";
        span($numContrato, "etiqueta", null, "Contrato");
    }

    #####################################################################################
    public function listaTarefas($idContrato = null)
    {
        # Conecta ao banco
        $contratos = new Contratos();

        # Tarefas atrasadas
        $select = "SELECT idTarefa
                     FROM tbtarefa JOIN tbcontrato USING(idContrato)
                    WHERE data IS NOT NULL
                      AND (idStatus = 1 OR idStatus = 3)
                      AND data < CURDATE()";

        if (!empty($idContrato)) {
            $select .= " AND tbtarefa.idContrato = {$idContrato}";
        }

        $select .= " ORDER BY data";

        $row = $contratos->select($select);
        $count = $contratos->count($select);

        if ($count > 0) {

            # Monta a tabela
            $tabela = new Tabela("tableTarefas");
            $tabela->set_titulo("Atrasadas");
            $tabela->set_label(array(""));
            $tabela->set_align(array("left"));
            $tabela->set_width(array(100));
            $tabela->set_classe(array("Tarefa"));
            $tabela->set_metodo(array("get_tarefa"));
            $tabela->set_conteudo($row);
            $tabela->set_totalRegistro(false);
            $tabela->show();
            br();
        }

        # Tarefas de hoje
        $select = "SELECT idTarefa
                     FROM tbtarefa JOIN tbcontrato USING(idContrato)
                    WHERE data IS NOT NULL
                      AND (idStatus = 1 OR idStatus = 3)
                      AND data = CURDATE()";

        if (!empty($idContrato)) {
            $select .= " AND tbtarefa.idContrato = {$idContrato}";
        }

        $select .= " ORDER BY data";

        $row = $contratos->select($select);
        $count = $contratos->count($select);

        if ($count > 0) {

            # Monta a tabela
            $tabela = new Tabela("tableTarefas");
            $tabela->set_titulo("Hoje");
            $tabela->set_label(array(""));
            $tabela->set_align(array("left"));
            $tabela->set_width(array(100));
            $tabela->set_classe(array("Tarefa"));
            $tabela->set_metodo(array("get_tarefa"));
            $tabela->set_conteudo($row);
            $tabela->set_totalRegistro(false);
            $tabela->show();
            br();
        }

        # tarefas próximas
        $select = "SELECT idTarefa
                        FROM tbtarefa JOIN tbcontrato USING(idContrato)
                    WHERE data IS NOT NULL
                        AND (idStatus = 1 OR idStatus = 3)
                        AND data > CURDATE()";

        if (!empty($idContrato)) {
            $select .= " AND tbtarefa.idContrato = {$idContrato}";
        }

        $select .= " ORDER BY data";

        $row = $contratos->select($select);
        $count = $contratos->count($select);

        if ($count > 0) {

            # Monta a tabela
            $tabela = new Tabela("tableTarefas");
            $tabela->set_titulo("Próximos Dias");
            $tabela->set_label(array(""));
            $tabela->set_align(array("left"));
            $tabela->set_width(array(100));
            $tabela->set_classe(array("Tarefa"));
            $tabela->set_metodo(array("get_tarefa"));
            $tabela->set_conteudo($row);
            $tabela->set_totalRegistro(false);
            $tabela->show();
        }

    }

#####################################################################################

}
