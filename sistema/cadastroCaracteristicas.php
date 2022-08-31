<?php

/**
 * Cadastro de Obs Geral do saldo
 *  
 * By Alat
 */
# Inicia as variáveis que receberão as sessions
$idUsuario = null;

# Configuração
include ("_config.php");

# Permissão de Acesso
$acesso = Verifica::acesso($idUsuario, [1, 9]);

if ($acesso) {
    # Conecta ao Banco de Dados
    $intra = new Intra();
    $contrato = new Contrato();
    $modalidade = new Modalidade();
    $pessoal = new Pessoal();

    # Verifica a fase do programa
    $fase = get('fase', 'editar');

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
    # Exibe os dados do Contrato
    $objeto->set_rotinaExtra("get_DadosContrato");
    $objeto->set_rotinaExtraParametro($idContrato);

    # Nome do Modelo
    $objeto->set_nome('Características do Especiais do Contrato');

    # select do edita
    $objeto->set_selectEdita('SELECT caracteristicas
                                FROM tbcontrato
                               WHERE idContrato = ' . $idContrato);

    # Caminhos
    $objeto->set_linkGravar('?fase=gravar');

    # Define o caminho de volta
    $objeto->set_linkListar('cadastroAditivo.php');
    $objeto->set_voltarForm('cadastroAditivo.php');

    # retira o botão incluir
    $objeto->set_botaoIncluir(false);

    # Classe do banco de dados
    $objeto->set_classBd('Contratos');

    # Nome da tabela
    $objeto->set_tabela('tbcontrato');

    # Nome do campo id
    $objeto->set_idCampo('idContrato');

//    # Tipo de label do formulário
//    $objeto->set_formlabelTipo(1);
    # Formulario
    $objeto->set_campos(array(
        array(
            'linha' => 1,
            "autofocus" => true,
            'nome' => 'caracteristicas',
            'label' => 'Características Especiais:',
            'tipo' => 'textarea',
            'col' => 12,
            'size' => array(80, 5))));

    # Log
    $objeto->set_idUsuario($idUsuario);

    ################################################################
    switch ($fase) {
        case "editar" :
        case "excluir" :
        case "gravar" :
            $objeto->$fase($idContrato);
            break;
    }

    $page->terminaPagina();
} else {
    loadPage("../../areaServidor/sistema/login.php");
}