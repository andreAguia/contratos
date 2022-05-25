<?php

/**
 * Cadastro de Status
 *
 * By Alat
 */
# Reservado para o servidor logado
$idUsuario = null;

# Configuração
include "_config.php";

# Permissão de Acesso
$acesso = Verifica::acesso($idUsuario, [1, 9]);

if ($acesso) {
    # Conecta ao Banco de Dados
    $status = new Status();

    # log
    if (get('i', false)) {
        # Grava no log a atividade
        $atividade = "Acessou o cadastro de status.";
        $data = date("Y-m-d H:i:s");
        $intra->registraLog($idUsuario, $data, $atividade, null, null, 7);
    }

    # Verifica a fase do programa
    $fase = get("fase", "listar");

    # pega o id (se tiver)
    $id = soNumeros(get("id"));

    # Começa uma nova página
    $page = new Page();
    $page->iniciaPagina();

    # Cabeçalho da Página
    AreaServidor::cabecalho();

    # Abre um novo objeto Modelo
    $objeto = new Modelo();

    ################################################################
    # Nome do Modelo
    $objeto->set_nome("Status de Contrato");

    # Botão de voltar da lista
    $objeto->set_voltarLista("cadastroContrato.php");

    # select da lista
    $objeto->set_selectLista("SELECT idStatus,
                                      status,
                                      obs,
                                      idStatus
                                 FROM tbstatus
                             ORDER BY status");

    # select do edita
    $objeto->set_selectEdita("SELECT status,
                                     obs
                                FROM tbstatus
                              WHERE idStatus = {$id}");

    # Caminhos
    $objeto->set_linkEditar("?fase=editar");
    $objeto->set_linkExcluir("?fase=excluir");
    $objeto->set_linkGravar("?fase=gravar");
    $objeto->set_linkListar("?fase=listar");

    # Parametros da tabela
    $objeto->set_label(array("Id", "Satatus", "Obs", "Contratos"));
    $objeto->set_width(array(5, 40, 40, 5));
    $objeto->set_align(array("center", "left", "left", "center"));
    $objeto->set_classe(array(null, null, null, "Status"));
    $objeto->set_metodo(array(null, null, null, "get_numContratos"));

    # Classe do banco de dados
    $objeto->set_classBd("Contratos");

    # Nome da tabela
    $objeto->set_tabela("tbstatus");

    # Nome do campo id
    $objeto->set_idCampo("idStatus");

    # Tipo de label do formulário
    $objeto->set_formlabelTipo(1);

    # Campos para o formulario
    $objeto->set_campos(array(
        array("linha" => 1,
            "nome" => "status",
            "label" => "Status:",
            "tipo" => "texto",
            "required" => true,
            "autofocus" => true,
            "col" => 6,
            "size" => 100),
        array("linha" => 2,
            "nome" => "obs",
            "label" => "Observação:",
            "tipo" => "textarea",
            "size" => array(80, 5))));

    # idUsuário para o Log
    $objeto->set_idUsuario($idUsuario);

    ################################################################
    switch ($fase) {
        case "":
        case "listar":
            $objeto->listar();
            break;

        ################################################################

        case "excluir":
            # Verifica se tem contrato com esse status
            $numContratos = $status->get_numContratos($id);

            if ($numContratos > 0) {
                alert("Existem contratos cadastrados com este status. Dessa forma o mesmo NÃO poderá ser excluída.");
                back(1);
            } else {
                $objeto->excluir($id);
            }
            break;

        ################################################################

        case "editar":
        case "gravar":
            $objeto->$fase($id);
            break;
    }

    $page->terminaPagina();
} else {
    loadPage("../../areaServidor/sistema/login.php");
}
