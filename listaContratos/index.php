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

$select = "SELECT idAditivo,
                idAditivo,
                idAditivo,
                idAditivo,
                idAditivo,
                dtAssinatura,
                idAditivo,
                idAditivo,
                idAditivo
           FROM tbaditivo
          WHERE idContrato = {$idContrato}
       ORDER BY dtAssinatura";

$resumo = $contratos->select($select);

# Exemplo de tabela simples
$tabela = new Tabela();
$tabela->set_titulo("Tabela Simples");
$tabela->set_conteudo($array);

$tabela->set_label(["id", "Tipo", "Objeto", "Publicação", "Aditivo", "Assinatura", "Duração", "Garantia", "Valor"]);
$tabela->set_align(["center", "center", "left", "center", "center", "center", "center", "center", "right"]);
$tabela->set_width([3, 15, 22, 10, 10, 10, 10, 10, 10]);

$tabela->set_classe([null, "Aditivo", "Aditivo", "Aditivo", "Aditivo", "Contrato", "Aditivo", "Aditivo", "Aditivo"]);
$tabela->set_metodo([null, "exibeTipoNumerado", "exibeObjeto", "exibePublicacao", "exibeAditivo", "exibeAssinaturaEReitor", "exibePeriodo", "exibeGarantia", "exibeValor"]);

$tabela->show();

$grid->fechaColuna();
$grid->fechaGrid();
