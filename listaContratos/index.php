<?php

# Configuração
include ("../sistema/_config.php");

# Começa uma nova página
$page = new Page();
$page->iniciaPagina();

# Limita o tamanho da tela
$grid = new Grid();
$grid->abreColuna(12);

# Conecta ao Banco de Dados
$contratos = new Contratos();

# Cabeçalho da Página
AreaServidor::cabecalho();
br();

$select = "SELECT idContrato,
                idContrato,
                idEmpresa,
                idContrato,
                idContrato,
                idContrato
           FROM tbcontrato DD JOIN tbmodalidade USING (idModalidade)
                           JOIN tbstatus USING (idStatus)
                           JOIN tbempresa USING (idEmpresa)
          WHERE true";

$array = $contratos->select($select);

# Exemplo de tabela simples
$tabela = new Tabela();
$tabela->set_titulo("Contratos");
$tabela->set_conteudo($array);

$tabela->set_label(["Contrato", "Objeto", "Empresa", "Processo", "Duração & Vigência", "Folha"]);
$tabela->set_classe(["Contrato", "Contrato", "Empresa", "Contrato", "Contrato"]);
$tabela->set_metodo(["exibeNumeroContrato", "exibeObjeto", "exibeEmpresaCnpj", "exibeProcessoERequisitante", "exibeTempoEVigencia"]);
$tabela->set_width([10, 20, 20, 20, 15]);
$tabela->set_align(["center", "left", "left", "left", "center", "left"]);
$tabela->set_bordaInterna(true);

# Botão 
$botao = new BotaoGrafico();
$botao->set_label('');
$botao->set_title('Acessar Contrato');
$botao->set_url("contrato.php?id=");
$botao->set_target("_blank");
$botao->set_imagem(PASTA_FIGURAS_GERAIS . "olho.png", 20, 20);

# Coloca o objeto link na tabela
$tabela->set_link(["", "", "", "", "", $botao]);

$tabela->show();

$grid->fechaColuna();
$grid->fechaGrid();
