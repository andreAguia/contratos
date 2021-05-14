<?php

# Pega os dados
$substituindo        = $campoValor[2];
$portariaEntrada     = $campoValor[3];
$dtPortariaEntrada   = $campoValor[4];
$dtPublicacaoEntrada = $campoValor[5];
$pgPublicacaoEntrada = $campoValor[6];

# Verifica se houve alguma substutuição
if (!empty($substituindo)) {
    
    # Monta os arrays de gravação
    $nomes = ["portariaSaida","dtPortariaSaida","dtPublicacaoSaida","pgPublicacaoSaida"];
    $valores = [$portariaEntrada,$dtPortariaEntrada,$dtPublicacaoEntrada,$pgPublicacaoEntrada];
    
    # Grava esse valor no registro do membro substiutuído
    $objeto->gravar($nomes, $valores, $substituindo);

}