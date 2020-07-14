<?php

/**
 * Cadastro de Campus
 *
 * By Alat
 */
# Reservado para o servidor logado
$idUsuario = null;

# Configuração
include "_config.php";

# Permissão de Acesso
$acesso = Verifica::acesso($idUsuario, 9);

if ($acesso) {
    # Conecta ao Banco de Dados
    $modalidade = new Modalidade();

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
    $objeto->set_nome("Modalidades de Contrato");

    # Botão de voltar da lista
    $objeto->set_voltarLista("cadastroContrato.php");

    # select da lista
    $objeto->set_selectLista("SELECT idModalidade,
                                      modalidade,
                                      obs,
                                      idModalidade,
                                      idModalidade
                                 FROM tbmodalidade
                             ORDER BY modalidade");

    # select do edita
    $objeto->set_selectEdita("SELECT modalidade,
                                     obs
                                FROM tbmodalidade
                              WHERE idModalidade = {$id}");

    # Caminhos
    $objeto->set_linkEditar("?fase=editar");
    $objeto->set_linkExcluir("?fase=excluir");
    $objeto->set_linkGravar("?fase=gravar");
    $objeto->set_linkListar("?fase=listar");

    # Parametros da tabela
    $objeto->set_label(array("Id", "Campus", "Obs", "Contratos"));
    $objeto->set_width(array(5, 40, 40, 5));
    $objeto->set_align(array("center", "left", "left", "center"));
    $objeto->set_classe(array(null, null, null, "Modalidade"));
    $objeto->set_metodo(array(null, null, null, "getNumContratos"));

    # Classe do banco de dados
    $objeto->set_classBd("Contratos");

    # Nome da tabela
    $objeto->set_tabela("tbmodalidade");

    # Nome do campo id
    $objeto->set_idCampo("idModalidade");

    # Tipo de label do formulário
    $objeto->set_formlabelTipo(1);

    # Campos para o formulario
    $objeto->set_campos(array(
        array("linha" => 1,
            "nome" => "modalidade",
            "label" => "Modalidade:",
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

    case "excluir" :
        # Verifica se tem contrato com essa modalidade
        $numContratos = $modalidade->getNumContratos($id);

        if ($numContratos > 0) {
            alert("Existem contratos cadastrados com esta modalidade. Dessa forma a mesma NÃO poderá ser excluída.");
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
