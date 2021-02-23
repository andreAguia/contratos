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
    $titulo = "Comissão de Fiscalização dos Contratos Ativos";
    $subTitulo = "Ordenado pelo Número do Contrato";

    # Pega os dados
    $select = "SELECT idContrato,
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
    $relatorio->set_label(array("Contrato", "Empresa", "Objeto", "Documentos", "Comissão de Fiscalização"));
    $relatorio->set_classe(array("Contrato", "Empresa", "Contrato", "Comissao", "Comissao"));
    $relatorio->set_metodo(array("getNumero", "exibeEmpresaRelatorio", "exibeObjetoRelatorio", "exibeDocumentosRelatorio", "listaComissaoRelatorio"));
    $relatorio->set_width(array(10, 15, 15, 20, 40));
    $relatorio->set_align(array("center", "left", "left", "left", "left"));
    $relatorio->set_bordaInterna(true);
    $relatorio->set_numeroOrdem(true);

    $relatorio->set_titulo($titulo);
    $relatorio->set_subtitulo($subTitulo);
    $relatorio->show();

    $page->terminaPagina();
}