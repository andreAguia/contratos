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
$acesso = Verifica::acesso($idUsuario, [1, 9, 10]);

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
                      idContrato,
                      year(dtInicial)
                 FROM tbcontrato
                WHERE idStatus = 1
             ORDER BY year(dtInicial), numero";

    $resumo = $contratos->select($select);

    # Monta o Relatório
    $relatorio = new Relatorio();
    $relatorio->set_conteudo($resumo);
    $relatorio->set_label(["Contrato", "Empresa", "Objeto", "Documentos", "Comissão de Fiscalização","Ano"]);
    $relatorio->set_classe(["Contrato", "Empresa", "Contrato", "Comissao", "Comissao"]);
    $relatorio->set_metodo(["getNumero", "exibeEmpresaRelatorio", "exibeObjetoRelatorio", "exibeDocumentosRelatorio", "listaComissaoRelatorio"]);
    $relatorio->set_width([10, 15, 15, 20, 40]);
    $relatorio->set_align(["center", "left", "left", "left", "left"]);
    $relatorio->set_numGrupo(5);
    $relatorio->set_bordaInterna(true);
    $relatorio->set_numeroOrdem(true);

    $relatorio->set_titulo($titulo);
    $relatorio->set_subtitulo($subTitulo . "<br/>Agrupado pelo Ano de Assinatura");
    $relatorio->set_cabecalhoGerencia("Setor de Contratos");
    $relatorio->show();

    $page->terminaPagina();
}