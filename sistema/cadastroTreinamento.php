<?php

/**
 * Cadastro de Treinamento de Membros de comissão
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
    $modalidade = new Modalidade();

    # log
    if (get('i', false)) {
        # Grava no log a atividade
        $atividade = "Acessou o cadastro de modalidade.";
        $data = date("Y-m-d H:i:s");
        $intra->registraLog($idUsuario, $data, $atividade, null, null, 7);
    }

    # Verifica a fase do programa
    $fase = get("fase", "listar");

    # pega o id (se tiver)
    $id = soNumeros(get("id"));

    # Começa uma nova página
    $page = new Page();
    $page->iniciaPagina();

    # Cabeçalho da Página
    AreaServidor::cabecalho();

    # Abre um novo objeto Modelo
    $objeto = new Modelo();

    ################################################################
    # Nome do Modelo
    $objeto->set_nome("Treinamento dos Membros de Comissão");

    # Botão de voltar da lista
    $objeto->set_voltarLista("cadastroContrato.php");

    # select da lista
    $objeto->set_selectLista("SELECT data,
                                     descricao,
                                     obs,
                                     idTreinamento,
                                     idTreinamento
                                FROM tbtreinamento
                            ORDER BY data");

    # select do edita
    $objeto->set_selectEdita("SELECT data,
                                     descricao,
                                     obs
                                FROM tbtreinamento
                              WHERE idTreinamento = {$id}");

    # Caminhos
    $objeto->set_linkEditar("?fase=editar");
    $objeto->set_linkExcluir("?fase=excluir");
    $objeto->set_linkGravar("?fase=gravar");
    $objeto->set_linkListar("?fase=listar");

    # Parametros da tabela
    $objeto->set_label(["Data", "Descrição", "Obs", "Alunos", null]);
    $objeto->set_colspanLabel([null, null, null, 2]);
    $objeto->set_width([10, 30, 35, 5, 5]);
    $objeto->set_align(["center", "left", "left", "center"]);
    $objeto->set_funcao(["date_to_php"]);

    # Ver alunos
    $alunos = new Link(null, "?fase=verAlunos&id=");
    $alunos->set_imagem(PASTA_FIGURAS_GERAIS . 'olho.png', 20, 20);
    $alunos->set_title("Exibe os alunos deste treinamento");

    $objeto->set_classe([null, null, null, "Treinamento"]);
    $objeto->set_metodo([null, null, null, "get_numAlunos"]);

    # Coloca o objeto link na tabela			
    $objeto->set_link([null, null, null, null, $alunos]);

    # Classe do banco de dados
    $objeto->set_classBd("Contratos");

    # Nome da tabela
    $objeto->set_tabela("tbtreinamento");

    # Nome do campo id
    $objeto->set_idCampo("idTreinamento");

    # Tipo de label do formulário
    $objeto->set_formLabelTipo(1);

    # Campos para o formulario
    $objeto->set_campos(array(
        array(
            'linha' => 1,
            'nome' => 'data',
            'label' => 'Data:',
            'tipo' => 'data',
            "required" => true,
            "autofocus" => true,
            'col' => 3,
            'size' => 5),
        array(
            "linha" => 1,
            "nome" => "descricao",
            "label" => "Descrição:",
            "tipo" => "texto",
            "required" => true,
            "col" => 9,
            "size" => 250),
        array(
            "linha" => 2,
            "nome" => "obs",
            "label" => "Observação:",
            "tipo" => "textarea",
            "size" => array(80, 5))));

    # idUsuário para o Log
    $objeto->set_idUsuario($idUsuario);

    ################################################################
    switch ($fase) {
        case "":
        case "listar":
            $objeto->listar();
            break;

        ################################################################    

        case "excluir" :
            $objeto->excluir($id);
            break;

        ################################################################

        case "editar":
        case "gravar":
            $objeto->$fase($id);
            break;

        ################################################################

        /*
         * Chama a rotina de cadastro de aluno
         */

        case "verAlunos" :
            br(8);
            aguarde();

            # Informa o $idTreinamento
            set_session('idTreinamento', $id);

            # Carrega a página específica
            loadPage('cadastroTreinamentoServidor.php');
            break;

        ################################################################
    }

    $page->terminaPagina();
} else {
    loadPage("../../areaServidor/sistema/login.php");
}
