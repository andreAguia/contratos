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
$acesso = Verifica::acesso($idUsuario, [1, 9]);

if ($acesso) {

    # Conecta ao Banco de Dados
    $contratos = new Contratos();

    # Começa uma nova página
    $page = new Page();
    $page->iniciaPagina();

    ######   
    # Título & Subtitulo
    $titulo = "Contratos Ativos";
    $subTitulo = "Ordenado pelo Número";

    # Pega os dados
    $select = "SELECT idContrato,
                      idContrato,
                      objeto,
                      idEmpresa,
                      idContrato,
                      idContrato,
                      idContrato
                 FROM tbcontrato 
                WHERE idStatus = 1
             ORDER BY year(dtAssinatura), numero";

    $resumo = $contratos->select($select);

    # Monta o Relatório
    $relatorio = new Relatorio();
    $relatorio->set_conteudo($resumo);
    $relatorio->set_label(array("Contrato", "Modalidade", "Objeto", "Empresa", "Processo", "Duração & Vigência", "Situação"));
    $relatorio->set_classe(array("Contrato", "Contrato", null, "Empresa", "Contrato", "Contrato", "Situacao"));
    $relatorio->set_metodo(array("exibeNumeroSiafeRelatorio", "exibeModalidade", null, "getEmpresaCnpj", "getProcesso", "exibeTempoEVigencia", "getSituacaoAtualEAlerta"));
    $relatorio->set_width(array(10, 15, 15, 15, 15, 15, 15));
    $relatorio->set_align(array("center", "center", "left", "left", "left", "center", "left"));
    $relatorio->set_bordaInterna(true);

    $relatorio->set_titulo($titulo);
    $relatorio->set_subtitulo($subTitulo);
    $relatorio->set_cabecalhoGerencia("Setor de Contratos");
    $relatorio->show();

    $page->terminaPagina();
}