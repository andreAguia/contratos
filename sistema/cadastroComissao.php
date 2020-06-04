<?php

/**
 * Cadastro de Campus
 *  
 * By Alat
 */
# Reservado para o servidor logado
$idUsuario = null;

# Configuração
include ("_config.php");

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
    # Exibe os dados do Servidor
    $objeto->set_rotinaExtra("get_DadosContrato");
    $objeto->set_rotinaExtraParametro($idContrato);

    # Nome do Modelo
    $objeto->set_nome("Comissão de Fiscalização");

    # Botão de voltar da lista
    $objeto->set_voltarLista("areaContrato.php");

    # select da lista
    $objeto->set_selectLista("SELECT tipo,
                                     idServidor,
                                     idServidor,
                                     idServidor,
                                     idComissao
                                FROM tbcomissao 
                               WHERE idContrato = {$idContrato}
                            ORDER BY tipo");

    # select do edita
    $objeto->set_selectEdita("SELECT idServidor,
                                     tipo,
                                     portariaEntrada,
                                     dtPortariaEntrada,
                                     dtPublicacaoEntrada,
                                     portariaSaida,
                                     dtPortariaSaida,
                                     dtPublicacaoSaida,
                                     obs,
                                     idContrato,
                                     idComissao
                                FROM tbcomissao
                              WHERE idComissao = {$id}");

    # Caminhos
    #$objeto->set_linkEditar("?fase=editar");
    #$objeto->set_linkExcluir("?fase=excluir");
    $objeto->set_linkGravar("?fase=gravar");
    $objeto->set_linkListar("?fase=listar");
    $objeto->set_linkIncluir("?fase=incluirMembro");

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
    );

    # Parametros da tabela
    $objeto->set_label(array("Tipo", "Nome", "Cargo", "Lotação", "Ver"));
    $objeto->set_align(array("center", "left", "left", "left"));
    $objeto->set_width(array(8, 30, 25, 25, 7));
    #$objeto->set_funcao(array(null, null, null, null, null, "exibeFoto"));
    $objeto->set_classe(array("Comissao", "pessoal", "pessoal", "pessoal"));
    $objeto->set_metodo(array("get_tipo", "get_nome", "get_cargo", "get_lotacao"));
    $objeto->set_numeroOrdem(true);
    $objeto->set_formatacaoCondicional($formatacaoCondicional);

    # Botão de exibição dos servidores com permissão a essa regra
    $botao = new BotaoGrafico();
    $botao->set_label('');
    $botao->set_title('Editar membro da comissão');
    $botao->set_url('?fase=exibeMembro&id=' . $id);
    $botao->set_imagem(PASTA_FIGURAS_GERAIS . 'ver.png', 20, 20);

    # Coloca o objeto link na tabela			
    $objeto->set_link(array(null, null, null, null, $botao));

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
                                       CONCAT(tbpessoa.nome," | ",IFnull(tbtipocargo.sigla,"")," - ",IFnull(tbcargo.nome,"")," | ",uenf_grh.tbsituacao.situacao)
                                  FROM uenf_grh.tbservidor JOIN uenf_grh.tbpessoa USING (idPessoa)
                                                      LEFT JOIN uenf_grh.tbsituacao ON (uenf_grh.tbservidor.situacao = uenf_grh.tbsituacao.idsituacao)
                                                      LEFT JOIN uenf_grh.tbcargo USING (idCargo)
                                                      LEFT JOIN uenf_grh.tbtipocargo USING (idTipoCargo)
                                 WHERE (idPerfil = 1 OR idPerfil = 4)
                              ORDER BY uenf_grh.tbsituacao.idSituacao, tbpessoa.nome');
    array_unshift($membro, array(null, null)); # Adiciona o valor de nulo
    # Dados da combo tipo
    $tipo = array(
        array(null, null),
        array(1, "Presidente"),
        array(2, "Membro"),
        array(3, "Suplente")
    );

    # Campos para o formulario
    $objeto->set_campos(array(
        array('linha'    => 1,
            'nome'     => 'idServidor',
            'label'    => 'Servidor:',
            'tipo'     => 'combo',
            'array'    => $membro,
            'title'    => 'Servidor membro ca comissão',
            'col'      => 9,
            'required' => true,
            'size'     => 30),
        array('linha'  => 1,
            'nome'   => 'tipo',
            'label'  => 'Tipo:',
            'tipo'   => 'combo',
            'array'  => $tipo,
            'padrao' => 2,
            'col'    => 3,
            'size'   => 15),
        array('linha' => 2,
            'nome'  => 'portariaEntrada',
            'label' => 'Portaria Designação:',
            'tipo'  => 'texto',
            'col'   => 2,
            'size'  => 10),
        array('linha' => 2,
            'nome'  => 'dtPortariaEntrada',
            'label' => 'De:',
            'tipo'  => 'date',
            'col'   => 3,
            'size'  => 15),
        array('linha' => 2,
            'nome'  => 'dtPublicacaoEntrada',
            'label' => 'Publicado no DOERJ em:',
            'tipo'  => 'date',
            'col'   => 3,
            'size'  => 15),
        array('linha' => 3,
            'nome'  => 'portariaSaida',
            'label' => 'Portaria de Saída:',
            'tipo'  => 'texto',
            'col'   => 2,
            'size'  => 10),
        array('linha' => 3,
            'nome'  => 'dtPortariaSaida',
            'label' => 'De:',
            'tipo'  => 'date',
            'col'   => 3,
            'size'  => 15),
        array('linha' => 3,
            'nome'  => 'dtPublicacaoSaida',
            'label' => 'Publicado no DOERJ em:',
            'tipo'  => 'date',
            'col'   => 3,
            'size'  => 15),
        array('linha' => 4,
            'nome'  => 'obs',
            'label' => 'Observação:',
            'tipo'  => 'textarea',
            'size'  => array(80, 5)),
        array("linha"  => 5,
            "nome"   => "idContrato",
            "label"  => "idContrato:",
            'tipo'   => 'hidden',
            'padrao' => $idContrato,
            "col"    => 3,
            "size"   => 11)
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

        case "exibeFicha" :
            botaoVoltar("areaContrato.php");
            # Exibe dados do contrato
            get_DadosContrato($idContrato);
            break;

        ##################################################################

        case "exibeMembro" :

            # Limita a tela
            $grid = new Grid();
            $grid->abreColuna(12);

            # Cria um menu
            $menu1 = new MenuBar();

            # Voltar
            $botaoVoltar = new Link("Voltar", "?");
            $botaoVoltar->set_class('button');
            $botaoVoltar->set_title('Voltar a página anterior');
            $botaoVoltar->set_accessKey('V');
            $menu1->add_link($botaoVoltar, "left");

            # Editar
            $botaoEditar = new Link("Editar", "cadastroComissao.php?fase=editar&id={$id}");
            $botaoEditar->set_class('button');
            $botaoEditar->set_title('Editar os dados deste membro');
            $menu1->add_link($botaoEditar, "right");

            $menu1->show();

            $grid->fechaColuna();
            $grid->fechaGrid();

            # Exibe dados do membro da comissão
            $comissao->exibeDadosMembro($id);
            break;

        ##################################################################
        
        case "incluirMembro" :
            
            echo "oi";
            break;
        
        ##################################################################
    }

    $page->terminaPagina();
}
else {
    loadPage("../../areaServidor/sistema/login.php");
}