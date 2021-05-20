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
    $contratos = new Contratos();
    $contrato = new Contrato();
    $pessoal = new Pessoal();
    $comissao = new Comissao();

    # Verifica a fase do programa
    $fase = get("fase", "listar");

    # pega o id (se tiver)
    $id = soNumeros(get("id"));

    # pega o contrato
    $idContrato = soNumeros(get_session('sessionContrato'));

    # Grava no log a atividade
    $atividade = "Acessou a página do controle de saldo do contrato " . $contrato->getNumero($idContrato);
    $data = date("Y-m-d H:i:s");
    $intra->registraLog($idUsuario, $data, $atividade, null, null, 7);

    # Começa uma nova página
    $page = new Page();
    $page->iniciaPagina();

    # Cabeçalho da Página
    AreaServidor::cabecalho();

    # Abre um novo objeto Modelo
    $objeto = new Modelo();

    ################################################################
    # Exibe os dados do Contrato
    if ($fase == "listar") {
        $objeto->set_rotinaExtra("get_DadosContratoPagamento");
    } else {
        $objeto->set_rotinaExtra("get_DadosContrato");
    }
    $objeto->set_rotinaExtraParametro($idContrato);

    # Nome do Modelo
    $objeto->set_nome("Pagamentos");

    # Botão de voltar da lista
    $objeto->set_voltarLista("cadastroAditivo.php");

    # select da lista
    $objeto->set_selectLista("SELECT anoReferencia,
                                     idPagamento,
                                     data,
                                     notaFiscal,
                                     idPagamento,                                     
                                     tipo,
                                     natureza,
                                     tbpagamento.obs,
                                     idPagamento
                                FROM tbpagamento LEFT JOIN tbnatureza USING (idNatureza)
                               WHERE idContrato = {$idContrato}
                            ORDER BY anoReferencia desc, mesReferencia desc, data desc");

    # select do edita
    $objeto->set_selectEdita("SELECT data,
                                     notaFiscal,                                     
                                     mesReferencia,
                                     anoReferencia,
                                     valor,
                                     tipo,
                                     idNatureza,
                                     obs,
                                     idContrato
                                FROM tbpagamento
                              WHERE idPagamento = {$id}");

    # Caminhos
    $objeto->set_linkEditar("?fase=editar");
    $objeto->set_linkExcluir("?fase=excluir");
    $objeto->set_linkGravar("?fase=gravar");
    $objeto->set_linkListar("?fase=listar");

    $objeto->set_rowspan(0);
    $objeto->set_grupoCorColuna(0);

    # Parametros da tabela
    $objeto->set_label(array("Ano", "Referência", "Data", "Nota Fiscal", "Valor", "Tipo", "Natureza", "Obs"));
    $objeto->set_align(array("center", "center", "center", "center", "right", "center", "center", "left"));
    $objeto->set_width(array(5, 13, 10, 10, 12, 5, 5, 30));
    $objeto->set_funcao(array(null, null, "date_to_php"));
    $objeto->set_classe(array(null, "Pagamento", null, null, "Pagamento", "Pagamento"));
    $objeto->set_metodo(array(null, "exibeReferencia", null, null, "exibeValor", "exibeTipo"));
    $objeto->set_numeroOrdem(true);
    $objeto->set_numeroOrdemTipo('d');

    # Classe do banco de dados
    $objeto->set_classBd("Contratos");

    # Nome da tabela
    $objeto->set_tabela("tbpagamento");

    # Nome do campo id
    $objeto->set_idCampo("idPagamento");

    # Tipo de label do formulário
    $objeto->set_formlabelTipo(1);

    # Dados da combo natureza
    $natureza = $contratos->select('SELECT idNatureza,
                                         natureza
                                    FROM tbnatureza
                                ORDER BY natureza');

    array_unshift($natureza, array(null, null));

    # Cria um array com os anos possíveis para a combo anoReferencia
    $anoInicial = 2010;
    $anoAtual = date('Y');
    $anoReferencia = arrayPreenche($anoInicial, $anoAtual + 1, "d");

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
            "autofocus" => true,
            'size' => 20),
        array(
            'linha' => 1,
            'nome' => 'notaFiscal',
            'label' => 'Nota Fiscal:',
            'tipo' => 'texto',
            'required' => true,
            'col' => 3,
            'size' => 30),
        array(
            'linha' => 1,
            'nome' => 'mesReferencia',
            'label' => 'Mês Referência:',
            'tipo' => 'combo',
            'padrao' => date('m'),
            'required' => true,
            'array' => $mes,
            'col' => 3,
            'size' => 5),
        array(
            'linha' => 1,
            'nome' => 'anoReferencia',
            'label' => 'Ano Referência:',
            'required' => true,
            'tipo' => 'combo',
            'padrao' => date('Y'),
            'array' => $anoReferencia,
            'col' => 3,
            'size' => 5),
        array(
            'linha' => 2,
            'nome' => 'valor',
            'label' => 'Valor:',
            'tipo' => 'moeda',
            'required' => true,
            'col' => 3,
            'size' => 15),
        array(
            'linha' => 2,
            'nome' => 'tipo',
            'label' => 'Tipo',
            'tipo' => 'combo',
            'required' => true,
            'padrao' => 1,
            'array' => [[1, "Pagamento"], [2, "Estorno"], [3, "Saldo Residual Anulado"]],
            'col' => 3,
            'size' => 3),
        array(
            'linha' => 2,
            'nome' => 'idNatureza',
            'label' => 'Natureza:',
            'tipo' => 'combo',
            'array' => $natureza,
            'col' => 3,
            'size' => 100),
        array(
            'linha' => 4,
            'nome' => 'obs',
            'label' => 'Observação:',
            'tipo' => 'textarea',
            'size' => array(80, 5)),
        array(
            "linha" => 5,
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
            $objeto->gravar($id, 'cadastroPagamentoExtra.php');
            break;

        case "porNatureza":
            # Limita o tamanho da tela
            $grid1 = new Grid();
            $grid1->abreColuna(12);

            # Cria um menu
            $menu1 = new MenuBar();

            # Voltar
            $linkVoltar = new Link("Voltar", "?");
            $linkVoltar->set_class('button');
            $linkVoltar->set_title('Voltar para página anterior');
            $linkVoltar->set_accessKey('V');
            $menu1->add_link($linkVoltar, "left");

            $menu1->show();

            tituloTable("Valor Liquidado Agrupado pela Natureza da Despesa");
            br();

            $grid1->fechaColuna();
            $grid1->fechaGrid();

            $grid1 = new Grid("center");
            $grid1->abreColuna(6);

            $pagamento = new Pagamento();
            $pagamento->exibeValorLiquidadoPorNatureza($id);

            $grid1->fechaColuna();
            $grid1->fechaGrid();
            break;
        ################################################################    
    }

    $page->terminaPagina();
} else {
    loadPage("../../areaServidor/sistema/login.php");
}
