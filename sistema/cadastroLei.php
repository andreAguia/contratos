<?php

/**
 * Cadastro da lei do Contrato
 *  
 * By Alat
 */
# Reservado para o servidor logado
$idUsuario = null;

# Configuração
include ("_config.php");

# Permissão de Acesso
$acesso = Verifica::acesso($idUsuario, [1, 9]);

if ($acesso) {
    # Conecta ao Banco de Dados
    $intra = new Intra();
    $contrato = new Contrato();

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
    $objeto->set_nome("Lei");

    # Botão de voltar da lista
    $objeto->set_voltarLista("cadastroContrato.php");

    # select da lista
    $objeto->set_selectLista("SELECT idLei,                                      
                                     dtPublicacao,
                                     lei,
                                     idLei,
                                     obs
                                FROM tblei
                            ORDER BY dtPublicacao DESC");

    # select do edita
    $objeto->set_selectEdita("SELECT idLei,                                      
                                     lei,
                                     dtPublicacao,
                                     obs
                                FROM tblei
                              WHERE idLei = {$id}");

    # Caminhos
    $objeto->set_linkEditar("?fase=editar");
    $objeto->set_linkExcluir("?fase=excluir");
    $objeto->set_linkGravar("?fase=gravar");
    $objeto->set_linkListar("?fase=listar");

    # Parametros da tabela
    $objeto->set_label(["id", "Publicação", "Lei", "Ver", "Obs"]);
    $objeto->set_width([5, 10, 30, 5, 40]);
    $objeto->set_align(["center", "center", "left", "center", "left"]);
    $objeto->set_funcao([null, "date_to_php"]);

    $objeto->set_classe([null, null, null, "contrato"]);
    $objeto->set_metodo([null, null, null, "exibePdfLei"]);

    # Classe do banco de dados
    $objeto->set_classBd("Contratos");

    # Nome da tabela
    $objeto->set_tabela("tblei");

    # Nome do campo id
    $objeto->set_idCampo("idLei");

    # Tipo de label do formulário
    $objeto->set_formLabelTipo(1);

    # Campos para o formulario
    $objeto->set_campos(array(
        array(
            "linha" => 1,
            'col' => 5,
            "nome" => "lei",
            "label" => "Lei:",
            'required' => true,
            'autofocus' => true,
            "tipo" => "texto",
            "size" => 200),
        array(
            'linha' => 1,
            'nome' => 'dtPublicacao',
            'label' => 'Publicado no DOERJ em:',
            'tipo' => 'date',
            'col' => 3,
            'size' => 15),
        array(
            'linha' => 2,
            'nome' => 'obs',
            'label' => 'Observação:',
            'tipo' => 'textarea',
            'size' => array(80, 5))
    ));

    # idUsuário para o Log
    $objeto->set_idUsuario($idUsuario);

    # Dados da rotina de Upload
    $pasta = PASTA_LEI;
    $nome = "Lei";
    $tabela = "tblei";
    $extensoes = ["pdf"];

    # Botão de Upload
    if (!empty($id)) {

        # Botão de Upload
        $botao = new Button("Upload {$nome}");
        $botao->set_url("cadastroLeiUpload.php?fase=upload&id={$id}");
        $botao->set_title("Faz o Upload da {$nome}");
        $botao->set_target("_blank");

        $objeto->set_botaoEditarExtra([$botao]);
    }

    ################################################################

    switch ($fase) {
        case "" :
        case "listar" :
            $objeto->listar();
            break;

        case "editar" :
        case "excluir" :
        case "gravar" :
            $objeto->$fase($id);
            break;
    }

    $page->terminaPagina();
} else {
    loadPage("../../areaServidor/sistema/login.php");
}