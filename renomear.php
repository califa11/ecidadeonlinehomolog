<?php

$caminho = "comprovante2019/";
$diretorio = dir($caminho);
 
while($arquivo = $diretorio -> read()){

	rename($caminho.$arquivo, $caminho.str_replace(' ', '',$arquivo));

}
$diretorio -> close();

echo "finalizou!";

?>