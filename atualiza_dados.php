<?

include("libs/db_conecta.php");
include("libs/db_stdlib.php");
include("libs/db_sql.php");
include("libs/db_utils.php");

db_postmemory($HTTP_GET_VARS,0);

validaUsuarioLogado();



if(isset($enviar)){

$sqlUpdate = "UPDATE protocolo.cgm
   SET z01_nome='$z01_nome',z01_estciv=$z01_estciv, z01_ender='$z01_ender', z01_numero='$z01_numero', z01_compl='$z01_compl', 
       z01_bairro='$z01_bairro', z01_munic='$z01_munic', z01_uf='$z01_uf', z01_cep='$z01_cep',z01_telef='$z01_telef',z01_telcel='$z01_telcel', z01_email='$z01_email',z01_fax='$z01_fax', z01_pai='$z01_pai', z01_mae='$z01_mae',z01_ultalt=now()
 WHERE z01_numcgm=".$_SESSION['CGM'];

 $resUp = pg_query($sqlUpdate);
 
 if(pg_affected_rows($resUp)>0){
	echo "<span style= 'color:red' >Dados atualizados com Sucesso !!!</span>";
 
 }

}


$aRetorno = array();

parse_str(base64_decode($HTTP_SERVER_VARS["QUERY_STRING"]),$aRetorno);

$iMatric  = $aRetorno['iMatric'];

$oRHPessoal = db_utils::getDao('rhpessoal');


$sSqlDadosServidor = "select * from               
											 rhpessoal  inner join cgm             on cgm.z01_numcgm                     = rhpessoal.rh01_numcgm
											     
											 where rhpessoal.rh01_regist = {$iMatric}";
 
$rsDadosServidor = $oRHPessoal->sql_record($sSqlDadosServidor);
//$oDadosServidor  = db_utils::fieldsMemory($rsDadosServidor,0);
db_fieldsmemory($rsDadosServidor,0);



?>
<html>
<head>
<title><?=$w01_titulo?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="config/estilos.css"        rel="stylesheet" type="text/css">
<link href="config/portalservidor.css" rel="stylesheet" type="text/css">

<script language="JavaScript" src="scripts/scripts.js"></script>
<script language="JavaScript" src="scripts/db_script.js"></script>
</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" bgcolor="<?=$w01_corbody?>" onLoad="" <?mens_OnHelp()?>>
  <form name="form1" method="post" action="">
    <table  class="tableForm" width="750px;">
   
										
										  
	 <tr align="center" valign="middle"> 
								<td width="39%"> 
									<fieldset>
										<legend align="center">
											<b><strong>Dados Pessoais:</strong></b>
										</legend>
										<table width="100%" border="0">
										<tr> 
        <td nowrap title=Nome Campo:z01_nome>          <strong>Nome:</strong>          </td>
		<td nowrap title="Nome Campo:z01_nome"> 
			<input title="Nome Campo:z01_nome" name="z01_nome"  type="text"     id="z01_nome"  value="<?=$z01_nome ?>"  size="40"
	maxlength="40"
     style="background-color:#FFFFFF;text-transform:uppercase;"     autocomplete='off'>
         </td>
    </tr>
	<tr> 
        <td nowrap title=Nome Campo:z01_nome>          <strong>Estado Civil:</strong>          </td>
		<td nowrap title="Nome Campo:z01_estciv"> 
			<select id="z01_estciv" name="z01_estciv" style="background-color:#FFFFFF;text-transform:uppercase;" >
<option  <?php if ($z01_estciv==1) { ?> selected="selected" <?php } ?> value="1">Solteiro</option>
<option <?php if ($z01_estciv==2) { ?> selected="selected" <?php } ?> value="2">Casado</option>
<option <?php if ($z01_estciv==3) { ?> selected="selected" <?php } ?> value="3">Viúvo</option>
<option <?php if ($z01_estciv==4) { ?> selected="selected" <?php } ?> value="4">Divorciado</option>
</select>
         </td>
    </tr>
	
    <tr> 
        <td nowrap title=Pai Campo:z01_pai>          <strong>Pai:</strong>          </td>
		<td nowrap title="Pai Campo:z01_pai"> 
			<input title="Pai Campo:z01_pai" name="z01_pai"  type="text"     id="z01_pai"  value="<?=$z01_pai ?>"  size="40"
	maxlength="40"
     style="background-color:#FFFFFF;text-transform:uppercase;"     autocomplete='off'>
         </td>
    </tr>
    <tr> 
		<td nowrap title=MÃE Campo:z01_mae>     <strong>Mãe:</strong>          </td>
        <td nowrap title="MÃE Campo:z01_mae">
			<input title="MÃE Campo:z01_mae" name="z01_mae"  type="text"     id="z01_mae"  value="<?=$z01_mae ?>"  size="40"
	maxlength="40"
     style="background-color:#FFFFFF;text-transform:uppercase;"     autocomplete='off'>
        </td>
      </tr>
	  <tr><td style="height:10px" ></td></tr>
											<tr> 
												<td nowrap title="Endereço

Campo:z01_ender                               "> 
													<strong>Endereço:</strong>												</td>
												<td nowrap> 
													
  <input title="Sequencial da tabela

Campo:z05_sequencia                           " name="z05_sequencia"  type="hidden"     id="z05_sequencia"  value=""  size="5"
	maxlength="10"
     style="background-color:#FFFFFF;"     autocomplete='off'>
  
  <input title="Código do logradouro cadastrado no sistema

Campo:j14_codigo                              " name="j14_codigo"  type="hidden"     id="j14_codigo"  value=""  size="5"
	maxlength="7"
     style="background-color:#FFFFFF;"     autocomplete='off'>
  
  <input title="Endereço

Campo:z01_ender                               " name="z01_ender"  type="text"     id="z01_ender"  value="<?=$z01_ender ?>"  size="41"
	maxlength="100"
     style="background-color:#FFFFFF;text-transform:uppercase;"     autocomplete='off'>
  												</td>
											</tr>
											<tr> 
												<td width="29%" nowrap title="Numero do endereco

Campo:z01_numero                              "> 
													<strong>Número:</strong> 
												</td>
												<td width="71%" nowrap>
													<a name="AN3"> 
														
  <input title="Numero do endereco

Campo:z01_numero                              " name="z01_numero"  type="text"     id="z01_numero"  value="<?=$z01_numero ?>"  size="8"
	maxlength="6"
     style="background-color:#FFFFFF;"     autocomplete='off'>
  														&nbsp; 
														<strong>Complemento:</strong>														
  <input title="Complemento do numero do endereco

Campo:z01_compl                               " name="z01_compl"  type="text"     id="z01_compl"  value="<?=$z01_compl ?>"  size="10"
	maxlength="100"
     style="background-color:#FFFFFF;text-transform:uppercase;"     autocomplete='off'>
  													</a> 
												</td>
											</tr>
											<tr> 
												<td nowrap title="Município

Campo:z01_munic                               "> 
													<strong>Município:</strong>												</td>
												<td nowrap> 
													
  <input title="Município

Campo:z01_munic                               " name="z01_munic"  type="text"     id="z01_munic"  value="<?=$z01_munic ?>"  size="20"
	maxlength="40"
     style="background-color:#FFFFFF;text-transform:uppercase;"     autocomplete='off'>
  												</td>
											</tr>
											<tr> 
												<td nowrap title="Unidade Federativa (estado)

Campo:z01_uf                                  "> 
													<strong>UF:</strong>												</td>
												<td nowrap> 
													
  <input title="Unidade Federativa (estado)

Campo:z01_uf                                  " name="z01_uf"  type="text"     id="z01_uf"  value="<?=$z01_uf ?>"  size="2"
	maxlength="2"
     style="background-color:#FFFFFF;text-transform:uppercase;"     autocomplete='off'>
  												</td>
											</tr>
											<tr> 
												<td nowrap title="Bairro

Campo:z01_bairro                              "> 
													<strong>Bairro:</strong>												</td>
												<td nowrap> 
													
  <input title="Bairro

Campo:z01_bairro                              " name="z01_bairro"  type="text"     id="z01_bairro"  value="<?=$z01_bairro ?>"  size="25"
	maxlength="40"
     style="background-color:#FFFFFF;text-transform:uppercase;"     autocomplete='off'>
  
  <input title="Código do bairro

Campo:j13_codi                                " name="j13_codi"  type="hidden"     id="j13_codi"  value=""  size="6"
	maxlength="6"
   style="background-color:#E6E4F1"     onblur="js_ValidaMaiusculo(this,'f',event);"
    onKeyUp="js_ValidaCampos(this,0,'Cód. do Bairro','t','f',event);"
    onKeyDown="return js_controla_tecla_enter(this,event);"
      autocomplete='off'>
  												</td>
											</tr>
											<tr> 
												<td nowrap title="CEP

Campo:z01_cep                                 "> 
													<strong>CEP:</strong>												</td>
												<td nowrap> 
													
  <input title="CEP

Campo:z01_cep                                 " name="z01_cep"  type="text"     id="z01_cep"  value="<?=$z01_cep ?>"  size="9"
	maxlength="8"
     style="background-color:#FFFFFF;text-transform:uppercase;"     autocomplete='off'>
  												</td>
											</tr>
											<tr> 
												<td nowrap title="Telefone

Campo:z01_telef                               "> 
													<strong>Telefone:</strong>												</td>
												<td nowrap> 
													
  <input title="Telefone

Campo:z01_telef                               " name="z01_telef"  type="text"     id="z01_telef"  value="<?=$z01_telef ?>"  size="12"
	maxlength="12"
     style="background-color:#FFFFFF;text-transform:uppercase;"     autocomplete='off'>
  												</td>
											</tr>
											<tr> 
												<td nowrap title="Telefone Celular

Campo:z01_telcel                              "> 
													<strong>Celular:</strong>												</td>
												<td nowrap> 
													
  <input title="Telefone Celular

Campo:z01_telcel                              " name="z01_telcel"  type="text"     id="z01_telcel"  value="<?=$z01_telcel ?>"  size="12"
	maxlength="12"
     style="background-color:#FFFFFF;text-transform:uppercase;"     autocomplete='off'>
  												</td>
											</tr>
											<tr> 
												<td nowrap title="Fax

Campo:z01_fax                                 "> 
													<strong>Fax:</strong>												</td>
												<td nowrap> 
													
  <input title="Fax

Campo:z01_fax                                 " name="z01_fax"  type="text"     id="z01_fax"  value="<?=$z01_fax ?>"  size="12"
	maxlength="12"
     style="background-color:#FFFFFF;text-transform:uppercase;"     autocomplete='off'>
  												</td>
											</tr>
											<tr> 
												<td nowrap title="Email

Campo:z01_email                               "> 
													<strong>Email:</strong>												</td>
												<td nowrap> 
													
  <input title="Email

Campo:z01_email                               " name="z01_email"  type="text"     id="z01_email"  value="<?=$z01_email ?>"  size="30"
	maxlength="100"
     style="background-color:#FFFFFF;"     autocomplete='off'>
  												</td>
											</tr>
											
											<input type="hidden" name="cgm" id="cgm" value="<?=$z01_numcgm ?>" >
											<!--<tr><td style="padding-top:20px" ><input type="submit" name="enviar" id="enviar" value="Salvar" ></td></tr>-->
										
									</fieldset> 
	  
</table>
    </table>
  </form>
</body>
