<?
session_start();

//if(isset($outro)){
// setcookie("cookie_codigo_cgm");
//header("location:digitainscricao.php");
//}

include("libs/db_conecta.php");
include("libs/db_stdlib.php");
include("libs/db_sql.php");
include("classes/db_matordem_classe.php");

$clmatordem = new cl_matordem();

parse_str(base64_decode($HTTP_SERVER_VARS["QUERY_STRING"]));
db_postmemory($HTTP_POST_VARS);
?>
<html>
<head>
<title><?=$w01_titulo?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script language="JavaScript" src="scripts/db_script.js"></script>
</head>
<script>
function js_mostra(){
	document.form1.submit(); 
}
function js_imprime(ord){
		
		jan = window.open('emp2_ordemcompra002.php?m51_codordem_ini='+ord+'&m51_codordem_fim='+ord,'','width='+(screen.availWidth-5)+',height='+(screen.availHeight-40)+',scrollbars=1,location=0 ');
	}
function js_alterar(orc,sol,forne,cgm){
	location.href='for_orcamlista.php?orc='+orc+'&sol='+sol+'&forne='+forne+'&cgm='+cgm;
}
</script>
<style type="text/css">
<?
db_estilosite()
?>
</style>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" bgcolor="<?=$w01_corbody?>">
<br>
<table width='600px' align='center' class="tab"  >
<form name="form1" method="post" target="">
<?


if($id_usuario!=""){
	if(!isset($mostra)){
		$mostra = 1;
	}
	
?>  <div align="center" class='titulo'>Ordems de Compra: 
	<select name="mostra"  onchange="js_mostra()">
<?
    echo"
    	 		 <option value=\"1\"".($mostra==1?" selected":"").">Apenas com saldo a entregar/liquidar</option>				 
                 <option value=\"2\"".($mostra==2?" selected":"").">Todos</option>
        </select> </div><br>";  
	
	//echo "logadooooooooooooooooooooo <br> id = $id_usuario ";

	$sSqlUsu = "select db_usuarios.id_usuario,senha,u.cgmlogin
    from db_usuarios
    inner join db_usuacgm u on u.id_usuario = db_usuarios.id_usuario
    where cgmlogin = ".$_SESSION["CGM"];
  $rsUsu   = pg_query($sSqlUsu);
	db_fieldsmemory($rsUsu,0); 
	if ($mostra == 1){

      $where = " and ((e60_vlremp - e60_vlranu - e60_vlrliq) > 0)";
	}else{

     $where = "";
	}
	$sql     = $clmatordem->sql_query_anu("","*","m51_codordem","m51_numcgm = $cgmlogin 
	                                      and matordemanu.m53_codordem is null
																				$where  ");

																				$result  = pg_exec($sql);
	$linhas  = pg_num_rows($result);
	if($linhas>0){
		    echo"
		    <tr >
				<th align='center'> Ordem de Compra
				</th>
				<th align='center'> Data
				</th> 
				<th align='center'>Empenho 
				</th> 
				<th align='center'> Emissao Empenho 
				</th> 
				<th align='center'> Nome da Institui��o 
				</th> 
				</th>
				<th align='center'>Imprimir
				</th>";
		for ($i = 0; $i < $linhas; $i ++) {
		
		  db_fieldsmemory($result,$i);
			
			echo "<tr align='center' class='texto'>";
			echo"<td>$m51_codordem</td>";
			
				echo"
				</td>
				<td> ".db_formatar($m51_data, 'd')."
				</td>
				<td>".$e60_codemp."</td>  
				<td> ".db_formatar($e60_emiss, 'd')."
				</td>
				<td>".$nomeinst."</td>  
				<td align='left'>
					<input name='imprimir' type='button' value='Imprimir' class='botao' onclick='js_imprime($m51_codordem,$id_usuario)'>
					";
					
				echo"</td>";
				echo"
				</tr>";
			
		}
	}
	
}else{ 
	echo " n�o logado";
}
?>
</form>
</table>
</body>
</html>
