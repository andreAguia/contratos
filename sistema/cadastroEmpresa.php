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
    # Conecta ao Banco de Dados
    $empresa = new Empresa();
    $pessoal = new Pessoal();

    # log
    if (get('i', false)) {
        # Grava no log a atividade
        $atividade = "Acessou o cadastro de empresas.";
        $data = date("Y-m-d H:i:s");
        $intra->registraLog($idUsuario, $data, $atividade, null, null, 7);
    }

    # Verifica a fase do programa
    $fase = get("fase");

    # pega o id (se tiver)
    $id = soNumeros(get("id"));
    $sessionContrato = get_session('sessionContrato');

    # Pega o parametro de pesquisa (se tiver)
    if (is_null(post('parametro'))) { # Se o parametro n?o vier por post (for nulo)
        $parametro = retiraAspas(get_session('sessionParametro')); # passa o parametro da session para a variavel parametro retirando as aspas
    } else {
        $parametro = post('parametro'); # Se vier por post, retira as aspas e passa para a variavel parametro
        set_session('sessionParametro', $parametro); # transfere para a session para poder recuperá-lo depois
    }

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
    $objeto->set_voltarLista("cadastroContrato.php");

    # controle de pesquisa
    $objeto->set_parametroLabel('Pesquisar');
    $objeto->set_parametroValue($parametro);

    # select da lista
    $objeto->set_selectLista("SELECT idEmpresa,
                                     idEmpresa,
                                     idEmpresa,
                                     idEmpresa,
                                     obs,
                                     idEmpresa
                                FROM tbempresa
                               WHERE razaosocial LIKE '%{$parametro}%'
                                  OR cnpj LIKE '%{$parametro}%'
                                  OR telefone1 LIKE '%{$parametro}%'
                                  OR telefone2 LIKE '%{$parametro}%'
                                  OR telefone3 LIKE '%{$parametro}%'
                                  OR email1 LIKE '%{$parametro}%'
                                  OR email2 LIKE '%{$parametro}%'
                                  OR email3 LIKE '%{$parametro}%'
                                  OR contato LIKE '%{$parametro}%'
                                  OR usuarioSei LIKE '%{$parametro}%'
                            ORDER BY razaosocial");

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
                                     preposto,
                                     endereco,
                                     bairro,
                                     idCidade,
                                     cep,
                                     representanteNome,
                                     representanteCpf,
                                     representanteIdentidade,
                                     representanteEndereco,
                                     representanteBairro,
                                     representanteIdCidade,
                                     representanteCep,
                                     obs
                                FROM tbempresa
                              WHERE idEmpresa = {$id}");

    # Caminhos
    $objeto->set_linkEditar("?fase=editar");
    $objeto->set_linkExcluir("?fase=excluir");
    $objeto->set_linkGravar("?fase=gravar");

    if (!empty($sessionContrato)) {
        $objeto->set_voltarForm("cadastroAditivo.php");
        $objeto->set_linkListar("cadastroAditivo.php");
    } else {
        $objeto->set_linkListar("?fase=listar");
    }

    # Parametros da tabela
    $objeto->set_label(array("Empresa", "Telefone", "Email", "Contatos", "Obs", "Contratos"));
    $objeto->set_width(array(25, 10, 15, 20, 10, 5));
    $objeto->set_align(array("left", "left", "left", "left", "left"));
    $objeto->set_classe(array("Empresa", "Empresa", "Empresa", "Empresa", null, "Empresa"));
    $objeto->set_metodo(array("getEmpresaCnpj", "getTelefones", "getEmails", "getContatos", null, "getNumContratos"));
    $objeto->set_funcao(array(null, null, null, null, "nl2br2"));

    # Classe do banco de dados
    $objeto->set_classBd("Contratos");

    # Nome da tabela
    $objeto->set_tabela("tbempresa");

    # Nome do campo id
    $objeto->set_idCampo("idEmpresa");

    # Tipo de label do formulário
    $objeto->set_formLabelTipo(1);

    # Pega os dados da combo de cidade
    $cidade = $pessoal->select('SELECT idCidade,
                                       CONCAT(tbcidade.nome," (",tbestado.uf,")")
                                  FROM tbcidade JOIN tbestado USING (idEstado)
                              ORDER BY proximidade,tbestado.uf,tbcidade.nome');
    array_unshift($cidade, array(null, null)); # Adiciona o valor de nulo
    # Campos para o formulario
    $objeto->set_campos(array(
        array('linha' => 1,
            'nome' => 'razaoSocial',
            'label' => 'Razão Social:',
            'tipo' => 'texto',
            'required' => true,
            'autofocus' => true,
            'col' => 8,
            'size' => 250),
        array('linha' => 1,
            'nome' => 'cnpj',
            'label' => 'CNPJ:',
            'tipo' => 'texto',
            'required' => true,
            'col' => 4,
            'size' => 30),
        array('linha' => 3,
            'nome' => 'telefone1',
            'label' => 'Telefone 1:',
            'tipo' => 'texto',
            'title' => 'Número de telefone',
            'col' => 4,
            'size' => 100),
        array('linha' => 3,
            'nome' => 'telefone2',
            'label' => 'Telefone 2:',
            'tipo' => 'texto',
            'title' => 'Número de telefone',
            'col' => 4,
            'size' => 100),
        array('linha' => 3,
            'nome' => 'telefone3',
            'label' => 'Telefone 3:',
            'tipo' => 'texto',
            'title' => 'Número de telefone',
            'col' => 4,
            'size' => 100),
        array('linha' => 4,
            'nome' => 'email1',
            'label' => 'Email 1:',
            'tipo' => 'texto',
            'title' => 'Email da empresa',
            'col' => 4,
            'size' => 100),
        array('linha' => 4,
            'nome' => 'email2',
            'label' => 'Email 2:',
            'tipo' => 'texto',
            'title' => 'Email da empresa',
            'col' => 4,
            'size' => 100),
        array('linha' => 4,
            'nome' => 'email3',
            'label' => 'Email 3:',
            'tipo' => 'texto',
            'title' => 'Email da empresa',
            'col' => 4,
            'size' => 100),
        array('linha' => 5,
            'nome' => 'contato',
            'label' => 'Contato na Empresa:',
            'tipo' => 'texto',
            'title' => 'Contato',
            'col' => 4,
            'size' => 150),
        array('linha' => 5,
            'nome' => 'usuarioSei',
            'label' => 'Pessoa da empresa cadastrada no SEI:',
            'tipo' => 'texto',
            'title' => 'Pessoa da empresa cadastrada no SEI',
            'col' => 4,
            'size' => 150),
        array('linha' => 5,
            'nome' => 'preposto',
            'label' => 'Preposto na Uenf:',
            'tipo' => 'texto',
            'title' => 'Pessoa da empresa dentro da Uenf',
            'col' => 4,
            'size' => 150),
        array('linha' => 5,
            'nome' => 'endereco',
            'label' => 'Endereço:',
            'tipo' => 'texto',
            'plm' => true,
            'title' => 'Endereço da Empresa',
            'col' => 12,
            'size' => 150),
        array('linha' => 6,
            'nome' => 'bairro',
            'label' => 'Bairro:',
            'tipo' => 'texto',
            'title' => 'Bairro',
            'plm' => true,
            'col' => 4,
            'size' => 50),
        array('linha' => 6,
            'nome' => 'idCidade',
            'label' => 'Cidade:',
            'tipo' => 'combo',
            'array' => $cidade,
            'title' => 'Cidade',
            'col' => 5,
            'size' => 30),
        array('linha' => 6,
            'nome' => 'cep',
            'label' => 'Cep:',
            'tipo' => 'cep',
            'title' => 'Cep',
            'col' => 3,
            'size' => 10),
        array('linha' => 7,
            'nome' => 'representanteNome',
            'label' => 'Nome do Representante Legal:',
            'tipo' => 'texto',
            'title' => 'Nome do represntante legal da empresa',
            'fieldset' => 'Representante Legal',
            'col' => 4,
            'size' => 250),
        array('linha' => 7,
            'nome' => 'representanteCpf',
            'label' => 'Cpf do Representante Legal:',
            'tipo' => 'cpf',
            'title' => 'Cpf do represntante legal da empresa',
            'col' => 4,
            'size' => 15),
        array('linha' => 7,
            'nome' => 'representanteIdentidade',
            'label' => 'Identidade do Representante Legal:',
            'tipo' => 'texto',
            'title' => 'Identidade do represntante legal da empresa',
            'col' => 4,
            'size' => 30),
        array('linha' => 8,
            'nome' => 'representanteEndereco',
            'label' => 'Endereço do Representante Legal:',
            'tipo' => 'texto',
            'plm' => true,
            'title' => 'Endereço da Empresa',
            'col' => 12,
            'size' => 150),
        array('linha' => 9,
            'nome' => 'representanteBairro',
            'label' => 'Bairro:',
            'tipo' => 'texto',
            'title' => 'Bairro',
            'plm' => true,
            'col' => 4,
            'size' => 50),
        array('linha' => 9,
            'nome' => 'representanteIdCidade',
            'label' => 'Cidade:',
            'tipo' => 'combo',
            'array' => $cidade,
            'title' => 'Cidade de Moradia do Servidor',
            'col' => 5,
            'size' => 30),
        array('linha' => 9,
            'nome' => 'representanteCep',
            'label' => 'Cep:',
            'tipo' => 'cep',
            'title' => 'Cep',
            'col' => 3,
            'size' => 10),
        array('linha' => 10,
            'nome' => 'obs',
            'fieldset' => 'fecha',
            'label' => 'Observação:',
            'tipo' => 'textarea',
            'size' => array(80, 5))));

    # idUsuário para o Log
    $objeto->set_idUsuario($idUsuario);

    ################################################################
    switch ($fase) {
        case "":
            br(8);
            aguarde();
            br();

            # Limita a tela
            $grid1 = new Grid("center");
            $grid1->abreColuna(5);
            p("Aguarde...", "center");
            $grid1->fechaColuna();
            $grid1->fechaGrid();

            loadPage('?fase=listar');
            break;

        ################################################################

        case "listar":
            $objeto->listar();
            break;

        ################################################################

        case "excluir":
            # Verifica se tem contrato com essa modalidade
            $numContratos = $empresa->getNumContratos($id);

            if ($numContratos > 0) {
                alert("Existem contratos cadastrados com esta empresa. Dessa forma a mesma NÃO poderá ser excluída.");
                back(1);
            } else {
                $objeto->excluir($id);
            }
            break;

        ################################################################

        case "editar":
            $objeto->editar($id);
            break;

        case "gravar":
            $objeto->gravar($id, null, "cadastroEmpresaPosGravacao.php");
            break;
    }

    $page->terminaPagina();
} else {
    loadPage("../../areaServidor/sistema/login.php");
}
