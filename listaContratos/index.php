<?php

# Configuração
include ("../sistema/_config.php");

# Começa uma nova página
$page = new Page();
$page->iniciaPagina();

# Cabeçalho da Página
AreaServidor::cabecalho();

# Exemplo de tabela simples
$tabela = new Tabela();
$tabela->set_titulo("Tabela Simples");
$tabela->set_conteudo($array);
$tabela->set_label(array("Time", "Jogos", "Pontos"));
$tabela->set_width(array(80, 10, 10));
$tabela->set_align(array("left", "center", "center"));
$tabela->set_colunaSomatorio(1);
$tabela->set_textoSomatorio("Total de Jogos:");
$tabela->set_totalRegistro(false);
$tabela->show();