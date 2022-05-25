<?php

/**
 * Rotina do menu de relatório
 *  
 * By Alat
 */
# Reservado para o servidor logado
$idUsuario = null;

# Configuração
include ("_config.php");

# Permissão de Acesso
$acesso = Verifica::acesso($idUsuario, [1, 9, 10]);

if ($acesso) {
    # Conecta ao Banco de Dados
    $intra = new Intra();
    $pessoal = new Pessoal();

    # Verifica a fase do programa
    $fase = get('fase', 'menu');

    # Zera as session do relatório
    set_session("sessionSelect");
    set_session("sessionLabel");
    set_session("sessionAlign");
    set_session("sessionClass");
    set_session("sessionMethod");
    set_session("sessionFunction");

    # Começa uma nova página
    $page = new Page();
    $page->iniciaPagina();

    # Cabeçalho da Página
    AreaServidor::cabecalho();

    # Limita o tamanho da tela
    $grid = new Grid();
    $grid->abreColuna(12);

    # Cria um menu
    $menu1 = new MenuBar();

    # Voltar
    $botaoVoltar = new Link("Voltar", "cadastroContrato.php");
    $botaoVoltar->set_class('button');
    $botaoVoltar->set_title('Voltar a página anterior');
    $botaoVoltar->set_accessKey('V');
    $menu1->add_link($botaoVoltar, "left");

    # Empresas
    $botao = new Button("Gerador de Planilha", "geradorRelatorios.php");
    $botao->set_title("Rotina de geração de relatórios personalizados");
    $botao->set_class("button");
    $menu1->add_link($botao, "right");

    $menu1->show();

    titulo("Relatórios");

    # Cria uma borda
    $callout = new Callout();
    $callout->abre();

    $menu = new Menu();
    $menu->add_item('titulo1', 'Contrato');
    $menu->add_item('linkWindow', 'Relatório de Contratos Ativos', '../relatorios/contratos.ativos.php');
    $menu->add_item('linkWindow', 'Relatório de Contratos Pendentes', '../relatorios/contratos.pendentes.php');
    $menu->add_item('linkWindow', 'Relatório de Contratos Encerrados', '../relatorios/contratos.encerrados.php');
    #$menu->add_item('titulo1', 'Aditivos');
    #$menu->add_item('linkWindow', 'Relatório de Contratos Ativos - Com Aditivos e Valores', '../relatorios/contratos.aditivo.valor.ativo.php');
    $menu->add_item('titulo1', 'Comissão de Fiscalização');
    $menu->add_item('linkWindow', 'Relatório Geral das Comissções de Fiscalização dos Contratos Ativos', '../relatorios/comissao.ativos.php');
    $menu->show();
    $callout->fecha();

    $grid->fechaColuna();
    $grid->fechaGrid();

    $page->terminaPagina();
} else {
    loadPage("../../areaServidor/sistema/login.php");
}