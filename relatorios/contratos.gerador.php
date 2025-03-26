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

    # Pega o título
    $parametroTitulo = post('parametroTitulo', 'Contratos');
    $parametroSubtitulo = post('parametroSubtitulo');

    # Pega as variáveis
    $select = get_session("sessionSelect");
    $label = get_session("sessionLabel");
    $align = get_session("sessionAlign");
    $class = get_session("sessionClass");
    $method = get_session("sessionMethod");
    $function = get_session("sessionFunction");
    $resumo = $contratos->select($select);

    # Monta o Relatório
    $relatorio = new Relatorio();
    $relatorio->set_conteudo($resumo);
    $relatorio->set_label($label);
    $relatorio->set_align($align);
    $relatorio->set_classe($class);
    $relatorio->set_metodo($method);
    $relatorio->set_funcao($function);
    $relatorio->set_bordaInterna(true);
    $relatorio->set_titulo($parametroTitulo);
    $relatorio->set_subtitulo($parametroSubtitulo);
    $relatorio->set_formCampos(array(
        array('nome' => 'parametroTitulo',
            'label' => 'Título:',
            'tipo' => 'texto',
            'size' => 150,
            'padrao' => $parametroTitulo,
            'title' => 'Título do Relatório',
            'onChange' => 'formPadrao.submit();',
            'col' => 12,
            'linha' => 1),
        array('nome' => 'parametroSubtitulo',
            'label' => 'Subtítulo:',
            'tipo' => 'texto',
            'size' => 150,
            'padrao' => $parametroSubtitulo,
            'title' => 'Subtítulo do Relatório',
            'onChange' => 'formPadrao.submit();',
            'col' => 12,
            'linha' => 1)
    ));

    $relatorio->set_cabecalhoGerencia("Setor de Contratos");
    $relatorio->show();
    $page->terminaPagina();
}