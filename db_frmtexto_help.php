<?
include("libs/db_conecta.php");
include("libs/db_stdlib.php");
include("libs/db_sql.php");

parse_str($HTTP_SERVER_VARS['QUERY_STRING']);

$result = pg_exec("select mens,alinhamento from db_confmensagem where cod = '".$nomepagina."'");
if(pg_numrows($result)>0){
  db_fieldsmemory($result,0);
}else{
  $mens = "";
}
$texto_help = $mens;

?>
<html>
<head>
<title><?=$w01_titulo?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script language="JavaScript" src="scripts/db_script.js"></script>
<style type="text/css">
</style>
</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" bgcolor="white" >
<center>
<table width="766" border="0" cellpadding="0" cellspacing="0" bgcolor="white">
  <tr>
     <td align="left" valign="top"> 
       <?=$texto_help?>
     </td>
  </tr>
</table>
</center>
</form>
</body>
</html>



