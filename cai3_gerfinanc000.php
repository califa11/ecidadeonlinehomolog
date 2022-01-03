<?
session_start();
require("libs/db_stdlib.php");
include("dbforms/db_funcoes.php");
parse_str($HTTP_SERVER_VARS['QUERY_STRING']);

?>
<html>
<head>
<title>Documento sem t&iacute;tulo</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script language="JavaScript" src="scripts/scripts.js"></script>
<script type="text/javascript" src="scripts/strings.js"></script>
<script type="text/javascript" src="scripts/prototype.js"></script>
<style type="text/css">
<?
db_estilosite();
?>
</style>
<?
 if (isset($suspensao)) { 
   ?>
   <iframe id="iframe" name="iframe" src="cai3_gerfinanc008.php?numcgm=<?=@$numcgm?>&matric=<?=@$matric?>&inscr=<?=@$inscr?>&tipo=<?=@$tipo?>&emrec=<?=@$emrec?>&agnum=<?=@$agnum?>&agpar=<?=@$agpar?>&db_datausu=<?=date('Y-m-d',db_getsession('DB_datausu'))?>&id_usuario=<?=@$id_usuario?>&cgccpf=<?=$cgccpf?>" width="100%" height="270"></iframe>
   <? 	
 } else if( isset($tipo) && $tipo == 3){
  ?>	
    <script>alert("Atenção!\n\Informe os valores clicando nas caixas de texto.\n\nApós, clique em Agrupar para selecionar\nas parcelas que deseja emitir o Recibo.");</script>
    <iframe id="iframe" name="iframe" src="cai3_gerfinanc002.php?numcgm=<?=@$numcgm?>&matric=<?=@$matric?>&inscr=<?=@$inscr?>&tipo=<?=@$tipo?>&emrec=<?=@$emrec?>&agnum=<?=@$agnum?>&agpar=<?=@$agpar?>&db_datausu=<?=date('Y-m-d',db_getsession('DB_datausu'))?>&id_usuario=<?=@$id_usuario?>&cgccpf=<?=$cgccpf?>" width="100%" height="270"></iframe>	
  <?
 } else if( isset($tipo) &&  $tipo == 19){
   ?>
   <iframe id="iframe" name="iframe" src="cai3_gerfinanc040.php?numcgm=<?=@$numcgm?>&matric=<?=@$matric?>&inscr=<?=@$inscr?>&tipo=<?=@$tipo?>&emrec=<?=@$emrec?>&agnum=<?=@$agnum?>&agpar=<?=@$agpar?>&db_datausu=<?=date('Y-m-d',db_getsession('DB_datausu'))?>&id_usuario=<?=@$id_usuario?>&cgccpf=<?=$cgccpf?>" width="100%" height="270"></iframe>
   <?
 } else if( isset($tipo) && $tipo == 34){
   $inicial = true;
   ?>
   <iframe id="iframe" name="iframe" src="cai3_gerfinanc050.php?numcgm=<?=@$numcgm?>&matric=<?=@$matric?>&inscr=<?=@$inscr?>&tipo=<?=@$tipo?>&emrec=<?=@$emrec?>&agnum=<?=@$agnum?>&agpar=<?=@$agpar?>&db_datausu=<?=date('Y-m-d',db_getsession('DB_datausu'))?>&id_usuario=<?=@$id_usuario?>&cgccpf=<?=$cgccpf?>" width="100%" height="270"></iframe>
   <?
 } else {
   ?>
     <iframe id="iframe" name="iframe" src="cai3_gerfinanc002.php?numcgm=<?=@$numcgm?>&matric=<?=@$matric?>&inscr=<?=@$inscr?>&tipo=<?=@$tipo?>&emrec=<?=@$emrec?>&agnum=<?=@$agnum?>&agpar=<?=@$agpar?>&db_datausu=<?=date('Y-m-d',db_getsession('DB_datausu'))?>&id_usuario=<?=@$id_usuario?>&cgccpf=<?=$cgccpf?>" width="100%" height="270"></iframe>
   <?
 }
 
?>
<br>


<?include("cai3_gerfinanc001.php");?>
</body>
</html>
