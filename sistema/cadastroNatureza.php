<?php

/**
 * Cadastro de Natureza de Pgto
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
    $status = new Status();

    # log
    if (get('i', false)) {
        # Grava no log a atividade
        $atividade = "Acessou o cadastro de natureza.";
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
    $objeto->set_nome("Natureza");

    # Botão de voltar da lista
    $objeto->set_voltarLista("cadastroContrato.php");

    # select da lista
    $objeto->set_selectLista("SELECT  codigo,
                                      natureza,
                                      obs,
                                      idNatureza
                                 FROM tbnatureza
                             ORDER BY natureza");

    # select do edita
    $objeto->set_selectEdita("SELECT codigo,
                                     natureza,
                                     obs
                                FROM tbnatureza
                              WHERE idNatureza = {$id}");

    # Caminhos
    $objeto->set_linkEditar("?fase=editar");
    $objeto->set_linkExcluir("?fase=excluir");
    $objeto->set_linkGravar("?fase=gravar");
    $objeto->set_linkListar("?fase=listar");

    # Parametros da tabela
    $objeto->set_label(array("Código", "Natureza", "Obs"));
    $objeto->set_width(array(15, 20, 50));
    $objeto->set_align(array("center", "center", "left"));

    # Classe do banco de dados
    $objeto->set_classBd("Contratos");

    # Nome da tabela
    $objeto->set_tabela("tbnatureza");

    # Nome do campo id
    $objeto->set_idCampo("idNatureza");

    # Tipo de label do formulário
    $objeto->set_formLabelTipo(1);

    # Campos para o formulario
    $objeto->set_campos(array(
        array("linha" => 1,
            "nome" => "codigo",
            "label" => "Código:",
            "tipo" => "texto",
            "autofocus" => true,
            "col" => 3,
            "size" => 10),
        array("linha" => 1,
            "nome" => "natureza",
            "label" => "Natureza:",
            "tipo" => "texto",
            "required" => true,
            "autofocus" => true,
            "col" => 9,
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
            $pagamento = new Pagamento();
            $getNumPgtoNatureza = $pagamento->getNumPgtoNatureza($id);

            if ($getNumPgtoNatureza > 0) {
                alert("Existem pagamentos cadastrados com esta natureza. Dessa forma a mesma NÃO poderá ser excluída.");
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
