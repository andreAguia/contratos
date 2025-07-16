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

# Cria um menu
$menu = new MenuBar();

# Voltar
$linkVoltar = new Link("Voltar", "https://uenf.br/dga/setordecontratos/contratos-ativos/");
$linkVoltar->set_class('button');
$menu->add_link($linkVoltar, "left");
$menu->show();

$select = "SELECT YEAR(dtInicial),
                numero,
                idContrato,
                idEmpresa,
                idContrato
           FROM tbcontrato DD JOIN tbmodalidade USING (idModalidade)
                           JOIN tbstatus USING (idStatus)
                           JOIN tbempresa USING (idEmpresa)
           WHERE idStatus = 1                
          ORDER BY YEAR(dtInicial) desc, dtInicial";

$array = $contratos->select($select);

# Exemplo de tabela simples
$tabela = new Tabela();
$tabela->set_titulo("Contratos Ativos");
$tabela->set_conteudo($array);

$tabela->set_label(["Ano", "Contrato", "Objeto", "Empresa", "Mais Informações"]);
$tabela->set_classe([null, null, "Contrato", "Empresa"]);
$tabela->set_metodo([null, null, "exibeObjeto", "exibeEmpresaRelatorio"]);
$tabela->set_width([10, 10, 35, 35, 10]);
$tabela->set_align(["center", "center", "left", "left"]);
#$tabela->set_bordaInterna(true);
$tabela->set_rowspan(0);
$tabela->set_grupoCorColuna(0);

# Botão 
$botao = new BotaoGrafico();
$botao->set_label('');
$botao->set_title('Acessar Contrato');
$botao->set_url("contrato.php?id=");
$botao->set_target("_blank");
$botao->set_imagem(PASTA_FIGURAS . "contratos.png", 20, 20);

# Coloca o objeto link na tabela
$tabela->set_link(["", "", "", "", $botao]);

$tabela->show();

$grid->fechaColuna();
$grid->fechaGrid();
