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
    $contrato = new Contratos();
    $pessoal = new Pessoal();

    # Verifica a fase do programa
    $fase = get('fase', 'listar');

    # pega o id (se tiver)
    $id = soNumeros(get('id', get_session('sessionContrato')));

    # Começa uma nova página
    $page = new Page();
    $page->iniciaPagina();

    # Cabeçalho da Página
    AreaServidor::cabecalho();

    # Abre um novo objeto Modelo
    $objeto = new Modelo();

    ################################################################
    # Nome do Modelo
    $objeto->set_nome('Contratos');

    # Botão de voltar da lista
    $objeto->set_voltarLista('../../../areaServidor/sistema/areaServidor.php');

    # select da lista
    $objeto->set_selectLista('SELECT status,
                                      numero,
                                      nome,
                                      modalidade,
                                      "OK",
                                      idContrato
                                 FROM tbcontrato JOIN tbmodalidade USING (idModalidade)
                                                 JOIN tbstatus USING (idStatus)
                             ORDER BY numero');

    # select do edita
    $objeto->set_selectEdita('SELECT numero,
                                     idModalidade,
                                     siafe,
                                     idStatus,
                                     objeto,
                                     idEmpresa,
                                     processoSei,
                                     processo,
                                     valor,
                                     garantia,
                                     dtPublicacao,
                                     pgPublicacao,
                                     dtAssinatura,
                                     dtInicial,
                                     prazo,
                                     tipoPrazo,
                                     obs
                                FROM tbcontrato
                              WHERE idContrato = ' . $id);

    # Caminhos
    $objeto->set_linkEditar('?fase=editar');
    $objeto->set_linkExcluir('?fase=excluir');
    $objeto->set_linkGravar('?fase=gravar');
    $objeto->set_linkListar('?fase=listar');

    # Parametros da tabela
    $objeto->set_label(array("Status", "Número", "Nome", "Modalidade", "Situação"));
    $objeto->set_width(array(10, 10, 20, 10, 40));
    $objeto->set_align(array("center"));

    # Classe do banco de dados
    $objeto->set_classBd('Contratos');

    # Nome da tabela
    $objeto->set_tabela('tbcontrato');

    # Nome do campo id
    $objeto->set_idCampo('idContrato');

    # Tipo de label do formulário
    $objeto->set_formlabelTipo(1);

    # Dados da combo status
    $status = $contrato->select('SELECT idStatus,
                                        status
                                   FROM tbstatus
                               ORDER BY status');

    array_unshift($status, array(null, null));

    # Dados da combo tipo
    $tipo = array(
        array(null, null),
        array(1, "Dias"),
        array(2, "Meses")
    );

    # Dados da combo modalidade
    $modalidade = $contrato->select('SELECT idModalidade,
                                            modalidade
                                       FROM tbmodalidade
                                   ORDER BY modalidade');

    array_unshift($modalidade, array(null, null));

    # Dados da combo empresa
    $empresa = $contrato->select('SELECT idEmpresa,
                                         razaoSocial
                                    FROM tbempresa
                              ORDER BY razaoSocial');

    array_unshift($empresa, array(null, null));

    # Campos para o formulario
    $objeto->set_campos(array(
        array('linha' => 1,
            'nome' => 'numero',
            'label' => 'Número:',
            'tipo' => 'texto',
            'required' => true,
            'autofocus' => true,
            'col' => 3,
            'size' => 10),
        array('linha' => 1,
            'nome' => 'idModalidade',
            'label' => 'Modalidade:',
            'tipo' => 'combo',
            'required' => true,
            'array' => $modalidade,
            'col' => 3,
            'size' => 15),                
        array('linha' => 1,
            'nome' => 'siafe',
            'label' => 'Siafe:',
            'tipo' => 'texto',
            'required' => true,
            'col' => 3,
            'size' => 15),
        array('linha' => 1,
            'nome' => 'idStatus',
            'label' => 'Status:',
            'tipo' => 'combo',
            'array' => $status,
            'required' => true,
            'col' => 3,
            'size' => 30),
        array('linha' => 1,
            'nome' => 'objeto',
            'label' => 'Objeto:',
            'tipo' => 'texto',
            'col' => 12,
            'size' => 250),
        array('linha' => 2,
            'nome' => 'idEmpresa',
            'label' => 'Empresa:',
            'tipo' => 'combo',
            'array' => $empresa,
            'required' => true,
            'col' => 12,
            'size' => 200),
        array('linha' => 2,
            'nome' => 'processoSei',
            'label' => 'Processo Sei:',
            'tipo' => 'sei',
            'col' => 4,
            'size' => 50),
        array('linha' => 2,
            'nome' => 'processo',
            'label' => 'Processo Físico:',
            'tipo' => 'processo',
            'col' => 3,
            'size' => 50),
        array('linha' => 2,
            'nome' => 'valor',
            'label' => 'Valor:',
            'tipo' => 'moeda',
            'col' => 3,
            'size' => 15),
        array('linha' => 2,
            'nome' => 'garantia',
            'label' => 'Garantia: (se houver)',
            'tipo' => 'percentagem',
            'col' => 2,
            'size' => 5),
        array('linha' => 3,
            'nome' => 'dtPublicacao',
            'label' => 'Publicação:',
            'tipo' => 'date',
            'required' => true,
            'col' => 3,
            'size' => 15),
        array('linha' => 3,
            'nome' => 'pgPublicacao',
            'label' => 'Pag:',
            'tipo' => 'texto',
            'col' => 2,
            'size' => 5),
        array('linha' => 3,
            'nome' => 'dtAssinatura',
            'label' => 'Assinatura:',
            'tipo' => 'date',
            'col' => 3,
            'size' => 15),        
        array('linha' => 4,
            'nome' => 'dtInicial',
            'label' => 'Data Inicial:',
            'tipo' => 'date',
            'col' => 3,
            'size' => 15),
        array('linha' => 4,
            'nome' => 'prazo',
            'label' => 'Prazo:',
            'tipo' => 'texto',
            'col' => 2,
            'size' => 15),
        array('linha' => 4,
            'nome' => 'tipoPrazo',
            'label' => 'Tipo:',
            'tipo' => 'combo',
            'array' => $tipo,
            'col' => 2,
            'size' => 15),
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
            loadPage("areaContrato.php");
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