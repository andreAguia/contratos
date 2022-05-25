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
$acesso = Verifica::acesso($idUsuario,  [1, 9]);

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
    $objeto->set_nome("Modalidades de Contrato");

    # Botão de voltar da lista
    $objeto->set_voltarLista("cadastroContrato.php");

    # select da lista
    $objeto->set_selectLista("SELECT idModalidade,
                                      tipo,
                                      modalidade,
                                      obs,
                                      idModalidade,
                                      idModalidade
                                 FROM tbmodalidade
                             ORDER BY tipo, modalidade");

    # select do edita
    $objeto->set_selectEdita("SELECT tipo,
                                     modalidade,
                                     obs
                                FROM tbmodalidade
                              WHERE idModalidade = {$id}");

    # Caminhos
    $objeto->set_linkEditar("?fase=editar");
    $objeto->set_linkExcluir("?fase=excluir");
    $objeto->set_linkGravar("?fase=gravar");
    $objeto->set_linkListar("?fase=listar");

    # Parametros da tabela
    $objeto->set_label(array("Id", "Tipo", "Modalidade", "Obs", "Contratos"));
    $objeto->set_width(array(5, 10, 20, 50, 5));
    $objeto->set_align(array("center", "center", "left", "left", "center"));
    $objeto->set_classe(array(null, null, null, null, "Modalidade"));
    $objeto->set_metodo(array(null, null, null, null, "getNumContratos"));
    
    $formatacaoCondicional = array(
        array('coluna'   => 1,
            'valor'    => "Despesa",
            'operador' => '=',
            'id'       => 'trModalidadeDespesa'),
        array('coluna'   => 1,
            'valor'    => "Receita",
            'operador' => '=',
            'id'       => 'trModalidadeReceita'),
    );
    $objeto->set_formatacaoCondicional($formatacaoCondicional);

    # Classe do banco de dados
    $objeto->set_classBd("Contratos");

    # Nome da tabela
    $objeto->set_tabela("tbmodalidade");

    # Nome do campo id
    $objeto->set_idCampo("idModalidade");

    # Tipo de label do formulário
    $objeto->set_formlabelTipo(1);

    # Campos para o formulario
    $objeto->set_campos(array(
        array(
            'linha' => 1,
            'nome' => 'tipo',
            'label' => 'Tipo:',
            'tipo' => 'combo',
            'padrao' => "Despesa",
            'array' => ["Despesa", "Receita"],
            'col' => 2,
            'size' => 5),
        array(
            "linha" => 1,
            "nome" => "modalidade",
            "label" => "Modalidade:",
            "tipo" => "texto",
            "required" => true,
            "autofocus" => true,
            "col" => 6,
            "size" => 100),
        array(
            "linha" => 2,
            "nome" => "obs",
            "label" => "Observação:",
            "tipo" => "textarea",
            "size" => array(80, 5))));

    # idUsuário para o Log
    $objeto->set_idUsuario($idUsuario);

    ################################################################
    switch ($fase)
    {
        case "":
        case "listar":
            $objeto->listar();
            break;

        ################################################################    

        case "excluir" :
            # Verifica se tem contrato com essa modalidade
            $numContratos = $modalidade->getNumContratos($id);

            if ($numContratos > 0) {
                alert("Existem contratos cadastrados com esta modalidade. Dessa forma a mesma NÃO poderá ser excluída.");
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
