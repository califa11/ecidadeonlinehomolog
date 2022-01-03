<?
/*
 *     E-cidade Software Publico para Gestao Municipal                
 *  Copyright (C) 2012  DBselller Servicos de Informatica             
 *                            www.dbseller.com.br                     
 *                         e-cidade@dbseller.com.br                   
 *                                                                    
 *  Este programa e software livre; voce pode redistribui-lo e/ou     
 *  modifica-lo sob os termos da Licenca Publica Geral GNU, conforme  
 *  publicada pela Free Software Foundation; tanto a versao 2 da      
 *  Licenca como (a seu criterio) qualquer versao mais nova.          
 *                                                                    
 *  Este programa e distribuido na expectativa de ser util, mas SEM   
 *  QUALQUER GARANTIA; sem mesmo a garantia implicita de              
 *  COMERCIALIZACAO ou de ADEQUACAO A QUALQUER PROPOSITO EM           
 *  PARTICULAR. Consulte a Licenca Publica Geral GNU para obter mais  
 *  detalhes.                                                         
 *                                                                    
 *  Voce deve ter recebido uma copia da Licenca Publica Geral GNU     
 *  junto com este programa; se nao, escreva para a Free Software     
 *  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA          
 *  02111-1307, USA.                                                  
 *  
 *  Copia da licenca no diretorio licenca/licenca_en.txt 
 *                                licenca/licenca_pt.txt 
 */

error_reporting('** CONSULTA FUNCIONAL! **');
session_start();

include("libs/db_conecta.php");
include("libs/db_stdlib.php");
include("libs/db_sql.php");
include("libs/db_utils.php");
include("dbforms/db_funcoes.php");

validaUsuarioLogado();

$aRetorno = array();
parse_str(base64_decode($HTTP_SERVER_VARS["QUERY_STRING"]),$aRetorno);

$numcgm     = db_getsession("DB_login");
$anoFolha   = db_anofolha();
$mesFolha   = db_mesfolha();
$id_usuario = $aRetorno['id_usuario'];
db_logs("","",0,"Consulta Funcional.");

?>
<html>
<head>
<title><?=$w01_titulo?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="config/estilos.css"        rel="stylesheet" type="text/css">
<link href="config/portalservidor.css" rel="stylesheet" type="text/css">
<script language="JavaScript" src="scripts/db_script.js"></script>
</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" bgcolor="<?=$w01_corbody?>">
<table width="100%" border="0" bordercolor="#cccccc" cellpadding="2" cellspacing="0" class="texto">
 <tr>
  <td><br></td>
 </tr>
</table>
<?  
  $sqlPermConsServ = " select * 
                         from configdbpref 
                        where w13_permconsservdemit = true ";
  
  $rsPermConsServ = pg_query($sqlPermConsServ);
  $iPermConsServ  = pg_numrows($rsPermConsServ); 
  
 if ($iPermConsServ == 0) {
     $sDtDemissao = " and rh05_seqpes is null ";
 } else {
     $sDtDemissao = "";
 }

  $sqlRhCgmCont = " SELECT DISTINCT rh01_regist,
                                    rh37_descr,
                                    rh01_admiss,
                                    rh01_funcao,
                                    rh05_recis,
                                    rh02_instit
                    FROM   rhpessoal
                           LEFT JOIN rhpessoalmov
                                  ON rh02_regist = rh01_regist
                                     AND rh02_anousu = (case when rh02_instit = 1 then fc_anofolha(1) when rh02_instit = 3 then fc_anofolha(3) when rh02_instit = 4 then fc_anofolha(4) else fc_anofolha(1) end)
                                     AND rh02_mesusu = (case when rh02_instit = 1 then fc_mesfolha(1) when rh02_instit = 3 then fc_mesfolha(3) when rh02_instit = 4 then fc_mesfolha(4) else fc_mesfolha(1) end)
                           LEFT JOIN rhpesrescisao
                                  ON rh05_seqpes = rh02_seqpes
                           LEFT JOIN rhfuncao
                                  ON rh01_funcao = rh37_funcao
                                     AND rh37_instit = rh02_instit
                    WHERE  rh01_numcgm = '{$numcgm}' {$sDtDemissao} ";
           
  //die($sqlRhCgmCont);
  $rsRhCgmCont = pg_query($sqlRhCgmCont);
  $iRhCgmCont  = pg_numrows($rsRhCgmCont);      
    
    if ($iRhCgmCont == 0) {    
?>
<table align="center" width="90%" cellpadding="2" cellspacing="0" class="texto">
<tr>
  <td align="center"><b>** SELECIONE A MATRICULA DESEJADA **</b></td>
</tr>
</table>
<table align="center" width="90%" cellpadding="2" cellspacing="0" class="texto">
   <tr>
    <td align="center" height="100">
     <img src="imagens/atencao.gif"><br>
       <b>N�O PERMITE CONSULTA DE FUNCION�RIO DEMITIDO!</b>
    </td>
   </tr>
   <?
    } else {
   ?>
<table align="center" width="90%" cellpadding="2" cellspacing="0" class="texto">
  <tr>
    <td align="center"><b>** SELECIONE A MATRICULA DESEJADA **</b></td>
  </tr>
</table>
<table align="center"  class="tableForm">   
   <tr class="subTituloForm" align="center">
      <td>N� Matr�cula</td>
      <td>Data Admiss�o</td>
      <td>Cargo</td>
      <td>Data Demiss�o</td>
      <td>Instituicao</td>
   </tr>
   <?
       $corFundo = "#FFFFFF";
       $corOver  = "#ede67c";
       
       for ($x = 0; $x < $iRhCgmCont; $x++) {
        
         if ($iRhCgmCont > 0) {
           $oCgmCont = db_utils::fieldsMemory($rsRhCgmCont,$x);   
         }
         
         if ( $corFundo == "#FFFFFF" ) {
              $corFundo = "#FFFFFF";
         } else {
              $corFundo = "#FFFFFF";
         }
         
         $sUrl = base64_encode("id_usuario=".$id_usuario."&matricula=".$oCgmCont->rh01_regist."&instituicao=".$oCgmCont->rh02_instit);
         
    ?>
    <tr bgcolor="<?=$corFundo?>"   onmouseover="bgColor='<?=$corOver?>'" onmouseout="bgColor='<?=$corFundo?>'" 
        style="Cursor:pointer" title="Ver Detalhes" 
        onclick="js_passamatriculacontribuinte('<?=$sUrl;?>')">
     <td align="center">&nbsp;<?= $oCgmCont->rh01_regist; ?></td>
     <td align="center">&nbsp;<?= db_formatar($oCgmCont->rh01_admiss,'d'); ?></td>
     <td align="center">&nbsp;<?= $oCgmCont->rh37_descr;  ?></td> 
     <?
       if ($oCgmCont->rh05_recis == '') {
     ?>    
     <td align="center">&nbsp;</td>
     <?
       } else {
     ?> 
     <td align="center">&nbsp;<?= db_formatar($oCgmCont->rh05_recis,'d');  ?></td>
     <?
       }
     ?>     
     <td align="center">&nbsp;<?= $oCgmCont->rh02_instit;  ?></td> 
    </tr>
    <?
     }
    ?>
</table>
<?
 }
?>
</body>
</html>
<script>
function js_passamatriculacontribuinte(sUrl) {
  var sUrlEncode = sUrl;
  document.location.href = "cons_servidrhpessoal.php?"+sUrlEncode;
}
</script>