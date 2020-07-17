<?php

class Pagamento {

    public function getDados($idPagamento = null) {
        # Conecta ao Banco de Dados
        $contratos = new Contratos();

        # Verifica se foi informado
        if (vazio($idPagamento)) {
            alert("É necessário informar o id dp Pagamento.");
            return;
        }

        # Pega os dados
        $select = 'SELECT *
                     FROM tbpagamento
                    WHERE idPagamento = ' . $idPagamento;

        $row = $contratos->select($select, false);

        # Retorno
        return $row;
    }

#####################################################################################

    public function listaPagamentos($idContrato) {
        # Verifica se foi informado
        if (vazio($idContrato)) {
            alert("É necessário informar o id.");
            return;
        }

        # Conecta ao Banco de Dados
        $contratos = new Contratos();

        # monta o select
        $select = "SELECT data,
                          notaFiscal,
                          referencia,
                          valor,
                          idPagamento
                     FROM tbpagamento
                    WHERE idContrato = {$idContrato}
                 ORDER BY data";

        $row = $contratos->select($select);
        $total = $contratos->count($select);

        # Monta a tabela
        $tabela = new Tabela();
        $tabela->set_label(array("Data", "Nota Fiscal", "Referência", "Valor"));
        $tabela->set_align(array("center", "left", "left", "left"));
        $tabela->set_width(array(10, 20, 20, 20));
        #$objeto->set_funcao(array(null, null, null, null, null, "exibeFoto"));
        #$objeto->set_classe(array("Comissao", "pessoal", "Comissao", "pessoal", "Comissao"));
        #$objeto->set_metodo(array("getTipo", "get_foto", "getDadosMembro", "get_contatos", "getDadosDesignacao"));
        $tabela->set_numeroOrdem(true);
        $tabela->set_conteudo($row);
        $tabela->show();

        # Editar
        $div = new Div("divEdita1Comissao");
        $div->abre();

        $div = new Div("divEdita2");
        $div->abre();

        $botaoEditar = new Link("Editar", "cadastroComissao.php");
        $botaoEditar->set_class('tiny button secondary');
        $botaoEditar->set_title('Editar situação');
        $botaoEditar->show();

        $div->fecha();
        $div->fecha();
    }

#####################################################################################
}
