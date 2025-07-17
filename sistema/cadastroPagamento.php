<?php

/**
 * Cadastro de Pagamento
 *
 * By Alat
 */
# Reservado para o servidor logado
$idUsuario = null;

# Configuração
include "_config.php";

# Permissão de Acesso
$acesso = Verifica::acesso($idUsuario, [1, 9, 10]);

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

    # Pega os parâmetros
    $parametroAnoPgto = post('parametroAnoPgto', get_session('parametroAnoPgto'));
    $parametroNaturezaPgto = post('parametroNaturezaPgto', get_session('parametroNaturezaPgto'));

    # Joga os parâmetros par as sessions
    set_session('parametroAnoPgto', $parametroAnoPgto);
    set_session('parametroNaturezaPgto', $parametroNaturezaPgto);

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
    # Rotina que exibe o formul'ario de Pesquisa extra
    # Limita o tamanho da tela

    function exibeFormPesqPgto($array) {

        $idContrato = $array[0];
        $parametroAno = $array[1];
        $parametroNatureza = $array[2];

        $grid = new Grid();
        $grid->abreColuna(12);

        # Formulário de Pesquisa
        $form = new Form('?');

        $contratos = new Contratos();
        $contrato = new Contrato();

        /*
         * Ano do Contrato
         */

        # Pega os dados
        $comboAno = $contratos->select("SELECT DISTINCT YEAR(data), YEAR(data)
                                                  FROM tbpagamento
                                                 WHERE idContrato = {$idContrato}
                                              ORDER BY YEAR(data) DESC");

        array_unshift($comboAno, array(null, "Todos"));

        # Ano
        $controle = new Input('parametroAnoPgto', 'combo', 'Ano:', 1);
        $controle->set_size(20);
        $controle->set_title('Ano da assinatura do contrato');
        $controle->set_valor($parametroAno);
        $controle->set_onChange('formPadrao.submit();');
        $controle->set_linha(1);
        $controle->set_col(3);
        $controle->set_array($comboAno);
        $form->add_item($controle);
        /*
         * Natureza
         */

        # Pega os dados
        $comboNatureza = $contratos->select("SELECT DISTINCT idNatureza,
                                                    natureza
                                          FROM tbpagamento LEFT JOIN tbnatureza USING (idNatureza)
                                         WHERE idContrato = {$idContrato} 
                                      ORDER BY natureza");
        array_unshift($comboNatureza, array(null, "Todos"));

        # Natureza
        $controle = new Input('parametroNaturezaPgto', 'combo', 'Natureza da Despesa:', 1);
        $controle->set_size(20);
        $controle->set_title('Natureza da Despesa');
        $controle->set_valor($parametroNatureza);
        $controle->set_onChange('formPadrao.submit();');
        $controle->set_linha(1);
        $controle->set_col(3);
        $controle->set_array($comboNatureza);
        $form->add_item($controle);

        $form->show();

        $grid->fechaColuna();
        $grid->fechaGrid();
    }

    ################################################################
    # Exibe os dados do Contrato
    if ($fase == "listar") {
        # Exibe os dados do Contrato e dos pagamentos
        $objeto->set_rotinaExtra(["get_DadosContrato", "get_DadosContratoPagamento", "exibeFormPesqPgto"]);
        $objeto->set_rotinaExtraParametro([$idContrato, $idContrato, [$idContrato, $parametroAnoPgto, $parametroNaturezaPgto]]);
    } else {
        # Exibe somente os dados do contrato
        $objeto->set_rotinaExtra("get_DadosContrato");
        $objeto->set_rotinaExtraParametro($idContrato);
    }

    # Nome do Modelo
    $objeto->set_nome("Pagamentos");

    # Botão de voltar da lista
    $objeto->set_voltarLista("cadastroAditivo.php");

    # select da lista    
    $select = "SELECT anoReferencia,
                    idPagamento,
                    data,
                    notaFiscal,
                    idPagamento,                                     
                    tipo,
                    idNatureza,
                    tbpagamento.obs,
                    idPagamento
               FROM tbpagamento LEFT JOIN tbnatureza USING (idNatureza)
              WHERE idContrato = {$idContrato}";

    if (!empty($parametroAnoPgto)) {
        $select .= " AND anoReferencia = {$parametroAnoPgto}";
    }

    if (!empty($parametroNaturezaPgto)) {
        $select .= " AND idNatureza = {$parametroNaturezaPgto}";
    }

    $select .= " ORDER BY anoReferencia desc, mesReferencia desc, data desc";

    $objeto->set_selectLista($select);

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

    # Habilita o modo leitura para determinados usuários
    if (Verifica::acesso($idUsuario, 10)) {
        $objeto->set_modoLeitura(true);
    }

    # Caminhos                                
    $objeto->set_linkEditar("?fase=editar");
    $objeto->set_linkExcluir("?fase=excluir");
    $objeto->set_linkGravar("?fase=gravar");
    $objeto->set_linkListar("?fase=listar");

    $objeto->set_rowspan(0);
    $objeto->set_grupoCorColuna(0);

    # Parametros da tabela
    $objeto->set_label(["Ano", "Referência", "Data", "Nota Fiscal", "Valor", "Tipo", "Natureza", "Obs"]);
    $objeto->set_align(["center", "center", "center", "center", "right", "center", "center", "left"]);
    $objeto->set_width([5, 13, 10, 10, 12, 8, 8, 20]);
    $objeto->set_funcao([null, null, "date_to_php"]);
    $objeto->set_classe([null, "Pagamento", null, null, "Pagamento", "Pagamento", "Natureza"]);
    $objeto->set_metodo([null, "exibeReferencia", null, null, "exibeValor", "exibeTipo", "exibeNatureza"]);
    $objeto->set_numeroOrdem(true);
    $objeto->set_numeroOrdemTipo('d');

    # Classe do banco de dados
    $objeto->set_classBd("Contratos");

    # Nome da tabela
    $objeto->set_tabela("tbpagamento");

    # Nome do campo id
    $objeto->set_idCampo("idPagamento");

    # Tipo de label do formulário
    $objeto->set_formLabelTipo(1);

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
            'required' => true,
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

    # Edita Obs    
    $botaoObs = new Button("Obs Geral", "cadastroObsSaldo.php");
    $botaoObs->set_title("Insere / edita as observações gerais.");

    # Cronograma Físico Financeiro
    $botaoCronograma = new Button("Cronograma", "../relatorios/contratos.cronograma.fisicoFinanceiro.php");
    $botaoCronograma->set_title("Exibe o cronograma físico-financeiro");
    $botaoCronograma->set_target("_blank");

    # Relatório
    $imagem = new Imagem(PASTA_FIGURAS . 'print.png', null, 15, 15);
    $botaoRel = new Button();
    $botaoRel->set_title("Relatório dos Pagamentos");
    $botaoRel->set_url("../relatorios/contratos.pagamentos.php");
    $botaoRel->set_target("_blank");
    $botaoRel->set_imagem($imagem);

    if (Verifica::acesso($idUsuario, [1, 9])) {
        $objeto->set_botaoListarExtra([$botaoObs, $botaoRel]);
    } else {
        $objeto->set_botaoListarExtra([$botaoRel]);
    }

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
            #$objeto->gravar($id, 'cadastroPagamentoExtra.php'); // Retirado a pedido de Kátia
            $objeto->gravar($id);
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
