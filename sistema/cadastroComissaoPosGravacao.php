<?php

# Pega os dados
$substituindo        = $campoValor[3];
$portariaEntrada     = $campoValor[4];
$dtPortariaEntrada   = $campoValor[5];
$dtPublicacaoEntrada = $campoValor[6];
$pgPublicacaoEntrada = $campoValor[7];

# Verifica se houve alguma substutuição
if (!empty($substituindo)) {
    
    # Monta os arrays de gravação
    $nomes = ["portariaSaida","dtPortariaSaida","dtPublicacaoSaida","pgPublicacaoSaida"];
    $valores = [$portariaEntrada,$dtPortariaEntrada,$dtPublicacaoEntrada,$pgPublicacaoEntrada];
    
    # Grava esse valor no registro do membro substiutuído
    $objeto->gravar($nomes, $valores, $substituindo);

}