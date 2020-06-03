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

    $contrato = new Contrato();
    $situacao = new Situacao();
    $comissao = new Comissao();

    # Verifica a fase do programa
    $fase = get('fase');

    # pega o id (se tiver)
    $id = soNumeros(get('id', get_session('sessionContrato')));

    # Joga os parâmetros para as sessions    
    set_session('sessionContrato', $id);

    # Começa uma nova página
    $page = new Page();
    $page->iniciaPagina();

    # Cabeçalho da Página
    AreaServidor::cabecalho();

    # Limita a tela
    $grid = new Grid();
    $grid->abreColuna(12);

    # Cria um menu
    $menu1 = new MenuBar();

    # Voltar
    $botaoVoltar = new Link("Voltar", "areaInicial.php");
    $botaoVoltar->set_class('button');
    $botaoVoltar->set_title('Voltar a página anterior');
    $botaoVoltar->set_accessKey('V');
    $menu1->add_link($botaoVoltar, "left");

    # Editar
    $botaoEditar = new Link("Editar", "cadastroContrato.php?fase=editar&id={$id}");
    $botaoEditar->set_class('button');
    $botaoEditar->set_title('Editar os dados deste contrato');
    #$menu1->add_link($botaoEditar, "right");

    $menu1->show();

    # Título
    titulo("Area do Contrato");
    br();

    ################################################################

    switch ($fase) {
        case "" :
            $grid2 = new Grid();

            # Área Lateral
            $grid2->abreColuna(3);

            # Exibe dados do Contrato
            $contrato->exibeNumeroContrato($id);

            ########################################
            # Menu
            #tituloTable("Menu");

            $menu = new Menu("menuProcedimentos");
            $menu->add_item('titulo', 'Cadastros');
            $menu->add_item('link', 'Dados do Contrato', 'cadastroContrato.php?fase=editar');
            $menu->add_item('link', 'Comissão de Fiscalização', 'cadastroComissao.php');
            $menu->add_item('link', 'Situação', 'cadastroSituacao.php');

            $menu->add_item('titulo', 'Relatórios');
            $menu->show();

            #######################################
            # Área Principal            
            $grid2->fechaColuna();
            $grid2->abreColuna(9);
            
            $situacao->exibeSituacaoAtual($id);
            
            $contrato->exibeDadosContrato($id);
            
            /*
            $array = array(
                "Dados do Contrato",
                "Comissão de Fiscalização",
            );
            $tab = new Tab($array);
            $tab->abreConteudo();
                $contrato->exibeDadosContrato2($id);
            $tab->fechaConteudo();
            $tab->abreConteudo();
                $comissao->listaComissao($id);
            $tab->fechaConteudo();
            $tab->show();
            */
                   
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