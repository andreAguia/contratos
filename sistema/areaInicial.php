<?php

/**
 * Cadastro de Campus
 *  
 * By Alat
 */
# Reservado para o servidor logado
$idUsuario = NULL;

# Configuração
include ("_config.php");

# Permissão de Acesso
$acesso = Verifica::acesso($idUsuario, 9);

if ($acesso) {
    # Conecta ao Banco de Dados
    $intra = new Intra();
    $contratos = new Contratos();
    $pessoal = new Pessoal();

    # Verifica a fase do programa
    $fase = get('fase');

    # pega o id (se tiver)
    $id = soNumeros(get('id'));

    # Começa uma nova página
    $page = new Page();
    $page->iniciaPagina();

    # Cabeçalho da Página
    AreaServidor::cabecalho();

    # Zera as sessões
    set_session('sessionContrato');

    # Limita a tela
    $grid = new Grid();
    $grid->abreColuna(12);

    # Cria um menu
    $menu1 = new MenuBar();

    # Voltar
    $botaoVoltar = new Link("Voltar", "../../../areaServidor/sistema/areaServidor.php");
    $botaoVoltar->set_class('button');
    $botaoVoltar->set_title('Voltar a página anterior');
    $botaoVoltar->set_accessKey('V');
    $menu1->add_link($botaoVoltar, "left");

    # Ano Exercício
    $botaoVoltar = new Link("Ano Exercício");
    $botaoVoltar->set_class('button');
    $botaoVoltar->set_title('Férias por Ano Exercício');
    #$menu1->add_link($botaoVoltar,"right");
    # Ano por Fruíção
    $botaoVoltar = new Link("por Ano de Fruição", "areaFeriasFruicao.php");
    $botaoVoltar->set_class('button');
    $botaoVoltar->set_title('Férias por Ano em que foi realmente fruído');
    #$menu1->add_link($botaoVoltar,"right");

    $menu1->show();

    # Título
    titulo("Sistema de Gestão de Contratos");

    ################################################################

    switch ($fase) {
        case "" :
        case "exibeLista" :

            $grid2 = new Grid();

            # Área Lateral
            $grid2->abreColuna(12, 2);

            ########################################
            # Menu
            #tituloTable("Menu");

            $menu = new Menu("menuProcedimentos");
            $menu->add_item('titulo', 'Cadastros');
            $menu->add_item('link', 'Empresa', 'cadastroEmpresa.php');
            $menu->add_item('link', 'Modalidade', 'cadastroModalidade.php');
            $menu->add_item('link', 'Status', 'cadastroStatus.php');

            $menu->add_item('titulo', 'Relatórios');
            $menu->show();

            #######################################
            # Resumo Geral
            # Informa a classe com os parâmetros
            $lista = new Contrato();
            #$lista1->set_lotacao($parametroLotacao);
            # resumo geral
            #$lista1->showResumoGeral();
            # por dias
            #$lista1->showResumoPorDia();
            #######################################
            # Área Principal            
            $grid2->fechaColuna();
            $grid2->abreColuna(12, 10);
            br();

            $lista->listaContratos();

            $grid2->fechaColuna();
            $grid2->fechaGrid();
            break;

        ################################################################
    }



    $grid->fechaColuna();
    $grid->fechaGrid();

    $page->terminaPagina();
} else {
    loadPage("../../areaServidor/sistema/login.php");
}