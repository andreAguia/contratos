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
    $pessoal = new Pessoal();

    # log
    if (get('i', false)) {
        # Grava no log a atividade
        $atividade = "Acessou o cadastro de modalidade.";
        $data = date("Y-m-d H:i:s");
        $intra->registraLog($idUsuario, $data, $atividade, null, null, 7);
    }

    # Verifica a fase do programa
    $fase = get("fase", "listar");

    # Pega o treinamento
    $idTreinamento = get_session('idTreinamento');

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
    $objeto->set_nome("Servidores que Fizeram Esse Treinamento");

    # Botão de voltar da lista
    $objeto->set_voltarLista("cadastroTreinamento.php");

    # select da lista
    $objeto->set_selectLista("SELECT idServidor,
                                     idServidor,
                                     idTreinamentoServidor
                                FROM tbtreinamentoservidor
                               WHERE idTreinamento = {$idTreinamento}
                            ORDER BY idTreinamentoServidor");

    # select do edita
    $objeto->set_selectEdita("SELECT idServidor,
                                     idTreinamento
                                FROM tbtreinamentoservidor
                              WHERE idTreinamentoServidor = {$id}");

    # Caminhos
    $objeto->set_linkEditar("?fase=editar");
    $objeto->set_linkExcluir("?fase=excluir");
    $objeto->set_linkGravar("?fase=gravar");
    $objeto->set_linkListar("?fase=listar");

    # Parametros da tabela
    $objeto->set_label(["Foto", "Servidor"]);
    $objeto->set_width([15, 70]);
    $objeto->set_align(["center", "left"]);
    $objeto->set_classe(["Pessoal", "Pessoal"]);
    $objeto->set_metodo(["get_foto", "get_nomeECargoELotacaoEPerfilESituacao"]);

    # Classe do banco de dados
    $objeto->set_classBd("Contratos");

    # Nome da tabela
    $objeto->set_tabela("tbtreinamentoservidor");

    # Nome do campo id
    $objeto->set_idCampo("idTreinamentoServidor");

    # Tipo de label do formulário
    $objeto->set_formLabelTipo(1);

    # combo membro servidor
    $membro = $pessoal->select('SELECT idServidor,
                                       CONCAT(tbpessoa.nome," | ",IFNULL(tbtipocargo.sigla,"")," - ",IFNULL(tbcargo.nome,"")," | ",uenf_grh.tbsituacao.situacao)
                                  FROM uenf_grh.tbservidor JOIN uenf_grh.tbpessoa USING (idPessoa)
                                                      LEFT JOIN uenf_grh.tbsituacao ON (uenf_grh.tbservidor.situacao = uenf_grh.tbsituacao.idsituacao)
                                                      LEFT JOIN uenf_grh.tbcargo USING (idCargo)
                                                      LEFT JOIN uenf_grh.tbtipocargo USING (idTipoCargo)
                                 WHERE (idPerfil = 1 OR idPerfil = 2 OR idPerfil = 3 OR idPerfil = 4)
                              ORDER BY uenf_grh.tbsituacao.idSituacao, tbpessoa.nome');
    array_unshift($membro, array(null, null));

    # Campos para o formulario
    $objeto->set_campos(array(
        array(
            'linha' => 1,
            'nome' => 'idServidor',
            'label' => 'Servidor da Uenf:',
            'tipo' => 'combo',
            'array' => $membro,
            'title' => 'Servidor da Uenf membro da comissão',
            'col' => 6,
            "autofocus" => true,
            'size' => 30),
        array(
            "linha" => 1,
            "nome" => "idTReinamento",
            "label" => "TReinamento:",
            "tipo" => "hidden",
            "padrao" => $idTreinamento,
            "required" => true,
            'size' => 30,
            "col" => 3)));

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
