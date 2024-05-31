<?php

/**
 * Cadastro de Tipo de Membro de Comissão
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
    $comissao = new Comissao();

    # log
    if (get('i', false)) {
        # Grava no log a atividade
        $atividade = "Acessou o cadastro de tipo de membro de comissão.";
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
    $objeto->set_nome("Tipos de Membros de Comissão");

    # Botão de voltar da lista
    $objeto->set_voltarLista("cadastroMembros.php");

    # select da lista
    $objeto->set_selectLista("SELECT idTipoMembro,
                                      numOrdem,
                                      tipo,
                                      obs,
                                      idTipoMembro
                                 FROM tbtipomembro
                             ORDER BY numOrdem");

    # select do edita
    $objeto->set_selectEdita("SELECT numOrdem,
                                     tipo,
                                     obs
                                FROM tbtipomembro
                              WHERE idTipoMembro = {$id}");

    # Caminhos
    $objeto->set_linkEditar("?fase=editar");
    $objeto->set_linkExcluir("?fase=excluir");
    $objeto->set_linkGravar("?fase=gravar");
    $objeto->set_linkListar("?fase=listar");

    # Parametros da tabela
    $objeto->set_label(["Id", "Num Ordem", "Tipo", "Obs", "Membros"]);
    $objeto->set_width([5, 5, 20, 45, 10]);
    $objeto->set_align(["center", "center", "left", "left"]);
    $objeto->set_classe([null, null, null, null, "Comissao"]);
    $objeto->set_metodo([null, null, null, null, "get_numMembrosTipo"]);

    # Classe do banco de dados
    $objeto->set_classBd("Contratos");

    # Nome da tabela
    $objeto->set_tabela("tbtipomembro");

    # Nome do campo id
    $objeto->set_idCampo("idTipoMembro");

    # Tipo de label do formulário
    $objeto->set_formLabelTipo(1);

    # Campos para o formulario
    $objeto->set_campos(array(
        array("linha" => 1,
            "nome" => "numOrdem",
            "label" => "Num Ordem:",
            "tipo" => "texto",
            "required" => true,
            "autofocus" => true,
            "col" => 3,
            "size" => 3),
        array("linha" => 1,
            "nome" => "tipo",
            "label" => "Tipo:",
            "tipo" => "texto",
            "required" => true,
            "col" => 6,
            "size" => 100),
        array("linha" => 2,
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

        case "excluir":
            # Verifica se tem contrato com esse status
            $num = $comissao->get_numMembrosTipo($id);

            if ($num > 0) {
                alert("Existem membros cadastrados com este tipo. Dessa forma o mesmo NÃO poderá ser excluído.");
                back(1);
            } else {
                $objeto->excluir($id);
            }
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
