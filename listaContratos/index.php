<?php

# Configuração
include ("../sistema/_config.php");

# Começa uma nova página
$page = new Page();
$page->iniciaPagina();

# Cabeçalho da Página
AreaServidor::cabecalho();

echo "olá";
