<?php

/**
 * Cadastro de Campus
 *
 * By Alat
 */

/*
 * 
 * ainda não está sendo usado !!!!!!!
 * 
 * 
 * 
 * 
 * 
 * 
 */
# Reservado para o servidor logado
$idUsuario = null;

# Configuração
include "_config.php";

# Permissão de Acesso
$acesso = Verifica::acesso($idUsuario, 9);

if ($acesso) {
    # Conecta ao Banco de Dados
    $contratos = new Contratos();

    # Verifica a fase do programa
    $fase = get("fase", "listar");

    # pega o id (se tiver)
    $id = soNumeros(get("id"));
    $sessionContrato = get_session('sessionContrato');

    # Pega o parametro de pesquisa (se tiver)
    if (is_null(post('parametro'))) { # Se o parametro n?o vier por post (for nulo)
        $parametro = retiraAspas(get_session('sessionParametro')); # passa o parametro da session para a variavel parametro retirando as aspas
    } else {
        $parametro = post('parametro'); # Se vier por post, retira as aspas e passa para a variavel parametro
        set_session('sessionParametro', $parametro); # transfere para a session para poder recuperá-lo depois
    }

    # Começa uma nova página
    $page = new Page();
    $page->iniciaPagina();

    # Cabeçalho da Página
    AreaServidor::cabecalho();

    # Abre um novo objeto Modelo
    $objeto = new Modelo();

    ################################################################
    # Nome do Modelo
    $objeto->set_nome("Modelos de Checklist");

    # Botão de voltar da lista
    $objeto->set_voltarLista("cadastroContrato.php");

    # controle de pesquisa
    $objeto->set_parametroLabel('Pesquisar');
    $objeto->set_parametroValue($parametro);

    # select da lista
    $objeto->set_selectLista("SELECT numOrdem,
                                     tipo,
                                     tarefa,
                                     obs,
                                     idchecklistModelo
                                FROM tbchecklistmodelo
                            ORDER BY numOrdem");

    # select do edita
    $objeto->set_selectEdita("SELECT tipo,
                                     tarefa,
                                     obs
                                FROM tbchecklistmodelo
                              WHERE idchecklistModelo = {$id}");

    # Caminhos
    $objeto->set_linkEditar("?fase=editar");
    $objeto->set_linkExcluir("?fase=excluir");
    $objeto->set_linkGravar("?fase=gravar");
    $objeto->set_linkListar("?fase=listar");

    # Parametros da tabela
    $objeto->set_label(array("#", "Tipo", "Tarefa", "Obs"));
    $objeto->set_width(array(10, 20, 40, 20));
    $objeto->set_align(array("center", "left", "left", "left"));
//    $objeto->set_classe(array("Empresa", "Empresa", "Empresa", "Empresa", "Empresa"));
//    $objeto->set_metodo(array("getEmpresaCnpj", "getTelefones", "getEmails", "getContatos", "getNumContratos"));
    # Classe do banco de dados
    $objeto->set_classBd("Contratos");

    # Nome da tabela
    $objeto->set_tabela("tbchecklistmodelo");

    # Nome do campo id
    $objeto->set_idCampo("idchecklistModelo");

    # Tipo de label do formulário
    $objeto->set_formlabelTipo(1);

    # Pega os dados da combo de cidade
    $tipos = $contratos->select('SELECT DISTINCT tipo, tipo
                                  FROM tbchecklistmodelo 
                              ORDER BY tipo');
    array_unshift($tipos, array(null, null));

    # Campos para o formulario
    $objeto->set_campos(array(
        array('linha'    => 1,
            'nome'     => 'tipo',
            'label'    => 'Tipo:',
            'tipo'     => 'texto',
            'required' => true,
            'datalist'    => $tipos,
            'col'      => 3,
            'size'     => 30),
        array('linha' => 1,
            'nome'  => 'tarefa',
            'label' => 'Tarefa:',
            'tipo'  => 'texto',
            'title' => 'tarefa',
            'col'   => 9,
            'size'  => 250),
        array('linha' => 2,
            'nome'  => 'obs',
            'label' => 'Observação:',
            'tipo'  => 'textarea',
            'size'  => array(80, 5))));

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
            $objeto->excluir($id);
            break;

        ################################################################

        case "editar":
            $objeto->editar($id);
            break;

        case "gravar":
            $objeto->gravar($id);
            break;
    }

    $page->terminaPagina();
} else {
    loadPage("../../areaServidor/sistema/login.php");
}
