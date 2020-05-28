<?php
class Situacao
{
 /**
  * Abriga as várias rotina referentes a concurso
  *
  * @author André Águia (Alat) - alataguia@gmail.com
  * 
  * @var private $idConcurso integer NULL O id do concurso
  */
    
    private $idSituacao = null;

##############################################################

    public function __construct($idSituacao = NULL){
    /**
     * Inicia a Classe somente
     * 
     * @param $idContrato integer NULL O id do concurso
     * 
     * @syntax $concurso = new Concurso([$idConcurso]);
     */
        
        $this->idSituacao = $idSituacao;
    }
  
##############################################################

    public function get_dados($idSituacao = NULL){

    /**
     * Informa os dados da base de dados
     * 
     * @param $idConcurso integer NULL O id do concurso
     * 
     * @syntax $concurso->get_dados([$idConcurso]);
     */
        
        # Joga o valor informado para a variável da classe
        if(!vazio($idSituacao)){
            $this->idSituacao = $idSituacao;
        }
        
        # Conecta ao Banco de Dados
        $contratos = new Contratos();

        # Verifica se foi informado
        if(vazio($this->idSituacao)){
            alert("É necessário informar o id da Situação.");
            return;
        }

        # Pega os dados
        $select = 'SELECT * 
                     FROM tbsituacao
                    WHERE idSituacao = '.$this->idSituacao;
       
        $row = $contratos->select($select,FALSE);

        # Retorno
        return $row;
    }

    #####################################################################################
    
    public function get_situacaoAtual($idContrato){
        
        # Conecta ao Banco de Dados
        $contratos = new Contratos();

        # Verifica se foi informado
        if(vazio($idContrato)){
            alert("É necessário informar o id do Contrato.");
            return;
        }
        
        # Pega os dados
        $select = "SELECT situacao
                     FROM tbsituacao
                    WHERE idContrato = {$idContrato}
                 ORDER BY idSituacao desc";
       
        $row = $contratos->select($select,FALSE);
        
        # Retorno
        if(vazio($row)){
            return "---";
        }else{            
            return $row[0];
        }
    }
    
    #####################################################################################
    
    function exibeSituacaoAtual($idContrato){ 
        
        # Conecta ao Banco de Dados
        $contratos = new Contratos();

        # Verifica se foi informado
        if(vazio($idContrato)){
            alert("É necessário informar o id do Contrato.");
            return;
        }
        
        # Pega os dados
        $select = "SELECT *
                     FROM tbsituacao
                    WHERE idContrato = {$idContrato}
                 ORDER BY idSituacao desc";
                    
        $situacao = $contratos->select($select,FALSE);
        
        # Limita a tela
        $grid = new Grid();
        $grid->abreColuna(12);
        
        $painel = new Callout("warning");
        $painel->abre();
            
            p(date_to_php($situacao['data']),"situacaoData");
            p("Situação Atual","contratoLabel");
            p($situacao['situacao'],"contratoConteudo");
            
                        
        $grid->fechaColuna();
        $grid->fechaGrid(); 
        $painel->fecha();
    }

    ###########################################################
}
