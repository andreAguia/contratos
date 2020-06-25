<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

# Pega os dados
$substituindo        = $campoValor[2];
$portariaEntrada     = $campoValor[3];
$dtPortariaEntrada   = $campoValor[4];
$dtPublicacaoEntrada = $campoValor[5];
$pgPublicacaoEntrada = $campoValor[6];

# Verifica se houve alguma substutuição
if (!vazio($substituindo)) {
    
    # Monta os arrays de gravação
    $nomes = ["portariaSaida","dtPortariaSaida","dtPublicacaoSaida","pgPublicacaoSaida"];
    $valores = [$portariaEntrada,$dtPortariaEntrada,$dtPublicacaoEntrada,$pgPublicacaoEntrada];
    
    # Grava esse valor no registro do membro substiutuído
    $objeto->gravar($nomes, $valores, $substituindo);

}