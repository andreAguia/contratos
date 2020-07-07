<?php

/*
 * Faz com que após ser incluído o sistema possa
 * ser redirecionado 
 */

if (get_session('inclusaoEmpresa') == "nova") {

    # Joga os parâmetros para as sessions
    set_session("inclusaoEmpresa", $id);
    $this->linkListar = "cadastroContrato.php?fase=editar";
}
