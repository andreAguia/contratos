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
    $intra     = new Intra();
    $contratos = new Contratos();
    $pessoal   = new Pessoal();

    $contrato = new Contrato();
    $situacao = new Situacao();
    $comissao = new Comissao();
    $empresa  = new Empresa();
    $aditivo  = new Aditivo();

    # Verifica a fase do programa
    $fase = get('fase');

    # pega o id (se tiver)
    $id = soNumeros(get('id', get_session('sessionContrato')));

    # Começa uma nova página
    $page = new Page();
    $page->iniciaPagina();

    # Limita a tela
    $grid = new Grid();
    $grid->abreColuna(12);

    # Joga os parâmetros para as sessions
    set_session('sessionContrato', $id);

    # Cabeçalho da Página
    AreaServidor::cabecalho();

    # Cria um menu
    $menu1 = new MenuBar();

    # Voltar
    $botaoVoltar = new Link("Voltar", "cadastroContrato.php");
    $botaoVoltar->set_class('button');
    $botaoVoltar->set_title('Voltar a página anterior');
    $botaoVoltar->set_accessKey('V');
    $menu1->add_link($botaoVoltar, "left");

    # Editar
    $botaoEditar = new Link("Editar", "cadastroContrato.php?fase=editar&id={$id}");
    $botaoEditar->set_class('button');
    $botaoEditar->set_title('Editar contrato');
    $menu1->add_link($botaoEditar, "right");

    $menu1->show();

    ################################################################

    switch ($fase) {
        case "":
        case "inicial":
            # Carrega os dados com contrado editado
            $conteudo = $contrato->getDados($id);

            # Exibe os dados do contrado
            get_DadosContrato($id);

            $grid->fechaColuna();
            $grid->abreColuna(4);

            # Exibe o valor
            $contrato->exibeValorTotal($id);

            $comissao->listaComissao($id);

            # Exibe dados da empresa
            $idEmpresa = $conteudo["idEmpresa"];
            $empresa->exibeDados($idEmpresa);

            $grid->fechaColuna();
            $grid->abreColuna(8);

            # Exibe alertas (se tiver)
            $alerta = new AlertaContrato($id, true);

            # Exibe a situação atual
            $situacao->exibeSituacaoAtual($id);

            # Exibe outros dados do contrato
            $contrato->exibeDadosContrato($id);
            $aditivo->listaAditivos($id);
            break;
    }

    $grid->fechaColuna();
    $grid->fechaGrid();

    # Exibe o rodapé da página
    Grh::rodape($idUsuario);

    $page->terminaPagina();
} else {
    loadPage("../../areaServidor/sistema/login.php");
}
