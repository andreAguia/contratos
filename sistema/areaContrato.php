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
    $intra = new Intra();
    $contratos = new Contratos();
    $pessoal = new Pessoal();

    $contrato = new Contrato();
    $situacao = new Situacao();
    $comissao = new Comissao();
    $empresa = new Empresa();
    $aditivo = new Aditivo();
    $tarefa = new Tarefa();

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
    $grid->abreColuna(10);

    # Cria um menu
    $menu1 = new MenuBar();

    # Voltar
    $botaoVoltar = new Link("Voltar", "cadastroContrato.php");
    $botaoVoltar->set_class('button');
    $botaoVoltar->set_title('Voltar a página anterior');
    $botaoVoltar->set_accessKey('V');
    $menu1->add_link($botaoVoltar, "left");

    # Aditivo
    $botaoEditar = new Link("Aditivos", "cadastroAditivo.php");
    $botaoEditar->set_class('button');
    $botaoEditar->set_title('Acessa os aditivos do contrato');
    #$menu1->add_link($botaoEditar, "right");

    # Comissão
    $botaoEditar = new Link("Comissão", "cadastroComissao.php");
    $botaoEditar->set_class('button');
    $botaoEditar->set_title('Acessa os membros da comissão de fiscalização deste contrato');
    #$menu1->add_link($botaoEditar, "right");

    # Situação
    $botaoEditar = new Link("Situação", "cadastroSituacao.php");
    $botaoEditar->set_class('button');
    $botaoEditar->set_title('Acessa o cadastro de situação deste contrato.');
    #$menu1->add_link($botaoEditar, "right");

    # Editar
    $botaoEditar = new Link("Editar", "cadastroContrato.php?fase=editar&id={$id}");
    $botaoEditar->set_class('button');
    $botaoEditar->set_title('Editar contrato');
    #$menu1->add_link($botaoEditar, "right");

    $menu1->show();

    $grid->fechaColuna();
    $grid->abreColuna(2);

    $contrato->exibeStatus($id);

    $grid->fechaColuna();
    $grid->abreColuna(12);

    ################################################################

    switch ($fase) {
        case "":
        case "inicial":
            # Carrega os dados com contrado editado
            $conteudo = $contrato->get_dados($id);

            # Exibe os dados do contrado
            get_DadosContrato($id);

            # Exibe alertas (se tiver)
            $alerta = new AlertaContrato($id);

            $grid->fechaColuna();
            $grid->abreColuna(5);

            # Exibe dados da empresa
            $idEmpresa = $conteudo["idEmpresa"];
            $empresa->exibeDados($idEmpresa);

            #$comissao->exibeComissao($id);
            $comissao->listaComissao($id);

            $grid->fechaColuna();
            $grid->abreColuna(7);

            # Exibe a situação atual
            $situacao->exibeSituacaoAtual($id);

            # Exibe as tarefas
            $painel = new Callout();
            $painel->abre();
            p("Tarefas", "contratoLabelCallout");

            $tarefa->listaTarefas($id);

            $div = new Div("divEdita1");
            $div->abre();

            # Editar
            $div = new Div("divEdita2");
            $div->abre();

            # Editar
            $botaoEditar = new Link("Editar", "cadastroSituacao.php");
            $botaoEditar->set_class('tiny button secondary');
            $botaoEditar->set_title('Editar situação');
            $botaoEditar->show();

            $div->fecha();

            $div->fecha();
            $painel->fecha();

            # Exibe outros dados do contrato
            $contrato->exibeDadosContrato($id);
            $aditivo->exibeAditivoContrato($id);

            break;

            ################################################################
    }

    $grid->fechaColuna();
    $grid->fechaGrid();

    # Exibe o rodapé da página
    Grh::rodape($idUsuario);

    $page->terminaPagina();
} else {
    loadPage("../../areaServidor/sistema/login.php");
}
