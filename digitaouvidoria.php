<?
/*
 *     E-cidade Software Publico para Gestao Municipal                
 *  Copyright (C) 2009  DBselller Servicos de Informatica             
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

include("libs/db_conecta.php");
include("libs/db_stdlib.php");
include("libs/db_sql.php");
include("classes/db_db_tipo_classe.php");
include("classes/db_db_ouvidoria_classe.php");
@session_start();
parse_str(base64_decode($HTTP_SERVER_VARS["QUERY_STRING"]));
$result = pg_exec("SELECT distinct m_publico,m_arquivo,m_descricao
                       FROM db_menupref 
                       WHERE m_arquivo = 'digitaouvidoria.php'
                       ORDER BY m_descricao
                       ");
db_fieldsmemory($result,0);
if($m_publico != 't'){
  if(!session_is_registered("DB_acesso"))
    echo"<script>location.href='index.php?".base64_encode('erroscripts=3')."'</script>";
}
$db_verificaip = db_verifica_ip();
$cldb_ouvidoria = new cl_db_ouvidoria;
$cldb_tipo = new cl_db_tipo;
mens_help();
db_mensagem("ouvidoria_cab","ouvidoria_rod");
db_postmemory($HTTP_POST_VARS);
if(isset($HTTP_POST_VARS["confirma"])) {  
  $tipo = $HTTP_POST_VARS["tipo"];
  $comentario = $HTTP_POST_VARS["comentario"];
  $email = $HTTP_POST_VARS["email"];
  if(isset($HTTP_POST_VARS["receb_noticias"])) {  
    $receb_noticias = $HTTP_POST_VARS["receb_noticias"];
  }else{
     $receb_noticias = '0';
  }
  if(isset($HTTP_POST_VARS["outr"])) {
    $outr = $HTTP_POST_VARS["outr"];
  }else{
    $outr = "";
  }
  if($tipo == "outro")
    $tipo = $outr;
$cldb_ouvidoria->tipo = $tipo;
$cldb_ouvidoria->comentario = $comentario;
$cldb_ouvidoria->email = $email;
$cldb_ouvidoria->receb_noticias = $receb_noticias;
$cldb_ouvidoria->data = "2004-01-01";
$cldb_ouvidoria->revisado = "null";
$cldb_ouvidoria->login = "1";
$cldb_ouvidoria->texto = "null";
$cldb_ouvidoria->incluir(@$id_ouvidoria);
//$cldb_ouvidoria->erro(true,false);
  if($cldb_ouvidoria->erro_status == '0')
    $DB_MSG = "Mensagem não enviada, tente novamente mais tarde";
  else
    $DB_MSG = "Mensagem enviada com sucesso.";
    echo "<script>location.href = 'digitaouvidoria.php?".base64_encode('mensagem='.$DB_MSG)."'</script>";  
}

?>
<html>
<head>
<title><?=$w01_titulo?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script language="JavaScript" src="scripts/db_script.js"></script>
<script>
function js_tipo() {
  var aux = document.form1.tipo.value;
  if(aux == "outro") {
    document.form1.outr.disabled = false;
    document.form1.Ioutro.style.backgroundColor = "<?=$w01_corfundoinput?>";
    document.form1.Ioutro.style.border = "<? echo $w01_bordainput." ".$w01_estiloinput." ".$w01_corbordainput;?>";
    document.form1.outr.focus();
  } else {
    document.form1.outr.value = "";
    document.form1.outr.disabled = true;
    document.form1.Ioutro.style.backgroundColor = "<?=$w01_corbody?>";
    document.form1.Ioutro.style.borderStyle = "none";
    document.form1.Ioutro.style.borderColor = "<?=$w01_corbody?>";
    document.form1.comentario.focus();
  }
}
function js_submeter() {
  var em = new String(document.form1.email.value);
  if(document.form1.comentario.value == "") {
    alert("Seus comentários são muito importante");
    document.form1.comentario.focus();
        return false;
  } else if(em.indexOf("@") == -1 || em == "" || em.indexOf(".")== -1 ) {
    alert('Formato de email inválido');
    document.form1.email.focus();
        return false;
  } else
    return true;
}
</script>
<style type="text/css">
<?db_estilosite();?>
</style>
<link href="estilos.css" rel="stylesheet" type="text/css">
<link href="config/estilos.css" rel="stylesheet" type="text/css">
</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" bgcolor="<?=$w01_corbody?>" onLoad="" <? mens_OnHelp() ?>>
<br>
<?//mens_div();?>
<center>
 <table width="100%" border="0" cellspacing="0" cellpadding="0" class="texto">
  <tr>
   <td height="50" align="<?=$DB_align1?>">
    <?=$DB_mens1?>
   </td>
  </tr>
  <tr>
    <td align="center" valign="middle">
      <form name="form1" method="post">
       <table border="0" cellspacing="0" cellpadding="0" class="texto">
        <tr>
          <td align="left" valign="middle" style="font-size:12px"> Tipo:&nbsp;
           <select name="tipo" class="digitatexto" onchange="js_tipo()">
            <?
              $result = $cldb_tipo->sql_record($cldb_tipo->sql_query("","*","",""));
              $numR = $cldb_tipo->numrows;
              for($i = 0;$i < $numR;$i++){
                db_fieldsmemory($result,$i);
                echo "<option value=\"".$w03_codtipo."\">".$w03_tipo."</option>\n";
              }
            ?>
           <option value="outro">Outros ...</option>
           </select>
           <input type="text" name="outr" id="Ioutro" style="border-style:none;border-color:white;background-color:<?=$w01_corbody?>" disabled>
          </td>
        </tr>
        <tr>
          <td align="left" valign="middle">
           <textarea name="comentario" cols="50" rows="5" class="digitatexto"></textarea>
          </td>
        </tr>
        <tr>
          <td align="left" valign="middle" style="font-size:12px">
           Email para Resposta:
           <input type="text" name="email" size="60" class="digitatexto">
          </td>
        </tr>
        <tr>
          <td align="left" valign="middle">
           <input type="checkbox" name="receb_noticias" value="1" id="not" class="digitatexto" style="border-style:none">
           <label for="not" style="font-size:12px">Gostaria
           de Receber as Not&iacute;cias da Prefeitura em meu
           Email</label> </td>
        </tr>
        <tr>
         <td align="center" valign="middle">
          <input type="submit" class="botao" name="confirma" value="Confirma " onclick="return js_submeter()">
          <input type="reset" name="cancela" class="botao" value="Limpar">
         </td>
        </tr>
      </table>
      </form>
    </td>
  </tr>
  <tr>
   <td align="<?=$DB_align2?>">
    <?=$DB_mens2?>
   </td>
  </tr>
</table>
</center>
<?
db_logs("","",0,"Acesso a Ouvidoria.");
if(isset($mensagem))
  echo "<script>alert('".$mensagem."'); location.href= 'digitaouvidoria.php'</script>\n";
?>