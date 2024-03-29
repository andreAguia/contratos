<?php

/**
 * Cadastro de Campus
 *  
 * By Alat
 */
# Reservado para o servidor logado
$idUsuario = null;

# Configuração
include ("_config.php");

# Permissão de Acesso
$acesso = Verifica::acesso($idUsuario, [1, 9]);

if ($acesso) {

    # Conecta ao Banco de Dados
    $contratos = new Contratos();

    # log
    if (get('i', false)) {
        # Grava no log a atividade
        $atividade = "Acessou a área que lista os servidores das comissões de fiscalização.";
        $data = date("Y-m-d H:i:s");
        $intra->registraLog($idUsuario, $data, $atividade, null, null, 7);
    }

    # Verifica a fase do programa
    $fase = get("fase", "listar");

    # pega o id (se tiver)
    $id = soNumeros(get("id"));

    # Pega os parâmetros
    $parametroMembro = post("parametroMembro");

    # Começa uma nova página
    $page = new Page();
    $page->iniciaPagina();

    # Cabeçalho da Página
    AreaServidor::cabecalho();

    ################################################################

    switch ($fase) {
        case "":
        case "listar" :

            # Limita o tamanho da tela
            $grid = new Grid();
            $grid->abreColuna(12);

            # Cria um menu
            $menu = new MenuBar();

            # Botão voltar
            $linkBotao1 = new Link("Voltar", "cadastroContrato.php");
            $linkBotao1->set_class('button');
            $linkBotao1->set_title('Volta para a página anterior');
            $linkBotao1->set_accessKey('V');
            $menu->add_link($linkBotao1, "left");

            $menu->show();

            /*
             *  Formulário de Pesquisa
             */
            $form = new Form('?');
            /*
             * Empresa
             */

            # Pega os dados
            $comboMembro = $contratos->select('SELECT DISTINCT idServidor, tbpessoa.nome
                                                  FROM uenf_contratos.tbcomissao LEFT JOIN uenf_grh.tbservidor USING (idServidor)
                                                                                      JOIN uenf_grh.tbpessoa USING (idPessoa)
                                           ORDER BY tbpessoa.nome');

            array_unshift($comboMembro, array(null, "Todas"));

            # Empresa
            $controle = new Input('parametroMembro', 'texto', 'Membro:', 1);
            $controle->set_size(20);
            $controle->set_title('Pesquisa por Membro da Comissão');
            $controle->set_valor($parametroMembro);
            $controle->set_onChange('formPadrao.submit();');
            $controle->set_linha(1);
            $controle->set_col(5);
            #$controle->set_array($comboMembro);
            $controle->set_autofocus(true);
            $form->add_item($controle);

            $form->show();

            $select = "(SELECT uenf_grh.tbpessoa.nome,
                                idComissao,
                                idContrato,
                                idContrato,
                                idComissao
                           FROM tbcomissao JOIN uenf_grh.tbservidor USING(idServidor)
                                           JOIN uenf_grh.tbpessoa USING(idPessoa)";

            if (!empty($parametroMembro)) {
                $select .= " WHERE tbpessoa.nome like '%{$parametroMembro}%'";
            }

            $select .= " ) UNION 
                    (SELECT tbmembroexterno.nome,
                            idComissao,
                            idContrato,
                            idContrato,
                            idComissao
                       FROM tbcomissao JOIN tbmembroexterno USING (idMembroExterno)";

            if (!empty($parametroMembro)) {
                $select .= " WHERE tbmembroexterno.nome like '%{$parametroMembro}%'";
            }

            $select .= " ) ORDER BY 1";

            $row = $contratos->select($select);

            # Monta a tabela
            $tabela = new Tabela();
            $tabela->set_titulo("Membros de Comissão de Fiscalização");
            $tabela->set_label(["Servidor", "Tipo", "Contrato", "Objeto", "Designação"]);
            $tabela->set_align(["left", "center", "center", "left", "left"]);
            $tabela->set_width([20, 10, 10, 40, 20]);
            $tabela->set_funcaoDepoisClasse([null, "ressaltaSaiu"]);
            $tabela->set_classe([null, "Comissao", "Contrato", "Contrato", "Comissao"]);
            $tabela->set_metodo([null, "getTipo", "exibeNumeroContratoSimples", "exibeObjeto", "getDadosDesignacao"]);
            $tabela->set_conteudo($row);
            $tabela->set_rowspan(0);
            $tabela->set_grupoCorColuna(0);
            $tabela->show();
            break;
    }

    $page->terminaPagina();
} else {
    loadPage("../../areaServidor/sistema/login.php");
}    