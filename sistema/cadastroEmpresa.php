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

    # Começa uma nova página
    $page = new Page();
    $page->iniciaPagina();

    # Cabeçalho da Página
    AreaServidor::cabecalho();

    # Abre um novo objeto Modelo
    $objeto = new Modelo();

    ################################################################
    # Nome do Modelo
    $objeto->set_nome("Cadastro de Empresas");

    # Botão de voltar da lista
    $objeto->set_voltarLista("areaInicial.php");

    # select da lista
    $objeto->set_selectLista("SELECT idEmpresa,
                                      nome,
                                      razaoSocial,
                                      cnpj
                                 FROM tbempresa
                             ORDER BY idEmpresa");

    # select do edita
    $objeto->set_selectEdita("SELECT razaoSocial,
                                     nome,
                                     cnpj,
                                     endereco,
                                     bairro,
                                     idCidade,
                                     cep,
                                     obs
                                FROM tbempresa
                              WHERE idEmpresa = {$id}");

    # Caminhos
    $objeto->set_linkEditar("?fase=editar");
    $objeto->set_linkExcluir("?fase=excluir");
    $objeto->set_linkGravar("?fase=gravar");
    $objeto->set_linkListar("?fase=listar");

    # Parametros da tabela
    $objeto->set_label(array("Id", "Nome", "Razão Social", "CNPJ"));
    #$objeto->set_width(array(5,40,45));
    $objeto->set_align(array("center", "left", "left"));

    # Classe do banco de dados
    $objeto->set_classBd("Contratos");

    # Nome da tabela
    $objeto->set_tabela("tbempresa");

    # Nome do campo id
    $objeto->set_idCampo("idEmpresa");

    # Tipo de label do formulário
    $objeto->set_formlabelTipo(1);

    # Pega os dados da combo de cidade
    $cidade = $pessoal->select('SELECT idCidade,
                                       CONCAT(tbcidade.nome," (",tbestado.uf,")")
                                  FROM tbcidade JOIN tbestado USING (idEstado)
                              ORDER BY proximidade,tbestado.uf,tbcidade.nome');
    array_unshift($cidade, array(NULL, NULL)); # Adiciona o valor de nulo
    # Campos para o formulario
    $objeto->set_campos(array(
        array('linha' => 1,
            'nome' => 'razaoSocial',
            'label' => 'Razão Social:',
            'tipo' => 'texto',
            'required' => true,
            'autofocus' => TRUE,
            'col' => 12,
            'size' => 250),
        array('linha' => 2,
            'nome' => 'nome',
            'label' => 'Nome:',
            'tipo' => 'texto',
            'required' => true,
            'col' => 8,
            'size' => 200),
        array('linha' => 2,
            'nome' => 'cnpj',
            'label' => 'CNPJ:',
            'tipo' => 'texto',
            'required' => true,
            'col' => 4,
            'size' => 30),
        array('linha' => 3,
            'nome' => 'endereco',
            'label' => 'Endereço:',
            'tipo' => 'texto',
            'plm' => TRUE,
            'title' => 'Endereço do Servidor',
            'col' => 12,
            'size' => 150),
        array('linha' => 4,
            'nome' => 'bairro',
            'label' => 'Bairro:',
            'tipo' => 'texto',
            'title' => 'Bairro',
            'plm' => TRUE,
            'col' => 4,
            'size' => 50),
        array('linha' => 4,
            'nome' => 'idCidade',
            'label' => 'Cidade:',
            'tipo' => 'combo',
            'array' => $cidade,
            'title' => 'Cidade de Moradia do Servidor',
            'col' => 5,
            'size' => 30),
        array('linha' => 4,
            'nome' => 'cep',
            'label' => 'Cep:',
            'tipo' => 'cep',
            'title' => 'Cep',
            'col' => 3,
            'size' => 10),
        array('linha' => 5,
            'nome' => 'obs',
            'label' => 'Observação:',
            'tipo' => 'textarea',
            'size' => array(80, 5))));

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
    }

    $page->terminaPagina();
} else {
    loadPage("../../areaServidor/sistema/login.php");
}