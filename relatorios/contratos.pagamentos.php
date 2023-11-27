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
    $contratos = new Contratos();

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

    # Título
    p("Contrato " . $conteudo["numero"], "pRelatorioTitulo");

    if (!empty($conteudo['siafe'])) {
        p("Siafe: {$conteudo['siafe']}", "pRelatorioSubtitulo");
    }

    if (!empty($conteudo['rubrica'])) {
        p("Rubrica: {$conteudo['rubrica']}", "pRelatorioSubtitulo");
    }

    p($contrato->exibeModalidadeRel($idContrato), "pRelatorioSubtitulo");

    # Exibe o Resumo
    br();
    $contrato->exibeResumoDadosRel($idContrato);

    # Exibe o valor
    $pagamento = new Pagamento();
    $pagamento->exibeSaldosPgtoRel($idContrato);

    br();
    p("<b>Pagamentos</b>", "center", "f16");

    # Exibe os pagamentos
    $select = "SELECT CONCAT('Ano Referência: ',anoReferencia),
                      idPagamento,
                      data,
                      notaFiscal,
                      idPagamento,                                     
                      tipo,
                      natureza,
                      tbpagamento.obs,
                      idPagamento
                 FROM tbpagamento LEFT JOIN tbnatureza USING (idNatureza)
                WHERE idContrato = {$idContrato}
             ORDER BY anoReferencia desc, mesReferencia desc, data desc";

    $row = $contratos->select($select);

    $relatorio->set_label(["Ano", "Referência", "Data", "Nota Fiscal", "Valor", "Tipo", "Natureza", "Obs"]);
    $relatorio->set_align(["center", "center", "center", "center", "center", "center", "center", "left"]);
    $relatorio->set_width([5, 15, 10, 15, 15, 5, 5, 30]);
    $relatorio->set_funcao([null, null, "date_to_php"]);    
    $relatorio->set_classe([null, "Pagamento", null, null, "Pagamento", "Pagamento"]);
    $relatorio->set_metodo([null, "exibeReferencia", null, null, "exibeValor", "exibeTipo"]);
    $relatorio->set_bordaInterna(true);
    $relatorio->set_numGrupo(0);

    $relatorio->set_numeroOrdem(true);
    $relatorio->set_numeroOrdemTipo('d');
    $relatorio->set_cabecalhoRelatorio(false);
    $relatorio->set_menuRelatorio(false);
    $relatorio->set_log(false);

    $relatorio->set_conteudo($row);
    $relatorio->show();

    $page->terminaPagina();
}