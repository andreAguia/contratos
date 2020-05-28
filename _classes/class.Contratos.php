<?php

class Contratos extends Bd {
    /** 
     * Classe de acesso ao Banco de Dados
     * 
     * @author André Águia (Alat) - alataguia@gmail.com
     * 
     * @var private $servidor string localhost O nome do servidor do banco de dados
     * @var private $usuario  string NULL      O nome do usuário no banco de dados
     * @var private $senha    string NULL      A senha de acesso ao banco de dados
     * @var private $banco    string pessoal   O nome do banco de dados a ser acessado pela classe
     * @var private $sgdb     string mysql     O nome do SGDB a ser utilizado
     * @var private $tabela   string NULL      A tabela que está sendo acessada
     * @var private $idCampo  string NULL      O nome do campo id da tabela    
     */
	
    private $servidor = "localhost";
    private $usuario = "intranet";
    private $senha = "txzVHnMdh53ZWX9p";
    private $banco = "uenf_contratos";
    private $sgdb = "mysql";
    private $tabela;
    private $idCampo;
    
###########################################################
	
    /**
     * Faz uma conexão
     */ 
    
    public function __construct(){
        parent::__construct($this->servidor,$this->usuario,$this->senha,$this->banco,$this->sgdb);		
    }
    
###########################################################
	
    /**
     * Método set_tabela
     * 
     * @param  	$nomeTabela	-> Nome da tabela do banco de dados intra que será utilizada
     */
    public function set_tabela($nomeTabela){
        $this->tabela = $nomeTabela;
    }

###########################################################

    /**
     * Método set_idCampo
     * 
     * @param  	$idCampo)	-> Nome do campo chave da tabela
     */
    public function set_idCampo($idCampo)
    {
        $this->idCampo = $idCampo;
    }

###########################################################

    /**
     * Método Gravar
     */
    public function gravar($campos = NULL,$valor = NULL,$idValor = NULL,$tabela = NULL,$idCampo = NULL,$alerta = FALSE){

        if(is_null($tabela)){
            $tabela = $this->tabela;
        }

        if(is_null($idCampo)){
            $idCampo = $this->idCampo;
        }

        parent::gravar($campos,$valor,$idValor,$tabela,$idCampo,$alerta);
    }

###########################################################

    /**
     * Método Excluir
     */
    public function excluir($idValor = NULL,$tabela = NULL,$idCampo = 'id'){

        # efetua a exclus�o
        parent::excluir($idValor,$this->tabela,$this->idCampo);

        return TRUE;		
    }

##############################################################
    
}