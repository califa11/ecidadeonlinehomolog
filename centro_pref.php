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

session_start();

include_once("functions/func_sys.php");
include("libs/db_conecta.php");
include("libs/db_stdlib.php");
include("libs/db_sql.php");
mens_help();
db_mensagem("corpoprincipal","mensagemsenha");
//db_fieldsmemory($result,0);
parse_str(base64_decode($HTTP_SERVER_VARS["QUERY_STRING"]));
/*
if(isset($again)){
  db_logs("","","0","index.php - usuario fez log-off.");
  db_redireciona("index.php");
}


if(isset($login)){
  $result = pg_exec($conn,"select db_usuarios.id_usuario,senha,u.cgmlogin
                           from db_usuarios
                                inner join db_usuacgm u on u.id_usuario = db_usuarios.id_usuario
                           where login = '$DB_login'");
  if(pg_numrows($result) == 0) {
    $erroscripts = "1";
  }elseif($DB_senha != md5(~pg_result($result,0,"senha"))) {
    $erroscripts = "2";
  }else{
    session_register("DB_login");
    db_fieldsmemory($result,0,true,true);
    $HTTP_SESSION_VARS["DB_login"] = $cgmlogin;
    $DB_LOGADO = "";
    db_logs("","","0","index.php - Usu�rio fez login.");
    $sql = "select fc_permissaodbpref($cgmlogin,0,0)";
    $result = pg_exec($sql);
    if(pg_numrows($result)==0){
      db_redireciona("centro_pref.php?".base64_encode("erroscripts='4'"));
    }
    $HTTP_SESSION_VARS["DB_acesso"] = pg_result($result,0,0);
    $HTTP_SESSION_VARS["hora"] = date("H:i:s");
  }
}
*/
?>
<html>
<head>
<title><?=$w01_titulo?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script language="JavaScript" type="text/javascript" src="scripts/md5.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/db_script.js"></script>
</script>
<script>
function js_alapucha(evt) {
    evt = (evt) ? evt : (window.event) ? window.event : "";
      if(evt.keyCode == 13)
            js_submeter();
}
function js_submeter() {
  document.form1.DB_senha.value = calcMD5(document.form1.senha.value);
  document.form1.DB_login.value = document.form1.login.value;
  document.form1.senha.value = "";
  document.form1.login.value = "";
  wname = 'wname' + Math.floor(Math.random() * 10000);
  document.form1.submit();
}
</script>
<style type="text/css">
<?db_estilosite()?>
</style>

</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" bgcolor="<?=$w01_corbody?>">
<div class="bold4" align="center">
 <br><br><br><br>


<h1>
CARO(A) SERVIDOR(A) DA SAUDE MUNICIPAL, INFORMAMOS QUE, SE EM SEU CONTRACHEQUE DE JANEIRO/2017 N�O CONSTA O ABONO COMPLEMENTANDO O SEU SAL�RIO BASE PARA R$ 937,00 (SALARIO MINIMO NACIONAL) CONFORME COMPROMISSO DA ATUAL ADMINISTRA��O, SAIBA QUE FOI DETECTADO E RESOLVIDO ESTE PROBLEMA, SENDO CONFECCIONADA IMEDIATAMENTE UMA FOLHA DE PAGAMENTO COMPLEMENTAR COM O DEVIDO VALOR QUE SER� CREDITADO JUNTAMENTE COM SUA  FOLHA NORMAL.
TAL FATO � O RESGATE DO RESPEITO AO SERVIDOR PUBLICO MUNICIPAL.

</h1>


 <?=//$DB_mens1?>
</div><br><br><br>
<?db_rodape();?>
</body>
</html>
