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
    $contratos = new Contratos();

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
    $objeto->set_nome('Setor Requisitante');

    # select do edita
    $objeto->set_selectEdita('SELECT marcador1,
                                     marcador2,
                                     marcador3,
                                     marcador4,
                                     marcador5,
                                     marcador6
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

    # Pega os dados
    $comboMarcador = $contratos->select('SELECT idMarcador, 
                                                marcador
                                           FROM tbmarcador
                                       ORDER BY marcador');

    array_unshift($comboMarcador, array(null, null));

    # Formulario
    $objeto->set_campos(array(
        array(
            'linha' => 1,
            "autofocus" => true,
            'nome' => 'marcador1',
            'label' => 'Marcador 1:',
            'tipo' => 'combo',
            'array' => $comboMarcador,
            'col' => 4,
            'size' => 30),
        array(
            'linha' => 1,
            "autofocus" => true,
            'nome' => 'marcador2',
            'label' => 'Marcador 2:',
            'tipo' => 'combo',
            'array' => $comboMarcador,
            'col' => 4,
            'size' => 30),
         array(
            'linha' => 1,
            "autofocus" => true,
            'nome' => 'marcador3',
            'label' => 'Marcador 3:',
            'tipo' => 'combo',
            'array' => $comboMarcador,
            'col' => 4,
            'size' => 30),
        array(
            'linha' => 2,
            "autofocus" => true,
            'nome' => 'marcador4',
            'label' => 'Marcador 4:',
            'tipo' => 'combo',
            'array' => $comboMarcador,
            'col' => 4,
            'size' => 30),
        array(
            'linha' => 2,
            "autofocus" => true,
            'nome' => 'marcador5',
            'label' => 'Marcador 5:',
            'tipo' => 'combo',
            'array' => $comboMarcador,
            'col' => 4,
            'size' => 30),
        array(
            'linha' => 2,
            "autofocus" => true,
            'nome' => 'marcador6',
            'label' => 'Marcador 6:',
            'tipo' => 'combo',
            'array' => $comboMarcador,
            'col' => 4,
            'size' => 30),
        
        ));

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