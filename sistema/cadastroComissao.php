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
    $intra    = new Intra();
    $contrato = new Contrato();
    $pessoal  = new Pessoal();
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

    if ($fase == "listar") {

        # Limita o tamanho da tela
        $grid = new Grid();
        $grid->abreColuna(12);

        # Cria um menu
        $menu = new MenuBar();

        # Botão voltar
        $linkBotao1 = new Link("Voltar", "areaContrato.php");
        $linkBotao1->set_class('button');
        $linkBotao1->set_title('Volta para a página anterior');
        $linkBotao1->set_accessKey('V');
        $menu->add_link($linkBotao1, "left");

        # Incluir
        $linkBotao2 = new Link("Incluir", '?fase=editar');
        $linkBotao2->set_class('button');
        $linkBotao2->set_title('Incluir');
        $linkBotao2->set_accessKey('I');
        $menu->add_link($linkBotao2, "right");

        # Relatório
        $imagem   = new Imagem(PASTA_FIGURAS . 'print.png', null, 15, 15);
        $botaoRel = new Button();
        $botaoRel->set_imagem($imagem);
        $botaoRel->set_title("Imprimir Relatório de Histórico de Processo de readaptação");
        $botaoRel->set_url("../grhRelatorios/servidorReadaptacao.php");
        $botaoRel->set_target("_blank");
        #$menu->add_link($botaoRel, "right");
        $menu->show();

        $objeto->set_botaoVoltarLista(false);
        $objeto->set_botaoIncluir(false);

        # Exibe os dados do Contrato
        get_DadosContrato($idContrato);

        $grid->fechaColuna();
        $grid->fechaGrid();
    } else {
        # Exibe os dados do Contrato
        $objeto->set_rotinaExtra("get_DadosContrato");
        $objeto->set_rotinaExtraParametro($idContrato);
    }

    ################################################################
    # Nome do Modelo
    $objeto->set_nome("Comissão de Fiscalização");

    # Botão de voltar da lista
    $objeto->set_voltarLista("areaContrato.php");

    # select da lista
    $objeto->set_selectLista("SELECT idComissao,
                                     idServidor,
                                     idComissao,
                                     idServidor,
                                     idComissao
                                FROM tbcomissao
                               WHERE idContrato = {$idContrato}
                            ORDER BY dtPublicacaoSaida, tipo");

    # select do edita
    $objeto->set_selectEdita("SELECT idServidor,
                                     tipo,
                                     substituindo,
                                     portariaEntrada,
                                     dtPortariaEntrada,
                                     dtPublicacaoEntrada,
                                     pgPublicacaoEntrada,
                                     portariaSaida,
                                     dtPortariaSaida,
                                     dtPublicacaoSaida,
                                     pgPublicacaoSaida,
                                     obs,
                                     idContrato
                                FROM tbcomissao
                              WHERE idComissao = {$id}");

    # Caminhos
    $objeto->set_linkEditar("?fase=editar");
    $objeto->set_linkExcluir("?fase=excluir");
    $objeto->set_linkGravar("?fase=gravar");
    $objeto->set_linkListar("?fase=listar");
    $objeto->set_linkIncluir("?fase=editar");

    $formatacaoCondicional = array(
        array('coluna'   => 0,
            'valor'    => "Presidente",
            'operador' => '=',
            'id'       => 'presidenteComissao'),
        array('coluna'   => 0,
            'valor'    => "Membro",
            'operador' => '=',
            'id'       => 'membroComissao'),
        array('coluna'   => 0,
            'valor'    => "Suplente",
            'operador' => '=',
            'id'       => 'cuplenteComissao'),
        array('coluna'   => 0,
            'valor'    => "Saiu",
            'operador' => '=',
            'id'       => 'saiuComissao'),
    );

    # Parametros da tabela
    $objeto->set_label(array("Tipo", "Foto", "Nome", "Contatos", "Designação"));
    $objeto->set_align(array("center", "left", "left", "left", "left"));
    $objeto->set_width(array(8, 8, 25, 20, 20));
    #$objeto->set_funcao(array(null, null, null, null, null, "exibeFoto"));
    $objeto->set_classe(array("Comissao", "pessoal", "Comissao", "pessoal", "Comissao"));
    $objeto->set_metodo(array("getTipo", "get_foto", "getDadosMembro", "get_contatos", "getDadosDesignacao"));
    $objeto->set_numeroOrdem(true);
    $objeto->set_formatacaoCondicional($formatacaoCondicional);

    # Botão de exibição dos servidores com permissão a essa regra
    $botao = new BotaoGrafico();
    $botao->set_label('');
    $botao->set_title('Editar membro da comissão');
    $botao->set_url('?fase=exibeMembro&id=' . $id);
    $botao->set_imagem(PASTA_FIGURAS_GERAIS . 'ver.png', 20, 20);

    # Coloca o objeto link na tabela
    #$objeto->set_link(array(null, null, null, null, $botao));
    # Classe do banco de dados
    $objeto->set_classBd("Contratos");

    # Nome da tabela
    $objeto->set_tabela("tbcomissao");

    # Nome do campo id
    $objeto->set_idCampo("idComissao");

    # Tipo de label do formulário
    $objeto->set_formlabelTipo(1);

    # Pega os dados da combo de servidor
    $membro = $pessoal->select('SELECT idServidor,
                                       CONCAT(tbpessoa.nome," | ",IFNULL(tbtipocargo.sigla,"")," - ",IFNULL(tbcargo.nome,"")," | ",uenf_grh.tbsituacao.situacao)
                                  FROM uenf_grh.tbservidor JOIN uenf_grh.tbpessoa USING (idPessoa)
                                                      LEFT JOIN uenf_grh.tbsituacao ON (uenf_grh.tbservidor.situacao = uenf_grh.tbsituacao.idsituacao)
                                                      LEFT JOIN uenf_grh.tbcargo USING (idCargo)
                                                      LEFT JOIN uenf_grh.tbtipocargo USING (idTipoCargo)
                                 WHERE (idPerfil = 1 OR idPerfil = 2 OR idPerfil = 3 OR idPerfil = 4)
                              ORDER BY uenf_grh.tbsituacao.idSituacao, tbpessoa.nome');
    array_unshift($membro, array(null, null));

    # Pega os dados da combo de servidor
    $selectCombo = "SELECT idComissao,
                           CONCAT(tbpessoa.nome,  IF(portariaSaida IS NULL,'',' - Saiu'))
                      FROM uenf_contratos.tbcomissao JOIN uenf_grh.tbservidor USING (idServidor)
                                                     JOIN  uenf_grh.tbpessoa  USING (idPessoa)
                      WHERE tbcomissao.idContrato = {$idContrato}";
    if (!empty($id)) {
        $selectCombo .= " AND idComissao <> {$id}";
    }

    $selectCombo .= " ORDER BY portariaSaida, uenf_grh.tbpessoa.nome";

    $substituindo = $pessoal->select($selectCombo);

    array_unshift($substituindo, array(null, null));  // Adiciona o valor de nulo
    # Dados da combo tipo
    $tipo = array(
        array(null, null),
        array(1, "Presidente"),
        array(2, "Membro"),
        array(3, "Suplente"),
    );

    # Campos para o formulario
    $objeto->set_campos(array(
        array(
            'linha'    => 1,
            'nome'     => 'idServidor',
            'label'    => 'Servidor:',
            'tipo'     => 'combo',
            'array'    => $membro,
            'title'    => 'Servidor membro da comissão',
            'col'      => 9,
            'required' => true,
            'size'     => 30),
        array(
            'linha'  => 1,
            'nome'   => 'tipo',
            'label'  => 'Tipo:',
            'tipo'   => 'combo',
            'array'  => $tipo,
            'padrao' => 2,
            'col'    => 3,
            'size'   => 15),
        array(
            'linha' => 2,
            'nome'  => 'substituindo',
            'label' => 'Substituindo:',
            'tipo'  => 'combo',
            'array' => $substituindo,
            'title' => 'Servidor que sai para dar vaga',
            'col'   => 9,
            'size'  => 30),
        array(
            'linha'  => 3,
            'nome'   => 'portariaEntrada',
            'label'  => 'Portaria Designação:',
            'tipo'   => 'texto',
            'col'    => 2,
            'padrao' => $comissao->getUltimaPortariaEntrada($idContrato),
            'size'   => 10),
        array(
            'linha'  => 3,
            'nome'   => 'dtPortariaEntrada',
            'label'  => 'De:',
            'tipo'   => 'date',
            'col'    => 3,
            'padrao' => date_to_bd($comissao->getUltimaDataPortariaEntrada($idContrato)),
            'size'   => 15),
        array(
            'linha'  => 3,
            'nome'   => 'dtPublicacaoEntrada',
            'label'  => 'Publicado no DOERJ em:',
            'tipo'   => 'date',
            'col'    => 3,
            'padrao' => date_to_bd($comissao->getUltimaDataPublicacaoEntrada($idContrato)),
            'size'   => 15),
        array(
            'linha' => 3,
            'nome'  => 'pgPublicacaoEntrada',
            'label' => 'Página:',
            'tipo'  => 'texto',
            'col'   => 2,
            'size'  => 10),
        array(
            'linha' => 4,
            'nome'  => 'portariaSaida',
            'label' => 'Portaria de Saída:',
            'tipo'  => 'texto',
            'col'   => 2,
            'size'  => 10),
        array(
            'linha' => 4,
            'nome'  => 'dtPortariaSaida',
            'label' => 'De:',
            'tipo'  => 'date',
            'col'   => 3,
            'size'  => 15),
        array(
            'linha' => 4,
            'nome'  => 'dtPublicacaoSaida',
            'label' => 'Publicado no DOERJ em:',
            'tipo'  => 'date',
            'col'   => 3,
            'size'  => 15),
        array(
            'linha' => 4,
            'nome'  => 'pgPublicacaoSaida',
            'label' => 'Página:',
            'tipo'  => 'texto',
            'col'   => 2,
            'size'  => 10),
        array(
            'linha' => 5,
            'nome'  => 'obs',
            'label' => 'Observação:',
            'tipo'  => 'textarea',
            'size'  => array(80, 5)),
        array(
            "linha"  => 5,
            "nome"   => "idContrato",
            "label"  => "idContrato:",
            'tipo'   => 'hidden',
            'padrao' => $idContrato,
            "col"    => 3,
            "size"   => 11),
    ));

    # idUsuário para o Log
    $objeto->set_idUsuario($idUsuario);

    ################################################################
    switch ($fase) {
        case "":
        case "listar" :
            # Divide a página em 2 colunas
            $grid = new Grid();

            /*
             * Processos
             */

            $grid->abreColuna(12, 4);

            $comissao->exibeProcesso($idContrato);

            $grid->fechaColuna();

            /*
             * Portarias
             */

            $grid->abreColuna(12, 4);

            $comissao->exibePortarias($idContrato);

            $grid->fechaColuna();

            /*
             * Documentos
             */

            $grid->abreColuna(12, 4);

            $comissao->exibeMenuDocumentos($idContrato);

            $grid->fechaColuna();
            $grid->fechaGrid();

            $objeto->listar();
            break;

        case "editar":
        case "excluir":
            $objeto->$fase($id);
            break;

        case "gravar":
            $objeto->gravar($id, null, "cadastroComissaoPosGravacao.php");
            break;

        case "exibeFicha":
            botaoVoltar("areaContrato.php");

            # Exibe dados do contrato
            get_DadosContrato($idContrato);
            break;

        case "cadastroProcesso":

            botaoVoltar("?");

            # Exibe dados do contrato
            get_DadosContrato($idContrato);

            # Pega os valores do banco
            $conteudo = $contrato->getDados($idContrato);

            $painel = new Callout();
            $painel->abre();

            tituloTable("Processo da Comissão de Fiscalização");
            br();

            # Formuário exemplo de login
            $form = new Form('?fase=valida', 'login');

            /*
             *  processo Sei
             */
            $controle = new Input('processoComissaoSei', 'sei', 'Processo Sei:', 1);
            $controle->set_autofocus(true);
            $controle->set_size(50);
            $controle->set_linha(1);
            $controle->set_col(6);
            $controle->set_valor($conteudo["processoComissaoSei"]);
            $controle->set_title('O processo Sei da comissão de fiscalização');
            $form->add_item($controle);

            /*
             *  processo físico
             */
            $controle = new Input('processoComissao', 'texto', 'Processo Físico:', 1);
            $controle->set_size(50);
            $controle->set_linha(1);
            $controle->set_col(6);
            $controle->set_valor($conteudo["processoComissao"]);
            $controle->set_title('O processo Físico da comissão de fiscalização');
            $form->add_item($controle);

            /*
             *  submit
             */
            $controle = new Input('submit', 'submit');
            $controle->set_valor('Entrar');
            $controle->set_linha(3);
            $controle->set_tabIndex(3);
            $controle->set_accessKey('E');
            $form->add_item($controle);

            $form->show();
            $painel->fecha();
            break;

        ##################################################################

        case "valida":

            # Pega os dados digitados
            $processoComissaoSei = post("processoComissaoSei");
            $processoComissao    = post("processoComissao");

            # Pega os valores anteriores
            $conteudo = $contrato->getDados($idContrato);

            # Grava os valores
            $objeto = new Contratos();
            $objeto->set_tabela("tbcontrato");
            $objeto->set_idCampo("idContrato");
            $objeto->gravar(
                    ["processoComissaoSei", "processoComissao"],
                    [$processoComissaoSei, $processoComissao],
                    $idContrato);

            # Registra o log
            $intra     = new Intra();
            $data      = date("Y-m-d H:i:s");
            $atividade = "Alterou: ";
            $alterou   = false;

            if ($conteudo["processoComissaoSei"] <> $processoComissaoSei) {
                $atividade .= "[processoComissaoSei] {$conteudo["processoComissaoSei"]} -> {$processoComissaoSei}; ";
                $alterou   = true;
            }

            if ($conteudo["processoComissao"] <> $processoComissao) {
                $atividade .= "[processoComissao] {$conteudo["processoComissao"]} -> {$processoComissao}; ";
                $alterou   = true;
            }

            if ($alterou) {
                $intra->registraLog($idUsuario,
                                    $data,
                                    $atividade,
                                    "tbcontrato",
                                    $idContrato,
                                    2);
            }
            loadPage("?");
            break;
    }

    $page->terminaPagina();
} else {
    loadPage("../../areaServidor/sistema/login.php");
}
