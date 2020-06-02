<?php

/**
 * Cadastro de Campus
 *  
 * By Alat
 */
# Reservado para o servidor logado
$idUsuario = NULL;

# Configuração
include ("_config.php");

# Permissão de Acesso
$acesso = Verifica::acesso($idUsuario, 9);

if ($acesso) {
    # Conecta ao Banco de Dados
    $intra = new Intra();
    $contrato = new Contrato();
    $pessoal = new Pessoal();

    # Verifica a fase do programa
    $fase = get("fase", "listar");

    # pega o id (se tiver)
    $id = soNumeros(get("id"));

    # pega o contrato
    $idContrato = soNumeros(get_session('sessionContrato'));

    # Começa uma nova página
    $page = new Page();
    $page->iniciaPagina();

    # Cabeçalho da Página
    AreaServidor::cabecalho();

    # Abre um novo objeto Modelo
    $objeto = new Modelo();

    ################################################################
    # Exibe os dados do Servidor
    $objeto->set_rotinaExtra("get_DadosContrato");
    $objeto->set_rotinaExtraParametro($idContrato);
    
    # Nome do Modelo
    $objeto->set_nome("Comissão de Fiscalização");

    # Botão de voltar da lista
    $objeto->set_voltarLista("areaContrato.php");

    # select da lista
    $objeto->set_selectLista("SELECT tipo,
                                      idServidor,
                                      idServidor,
                                      idServidor,
                                      idComissao,
                                      idComissao
                                 FROM tbcomissao
                                WHERE idContrato = {$idContrato}
                             ORDER BY tipo");

    # select do edita
    $objeto->set_selectEdita("SELECT idServidor,
                                     tipo,
                                     portariaEntrada,
                                     dtPortariaEntrada,
                                     dtPublicacaoEntrada,
                                     portariaSaida,
                                     dtPortariaSaida,
                                     dtPublicacaoSaida,
                                     obs,
                                     idContrato,
                                     idComissao
                                FROM tbcomissao
                              WHERE idComissao = {$id}");

    # Caminhos
    $objeto->set_linkEditar("?fase=editar");
    $objeto->set_linkExcluir("?fase=excluir");
    $objeto->set_linkGravar("?fase=gravar");
    $objeto->set_linkListar("?fase=listar");

    # Parametros da tabela
    $objeto->set_label(array("Tipo", "Nome", "Cargo", "Lotação", "Designado em"));
    $objeto->set_align(array("center", "left", "left", "left", "left"));
    $objeto->set_width(array(5, 20, 20, 20, 25));
    $objeto->set_classe(array("Comissao", "pessoal", "pessoal", "pessoal", "Comissao"));
    $objeto->set_metodo(array("get_tipo", "get_nome", "get_cargo", "get_lotacao", "get_portariaEntrada"));
    $objeto->set_numeroOrdem(true);

    # Classe do banco de dados
    $objeto->set_classBd("Contratos");

    # Nome da tabela
    $objeto->set_tabela("tbcomissao");

    # Nome do campo id
    $objeto->set_idCampo("idComissao");

    # Tipo de label do formulário
    $objeto->set_formlabelTipo(1);

    # Pega os dados da combo de servidor
    $membro = $pessoal->select('SELECT idServidor, 
                                       CONCAT(tbpessoa.nome," | ",IFNULL(tbtipocargo.sigla,"")," - ",IFNULL(tbcargo.nome,"")," | ",uenf_grh.tbsituacao.situacao)
                                  FROM uenf_grh.tbservidor JOIN uenf_grh.tbpessoa USING (idPessoa)
                                                      LEFT JOIN uenf_grh.tbsituacao ON (uenf_grh.tbservidor.situacao = uenf_grh.tbsituacao.idsituacao)
                                                      LEFT JOIN uenf_grh.tbcargo USING (idCargo)
                                                      LEFT JOIN uenf_grh.tbtipocargo USING (idTipoCargo)
                                 WHERE (idPerfil = 1 OR idPerfil = 4)
                              ORDER BY uenf_grh.tbsituacao.idSituacao, tbpessoa.nome');
    array_unshift($membro, array(NULL, NULL)); # Adiciona o valor de nulo
    # Dados da combo tipo
    $tipo = array(
        array(null, null),
        array(1, "Presidente"),
        array(2, "Membro"),
        array(3, "Suplente")
    );

    # Campos para o formulario
    $objeto->set_campos(array(
        array('linha' => 1,
            'nome' => 'idServidor',
            'label' => 'Membro:',
            'tipo' => 'combo',
            'array' => $membro,
            'title' => 'Servidor membro ca comissão',
            'col' => 9,
            'required' => true,
            'size' => 30),
        array('linha' => 1,
            'nome' => 'tipo',
            'label' => 'Tipo:',
            'tipo' => 'combo',
            'array' => $tipo,
            'padrao' => 2,
            'col' => 3,
            'size' => 15),
        array('linha' => 2,
            'nome' => 'portariaEntrada',
            'label' => 'Portaria Número:',
            'tipo' => 'texto',
            'col' => 2,
            'size' => 10),
        array('linha' => 2,
            'nome' => 'dtPortariaEntrada',
            'label' => 'De:',
            'tipo' => 'date',
            'col' => 3,
            'size' => 15),
        array('linha' => 2,
            'nome' => 'dtPublicacaoEntrada',
            'label' => 'Publicado no DOERJ em:',
            'tipo' => 'date',
            'col' => 3,
            'size' => 15),
        array('linha' => 3,
            'nome' => 'portariaSaida',
            'label' => 'Portaria Número:',
            'tipo' => 'texto',
            'col' => 2,
            'size' => 10),
        array('linha' => 3,
            'nome' => 'dtPortariaSaida',
            'label' => 'De:',
            'tipo' => 'date',
            'col' => 3,
            'size' => 15),
        array('linha' => 3,
            'nome' => 'dtPublicacaoSaida',
            'label' => 'Publicado no DOERJ em:',
            'tipo' => 'date',
            'col' => 3,
            'size' => 15),
        array('linha' => 4,
            'nome' => 'obs',
            'label' => 'Observação:',
            'tipo' => 'textarea',
            'size' => array(80, 5)),
        array("linha" => 5,
            "nome" => "idContrato",
            "label" => "idContrato:",
            'tipo' => 'hidden',
            'padrao' => $idContrato,
            "col" => 3,
            "size" => 11)
    ));

    # idUsuário para o Log
    $objeto->set_idUsuario($idUsuario);

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
        
        case "exibeFicha" :
            botaoVoltar("areaContrato.php");
            # Exibe dados do contrato
            get_DadosContrato($idContrato);
            break;
            
    }

    $page->terminaPagina();
} else {
    loadPage("../../areaServidor/sistema/login.php");
}