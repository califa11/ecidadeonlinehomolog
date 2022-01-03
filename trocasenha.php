<?
session_start();
include("libs/db_conecta.php");
include("libs/db_stdlib.php");
include("libs/db_sql.php");
include("libs/smtp.class.php");

parse_str(base64_decode($HTTP_SERVER_VARS["QUERY_STRING"]));
/*
$result = pg_exec("SELECT distinct m_publico,m_arquivo,m_descricao
                       FROM db_menupref 
                       WHERE m_arquivo = 'digitaaidof.php'
                       ORDER BY m_descricao
                       ");
db_fieldsmemory($result,0);
if($m_publico != 't'){
  if(!session_is_registered("DB_acesso")){
  	
    die("DBacesso =  $DB_acesso");
    
    echo"<script>location.href='index.php?".base64_encode('erroscripts=3')."'</script>";
    
  }
  echo "xxxxx";
}
*/

mens_help();
$id_usuario = $_SESSION['id'];  
//db_mensagem("aidof_cab","aidof_rod");
if(isset($HTTP_POST_VARS["alterar"])) {
  db_postmemory($HTTP_POST_VARS);
  $result = pg_exec("select senha as senhaantiga from db_usuarios where id_usuario = $id_usuario");
  db_fieldsmemory($result,0);
  if(strcmp(~$senhaantiga,$senhaatual) == 0){
    pg_exec("BEGIN");
    pg_exec("update db_usuarios set
                       senha = '".~$senha."'
                       where id_usuario = $id_usuario") or die("Erro(38) alterando db_usuarios: ".pg_errormessage());
    pg_exec("COMMIT");
  }else{
    db_msgbox("campo senha atual não confere!");
    db_redireciona($HTTP_SERVER_VARS['PHP_SELF']."?".base64_encode("id_usuario=".$id_usuario));
  }
  if(isset($enviaemail) && ($enviaemail == "sim")){
    $mensagemDestinatario = "";
echo"    
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda.</title>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">
</head>

<body bgcolor=\"#FFFFFF\" leftmargin=\"0\" topmargin=\"0\" marginwidth=\"0\" marginheight=\"0\">
<table width=\"100%\" height=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">
  <tr> 
    <td nowrap align=\"center\" valign=\"top\"><table width=\"100%\" height=\"100%\" border=\"0\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\">
        <tr> 
          <td width=\"146\" nowrap bgcolor=\"#6699CC\"><font color=\"#FFFFFF\" ></font></td>
          <td height=\"60\"  nowrap align=\"left\" bgcolor=\"#6699CC\"><font color=\"#FFFFFF\"><strong>&nbsp;&nbsp;&nbsp;.: Senha Alterada :.
        </tr>
        <tr> 
          <td colspan=\"2\"><table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
              <tr> 
                <td>&nbsp;</td>
              </tr>
              <tr> 
                <td><ul>
                    <li><font size=\"2\" face=\"Arial, Helvetica, sans-serif\">Você alterou sua senha no site Prefeitura-OnLine,<br> este e-mail foi enviado conforme solicitado no site para verificação</li>
                  </ul></td>
              </tr>
              <tr> 
                <td><font size=\"2\" face=\"Arial, Helvetica, sans-serif\">&nbsp;</font></td>
              </tr>
              <tr> 
                <td><font size=\"2\" face=\"Arial, Helvetica, sans-serif\">&nbsp;</font></td>
              </tr>
              <tr> 
                <td><ul>
                    <li>Usuário : <font size=\"2\" face=\"Arial, Helvetica, sans-serif\"><strong>$nome</strong></font></li>
                  </ul></td>
              </tr>
              <tr> 
                <td><ul>
                    <li>Login : <font size=\"2\" face=\"Arial, Helvetica, sans-serif\"><strong>$login</strong></font></li>
                  </ul></td>
              </tr>
              <tr> 
                <td><ul>
                    <li>Nova Senha : <font size=\"2\" face=\"Arial, Helvetica, sans-serif\"><strong>$senha</strong></font></li>
                  </ul></td>
              </tr>
              <tr> 
                <td><ul>
                    <li>Data da alteração : <font size=\"2\" face=\"Arial, Helvetica, sans-serif\"><strong>".db_formatar(date("Y-m-d"),'d')."</strong></font></li>
                  </ul></td>
              </tr>
              <tr> 
                <td><ul>
                    <li>Hora : <font size=\"2\" face=\"Arial, Helvetica, sans-serif\"><strong>".$hora = db_getsession("hora")."</strong></font></li>
                  </ul></td>
              </tr>
              <tr> 
                <td><font size=\"2\" face=\"Arial, Helvetica, sans-serif\">&nbsp;</font></td>
              </tr>
              <tr> 
                <td>&nbsp;</td>
              </tr>
              <tr> 
                <td align=\"center\"><p><font size=\"1\">Este e-mail foi enviado automaticamente 
                    por favor não responda-o</font></p></td>
              </tr>
              <tr> 
                <td align=\"center\"><p><a href=\"http://200.102.214.168\"><font size=\"1\">DBSeller Inform&aacute;tica 
                    Ltda.</font></a></p></td>
              </tr>
            </table></td>
        </tr>
      </table></td>
  </tr>
</table>

</body>
</html>";


//  $headers = "Content-Type:text/html;";
//  $enviando = mail($email,"Alteração de senha do site Prefeitura On-Line",$mensagemDestinatario,$headers);
  
    $rsConsultaConfigDBPref = $clconfigdbpref->sql_record($clconfigdbpref->sql_query_file(db_getsession('DB_instit'),"w13_emailadmin"));
    db_fieldsmemory($rsConsultaConfigDBPref,0);
      
    $oMail = new Smtp();
    $oMail->Send($email,$w13_emailadmin,'Alteração de senha do site Prefeitura On-Line',$mensagemDestinatario);
  
  }
  db_msgbox("Senha alterada com sucesso!");
  db_redireciona("centro_pref.php");
}
?>
<html>
<head>
<title><?=$w01_titulo?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script language="JavaScript" src="scripts/db_script.js"></script>
<script>
function js_versenha(){
  var senha = new String(document.form1.senha.value);
  var versenha = new String(document.form1.ver_senha.value);
  if(senha == "" || versenha == ""){
    alert('Campo senha e verificação devem ser preenchidos!');
    return false;
  }
  if(senha.toString() == versenha.toString()){
    return true;
  }else{
    alert('A senha e sua verificação devem ser iguais!');
    return false;
    document.form1.senha.focus();
  }
return false;
}
</script>
<style type="text/css">
<?db_estilosite();
?>
</style>
</head>

<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" bgcolor="<?=$w01_corbody?>" onLoad="" <? mens_OnHelp() ?>>
<?
mens_div();
?>
<center>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td height="60" align="<?=@$DB_align1?>">
      <?=@$DB_mens1?>
    </td>
  </tr>
  <tr>
    <td height="200" align="center" valign="middle"><!-- InstanceBeginEditable name="digita" -->
    <?
    $sql = "SELECT nome,login, senha as senhaatual
            FROM db_usuarios
            where id_usuario = $id_usuario";
    $result = pg_exec($sql);
    if(pg_numrows($result) > 0)
      db_fieldsmemory($result,0);
      ?>
        <form name="form1" method="post" onSubmit="return js_versenha()">
          <input type="hidden" name="id_usuario" value="<?=@$id_usuario?>">
          <table width="452" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td height="25" nowrap><strong> 
                Nome:
              </strong></td>
              <td height="25" nowrap>
              <input name="nome" type="text" value="<?=@$nome?>" readonly size="40" maxlength="40">
              </td>
            </tr>
            <tr>
              <td height="25" nowrap><strong> 
                Login
              </strong></td>
              <td height="25" nowrap>
              <input name="login" type="text" value="<?=@$login?>" readonly size="10" maxlength="20">
              </td>
            </tr>
            <tr> 
              <td height="25" nowrap><strong>Senha Atual:</strong></td>
              <td height="25" nowrap><input name="senhaatual" type="password" size="20" maxlength="20"></td>
            </tr>
            <tr> 
              <td height="25" nowrap><strong>Senha:</strong></td>
              <td height="25" nowrap><input name="senha" type="password" id="senha" size="20" maxlength="20"></td>
            </tr>
            <tr> 
              <td height="25" nowrap><strong>Verifica senha:</strong></td>
              <td height="25" nowrap><input name="ver_senha" type="password" id="ver_senha" size="20" maxlength="20"></td>
            </tr>
            <tr> 
              <td height="25" nowrap>&nbsp;</td>
              <td height="25" nowrap><br> 
                &nbsp; <input name="alterar" accesskey="a" type="submit" value="Alterar">
              </td>
            </tr>
          </table>
        </form>
                                  
                                  <!-- InstanceEndEditable --> 
                  </td>
                </tr>
                <tr> 
                  <td height="60" align="<?=@$DB_align2?>">
                    <?=@$DB_mens2?>
                  </td>
                </tr>
              </table>
                      
            </td>
      </tr>
      </table>
</center>
</body>
