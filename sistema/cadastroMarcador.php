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
$acesso = Verifica::acesso($idUsuario, [1, 9]);

if ($acesso) {

    # log
    if (get('i', false)) {
        # Grava no log a atividade
        $atividade = "Acessou o cadastro de marcadores.";
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
    $objeto->set_nome("Marcadores");

    # Botão de voltar da lista
    $objeto->set_voltarLista("cadastroContrato.php");

    # select da lista
    $objeto->set_selectLista("SELECT idMarcador,
                                     idMarcador,
                                     obs,
                                     idMarcador
                                FROM tbmarcador
                            ORDER BY marcador");

    # select do edita
    $objeto->set_selectEdita("SELECT marcador,
                                     cor,
                                     obs
                                FROM tbmarcador
                              WHERE idMarcador = {$id}");

    # Caminhos
    $objeto->set_linkEditar("?fase=editar");
    $objeto->set_linkExcluir("?fase=excluir");
    $objeto->set_linkGravar("?fase=gravar");
    $objeto->set_linkListar("?fase=listar");

    # Parametros da tabela
    $objeto->set_label(["Id", "Marcador", "Obs", "Contratos"]);
    $objeto->set_width([5, 20, 60, 5]);
    $objeto->set_align(["center", "center", "left"]);
    $objeto->set_classe([null, "Marcador", null, "Marcador"]);
    $objeto->set_metodo([null, "exibe_marcador", null, "get_numContratos"]);

    # Classe do banco de dados
    $objeto->set_classBd("Contratos");

    # Nome da tabela
    $objeto->set_tabela("tbmarcador");

    # Nome do campo id
    $objeto->set_idCampo("idMarcador");

    # Tipo de label do formulário
    $objeto->set_formLabelTipo(1);

    # Campos para o formulario
    $objeto->set_campos(array(
        array(
            "linha" => 1,
            "nome" => "marcador",
            "label" => "Marcador:",
            "tipo" => "texto",
            "required" => true,
            "autofocus" => true,
            "col" => 6,
            "size" => 100),
        array(
            'linha' => 1,
            'nome' => 'cor',
            'label' => 'Cor:',
            'tipo' => 'combo',
            'padrao' => "Despesa",
            'array' => [
                ["primary", "Azul"],
                ["secondary", "Cinza"],
                ["success", "Verde"],
                ["alert", "Vermelho"],
                ["warning", "Laranja"],
            ],
            'col' => 2,
            'size' => 5),
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
            # Verifica se tem contrato com esse marcador
            $marcador = new Marcador();
            $numContratos = $marcador->get_numContratos($id);

            if ($numContratos > 0) {
                alert("Existem contratos cadastrados com este Marcador. Dessa forma, o mesmo NÃO poderá ser excluído.");
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
