<?php

/**
 * Cadastro de Natureza de Pgto
 *
 * By Alat
 */
# Reservado para o servidor logado
$idUsuario = null;

# Configuração
include "_config.php";

# Permissão de Acesso
$acesso = Verifica::acesso($idUsuario, [1, 9]);

if ($acesso) {
    # Conecta ao Banco de Dados
    $status = new Status();

    # log
    if (get('i', false)) {
        # Grava no log a atividade
        $atividade = "Acessou o cadastro de Membros Externos da comissão.";
        $data = date("Y-m-d H:i:s");
        $intra->registraLog($idUsuario, $data, $atividade, null, null, 7);
    }

    # Verifica a fase do programa
    $fase = get("fase", "listar");

    # pega o id (se tiver)
    $id = soNumeros(get("id"));

    # Começa uma nova página
    $page = new Page();
    $page->iniciaPagina();

    # Cabeçalho da Página
    AreaServidor::cabecalho();

    # Abre um novo objeto Modelo
    $objeto = new Modelo();

    ################################################################
    # Nome do Modelo
    $objeto->set_nome("Membros Externos de Comissões");

    # Botão de voltar da lista
    $objeto->set_voltarLista("cadastroContrato.php");

    # select da lista
    $objeto->set_selectLista("SELECT  cpf,
                                      nome,
                                      orgao,
                                      telefone,
                                      email,
                                      obs,
                                      idMembroExterno
                                 FROM tbmembroexterno
                             ORDER BY nome");

    # select do edita
    $objeto->set_selectEdita("SELECT cpf,
                                     nome,
                                     orgao,
                                     telefone,
                                     email,
                                     obs
                                FROM tbmembroexterno
                               WHERE idMembroExterno = {$id}");

    # Caminhos
    $objeto->set_linkEditar("?fase=editar");
    $objeto->set_linkExcluir("?fase=excluir");
    $objeto->set_linkGravar("?fase=gravar");
    $objeto->set_linkListar("?fase=listar");

    # Parametros da tabela
    $objeto->set_label(["Cpf", "Nome", "Órgão", "Telefones", "E-mail", "Obs"]);
    $objeto->set_width([10, 20, 20, 15, 15, 20]);
    $objeto->set_align(["center", "left", "left", "left", "left", "left"]);

    # Classe do banco de dados
    $objeto->set_classBd("Contratos");

    # Nome da tabela
    $objeto->set_tabela("tbmembroexterno");

    # Nome do campo id
    $objeto->set_idCampo("idMembroExterno");

    # Tipo de label do formulário
    $objeto->set_formLabelTipo(1);

    # Campos para o formulario
    $objeto->set_campos(array(
        array("linha" => 1,
            "nome" => "cpf",
            "label" => "CPF:",
            "tipo" => "cpf",
            "required" => true,
            "autofocus" => true,
            "col" => 3,
            "size" => 50),
        array("linha" => 1,
            "nome" => "nome",
            "label" => "Nome:",
            "tipo" => "texto",
            "required" => true,
            "col" => 9,
            "size" => 250),
        array("linha" => 2,
            "nome" => "orgao",
            "label" => "Órgão:",
            "tipo" => "texto",
            "required" => true,
            "col" => 4,
            "size" => 250),
        array("linha" => 2,
            "nome" => "telefone",
            "label" => "Telefone:",
            "tipo" => "texto",
            "col" => 4,
            "size" => 250),
        array("linha" => 2,
            "nome" => "email",
            "label" => "E-mail:",
            "tipo" => "texto",
            "col" => 4,
            "size" => 250),
        array("linha" => 3,
            "nome" => "obs",
            "label" => "Observação:",
            "tipo" => "textarea",
            "size" => array(80, 5))));

    # idUsuário para o Log
    $objeto->set_idUsuario($idUsuario);

    ################################################################
    switch ($fase) {
        case "":
        case "listar":
            $objeto->listar();
            break;

        ################################################################

        case "excluir":
            # Verifica se tem contrato com esse status
            $pagamento = new Pagamento();
            $getNumPgtoNatureza = $pagamento->getNumPgtoNatureza($id);

            if ($getNumPgtoNatureza > 0) {
                alert("Existem pagamentos cadastrados com esta natureza. Dessa forma a mesma NÃO poderá ser excluída.");
                back(1);
            } else {
                $objeto->excluir($id);
            }
            break;

        ################################################################

        case "editar":
        case "gravar":
            $objeto->$fase($id);
            break;
    }

    $page->terminaPagina();
} else {
    loadPage("../../areaServidor/sistema/login.php");
}
