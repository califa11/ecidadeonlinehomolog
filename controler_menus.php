<?
session_start();
global $HTTP_SESSION_VARS;
include ("libs/db_stdlib.php");
db_postmemory($HTTP_SERVER_VARS);

if (!isset ($login)) { // sem estar logado
	
  if (!isset ($DB_LOGADO)) { // entra aqui tb
    // echo("destruindo sessoes!");
    session_destroy();
  } else {
    session_register("DB_acesso");
  }
} else {
  session_register("DB_acesso");
}

if($menu == "issqn"){
   echo "<script>location.href = 'digitainscricao.php';</script>";
}
if($menu == "issqn_retido"){
   echo "<script>location.href = 'digitaissqn.php';</script>";
}
if($menu == "cad_empresa"){
   echo "<script>location.href = 'listaescritorios.php';</script>";
}
if($menu == "certidao"){
   echo "<script>location.href = 'certidao.php';</script>";
}
if($menu == "imoveis"){
   echo "<script>location.href = 'digitamatricula_arapiraca.php';</script>";
}
if($menu == "itbi_urbano"){
   echo "<script>location.href = 'itbi_urbano.php';</script>";
}
if($menu == "itbi_rural"){
   echo "<script>location.href = 'itbi_rural.php';</script>";
}
if($menu == "itbi_consulta"){
   echo "<script>location.href = 'itbi_consulta.php';</script>";
}
if($menu == "dai"){
   echo "<script>location.href = 'digitadae.php';</script>";
}
if($menu == "contribuinte"){
   echo "<script>location.href = 'digitacontribuinte.php';</script>";
}	
if($menu == "aidof"){
   echo "<script>location.href = 'digitaaidof.php';</script>";
}
if($menu == "protocolo"){
   echo "<script>location.href = 'digitaconsultaprocesso.php';</script>";
}
if($menu == "licitacao"){
   echo "<script>location.href = 'lic_menu.php';</script>";
}
if($menu == "fornecedor"){
   echo "<script>location.href = 'digitafornecedor.php';</script>";
}
if($menu == "orcamento"){
   echo "<script>location.href = 'for_orcamento.php';</script>";
}
if($menu == "ordem_compra"){
   echo "<script>location.href = 'for_ordemcompra.php';</script>";
}
?>