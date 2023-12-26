<?php

/**
 * Controle de Receita (Aluguel)
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
    $intra = new Intra();
    $contratos = new Contratos();
    $contrato = new Contrato();
    $pessoal = new Pessoal();
    $comissao = new Comissao();

    # Verifica a fase do programa
    $fase = get("fase", "listar");

    # pega o id (se tiver)
    $id = soNumeros(get("id"));

    # pega o contrato
    $idContrato = soNumeros(get_session('sessionContrato'));

    # Grava no log a atividade
    $atividade = "Acessou a página do controle de processos de execução de contrato " . $contrato->getNumero($idContrato);
    $data = date("Y-m-d H:i:s");
    $intra->registraLog($idUsuario, $data, $atividade, null, null, 7);

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
    $objeto->set_nome("Controle dos Processos de Execução");

    # Botão de voltar da lista
    $objeto->set_voltarLista("cadastroAditivo.php");

    # select da lista
    $objeto->set_selectLista("SELECT IF(processo IS NUll, SUBSTR(processoAntigo, -4), SUBSTR(processo, -4)),
                                     IF(processo IS NUll, CONCAT('E-26/009/',processoAntigo), CONCAT('SEI - ',processo)),
                                     idProcessoExecucao
                                FROM tbprocessoexecucao
                               WHERE idContrato = {$idContrato}
                            ORDER BY 1");

    # select do edita
    $objeto->set_selectEdita("SELECT processo,
                                     processoAntigo,
                                     idContrato
                                FROM tbprocessoexecucao
                              WHERE idProcessoExecucao = {$id}");

    # Caminhos
    $objeto->set_linkEditar("?fase=editar");
    $objeto->set_linkExcluir("?fase=excluir");
    $objeto->set_linkGravar("?fase=gravar");
    $objeto->set_linkListar("?fase=listar");

    $objeto->set_rowspan(0);
    $objeto->set_grupoCorColuna(0);

    # Parametros da tabela
    $objeto->set_label(["Ano", "Processo"]);
    $objeto->set_align(["center", "left"]);
    $objeto->set_width([10, 70]);
    #$objeto->set_funcao(array(null, null, "date_to_php"));
    #$objeto->set_classe(array(null, "Receita", null, null, "Receita", "Receita"));
    #$objeto->set_metodo(array(null, "exibeReferencia", null, null, "exibeValor", "exibeValorEnergia"));
    $objeto->set_numeroOrdem(true);
    #$objeto->set_numeroOrdemTipo('d');
    # Classe do banco de dados
    $objeto->set_classBd("Contratos");

    # Nome da tabela
    $objeto->set_tabela("tbprocessoexecucao");

    # Nome do campo id
    $objeto->set_idCampo("idProcessoExecucao");

    # Tipo de label do formulário
    $objeto->set_formLabelTipo(1);

    # Campos para o formulario
    $objeto->set_campos(array(
        array(
            'linha' => 1,
            'nome' => 'processo',
            'label' => 'Processo Sei:',
            'tipo' => 'sei',
            "autofocus" => true,
            'col' => 4,
            'size' => 50),
        array(
            'linha' => 1,
            'nome' => 'processoAntigo',
            'label' => 'Processo Antigo:',
            'tipo' => 'processoAntigo',
            'col' => 4,
            'size' => 50),
        array(
            "linha" => 5,
            "nome" => "idContrato",
            "label" => "idContrato:",
            'tipo' => 'hidden',
            'padrao' => $idContrato,
            "col" => 3,
            "size" => 11),
    ));

    # idUsuário para o Log
    $objeto->set_idUsuario($idUsuario);
    
    # Mensagem ao editar e inserir
    $objeto->set_rotinaExtraEditar("callout");
    $objeto->set_rotinaExtraEditarParametro("Observe que somente um campo de processo deverá ser preenchido. Ou o Campo do Processo SEi ou do Processo Antigo.");

    ################################################################

    switch ($fase) {
        case "":
        case "listar" :
            $objeto->listar();
            break;

        case "editar":
        case "excluir":
            $objeto->$fase($id);
            break;

        case "gravar":
            $objeto->gravar($id,"cadastroProcessoExecucaoExtra.php");
            break;

        ################################################################    
    }

    $page->terminaPagina();
} else {
    loadPage("../../areaServidor/sistema/login.php");
}
