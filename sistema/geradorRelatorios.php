<?php

/**
 * Rotina do menu de relatório
 *  
 * By Alat
 */
# Reservado para o servidor logado
$idUsuario = null;

# Configuração
include ("_config.php");

# Pega os parâmetros
$postModalidade = post('postModalidade');
$postStatus = post('postStatus');
$postMaoDeObra = post('postMaoDeObra');
$postSiafe = post('postSiafe');
$postEmpresa = post('postEmpresa');
$postEmpresaCnpj = post('postEmpresaCnpj');
$postEmpresaEmail = post('postEmpresaEmail');
$postEmpresaContato = post('postEmpresaContato');
$postPublicacao = post('postPublicacao');
$postAssinatura = post('postAssinatura');
$postProcesso = post('postProcesso');
$postProposta = post('postProposta');
$postDuracao = post('postDuracao');
$postPrazo = post('postPrazo');
$postVigencia = post('postVigencia');
$postPeriodo = post('postPeriodo');
$postObjeto = post('postObjeto');
$postValorTotal = post('postValorTotal');
$postValorUltAditivo = post('postValorUltAditivo');
$postComissao = post('postComissao');
$postComissaoEmail = post('postComissaoEmail');

$parametroAno = post('parametroAno');
$parametroStatus = post('parametroStatus', 1);
$parametroModalidade = post('parametroModalidade');
$parametroModalidadeTipo = post('parametroModalidadeTipo','Todos');
$parametroEmpresa = post('parametroEmpresa');
$parametroMaoDeObra = post('parametroMaoDeObra');

# Permissão de Acesso
$acesso = Verifica::acesso($idUsuario, [9, 10]);

if ($acesso) {
    # Conecta ao Banco de Dados
    $intra = new Intra();
    $contratos = new Contratos();
    $pessoal = new Pessoal();

    # Verifica a fase do programa
    $fase = get('fase', 'menu');

    # Começa uma nova página
    $page = new Page();
    $page->iniciaPagina();

    # Cabeçalho da Página
    AreaServidor::cabecalho();

    # Limita o tamanho da tela
    $grid = new Grid();
    $grid->abreColuna(12);

    # Cria um menu
    $menu1 = new MenuBar();

    # Voltar
    $botaoVoltar = new Link("Voltar", "relatorios.php");
    $botaoVoltar->set_class('button');
    $botaoVoltar->set_title('Voltar a página anterior');
    $botaoVoltar->set_accessKey('V');
    $menu1->add_link($botaoVoltar, "left");

    # Empresas
    $botao = new Button("Gerador de Relatório", "geradorRelatorios.php");
    $botao->set_title("Rotina de geração de relatórios personalizados");
    $botao->set_class("button");
    #$menu1->add_link($botao, "right");

    $menu1->show();

    titulo("Gerador de Planilha Personalizada");
    br();

    # Monta o formulário
    $form = new Form('?');
    
    $controle = new Input('postNumero', 'simnao', 'Número:', 1);
    $controle->set_size(5);
    $controle->set_title('Número do contrato');
    $controle->set_valor("Sim");
    $controle->set_disabled(true);
    $controle->set_readonly(true);
    $controle->set_linha(1);
    $controle->set_col(1);
    $controle->set_fieldset("Informe as Colunas:");
    $form->add_item($controle);

    $controle = new Input('postSiafe', 'simnao', 'Siafe:', 1);
    $controle->set_size(5);
    $controle->set_title('Número do siafe');
    $controle->set_valor($postSiafe);
    $controle->set_onChange('formPadrao.submit();');
    $controle->set_linha(1);
    $controle->set_col(1);
    $form->add_item($controle);

    $controle = new Input('postModalidade', 'simnao', 'Modalidade:', 1);
    $controle->set_size(5);
    $controle->set_title('Modalidade do contrato');
    $controle->set_valor($postModalidade);
    $controle->set_autofocus(true);
    $controle->set_onChange('formPadrao.submit();');
    $controle->set_linha(1);
    $controle->set_col(1);
    $form->add_item($controle);

    $controle = new Input('postStatus', 'simnao', 'Status:', 1);
    $controle->set_size(5);
    $controle->set_title('Status do Contrato');
    $controle->set_valor($postStatus);
    $controle->set_onChange('formPadrao.submit();');
    $controle->set_linha(1);
    $controle->set_col(1);
    $form->add_item($controle);
    
    $controle = new Input('postObjeto', 'simnao', 'Objeto:', 1);
    $controle->set_size(5);
    $controle->set_title('Objeto do contrato');
    $controle->set_valor($postObjeto);
    $controle->set_onChange('formPadrao.submit();');
    $controle->set_linha(1);
    $controle->set_col(1);
    $form->add_item($controle);

    $controle = new Input('postMaoDeObra', 'simnao', 'Mão Obra:', 1);
    $controle->set_size(5);
    $controle->set_title('Mão de Obra Alocada?');
    $controle->set_valor($postMaoDeObra);
    $controle->set_onChange('formPadrao.submit();');
    $controle->set_linha(1);
    $controle->set_col(1);
    $form->add_item($controle);
    
    $controle = new Input('postProcesso', 'simnao', 'Processo:', 1);
    $controle->set_size(5);
    $controle->set_title('O Processo do contrato');
    $controle->set_valor($postProcesso);
    $controle->set_onChange('formPadrao.submit();');
    $controle->set_linha(1);
    $controle->set_col(1);
    $form->add_item($controle);
    
    $controle = new Input('postPublicacao', 'simnao', 'Publicação:', 1);
    $controle->set_size(5);
    $controle->set_title('A data da publicação');
    $controle->set_valor($postPublicacao);
    $controle->set_onChange('formPadrao.submit();');
    $controle->set_linha(1);
    $controle->set_col(1);
    $form->add_item($controle);
    
    $controle = new Input('postAssinatura', 'simnao', 'Assinatura:', 1);
    $controle->set_size(5);
    $controle->set_title('A data da Assinatura');
    $controle->set_valor($postAssinatura);
    $controle->set_onChange('formPadrao.submit();');
    $controle->set_linha(1);
    $controle->set_col(1);
    $form->add_item($controle);

    $controle = new Input('postProposta', 'simnao', 'Proposta:', 1);
    $controle->set_size(5);
    $controle->set_title('A data da proposta');
    $controle->set_valor($postProposta);
    $controle->set_onChange('formPadrao.submit();');
    $controle->set_linha(1);
    $controle->set_col(1);
    $form->add_item($controle);
    
    ####################
    
    $controle = new Input('postDuracao', 'simnao', 'Duração:', 1);
    $controle->set_size(5);
    $controle->set_title('O periodo, prazo e vigência tudo junto ;)');
    $controle->set_valor($postDuracao);
    $controle->set_onChange('formPadrao.submit();');
    $controle->set_linha(2);
    $controle->set_col(1);
    $form->add_item($controle);
    
    $controle = new Input('postPeriodo', 'simnao', 'Periodo:', 1);
    $controle->set_size(5);
    $controle->set_title('O periodo do contrato');
    $controle->set_valor($postPeriodo);
    $controle->set_onChange('formPadrao.submit();');
    $controle->set_linha(2);
    $controle->set_col(1);
    $form->add_item($controle);
    
    $controle = new Input('postPrazo', 'simnao', 'Prazo:', 1);
    $controle->set_size(5);
    $controle->set_title('o prazo do contrato');
    $controle->set_valor($postPrazo);
    $controle->set_onChange('formPadrao.submit();');
    $controle->set_linha(2);
    $controle->set_col(1);
    $form->add_item($controle);
    
    $controle = new Input('postVigencia', 'simnao', 'Vigencia:', 1);
    $controle->set_size(5);
    $controle->set_title('A vigencia do contrato');
    $controle->set_valor($postVigencia);
    $controle->set_onChange('formPadrao.submit();');
    $controle->set_linha(2);
    $controle->set_col(1);
    $form->add_item($controle);
    
    ####################

    $controle = new Input('postEmpresa', 'simnao', 'Empresa:', 1);
    $controle->set_size(5);
    $controle->set_title('A empresa contratada');
    $controle->set_valor($postEmpresa);
    $controle->set_onChange('formPadrao.submit();');
    $controle->set_linha(3);
    $controle->set_col(1);
    $form->add_item($controle);

    $controle = new Input('postEmpresaCnpj', 'simnao', 'CNPJ:', 1);
    $controle->set_size(5);
    $controle->set_title('O CNPJ da empresa contratada');
    $controle->set_valor($postEmpresaCnpj);
    $controle->set_onChange('formPadrao.submit();');
    $controle->set_linha(3);
    $controle->set_col(1);
    $form->add_item($controle);
    
    $controle = new Input('postEmpresaEmail', 'simnao', 'Emp.Email:', 1);
    $controle->set_size(5);
    $controle->set_title('O e-mail da empresa contratada');
    $controle->set_valor($postEmpresaEmail);
    $controle->set_onChange('formPadrao.submit();');
    $controle->set_linha(3);
    $controle->set_col(1);
    $form->add_item($controle);    
    
    $controle = new Input('postEmpresaContato', 'simnao', 'Emp.Contato:', 1);
    $controle->set_size(5);
    $controle->set_title('O contato da empresa contratada');
    $controle->set_valor($postEmpresaContato);
    $controle->set_onChange('formPadrao.submit();');
    $controle->set_linha(3);
    $controle->set_col(1);
    $form->add_item($controle);

    $controle = new Input('postComissao', 'simnao', 'Comissão:', 1);
    $controle->set_size(5);
    $controle->set_title('Os membros da comissão');
    $controle->set_valor($postComissao);
    $controle->set_onChange('formPadrao.submit();');
    $controle->set_linha(3);
    $controle->set_col(1);
    $form->add_item($controle);

    $controle = new Input('postComissaoEmail', 'simnao', 'Com.Email:', 1);
    $controle->set_size(5);
    $controle->set_title('Os membros da comissão com e-mail');
    $controle->set_valor($postComissaoEmail);
    $controle->set_onChange('formPadrao.submit();');
    $controle->set_linha(3);
    $controle->set_col(1);
    $form->add_item($controle);

    $controle = new Input('postValorUltAditivo', 'simnao', 'Val.Ult.TA:', 1);
    $controle->set_size(5);
    $controle->set_title('O valor do último aditivo');
    $controle->set_valor($postValorUltAditivo);
    $controle->set_onChange('formPadrao.submit();');
    $controle->set_linha(3);
    $controle->set_col(1);
    $form->add_item($controle);

    $controle = new Input('postValorTotal', 'simnao', 'Val.Tot:', 1);
    $controle->set_size(5);
    $controle->set_title('O valor total do contrato');
    $controle->set_valor($postValorTotal);
    $controle->set_onChange('formPadrao.submit();');
    $controle->set_linha(3);
    $controle->set_col(1);
    $form->add_item($controle);

    /*
     * Ano do Contrato
     */

    # Pega os dados
    $comboAno = $contratos->select('SELECT DISTINCT YEAR(dtAssinatura), YEAR(dtAssinatura)
                                                  FROM tbcontrato
                                                 WHERE dtAssinatura IS NOT NULL
                                              ORDER BY YEAR(dtAssinatura)');

    array_unshift($comboAno, array(null, "Todos"));

    # Ano
    $controle = new Input('parametroAno', 'combo', 'Ano:', 1);
    $controle->set_size(20);
    $controle->set_title('Ano da assinatura do contrato');
    $controle->set_valor($parametroAno);
    $controle->set_onChange('formPadrao.submit();');
    $controle->set_linha(4);
    $controle->set_col(2);
    $controle->set_array($comboAno);
    $controle->set_fieldset("Informe o Filtro:");
    $form->add_item($controle);

    /*
     * Status
     */

    # Pega os dados
    $comboStatus = $contratos->select('SELECT idStatus, status
                                      FROM tbstatus
                                  ORDER BY idStatus');

    array_unshift($comboStatus, array(null, "Todos"));

    # Status
    $controle = new Input('parametroStatus', 'combo', 'Status:', 1);
    $controle->set_size(20);
    $controle->set_title('Status do contrato');
    $controle->set_valor($parametroStatus);
    $controle->set_onChange('formPadrao.submit();');
    $controle->set_linha(4);
    $controle->set_col(2);
    $controle->set_array($comboStatus);
    $form->add_item($controle);

    /*
     * Modalidade
     */

    # Pega os dados
    $comboModalidade = $contratos->select('SELECT idModalidade, modalidade
                                               FROM tbmodalidade
                                           ORDER BY idModalidade');

    array_unshift($comboModalidade, array(null, "Todos"));

    # Modalidade
    $controle = new Input('parametroModalidade', 'combo', 'Modalidade:', 1);
    $controle->set_size(20);
    $controle->set_title('Modalidade do contrato');
    $controle->set_valor($parametroModalidade);
    $controle->set_onChange('formPadrao.submit();');
    $controle->set_linha(4);
    $controle->set_col(2);
    $controle->set_array($comboModalidade);
    $form->add_item($controle);
    
    # Tipo de Modalidade
    $controle = new Input('parametroModalidadeTipo', 'combo', 'Tipo:', 1);
    $controle->set_size(20);
    $controle->set_title('Se é despesa ou receita');
    $controle->set_valor($parametroModalidadeTipo);
    $controle->set_onChange('formPadrao.submit();');
    $controle->set_linha(4);
    $controle->set_col(2);
    $controle->set_array(["Todos","Despesa","Receita"]);    
    $form->add_item($controle);
    
    # Mao de obra alocada
    $controle = new Input('parametroMaoDeObra', 'combo', 'Mão de Obra:', 1);
    $controle->set_size(20);
    $controle->set_title('Se tem mão de obra alocada');
    $controle->set_valor($parametroMaoDeObra);
    $controle->set_onChange('formPadrao.submit();');
    $controle->set_linha(4);
    $controle->set_col(2);
    $controle->set_array(array(array("S","Sim"),array("N","Não"),array(null,"Todos")));    
    $form->add_item($controle);

    /*
     * Empresa
     */

    # Pega os dados
    $comboEmpresa = $contratos->select('SELECT idEmpresa, razaoSocial
                                               FROM tbempresa
                                           ORDER BY razaoSocial');

    array_unshift($comboEmpresa, array(null, "Todas"));

    # Empresa
    $controle = new Input('parametroEmpresa', 'combo', 'Empresa:', 1);
    $controle->set_size(20);
    $controle->set_title('Empresa contratada');
    $controle->set_valor($parametroEmpresa);
    $controle->set_onChange('formPadrao.submit();');
    $controle->set_linha(4);
    $controle->set_col(2);
    $controle->set_array($comboEmpresa);
    $form->add_item($controle);

    $form->show();

    # Inicia os arrays com o número do processo
    $field[] = "numero";
    $label[] = "Número";
    $align[] = "center";
    $class[] = "";
    $method[] = "";
    $function[] = "";

    if ($postSiafe) {
        $field[] = "siafe";
        $label[] = "Siafe";
        $align[] = "center";
        $class[] = "";
        $method[] = "";
        $function[] = "";
    }

    # Preenche os arrays de acordo com o formulario
    if ($postModalidade) {
        $field[] = "idContrato";
        $label[] = "Modalidade";
        $align[] = "center";
        $class[] = "Contrato";
        $method[] = "exibeModalidade";
        $function[] = "";
    }

    if ($postStatus) {
        $field[] = "idContrato";
        $label[] = "Status";
        $align[] = "center";
        $class[] = "Contrato";
        $method[] = "getStatus";
        $function[] = "";
    }

    if ($postObjeto) {
        $field[] = "objeto";
        $label[] = "Objeto";
        $align[] = "left";
        $class[] = "";
        $method[] = "";
        $function[] = "";
    }

    if ($postMaoDeObra) {
        $field[] = "IF(maoDeObra,'Sim','---')";
        $label[] = "Mão de Obra";
        $align[] = "center";
        $class[] = "";
        $method[] = "";
        $function[] = "";
    }
    
    if ($postProcesso) {
        $field[] = "idContrato";
        $label[] = "Processo";
        $align[] = "center";
        $class[] = "Contrato";
        $method[] = "getProcesso";
        $function[] = "";
    }

    if ($postPublicacao) {
        $field[] = "idContrato";
        $label[] = "Publicação";
        $align[] = "center";
        $class[] = "Contrato";
        $method[] = "getDtPublicacao";
        $function[] = "";
    }

    if ($postAssinatura) {
        $field[] = "dtAssinatura";
        $label[] = "Assinatura";
        $align[] = "center";
        $class[] = "";
        $method[] = "";
        $function[] = "date_to_php";
    }    

    if ($postProposta) {
        $field[] = "dtProposta";
        $label[] = "Proposta";
        $align[] = "center";
        $class[] = "";
        $method[] = "";
        $function[] = "date_to_php";
    }
    
    if ($postDuracao) {
        $field[] = "idContrato";
        $label[] = "Duração";
        $align[] = "center";
        $class[] = "Contrato";
        $method[] = "exibeDuracao";
        $function[] = "";
    }
    
    if ($postPeriodo) {
        $field[] = "idContrato";
        $label[] = "Periodo";
        $align[] = "center";
        $class[] = "Contrato";
        $method[] = "exibePeriodo";
        $function[] = "";
    }
    
    if ($postPrazo) {
        $field[] = "idContrato";
        $label[] = "Prazo";
        $align[] = "center";
        $class[] = "Contrato";
        $method[] = "exibePrazo";
        $function[] = "";
    }
    
    if ($postVigencia) {
        $field[] = "idContrato";
        $label[] = "Vigencia";
        $align[] = "center";
        $class[] = "Contrato";
        $method[] = "getVigencia";
        $function[] = "";
    }

    if ($postEmpresa) {
        $field[] = "idEmpresa";
        $label[] = "Empresa";
        $align[] = "left";
        $class[] = "Empresa";
        $method[] = "getEmpresa";
        $function[] = "";
    }

    if ($postEmpresaCnpj) {
        $field[] = "idEmpresa";
        $label[] = "CNPJ";
        $align[] = "left";
        $class[] = "Empresa";
        $method[] = "getCnpj";
        $function[] = "";
    }
    
    if ($postEmpresaEmail) {
        $field[] = "idEmpresa";
        $label[] = "Empresa Email";
        $align[] = "left";
        $class[] = "Empresa";
        $method[] = "getEmails";
        $function[] = "";
    }
    
    if ($postEmpresaContato) {
        $field[] = "idEmpresa";
        $label[] = "Empresa Contato";
        $align[] = "left";
        $class[] = "Empresa";
        $method[] = "getContatoComTel";
        $function[] = "";
    }

    if ($postComissao) {
        $field[] = "idContrato";
        $label[] = "Comissão";
        $align[] = "left";
        $class[] = "Comissao";
        $method[] = "listaComissaoTabela";
        $function[] = "";
    }

    if ($postComissaoEmail) {
        $field[] = "idContrato";
        $label[] = "Comissão";
        $align[] = "left";
        $class[] = "Comissao";
        $method[] = "listaComissaoEmailTabela";
        $function[] = "";
    }

    if ($postValorUltAditivo) {
        $field[] = "idContrato";
        $label[] = "Valor Último Aditivo";
        $align[] = "right";
        $class[] = "Contrato";
        $method[] = "exibeValorUltimoAditivo";
        $function[] = "";
    }

    if ($postValorTotal) {
        $field[] = "idContrato";
        $label[] = "Valor Total";
        $align[] = "right";
        $class[] = "Contrato";
        $method[] = "exibeValorTotal";
        $function[] = "";
    }

    if (count($field) > 0) {

        # Monta o select
        $select = "SELECT ";

        foreach ($field as $item) {
            $select .= "{$item},";
        }

        $select = rtrim($select, ',');

        # Adiciona as tabelas
        $select .= " FROM tbcontrato JOIN tbmodalidade USING (idModalidade)";

        # Adiciona filtro
        $select .= " WHERE true";

        if (!empty($parametroEmpresa)) {
            $select .= " AND idEmpresa = {$parametroEmpresa}";
        }

        if (!empty($parametroAno)) {
            $select .= " AND YEAR(dtAssinatura) = {$parametroAno}";
        }

        if (!empty($parametroModalidade)) {
            $select .= " AND idModalidade = {$parametroModalidade}";
        }
        
        if ($parametroModalidadeTipo <> "Todos") {
            $select .= " AND tbmodalidade.tipo = '{$parametroModalidadeTipo}'";
        }

        if (!empty($parametroStatus)) {
            $select .= " AND idStatus = {$parametroStatus}";
        }
        
        if (!empty($parametroMaoDeObra)) {
            if($parametroMaoDeObra == "S"){
                $select .= " AND maoDeObra = 1";
            }else{
                $select .= " AND maoDeObra <> 1";
            }
        }

        # Estabelece a ordenação
        $select .= " ORDER BY numero";

        #var_dump($select);
        
        $contratos = new Contratos();
        $row = $contratos->select($select);
    } else {
        $row = null;
    }

    $tabela = new Tabela();
    $tabela->set_titulo("Planilha Personalizada para Cópia");
    $tabela->set_label($label);
    $tabela->set_align($align);
    #$tabela->set_width($width);
    $tabela->set_classe($class);
    $tabela->set_metodo($method);
    $tabela->set_funcao($function);
    $tabela->set_conteudo($row);
    $tabela->show();

    $grid->fechaColuna();
    $grid->fechaGrid();

    $page->terminaPagina();
} else {
    loadPage("../../areaServidor/sistema/login.php");
}