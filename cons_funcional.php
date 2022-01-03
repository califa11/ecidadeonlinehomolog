<?
error_reporting('** CONSULTA FUNCIONAL! **');
session_start();

//print_r($_SESSION);

include("libs/db_conecta.php");
include("libs/db_stdlib.php");
include("libs/db_sql.php");
include("libs/db_utils.php");
include("dbforms/db_funcoes.php");

parse_str(base64_decode($HTTP_SERVER_VARS["QUERY_STRING"]));
$oGet = db_utils::postMemory($_GET);
 
$numcgm     = db_getsession("DB_login");
$anoFolha   = db_anofolha();
$mesFolha   = db_mesfolha();
db_logs("","",0,"Consulta Funcional.");

?>
<html>
<head>
<title><?=$w01_titulo?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="config/estilos.css" rel="stylesheet" type="text/css">
<script language="JavaScript" src="scripts/db_script.js"></script>
</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" bgcolor="<?=$w01_corbody?>">
<table width="100%" border="0" bordercolor="#cccccc" cellpadding="2" cellspacing="0" class="texto">
 <tr>
  <td><br></td>
 </tr>
</table>
<?
if ($id_usuario != "") { 
?>

<script>
  var idusuario = '<?=base64_encode("id_usuario=".$id_usuario)?>';
  document.location.href = 'cons_cgmservrhpessoal.php?'+idusuario;
</script>

<?
} else if ($w13_permfornsemlog == "f") {
?>
 <table width="300" align="center" border="0" bordercolor="#cccccc" cellpadding="2" cellspacing="0" class="texto">
  <tr height="220">
   <td align="center">
    <img src="imagens/atencao.gif"><br>
    Para acessar suas informações, efetue login.
   </td>
  </tr>
 </table>
<?
}
?>
</body>
</html>