<?php

//

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
$acesso = Verifica::acesso($idUsuario, [1, 9, 10]);

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

    # Pega os dados do contrato
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
    $grid->abreColuna(12);

    # Título    
    p("Cronograma Físico-Financeiro", "pRelatorioTitulo");

    # Primeira Linha do cabeçalho
    $select = "SELECT idEmpresa, 
                      idContrato,
                      idContrato, 
                      idContrato
                 FROM tbcontrato
                WHERE idContrato = {$idContrato}";

    $contratos = new Contratos();
    $row = $contratos->select($select);

    $empresa = new Empresa();

    # Limita o tamanho da tela
    $grid = new Grid();
    $grid->abreColuna(12);

    # Monta o Relatório
    $relatorio = new Relatorio();
    $relatorio->set_conteudo($row);
    $relatorio->set_label(["Contratada", "Contrato", "Vigência", "Valor"]);
    $relatorio->set_width([30, 20, 30, 20]);
    #$relatorio->set_funcao($function);
    $relatorio->set_classe(["Empresa", "Contrato", "Contrato", "Contrato"]);
    $relatorio->set_metodo(["getEmpresa", "exibeNumeroContratoSimples", "exibeDuracaoSimples", "exibeValor"]);
    $relatorio->set_subTotal(false);
    $relatorio->set_totalRegistro(false);
    $relatorio->set_dataImpressao(false);
    $relatorio->set_cabecalhoRelatorio(false);
    $relatorio->set_menuRelatorio(false);
    $relatorio->set_log(false);
    $relatorio->set_exibeLinhaFinal(false);
    $relatorio->show();

    # Segunda Linha do cabeçalho
    $select = "SELECT if(natDespesa = 1,'( X ) Obra   (   ) Serviço', '(  ) Obra   ( X ) Serviço'),
                      localExecucao
                 FROM tbcontrato
                WHERE idContrato = {$idContrato}";

    $contratos = new Contratos();
    $row = $contratos->select($select);

    $empresa = new Empresa();

    # Limita o tamanho da tela
    $grid = new Grid();
    $grid->abreColuna(12);

    # Monta o Relatório
    $relatorio = new Relatorio();
    $relatorio->set_conteudo($row);
    $relatorio->set_label(["Natureza da Despesa", "Local de Execução"]);
    $relatorio->set_width([30, 70]);
    #$relatorio->set_funcao($function);    
    $relatorio->set_subTotal(false);
    $relatorio->set_totalRegistro(false);
    $relatorio->set_dataImpressao(false);
    $relatorio->set_cabecalhoRelatorio(false);
    $relatorio->set_menuRelatorio(false);
    $relatorio->set_log(false);
    $relatorio->show();

    # Data da Impressão
    p('Emitido em: ' . date('d/m/Y - H:i:s') . " (" . $idUsuario . ")", 'pRelatorioDataImpressao');
    $page->terminaPagina();
}