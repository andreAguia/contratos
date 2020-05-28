<?php
/**
 * Cadastro de Campus
 *  
 * By Alat
 */

# Reservado para o servidor logado
$idUsuario = NULL;

# Configuração
include ("_config.php");

# Permissão de Acesso
$acesso = Verifica::acesso($idUsuario,9);

if($acesso){    
    # Conecta ao Banco de Dados
    $intra = new Intra();
    $contrato = new Contrato();
    $pessoal = new Pessoal();
	
    # Verifica a fase do programa
    $fase = get("fase","listar");
    
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
    $objeto->set_nome("Status de Contrato");

    # Botão de voltar da lista
    $objeto->set_voltarLista("areaInicial.php");
    
    # select da lista
    $objeto->set_selectLista ("SELECT idStatus,
                                      status,
                                      obs
                                 FROM tbstatus
                             ORDER BY status");

    # select do edita
    $objeto->set_selectEdita("SELECT status,
                                     obs
                                FROM tbstatus
                              WHERE idStatus = {$id}");

    # Caminhos
    $objeto->set_linkEditar("?fase=editar");
    $objeto->set_linkExcluir("?fase=excluir");
    $objeto->set_linkGravar("?fase=gravar");
    $objeto->set_linkListar("?fase=listar");

    # Parametros da tabela
    $objeto->set_label(array("Id","Campus","Obs"));
    $objeto->set_width(array(5,40,45));
    $objeto->set_align(array("center","left","left"));

    # Classe do banco de dados
    $objeto->set_classBd("Contratos");

    # Nome da tabela
    $objeto->set_tabela("tbstatus");

    # Nome do campo id
    $objeto->set_idCampo("idStatus");

    # Tipo de label do formulário
    $objeto->set_formlabelTipo(1);

    # Campos para o formulario
    $objeto->set_campos(array(
        array ("linha" => 1,
               "nome" => "status",
               "label" => "Status:",
               "tipo" => "texto",
               "required" => TRUE,
               "autofocus" => TRUE,
               "col" => 6,
               "size" => 100), 
        array ("linha" => 2,
               "nome" => "obs",
               "label" => "Observação:",
               "tipo" => "textarea",
               "size" => array(80,5))));

    # idUsuário para o Log
    $objeto->set_idUsuario($idUsuario);

    ################################################################
    switch ($fase){
        case "" :
        case "listar" :
            $objeto->listar();
            break;

        case "editar" :	
        case "excluir" :	
        case "gravar" :
            $objeto->$fase($id);
            break;
    }									 	 		

    $page->terminaPagina();
}else{
    loadPage("../../areaServidor/sistema/login.php");
}