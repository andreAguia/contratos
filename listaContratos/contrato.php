<?php

//

/**
 * Relatório
 *    
 * By Alat
 */
# Configuração
include ("../sistema/_config.php");

# Conecta ao Banco de Dados
$contrato = new Contrato();
$comissao = new Comissao();
$aditivo = new Aditivo();
$empresa = new Empresa();

# Começa uma nova página
$page = new Page();
$page->iniciaPagina();

# pega o contrato
$idContrato = soNumeros(get('id'));

# Pega os dados
$conteudo = $contrato->getDados($idContrato);

###### 
# Menu do Relatório
$menuRelatorio = new menuRelatorio();
$menuRelatorio->show();

# Relatório
$relatorio = new Relatorio();
$relatorio->set_cabecalhoGerencia("Setor de Contratos");
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

p($contrato->exibeModalidadeRel($idContrato), "pRelatorioSubtitulo");

$grid->fechaColuna();
$grid->abreColuna(3);

# Exibe o valor
$contrato->exibeValorTotalRel($idContrato);

$grid->fechaColuna();
$grid->fechaGrid();

# Exibe o Resumo
$contrato->exibeResumoDadosRel($idContrato);

# Exibe o contrato
$contrato->exibeDadosContrato2Rel($idContrato);

# Exibe os aditivos
$aditivo->exibeAditivosContratoRel($idContrato);

//# Exibe dados da Empresa
//$grid = new Grid();
//$grid->abreColuna(7);
//$empresa->exibeDadosRel($conteudo["idEmpresa"]);
//$grid->fechaColuna();

//# Exibe processos de execução
//$grid->abreColuna(5);
//$contrato->listaProcessosExecucaoRel($idContrato);
//
////    # Exibe o processo de fiscalização
////    $comissao->exibeProcessoFiscalizacaoRel($idContrato);   // retirado a pedido de Kátia
//$grid->fechaColuna();
//$grid->fechaGrid();

# Exibe a comissão    
$comissao->listaComissaoRel($idContrato, "Visualizou a Folha de Rosto do Contrato " . $conteudo["numero"]);

# Data da Impressão
p('Emitido em: ' . date('d/m/Y - H:i:s') . " (" . $idUsuario . ")", 'pRelatorioDataImpressao');
$page->terminaPagina();
