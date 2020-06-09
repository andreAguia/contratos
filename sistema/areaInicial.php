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
    
    p(SISTEMA, 'tituloInicial');

    # Zera as sessões
    set_session('sessionContrato');

    # Limita a tela
    $grid = new Grid();
    $grid->abreColuna(12);

    # Cria um menu
    $menu = new MenuBar();

    # Voltar
    $botaoVoltar = new Link("Voltar", "../../../areaServidor/sistema/areaServidor.php");
    $botaoVoltar->set_class('button');
    $botaoVoltar->set_title('Voltar a página anterior');
    $botaoVoltar->set_accessKey('V');
    $menu->add_link($botaoVoltar, "left");

    # Empresa
    $botaoVoltar = new Link("Empresas","cadastroEmpresa.php");
    $botaoVoltar->set_class('button');
    $botaoVoltar->set_title('Cadastro de Empresas');
    $menu->add_link($botaoVoltar,"right");
    
    # Modalidade
    $botaoVoltar = new Link("Modalidades","cadastroModalidade.php");
    $botaoVoltar->set_class('button');
    $botaoVoltar->set_title('Cadastro de Modalidades');
    $menu->add_link($botaoVoltar,"right");
    
    # Status
    $botaoVoltar = new Link("Status","cadastroStatus.php");
    $botaoVoltar->set_class('button');
    $botaoVoltar->set_title('Cadastro de Status');
    $menu->add_link($botaoVoltar,"right");
    
    # Relatórios
    $imagem1 = new Imagem(PASTA_FIGURAS . 'print.png', null, 15, 15);
    $botaoRel = new Button();
    $botaoRel->set_url("relatorios.php");
    $botaoRel->set_title("Relatórios dos Sistema");
    $botaoRel->set_imagem($imagem1);
    $menu->add_link($botaoRel, "right");

    $menu->show();
    
    ################################################################

    switch ($fase) {
        case "" :
        case "exibeLista" :
            $lista = new Contrato();
            $lista->listaContratos();
            break;

        ################################################################
    }



    $grid->fechaColuna();
    $grid->fechaGrid();

    $page->terminaPagina();
} else {
    loadPage("../../areaServidor/sistema/login.php");
}