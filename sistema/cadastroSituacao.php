<?php

/**
 * Cadastro de Situação atual de um contrato
 *  
 * By Alat
 */
# Reservado para o servidor logado
$idUsuario = null;

# Configuração
include ("_config.php");

# Permissão de Acesso
$acesso = Verifica::acesso($idUsuario, [1, 9]);

if ($acesso) {
    # Conecta ao Banco de Dados
    $intra = new Intra();
    $contrato = new Contrato();
    $pessoal = new Pessoal();

    # Verifica a fase do programa
    $fase = get("fase", "listar");

    # pega o id (se tiver)
    $id = soNumeros(get("id"));

    # pega o contrato
    $idContrato = soNumeros(get_session('sessionContrato'));

    # Começa uma nova página
    $page = new Page();
    $page->iniciaPagina();

    # Cabeçalho da Página
    AreaServidor::cabecalho();

    # Abre um novo objeto Modelo
    $objeto = new Modelo();

    ################################################################
    # Exibe os dados do Contrato
    $objeto->set_rotinaExtra("get_DadosContrato");
    $objeto->set_rotinaExtraParametro($idContrato);

    # Nome do Modelo
    $objeto->set_nome("Situação");

    # Botão de voltar da lista
    $objeto->set_voltarLista("cadastroAditivo.php");

    # select da lista
    $objeto->set_selectLista("SELECT data,                                      
                                     situacao,
                                     obs,
                                     idSituacao
                                FROM tbsituacao
                               WHERE idContrato = {$idContrato}
                            ORDER BY data DESC");

    # select do edita
    $objeto->set_selectEdita("SELECT data,
                                     situacao,
                                     obs,
                                     idContrato
                                FROM tbsituacao
                              WHERE idSituacao = {$id}");

    # Caminhos
    $objeto->set_linkEditar("?fase=editar");
    $objeto->set_linkExcluir("?fase=excluir");
    $objeto->set_linkGravar("?fase=gravar");
    $objeto->set_linkListar("?fase=listar");

    # Parametros da tabela
    $objeto->set_label(array("Data", "Situacao do Contrato", "Obs"));
    $objeto->set_width(array(10, 40, 40));
    $objeto->set_align(array("center", "left", "left"));
    $objeto->set_funcao(array("date_to_php"));
    #$objeto->set_classe(array(null,"Situacao"));
    #$objeto->set_metodo(array(null,"get_situacao"));

    $objeto->set_formatacaoCondicional(array(array('coluna' => 0,
            'valor' => 0,
            'operador' => '=',
            'id' => 'logLogin')
    ));

    # Classe do banco de dados
    $objeto->set_classBd("Contratos");

    # Nome da tabela
    $objeto->set_tabela("tbsituacao");

    # Nome do campo id
    $objeto->set_idCampo("idSituacao");

    # Tipo de label do formulário
    $objeto->set_formlabelTipo(1);

    # Campos para o formulario
    $objeto->set_campos(array(
        array(
            'linha' => 1,
            'nome' => 'data',
            'label' => 'Data:',
            'tipo' => 'date',
            'required' => true,
            'col' => 3,
            'padrao' => date("Y-m-d"),
            'size' => 15),
        array(
            "linha" => 1,
            'col' => 9,
            "nome" => "situacao",
            "label" => "Situacao:",
            'required' => true,
            'autofocus' => true,
            "tipo" => "texto",
            "size" => 250),
        array(
            'linha' => 2,
            'nome' => 'obs',
            'label' => 'Observação:',
            'tipo' => 'textarea',
            'size' => array(80, 5),
        ),
        array(
            "linha" => 3,
            "nome" => "idContrato",
            "label" => "idContrato:",
            'tipo' => 'hidden',
            'padrao' => $idContrato,
            "col" => 3,
            "size" => 11),
    ));

    # idUsuário para o Log
    $objeto->set_idUsuario($idUsuario);

    ################################################################
    switch ($fase) {
        case "" :
        case "listar" :
            $objeto->listar();
            break;

        case "editar" :
        case "excluir" :
        case "gravar" :
            $objeto->$fase($id);
            break;
    }

    $page->terminaPagina();
} else {
    loadPage("../../areaServidor/sistema/login.php");
}