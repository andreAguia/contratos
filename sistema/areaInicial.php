<?php

/**
 * Cadastro de Campus
 *  
 * By Alat
 */
# Reservado para o servidor logado
$idUsuario = null;

# Configuração
include("_config.php");

# Permissão de Acesso
$acesso = Verifica::acesso($idUsuario, 9);

if ($acesso) {
    # Conecta ao Banco de Dados
    $intra     = new Intra();
    $contratos = new Contratos();
    $pessoal   = new Pessoal();

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
    set_session('parametroAno');
    set_session('parametroStatus');
    set_session('parametroModalidade');
    set_session('parametroEmpresa');
    set_session('sessionParametro');
    
    # Limita a tela
    $grid = new Grid();
    $grid->abreColuna(12);

    ################################################################

    switch ($fase) {
        case "":
            # nome do sistema
            p(SISTEMA, 'tituloInicial');

            # Monta o menu principal
            $menu = new MenuBar();

            # Voltar
            $botaoVoltar = new Link("Voltar", "../../../areaServidor/sistema/areaServidor.php");
            $botaoVoltar->set_class('button');
            $botaoVoltar->set_title('Voltar a página anterior');
            $botaoVoltar->set_accessKey('V');
            $menu->add_link($botaoVoltar, "left");

            # Relatórios
            $imagem1  = new Imagem(PASTA_FIGURAS . 'print.png', null, 15, 15);
            $botaoRel = new Button();
            $botaoRel->set_url("?fase=relatorio");
            $botaoRel->set_title("Relatórios dos Sistema");
            $botaoRel->set_imagem($imagem1);
            $menu->add_link($botaoRel, "right");

            $menu->show();
            $menu = new MenuContratos($idUsuario);

            # Exibe o rodapé da página
            Grh::rodape($idUsuario);
            break;

            ################################################################

        case "relatorio":
            # Monta o menu principal
            $menu = new MenuBar();

            # Voltar
            $botaoVoltar = new Link("Voltar", "?");
            $botaoVoltar->set_class('button');
            $botaoVoltar->set_title('Voltar a página anterior');
            $botaoVoltar->set_accessKey('V');
            $menu->add_link($botaoVoltar, "left");
            $menu->show();

            # Título
            Titulo("Relatórios");
            br(4);
            
            p("Rotina ainda não implementada!","center","f14");
            break;
    }



    $grid->fechaColuna();
    $grid->fechaGrid();

    $page->terminaPagina();
} else {
    loadPage("../../areaServidor/sistema/login.php");
}
