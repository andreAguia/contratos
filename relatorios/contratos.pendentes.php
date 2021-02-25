<?php

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
    $contratos = new Contratos();

    # Começa uma nova página
    $page = new Page();
    $page->iniciaPagina();

    ######   
    # Título & Subtitulo
    $titulo = "Contratos Pendentes";
    $subTitulo = "Ordenado pela Data de Vigência";

   # Pega os dados
    $select = "SELECT idContrato,
                      idContrato,
                      objeto,
                      idEmpresa,
                      idContrato,
                      idContrato,
                      idContrato
                 FROM tbcontrato JOIN tbmodalidade USING (idModalidade)
                                 JOIN tbstatus USING (idStatus)
                                 JOIN tbempresa USING (idEmpresa)
                WHERE idStatus = 3
             ORDER BY (IFNULL(
                      (SELECT IF(tipoPrazo = 2,
                          SUBDATE(ADDDATE(dtInicial, INTERVAL prazo MONTH), INTERVAL 1 DAY),
                          ADDDATE(dtInicial, INTERVAL prazo-1 DAY)) as dtFinal
                     FROM tbaditivo
                    WHERE tbaditivo.idContrato = tbcontrato.idContrato
                      AND dtInicial IS NOT NULL 
                 ORDER BY dtAssinatura desc LIMIT 1),
                 IF(tipoPrazo = 2,SUBDATE(ADDDATE(dtInicial, INTERVAL prazo MONTH), INTERVAL 1 DAY),ADDDATE(dtInicial, INTERVAL prazo-1 DAY))))";
    

    $resumo = $contratos->select($select);

    # Monta o Relatório
    $relatorio = new Relatorio();
    $relatorio->set_conteudo($resumo);
    $relatorio->set_label(array("Contrato", "Modalidade", "Objeto", "Empresa", "Processo", "Duração & Vigência", "Situação"));
    $relatorio->set_classe(array("Contrato", "Contrato", null, "Empresa", "Contrato", "Contrato", "Situacao"));
    $relatorio->set_metodo(array("exibeNumeroSiafeRelatorio", "exibeModalidade", null, "getEmpresaCnpj", "getProcessoAdm", "exibeTempoEVigencia", "getSituacaoAtualEAlerta"));
    $relatorio->set_width(array(10, 15, 15, 15, 15, 15, 15));
    $relatorio->set_align(array("center", "center","left", "left", "left", "center", "left"));
    $relatorio->set_bordaInterna(true);

    $relatorio->set_titulo($titulo);
    $relatorio->set_subtitulo($subTitulo);
    $relatorio->show();

    $page->terminaPagina();
}