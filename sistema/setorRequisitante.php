<?php

/**
 * Setor Requisitante
 *
 * By Alat
 */
# Reservado para o servidor logado
$idUsuario = null;

# Configuração
include "_config.php";

# Permissão de Acesso
$acesso = Verifica::acesso($idUsuario, [1, 9]);

if ($acesso) {
    # Conecta ao Banco de Dados
    $empresa = new Empresa();
    $pessoal = new Pessoal();

    # log
    if (get('i', false)) {
        # Grava no log a atividade
        $atividade = "Acessou o controle de setor Requisitante.";
        $data = date("Y-m-d H:i:s");
        $intra->registraLog($idUsuario, $data, $atividade, null, null, 7);
    }

    # Verifica a fase do programa
    $fase = get("fase");

    # pega o id (se tiver)
    set_session('sessionContrato', soNumeros(get("id")));

    # Começa uma nova página
    $page = new Page();
    $page->iniciaPagina();

    # Limita o tamanho da tela
    $grid = new Grid();
    $grid->abreColuna(12);

    # Cabeçalho da Página
    AreaServidor::cabecalho();

    ################################################################

    switch ($fase) {
        case "" :

            # Cria um menu
            $menu1 = new MenuBar();

            # Voltar
            $botaoVoltar = new Link("Voltar", "cadastroContrato.php");
            $botaoVoltar->set_class('button');
            $botaoVoltar->set_title('Voltar a página anterior');
            $botaoVoltar->set_accessKey('V');
            $menu1->add_link($botaoVoltar, "left");
            $menu1->show();

            $select = "SELECT numero,
                      requisitante,
                      setorRequisitante,
                      idContrato
                 FROM tbcontrato ";
            $contratos = new Contratos();
            $array = $contratos->select($select);

            $tabela = new Tabela();
            $tabela->set_titulo("Controle de Setor Requisitante");
            $tabela->set_conteudo($array);
            $tabela->set_label(["Contrato", "Requisitante Texto", "Requisitante Combo"]);
            $tabela->set_width([20, 35, 35]);
            $tabela->set_align(["center", "left", "left"]);

            $tabela->set_editar('?fase=editaContrato&id=');
            $tabela->set_editarBotao("bullet_edit.png");
            $tabela->set_idCampo('idContrato');

            $tabela->show();
            break;

        case "editaContrato" :
            loadPage("cadastroRequisitante.php?i=true");            
            set_session("origem","setorRequisitante.php");
            break;
    }

    $grid->fechaColuna();
    $grid->fechaGrid();
}