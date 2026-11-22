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
    $objeto->set_selectLista("SELECT idTreinamento,
                                      data,
                                      descricao,
                                      obs,
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
    $objeto->set_label(["Id", "Data", "Descrição", "Obs", "Alunos"]);
    $objeto->set_width([5, 10, 30, 40, 5]);
    $objeto->set_align(["center", "center", "left", "left", "center"]);
    $objeto->set_funcao([null, "date_to_php"]);
//    $objeto->set_classe([null, null, null, null, "Modalidade"]);
//    $objeto->set_metodo([null, null, null, null, "getNumContratos"]);

    $objeto->set_rowspan(1);
    $objeto->set_grupoCorColuna(1);

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
    }

    $page->terminaPagina();
} else {
    loadPage("../../areaServidor/sistema/login.php");
}
