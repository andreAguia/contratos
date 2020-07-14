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
$acesso = Verifica::acesso($idUsuario, 9);

if ($acesso) {
    # Conecta ao Banco de Dados
    $intra = new Intra();
    $pessoal = new Pessoal();

    # Verifica a fase do programa
    $fase = get('fase', 'menu');

    # Começa uma nova página
    $page = new Page();
    $page->iniciaPagina();

    # Cabeçalho da Página
    AreaServidor::cabecalho();

    # Limita o tamanho da tela
    $grid = new Grid();
    $grid->abreColuna(12);

    # Botão voltar
    botaoVoltar('cadastroContrato.php');

    # Cria uma borda
    $callout = new Callout();
    $callout->abre();

    titulo("Relatórios");
    br();

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