<?php

/**
 * Cadastro de Campus
 *  
 * By Alat
 */
# Reservado para o servidor logado
$idUsuario = null;

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
                                     idEmpresa,
                                     idEmpresa,
                                     idEmpresa,
                                     idEmpresa,
                                     usuarioSei
                                FROM tbempresa
                            ORDER BY idEmpresa");

    # select do edita
    $objeto->set_selectEdita("SELECT razaoSocial,
                                     cnpj,                                     
                                     telefone1,
                                     telefone2,
                                     telefone3,
                                     email1,
                                     email2,
                                     email3,
                                     contato,
                                     usuarioSei,
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
    $objeto->set_label(array("Id", "Empresa", "Telefone", "Email", "Contatos"));
    $objeto->set_width(array(5,30,15,15,20));
    $objeto->set_align(array("center", "left", "left", "left", "left"));
    $objeto->set_classe(array(null, "Empresa", "Empresa", "Empresa","Empresa"));
    $objeto->set_metodo(array(null, "get_empresaCnpj", "get_telefones", "get_emails","get_contatos"));

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
    array_unshift($cidade, array(null, null)); # Adiciona o valor de nulo
    # Campos para o formulario
    $objeto->set_campos(array(
        array('linha'     => 1,
            'nome'      => 'razaoSocial',
            'label'     => 'Razão Social:',
            'tipo'      => 'texto',
            'required'  => true,
            'autofocus' => true,
            'plm'   => true,
            'col'       => 8,
            'size'      => 250),
        array('linha'    => 1,
            'nome'     => 'cnpj',
            'label'    => 'CNPJ:',
            'tipo'     => 'texto',
            'required' => true,
            'col'      => 4,
            'size'     => 30),
        array('linha' => 3,
            'nome'  => 'telefone1',
            'label' => 'Telefone 1:',
            'tipo'  => 'texto',
            'title' => 'Número de telefone',
            'col'   => 4,
            'size'  => 100),
        array('linha' => 3,
            'nome'  => 'telefone2',
            'label' => 'Telefone 2:',
            'tipo'  => 'texto',
            'title' => 'Número de telefone',
            'col'   => 4,
            'size'  => 100),
        array('linha' => 3,
            'nome'  => 'telefone3',
            'label' => 'Telefone 3:',
            'tipo'  => 'texto',
            'title' => 'Número de telefone',
            'col'   => 4,
            'size'  => 100),
        array('linha' => 4,
            'nome'  => 'email1',
            'label' => 'Email 1:',
            'tipo'  => 'texto',
            'title' => 'Email da empresa',
            'col'   => 4,
            'size'  => 100),
        array('linha' => 4,
            'nome'  => 'email2',
            'label' => 'Email 2:',
            'tipo'  => 'texto',
            'title' => 'Email da empresa',
            'col'   => 4,
            'size'  => 100),
        array('linha' => 4,
            'nome'  => 'email3',
            'label' => 'Email 3:',
            'tipo'  => 'texto',
            'title' => 'Email da empresa',
            'col'   => 4,
            'size'  => 100),
        array('linha' => 5,
            'nome'  => 'contato',
            'label' => 'Contato na Empresa:',
            'tipo'  => 'texto',
            'title' => 'Contato',
            'col'   => 6,
            'size'  => 150),
        array('linha' => 5,
            'nome'  => 'usuarioSei',
            'label' => 'Pessoa da empresa cadastrada no SEI:',
            'tipo'  => 'texto',
            'title' => 'Contato',
            'col'   => 6,
            'size'  => 150),
        array('linha' => 5,
            'nome'  => 'endereco',
            'label' => 'Endereço:',
            'tipo'  => 'texto',
            'plm'   => true,
            'title' => 'Endereço da Empresa',
            'col'   => 12,
            'size'  => 150),
        array('linha' => 6,
            'nome'  => 'bairro',
            'label' => 'Bairro:',
            'tipo'  => 'texto',
            'title' => 'Bairro',
            'plm'   => true,
            'col'   => 4,
            'size'  => 50),
        array('linha' => 6,
            'nome'  => 'idCidade',
            'label' => 'Cidade:',
            'tipo'  => 'combo',
            'array' => $cidade,
            'title' => 'Cidade de Moradia do Servidor',
            'col'   => 5,
            'size'  => 30),
        array('linha' => 6,
            'nome'  => 'cep',
            'label' => 'Cep:',
            'tipo'  => 'cep',
            'title' => 'Cep',
            'col'   => 3,
            'size'  => 10),
        array('linha' => 7,
            'nome'  => 'obs',
            'label' => 'Observação:',
            'tipo'  => 'textarea',
            'size'  => array(80, 5))));

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

        case "ver" :
            # Limita a tela
            $grid = new Grid();
            $grid->abreColuna(12);

            # Cria um menu
            $menu1 = new MenuBar();

            # Voltar
            $botaoVoltar = new Link("Voltar", "areaContrato.php");
            $botaoVoltar->set_class('button');
            $botaoVoltar->set_title('Voltar a página anterior');
            $botaoVoltar->set_accessKey('V');
            $menu1->add_link($botaoVoltar, "left");

            # Editar
            $botaoEditar = new Link("Editar", "cadastroContrato.php?fase=editar&id={$id}");
            $botaoEditar->set_class('button');
            $botaoEditar->set_title('Editar Empresa');
            $menu1->add_link($botaoEditar, "right");

            $menu1->show();

            $grid->fechaColuna();
            $grid->fechaGrid();
            break;
    }

    $page->terminaPagina();
}
else {
    loadPage("../../areaServidor/sistema/login.php");
}