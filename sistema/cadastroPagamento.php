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
    $intra = new Intra();
    $contrato = new Contrato();
    $pessoal = new Pessoal();
    $comissao = new Comissao();

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
    $objeto->set_nome("Comissão de Fiscalização");

    # Botão de voltar da lista
    $objeto->set_voltarLista(null);

    # select da lista
    $objeto->set_selectLista("SELECT data,
                                     notaFiscal,
                                     referencia,
                                     valor,
                                     idPagamento
                                FROM tbpagamento
                               WHERE idContrato = {$idContrato}
                            ORDER BY data");

    # select do edita
    $objeto->set_selectEdita("SELECT data,
                                     notaFiscal,
                                     referencia,
                                     valor,
                                     obs,
                                     idContrato
                                FROM tbpagamento
                              WHERE idPagamento = {$id}");

    # Caminhos
    $objeto->set_linkEditar("?fase=editar");
    $objeto->set_linkExcluir("?fase=excluir");
    $objeto->set_linkGravar("?fase=gravar");
    $objeto->set_linkListar("?fase=listar");
    $objeto->set_linkIncluir("?fase=editar");

    # Parametros da tabela
    $objeto->set_label(array("Data", "Nota Fiscal", "Referência", "Valor"));
    $objeto->set_align(array("center", "left", "left", "left"));
    $objeto->set_width(array(10, 20, 20, 20));
    #$objeto->set_funcao(array(null, null, null, null, null, "exibeFoto"));
    #$objeto->set_classe(array("Comissao", "pessoal", "Comissao", "pessoal", "Comissao"));
    #$objeto->set_metodo(array("getTipo", "get_foto", "getDadosMembro", "get_contatos", "getDadosDesignacao"));
    $objeto->set_numeroOrdem(true);
    
    # Classe do banco de dados
    $objeto->set_classBd("Contratos");

    # Nome da tabela
    $objeto->set_tabela("tbpagamento");

    # Nome do campo id
    $objeto->set_idCampo("idPagamento");

    # Tipo de label do formulário
    $objeto->set_formlabelTipo(1);
    
    # Campos para o formulario
    $objeto->set_campos(array(
        array(
            'linha' => 1,
            'nome' => 'data',
            'label' => 'Data:',
            'tipo' => 'data',
            'title' => 'Data do pagamento',
            'col' => 3,
            'required' => true,
            'size' => 20),
        array(
            'linha' => 1,
            'nome' => 'notaFiscal',
            'label' => 'Nota Fiscal:',
            'tipo' => 'texto',
            'col' => 3,
            'size' => 30),
        array(
            'linha' => 1,
            'nome' => 'referencia',
            'label' => 'Refferência:',
            'tipo' => 'texto',
            'col' => 3,
            'size' => 30),
        array(
            'linha' => 1,
            'nome' => 'valor',
            'label' => 'Valor: (se houver)',
            'tipo' => 'moeda',
            'col' => 3,
            'size' => 15),
        array(
            'linha' => 2,
            'nome' => 'obs',
            'label' => 'Observação:',
            'tipo' => 'textarea',
            'size' => array(80, 5)),
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
        case "":
        case "listar" :
            $objeto->listar();
            break;

        case "editar":
        case "excluir":
            $objeto->$fase($id);
            break;

        case "gravar":
            $objeto->gravar($id);
            break;
        ################################################################    
    }

    $page->terminaPagina();
} else {
    loadPage("../../areaServidor/sistema/login.php");
}
