<?
session_start();
global $HTTP_SESSION_VARS;
//if (!session_is_registered("DB_codperfil")){  
  session_register("DB_codperfil");
//}
//includes
include_once ("functions/func_sys.php");
//include ("libs/db_conn.php");
include ("libs/db_conecta.php");
include ("libs/menu.php");
include ("libs/db_stdlib.php");
include ("libs/db_sql.php");
db_postmemory($HTTP_SERVER_VARS);
//echo "DB_login = $DB_login";

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

db_mensagem("corpoprincipal", "mensagemsenha");
//db_fieldsmemory($result, 0);
parse_str(base64_decode($HTTP_SERVER_VARS["QUERY_STRING"]));

if (isset ($again)) {
$cgm = $_SESSION['CGM']; 

  setcookie("cookie_codigo_cgm");
  db_logs("", "", "$cgm", "index.php - usuario fez log-off.");
  db_redireciona("index.php");
}
//echo "<br><br><br> $login <br><br><br>";
if (isset ($DB_login)) { //echo" entra aqui qd  o usuario esta logado no sistema";

$sqllog = "select db_usuarios.id_usuario,senha,u.cgmlogin
		       from db_usuarios
		       inner join db_usuacgm u on u.id_usuario = db_usuarios.id_usuario
		       where login = '$DB_login'";
//echo"<br>$sqllog<br>";
$base=@$_SESSION["BASE"];
if($base!=""){
  $DB_BASEDADOS = $base;
  // echo"<br>sessão... conecta= $DB_BASEDADOS ";
}else{
  //echo"<br>não te base na sessão";
}


//echo ($sqllog."kkkkkkk<br>");
$result = pg_query($sqllog);
$linhas = pg_numrows($result);
//echo "<br>linhas= $linhas";
//echo " $nomeusuario $id_usuario $cgmlogin $cgmlogin_teste ";
if ($linhas == 0) {

		$erroscripts = "1";
}
elseif ($DB_senha != md5(~pg_result($result, 0, "senha"))) {

		$erroscripts = "2";
} else {

		//db_putsession("testesessao",123);

		db_fieldsmemory($result, 0);
		session_register("DB_login");
		$HTTP_SESSION_VARS["DB_login"] = $cgmlogin;
		session_register("CGM");
		$_SESSION["CGM"] = $cgmlogin;
		$DB_LOGADO = "";
		$usuario = db_getsession("DB_login");
		$cgm = $_SESSION['CGM'];
		//echo"CGM=$cgm ... usu= $usuario";
		/*
		db_logs("", "", "0", "index.php - Usuário fez login.");
		$sql = "select fc_permissaodbpref($cgmlogin,0,0)";
		$result = @ pg_query($sql);


		if (@ pg_numrows($result) == 0) {
		db_redireciona("index.php?".base64_encode("erroscripts='4'"));
		}
		*/
		$HTTP_SESSION_VARS["DB_acesso"] = pg_result($result, 0, 0);
		$HTTP_SESSION_VARS["hora"] = date("H:i:s");
}
}

//echo "erro = $erroscripts";
/*$usuario = db_getsession("DB_login");
 $cgm = db_getsession("CGM");
 echo"CGM=$cgm ... usu= $usuario";
 $hora = db_getsession("hora");
 */

//echo"..CGM=@$cgm ... usu= @S$usuario";
if (@$cgm != "") { //die($usuario);

$sql1 = "select nome,d.id_usuario as id_usuario
			 from db_usuarios d 
			 inner join db_usuacgm u on u.id_usuario = d.id_usuario 
			 where u.cgmlogin = '$cgm'";
$result = pg_query($sql1);
//echo"$sql1";
$nomeusuario = pg_result($result, 0, 'nome');
$id_usuario = pg_result($result, 0, 'id_usuario');
$cgm = $usuario;
session_register("id");
$_SESSION["id"] = $id_usuario;

$sql_usuario = "select id_usuario from configuracoes.db_usuarios where login='$DB_login'";
   
    $r = pg_query($sql_usuario);
    $v = pg_fetch_object($r);
    
    $_SESSION["id"] = $v->id_usuario;
    $_SESSION["id_externo"] = $v->id_usuario;


}

?>
<html>
<head>

<title>Prefeitura de Sete Lagoas - E-cidade Online</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script language="JavaScript" type="text/javascript"
	src="scripts/md5.js"></script>
<script language="JavaScript" type="text/javascript"
	src="scripts/db_script.js"></script>
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
<?
db_estilosite();
?>
</style>
<?

$result4 = pg_query("select upper(trim(munic)) as munic from db_config where codigo = ".db_getsession("DB_instit"));
if( pg_num_rows($result4) > 0 ) {
	db_fieldsmemory($result4, 0);

	switch ($munic) {
		case "CHARQUEADAS":
			$altura = 90;
			break;
		case "SAPIRANGA":
			$altura = 133;
			break;
		case "GUAIBA":
		case "ALEGRETE":
		case "BAGE":
		case "OSORIO":
		case "ARAPIRACA":
			$altura = 80;
			break;	
		case "ARROIO DO SAL":
		case "CARAZINHO":
			$altura = 100;
			break;
	}

} else {
	$altura = 90;
}
$base=@$_SESSION["BASE"];
$altura = 200;
?>
</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0"
	bgcolor="<?=$w01_corbody?>"
	onLoad="<?=!isset($DB_LOGADO) && (db_getsession('DB_login') == '')?'js_foco()':''?>">

<?
if (@$usuario!=""){
  $sqlusu= "select * from db_usuarios where id_usuario= $id_usuario";
  $resusu = pg_query($sqlusu);
  $linhausu=pg_num_rows($resusu);
  if ($linhausu >0){
    db_fieldsmemory($resusu, 0);
    if ($login=='admin'){
      echo"<a href='trocabase.php' target='CentroPref'>trocar base</a>";
    }
  }

}
?>

<table width="100%" align="center" border="0" cellpadding="0"
	cellspacing="0" bgcolor="<?=$w01_corbody?>">
 <!--  
	<tr>
		<td height="<? //=$altura?>px" colspan="3"
			 style="background-image:url('imagens/cabecalho.jpg'); background-repeat: no-repeat;" align="center"></td>
	</tr>
 --> 
 <?
  /*
   * Modificacao incluida para que nao repita ou corte a imagem  do cabecalho enviado pelo cliente
   */
 ?>
  <tr>
    <td colspan="3">
       <img src="imagens/cabecalho.jpg">
    </td>
  </tr> 
  
	<tr>
		<td colspan="3" height="1" bgcolor="#999999"></td>
	</tr>
	<tr height="15" bgcolor="<?=$w01_corbody?>" class="texto">

		<td><?



		if (db_getsession("DB_login") == "") {
		  echo "
		  				<form name=\"form1\" action=\"\" method=\"post\">
						<table class=\"texto\">
		      				<tr align=\"left\">
		  	    				<td>Login: 
		     	    				<input name=\"login\" type=\"text\" size=\"10\">&nbsp;&nbsp;&nbsp;&nbsp; </td>
			   					<td>Senha:
								    <input name=\"senha\" onKeyUp=\"js_alapucha(event)\" type=\"password\" size=\"10\"> &nbsp;
								    <input name=\"Submit\" type=\"button\" class=\"botao\" onClick=\"js_submeter()\" value=\"Acessar\">&nbsp;&nbsp;
						  	    	<input type='hidden' name='DB_senha'>
							      	<input type='hidden' name='DB_login'>
		  
		";
		  if ($w13_liberapedsenha == "t") {
		    ?> &nbsp;&nbsp;&nbsp;&nbsp;
           <a href="pedido_senha.php?eqm=0"  target="CentroPref">Pedido de Senha</a>&nbsp;&nbsp;
           <a href="pedido_senha.php?eqm=1"  target="CentroPref">Esqueci Minha Senha</a>&nbsp;&nbsp;
           <a href="rhpes_autcontracheq.php" target="CentroPref">Autenticidade de Contracheque</a>
		    <?


//#################################################################

	}
	echo " 
								</td>
		     				</tr>
		      			</table>
		     			</form>
		";
	$sql = "select * from db_usuarios where lower(login) = 'dbpref'";
	$result = pg_exec($sql);
	db_fieldsmemory($result, 0);
	$codperf = $id_usuario;
	//echo"<br>não existe usuario = $codperf";
}
elseif ($usuario != "") { 
	//echo"<br>existe usuario = $usuario";
	
	$sqlidusu = "select * from db_usuacgm where cgmlogin=$usuario";
	$resdelidusu = pg_query($sqlidusu);
	db_fieldsmemory($resdelidusu, 0);
	$usu = $id_usuario;
	echo $usuario." - ".$nomeusuario; // mostra o nome de quem esta logado
	//$del = "delete from db_permherda where id_usuario=$usu";
	// busca os perfis do usuario
	$sqlperfil="select * from db_permherda where id_usuario=$usu";
	//echo "<br>$sqlperfil";
	$resultperfil =pg_query($sqlperfil);
	$linhasperfil=pg_num_rows($resultperfil);
	if ($linhasperfil>0){
		for ($i = 0; $i < $linhasperfil; $i ++) {
			db_fieldsmemory($resultperfil, $i);
			// pega o id e busca o login na db_usuarios..ex...'contribuinte'
			$sql = "select * from db_usuarios where id_usuario= $id_perfil";
			//echo "<br>$sql";
			$result=pg_query($sql);
			$linhas=pg_num_rows($result);
			db_fieldsmemory($result, 0);
			
			if (strtolower($login) =='contribuinte' || strtolower($login) =='escritorio'|| strtolower($login) =='imobiliaria' || strtolower($login) =='fornecedor' || strtolower($login) =='funcionario' ){
				// deletar somente os que são perfis de dbpref
				$del="delete from db_permherda where id_usuario='$usu'and id_perfil =$id_perfil";
			//echo "<br>$del";
				$resdel = pg_query($del);
			}
			
		}
	}
	//echo $del;
	
	
	// se for administrador
	$sqlusu="select * from db_usuarios where id_usuario= $usu";
	//echo"<br>$sqlusu";
	$resusu = pg_query($sqlusu);
	$linhausu=pg_num_rows($resusu);
	if ($linhausu >0){
		db_fieldsmemory($resusu, 0);
		if ($login=='admin'){
			$sqlid = "select * from db_usuarios where lower(login) = 'administrador'";
			//echo"<br>$sqlid";
			$resultid = pg_exec($sqlid);
			$li = pg_num_rows($resultid);
			if ($li > 0) {
				db_fieldsmemory($resultid, 0);
				$ins = " insert into db_permherda values ($usu,$id_usuario)";
				$resins = pg_query($ins);
				$codperf = $usu;
			}
		}
	}
	
	
	
	//se for escritorio
	$sql = "select * from cadescrito where q86_numcgm= $usuario";
	$result = pg_exec($sql);
	$linhas = pg_num_rows($result);
	if ($linhas > 0) {
		//gravar na db_permherda (id_usuario e id_perfil)

		$sqlid = "select * from db_usuarios where lower(login) = 'escritorio'";
		$resultid = pg_exec($sqlid);
		$li = pg_num_rows($resultid);
		if ($li > 0) {
			db_fieldsmemory($resultid, 0);
			$ins = " insert into db_permherda values ($usu,$id_usuario)";
			$resins = pg_query($ins);
			$codperf = $usu;
		}
	}
	
	// se for imobiliaria.............
	$sqlimb = "select * from cadimobil where j63_numcgm = $usuario";
	$resultimb = pg_exec($sqlimb);
	$linhasimb = pg_num_rows($resultimb);
	if ($linhasimb > 0) {
		//deletar da db_permherda
		//gravar na db_permherda (id_usuario e id_perfil)
		$sqlid = "select * from db_usuarios where lower(login) = 'imobiliaria'";
		$resultid = pg_exec($sqlid);
		$li = pg_num_rows($resultid);
		if ($li > 0) {
			db_fieldsmemory($resultid, 0);
			$ins = " insert into db_permherda values ($usu,$id_usuario)";
			$resins = pg_query($ins);
			$codperf = $usu;

		}
	}
	
	// se for grafica
	$sqlgra = "select * from graficas where y20_grafica  = $usuario";
	$resultgra = pg_exec($sqlgra);
	$linhasgra = pg_num_rows($resultgra);
	if ($linhasgra > 0) { 
		//deletar da db_permherda
		//gravar na db_permherda (id_usuario e id_perfil)
		$sqlid = "select * from db_usuarios where lower(login) = 'grafica'";
		$resultid = pg_exec($sqlid);
		$li = pg_num_rows($resultid);
		if ($li > 0) {
			db_fieldsmemory($resultid, 0);
			$ins = " insert into db_permherda values ($usu,$id_usuario)";
			$resins = pg_query($ins);
			$codperf = $usu;

		}
	}
		
	// se for fornecedor
	$sqlfor = "select * from pcforne where pc60_numcgm= $usuario";
	$resultfor = pg_exec($sqlfor);
	$linhasfor = pg_num_rows($resultfor);
	if ($linhasfor > 0) {
		//deletar da db_permherda
		//gravar na db_permherda (id_usuario e id_perfil)
		$sqlid = "select * from db_usuarios where lower(login) = 'fornecedor'";
		$resultid = pg_exec($sqlid);
		$li = pg_num_rows($resultid);
		if ($li > 0) {
			db_fieldsmemory($resultid, 0);
			$ins = " insert into db_permherda values ($usu,$id_usuario)";
			$resins = pg_query($ins);
			$codperf = $usu;
		}
	}
	
	if($w13_utilizafolha=='t'){
		// se for funcionario
		
		$sqlfun = "	select rh01_regist, rh01_numcgm 
														from rhpessoal 
														inner join rhpessoalmov on rh01_regist = rh02_regist 
														left  join rhpesrescisao on rh02_seqpes = rh05_seqpes
														where rh01_numcgm=$usuario";
		$resultfun = pg_exec($sqlfun);
		$linhasfun = pg_num_rows($resultfun);
		if ($linhasfun > 0) {
			//deletar da db_permherda
			//gravar na db_permherda (id_usuario e id_perfil)
			$sqlid = "select * from db_usuarios where lower(login) = 'funcionario'";
			$resultid = pg_exec($sqlid);
			$li = pg_num_rows($resultid);
			if ($li > 0) {
				db_fieldsmemory($resultid, 0);
				$ins = " insert into db_permherda values ($usu,$id_usuario)";
				$resins = pg_query($ins);
				$codperf = $usu;
			}
		}
	}
	$sqlid = "select * from db_usuarios where lower(login) = 'contribuinte'";
	$resultid = pg_exec($sqlid);
	$li = pg_num_rows($resultid);
	if ($li > 0) {
		db_fieldsmemory($resultid, 0);
		$ins = " insert into db_permherda values ($usu,$id_usuario)";
		//echo "<br>$ins";
		$resins = pg_query($ins);
		$codperf = $usu;
	}
	
	

}
//echo  @$user;
//die ("xxx= $codperf");

$HTTP_SESSION_VARS["DB_codperfil"] = $id_usuario;

//print_r($HTTP_SESSION_VARS);

//$w13_liberaescritorios = 3;
?>	

		</td>
		<td> <?db_logon(isset($login)?false:true,$w13_liberaatucgm,$w13_liberaescritorios); ?></td>
		
 		<td align="left"> <?=date('d/m/Y') ?></td>
  	</tr>
 	<tr>
  		<td colspan="3" height="1" bgcolor="#999999"></td>
 	</tr>
 	
	<tr>
			<?$ano= date("Y"); ?>
			
		<td colspan="3"> <?db_menu_dbpref($_SESSION['id'],5457,$ano,$DB_INSTITUICAO,@$cgm,@$nomeusuario); ?>
		
		</td>
	</tr>
	<tr height="800">
  		<td colspan="3" width="100%" align="center">
<br><br><br>
<div style="padding-top:35px;font-width:bold;font-size:30px;color:red" ></div>
<div style="padding-top:10px;font-width:bold;font-size:20px" ></div>
   			
<iframe id="CentroPref" name="CentroPref" src="centro_pref.php" width="100%" height="100%" frameborder="0"></iframe>
  		</td>
 	</tr>
 	
</table>
</body>
</html>
<?



if (isset ($erroscripts) && !isset ($DB_LOGADO)) {
	if (@ $erroscripts == 1)
	echo "<script>alert('Login Inválido');</script>\n";
	elseif (@ $erroscripts == 2) echo "<script>alert('Senha Inválida');</script>\n";
	elseif (@ $erroscripts == 3) echo "<script>alert('Acesso a rotina inválido.');</script>\n";
	elseif (@ $erroscripts == 4) echo "<script>alert('Sem permissão de acesso, Contate a Prefeitura.');</script>\n";
}


?>
<script>
<?



if (!isset ($DB_LOGADO) && (db_getsession("DB_login") == "")) {

	function js_foco() {
		document.form1.login.focus();
	}

}
?>
</script>

