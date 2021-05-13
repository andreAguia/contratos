<?php //

/**
 * Relatório
 *    
 * By Alat
 */
# Inicia as variáveis que receberão as sessions
$idUsuario = null;              # Servidor logado
# Configuração
include ("../sistema/_config.php");

# Permissão de Acesso
$acesso = Verifica::acesso($idUsuario, [9, 10]);

if ($acesso) {

    # Conecta ao Banco de Dados
    $contrato = new Contrato();
    $comissao = new Comissao();
    $aditivo = new Aditivo();
    $empresa = new Empresa();

    # Começa uma nova página
    $page = new Page();
    $page->iniciaPagina();

    # pega o contrato
    $idContrato = soNumeros(get_session('sessionContrato'));

    # Pega os dados
    $conteudo = $contrato->getDados($idContrato);

    ###### 
    # Menu do Relatório
    $menuRelatorio = new menuRelatorio();
    $menuRelatorio->show();

    # Relatório
    $relatorio = new Relatorio();
    $relatorio->exibeCabecalho();
    
    # Exibe o valor e a comissão
    $grid = new Grid();
    $grid->abreColuna(3);

    # Intencionalmente vazio   

    $grid->fechaColuna();
    $grid->abreColuna(6);
    
    # Título
    p("Contrato " . $conteudo["numero"], "pRelatorioTitulo");
    
    if (!empty($conteudo['siafe'])) {
        p("Siafe: {$conteudo['siafe']}", "pRelatorioSubtitulo");
    }

    if (!empty($conteudo['rubrica'])) {
        p("Rubrica: {$conteudo['rubrica']}", "pRelatorioSubtitulo");
    }

    p($contrato->exibeModalidade($idContrato), "pRelatorioSubtitulo");
    
    $grid->fechaColuna();
    $grid->abreColuna(3);

    # Exibe o valor
    $contrato->exibeValorTotalRel($idContrato);

    $grid->fechaColuna();
    $grid->fechaGrid();

    # Exibe o Resumo
    $contrato->exibeResumoDadosRel($idContrato);

    # Exibe o contrato
    $contrato->exibeDadosContratoRel($idContrato);

    # Exibe os aditivos
    $aditivo->exibeAditivosContratoRel($idContrato);
    
    # Exibe dados da Empresa
    $grid = new Grid();
    $grid->abreColuna(7);
    $empresa->exibeDadosRel($conteudo["idEmpresa"]);
    $grid->fechaColuna();
    
    # Exibe processos de execução
    $grid->abreColuna(5);
    $contrato->listaProcessosExecucaoRel($idContrato);
    
    # Exibe o processo de fiscalização
    $comissao->exibeProcessoFiscalizacaoRel($idContrato);
    $grid->fechaColuna();
    $grid->fechaGrid();

    # Exibe a comissão    
    $comissao->listaComissaoRel($idContrato,"Visualizou a Folha de Rosto do Contrato " . $conteudo["numero"]);

    # Data da Impressão
    p('Emitido em: ' . date('d/m/Y - H:i:s') . " (" . $idUsuario . ")", 'pRelatorioDataImpressao');
    $page->terminaPagina();
}