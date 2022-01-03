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
session_start();
global $HTTP_SESSION_VARS;
//if (!session_is_registered("DB_codperfil")){  
session_register("DB_codperfil");
error_reporting('** CONSULTE AQUI O CGM! **');
session_start();

include("libs/db_conecta.php");
include("libs/db_stdlib.php");
include("libs/db_utils.php");
include("dbforms/db_funcoes.php");
include("libs/db_sql.php");
include("classes/db_cgm_classe.php");
include("libs/db_mail_class.php");

$oGet       = db_utils::postmemory($_GET);
$sVr        = $oGet->eqm;
$clcgm      = new cl_cgm();
$db_opcao   = 1;
parse_str(base64_decode($HTTP_SERVER_VARS["QUERY_STRING"]));
db_logs("","",0,"Consulta CGM.");




?>
<html>
<head>
<link href="config/estilos.css" rel="stylesheet" type="text/css">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/db_script.js"></script>
</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" bgcolor="<?=$w01_corbody?>">



<form name="form1" method="get" target="CentroPref">
<table id="servidor" width="80%" align="center" border="0" 
       cellpadding="5" cellspacing="1" bgcolor="<?$w01_corbody?>" class="bold4">

  <tr>
    <td width="15%">CPF:</td>
    <td width="1%"><span><font color='#E9000'> * </font></span></td>
    <td width="32%">
      <input type="text" id="cpf" name="cpf" size="15" maxlength="14"/>
	  <input type="submit" name="enviar" value="Consultar"/>
    </td>
    <th><div id="msgcpf" align="left"></div></th>
  </tr>
  
</table>
</form>

<?

function formataCpfCnpj($sCpfCnpj){

$cpfCnpj = str_replace(".", "", $sCpfCnpj);
$cpfCnpj = str_replace("/", "", $cpfCnpj);
$cpfCnpj = str_replace("-", "", $cpfCnpj);

return $cpfCnpj;
}

if(isset($_GET['cpf'])){
		$oGet  = db_utils::postmemory($_GET);

		$cpf  = formataCpfCnpj($oGet->cpf);

		$sql= "SELECT cgm.z01_numcgm, z01_nome
 FROM db_usuarios inner join cgm on CAST(db_usuarios.login AS int) = cgm.z01_numcgm where cgm.z01_cgccpf = '$cpf' and db_usuarios.usuext=1;";
		//die($sql);
		//echo $sql;
		
		$result=pg_query($sql);
		
		$linha=pg_num_rows($result);
		
		if($linha > 0){
			db_fieldsmemory($result,0);
			
			echo "<table id='servidorcgm' width='40%' align='center' border='1px' 
		   cellpadding='3' cellspacing='1' class='bold4'>
		   <tr>
				<td>LOGIN/CGM</td>
				<td>NOME</td>
		   </tr>
		   
		   ";
			for($i = 1; $i <= $linha; $i++){
				echo "<tr><td>$z01_numcgm</td><td>$z01_nome</td></tr>";
			
			}
			echo "</table>";
		}else{
		
			echo "<script type='text/javascript'>alert('Usuario nao cadastrado. Clique no link PEDIDO DE SENHA e solicite sua senha.');</script>";
		
		}
}
?>


</body>
</html>
