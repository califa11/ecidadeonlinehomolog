﻿<?
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

$id_usuario  = $aRetorno['id_usuario'];
$matricula   = $aRetorno['matricula'];
$instituicao = $aRetorno['instituicao'];
$numcgm      = db_getsession("DB_login");
$anoFolha    = db_anofolha($instituicao);
$mesFolha    = db_mesfolha($instituicao);

db_logs("","",0,"Consulta Funcional.");

$sUrl       = base64_encode("iMatric=".$matricula."&iInstit=".$instituicao);
$sUrlAverba = base64_encode("&averba");

/**
 * Caso o cliente seja Bage (codcli = 15) 
 * Então a variável lBloqueio passa a ser true e não mostrará os menus:
 * - Assentamento 
 * - Averbação do tempo de serviço 
 * - Férias
 * 
 * Do contrário todos os menus são mostrados normalmente.
 */
$lBloqueio = false;
$rsCodCli  = db_query("select db21_codcli from db_config where prefeitura is true limit 1");
$iCodCli   = db_utils::fieldsmemory($rsCodCli)->db21_codcli;
if ($iCodCli == 15 ) {
  $lBloqueio = true; 
}


$aRetorno = array();

parse_str(base64_decode($HTTP_SERVER_VARS["QUERY_STRING"]),$aRetorno);

$iMatric  = $aRetorno['iMatric'];

$oRHPessoal = db_utils::getDao('rhpessoal');


$sSqlDadosServidor = "select * from   rhpessoal  inner join cgm             on cgm.z01_numcgm                     = rhpessoal.rh01_numcgm
											     
											 where rhpessoal.rh01_regist = {$matricula}";
 
$rsDadosServidor = $oRHPessoal->sql_record($sSqlDadosServidor);
//$oDadosServidor  = db_utils::fieldsMemory($rsDadosServidor,0);
db_fieldsmemory($rsDadosServidor,0);


?>
<html>
<head>
<title><?=$w01_titulo?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="config/portalservidor.css" rel="stylesheet" type="text/css">
<script language="JavaScript" src="scripts/db_script.js">

</script>
<style>

p.big {
    line-height: 25px;
    font-size: 18;
}

input:focus{
  border: 1px solid rgb(239,132,60);
</style>
<script type="text/javascript">
/* Máscaras ER */
function mascara(o,f){
    v_obj=o
    v_fun=f
    setTimeout("execmascara()",1)
}
function execmascara(){
    v_obj.value=v_fun(v_obj.value)
}
function mtel(v){
    v=v.replace(/\D/g,"");             //Remove tudo o que não é dígito
    v=v.replace(/^(\d{2})(\d)/g,"($1) $2"); //Coloca parênteses em volta dos dois primeiros dígitos
    v=v.replace(/(\d)(\d{4})$/,"$1-$2");    //Coloca hífen entre o quarto e o quinto dígitos
    return v;
}
function id( el ){
	return document.getElementById( el );
}
window.onload = function(){
	id('celular_novo').onkeyup = function(){
		mascara( this, mtel );
	}
	id('celular2_novo').onkeyup = function(){
		mascara( this, mtel );
	}
	id('telefone_novo').onkeyup = function(){
		mascara( this, mtel );
	}
}
</script>
</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" bgcolor="<?=$w01_corbody?>">

  




<table width="100%" border="0" bordercolor="#cccccc" cellpadding="2" cellspacing="0" class="texto">
 <tr>
  <td><br></td>
 </tr>
</table>
<?
  if ($id_usuario != "") { 
?>
   <?php 
     

      $sql  = "SELECT * FROM atualizacadastrorh WHERE cgm='{$_SESSION['CGM']}' ";
      $result = pg_query($sql);
      if(pg_num_rows($result)>0){  ?>
<table width="100%" border="0" cellpadding="2" cellspacing="0" class="texto">
  <tr>
    <td valign="top" width="10%">
     <table width="200" height="10%" id="navigation" border="0">
        <tr>
           <td nowrap="nowrap" width="100%">
             <span class="navText" style="cursor: pointer;" onClick="js_atualizaFrame('dadosCadastrais');">Consulta Dados Cadastrais</span>           </td>
        </tr>
        
        <? if ($lBloqueio == false ) { ?>
        <tr>
           <td nowrap="nowrap" width="100%">
             <span class="navText" style="cursor: pointer;" onClick="js_atualizaFrame('assentamentos');">Assentamentos</span>           </td>
        </tr>     
        <tr>
           <td nowrap="nowrap" width="100%">
             <span class="navText" style="cursor: pointer;" onClick="js_atualizaFrame('averbacao');">Averbação de Tempo de Serviço</span>           </td>
        </tr>
        <? } ?>
        
        <tr>
            <td nowrap="nowrap" width="100%">
              <span class="navText" style="cursor: pointer;" onClick="js_atualizaFrame('dependentes');">Dependentes</span>            </td>
         </tr>
         
        <? if ($lBloqueio == false ) { ?>
         <tr>
            <td nowrap="nowrap" width="100%">
              <span class="navText" style="cursor: pointer;" onClick="js_atualizaFrame('ferias');">Comprovante de Rendimentos</span>            </td>
         </tr>
        <? } ?>
                 
   
		
         <tr>
            <td nowrap="nowrap" width="100%">
              <span class="navText" style="cursor: pointer;" onClick="js_atualizaFrame('fichaFinanceira');">Ficha Financeira</span>            </td>
         </tr>
		 
	       <? if ($instituicao == 1 ) { ?>
         <tr>
             <td nowrap="nowrap" width="100%"><span class="navText" style="cursor: pointer;" onClick="window.open('https://www.neoconsig.com.br/neoconsig')">Empréstimo Consignado</span></td>
         </tr>   
         <? } ?>

         <? if ($instituicao == 3 ) { ?>
          <tr>
            <td nowrap="nowrap" width="100%">
              <span class="navText" style="cursor: pointer;" onClick="js_atualizaFrame('conveniosaae');">Convênio Saúde - SAAE</span>            </td>
         </tr>
         <? } ?>

         <? if ($instituicao == 3 ) { ?>
          <tr>
            <td nowrap="nowrap" width="100%">
              <span class="navText" style="cursor: pointer;" onClick="js_atualizaFrame('cartilhasaae');">Cartilha</span>            </td>
         </tr>
         <? } ?>

                              
         <tr>
           <td nowrap="nowrap" width="100%">
             <span class="navText" style="cursor: pointer;" onClick="js_voltar('<?=$id_usuario?>');">Voltar</span>           </td>
         </tr>          
     </table>    </td>
    <td valign="top" width="97%">
      <?php }
      if(isset($_SESSION['CGM'])){

      $sql  = "SELECT * FROM atualizacadastrorh WHERE cgm='{$_SESSION['CGM']}' ";
      $result = pg_query($sql);
      if(pg_num_rows($result)==0){  ?>

      <style type="text/css">
        #enquete p,form {font-family: Arial, Verdana, Helvetica, sans-serif;font-size: 11px;color: #000000;}
      </style><center> 
      <fieldset style="margin-left: 15px;width: 600px;"  id="enquete">
      <legend align="center">
        <p style="font-size:20px" ><strong>Atualização dos Dados Cadastrais:</strong></p>      </legend>
      <div >
      <?php
      if(isset($_SESSION['CGM']) && isset($_POST['acao']) && $_POST['acao']=='Salvar'){
        $cgm      =   $_SESSION['CGM'];
      
       
        $telefone_novo =   pg_escape_string($_POST['telefone_novo']);
        $celular_novo =   pg_escape_string($_POST['celular_novo']);
        try {
             
          $sql  = "SELECT * FROM atualizacadastrorh WHERE cgm='$cgm' ";
          $result = pg_query($sql);
          if(pg_num_rows($result)>0){
            throw new Exception("Você já realizou o cadastro, obrigado.");
          }else{
                
            $sql  = "INSERT INTO atualizacadastrorh (cgm,nome_novo,matricula_novo,endereco_novo,numero_novo,complemento_novo,municipio_novo,estado_novo,bairro_novo,cep_novo,email_novo,telefone_novo,celular_novo,celular2_novo,data) VALUES ($cgm,'$nome_novo','$matricula_novo','$endereco_novo','$numero_novo','$complemento_novo','$municipio_novo','$estado_novo','$bairro_novo','$cep_novo','$email_novo','$telefone_novo','$celular_novo','$celular2_novo',NOW()) ";
            $query  = pg_query($sql);
            echo '<b style="color: green;font-size: 14px;">Atualização de Dados realizada com sucesso!</b>';

            echo "<script type='text/javascript'>
            alert('Acesso liberado, clique no menu Servidor.')
                    var tim = window.setTimeout('hideEnquete()', 3000);
                    function hideEnquete() {
                      document.getElementById('enquete').style.display='none'; 
                    }
                  </script>";
          }

        } catch (Exception $e) {
          echo '<b style="color: red;font-size: 20px;">'.$e->getMessage().'</b>';
        }
      }else{
      ?>
      
     <center><p style="font-size:19px">Caro Servidor, estamos atualizando as informações cadastrais, para continuar atualize seus dados:</p></center>
   
      <form action="" method="post"> <td nowrap title=Nome Campo:z01_nome>          <strong>Nome:</strong>          </td><td nowrap title="Nome"> 
       <input title="Nome" name="nome_novo"  style="background-color:#EDECE7;" type="text"     id="z01_nome"  value="<?=$z01_nome ?>"  size="40"
	maxlength="50"
     style="background-color:#FFFFFF;text-transform:uppercase;"     autocomplete='off' readonly></td>
<td nowrap title=Nome Campo:matricula>          <strong>Matrícula:</strong>          </td><td nowrap title="Nome Campo:Matrícula"> 
       <input title="Matrícula" name="matricula_novo" style="background-color:#EDECE7;" type="text"     id="matricula"  value="<?=$matricula ?>"  size="20" maxlength="40" autocomplete='off' readonly></td>
<br> <br>

     <td nowrap title="E-mail"> 
													<strong>Endereço:</strong>												</td>
      <input title="Endereço" name="endereco_novo"  type="text"     id="z01_ender"  value="<?=$z01_ender ?>"  size="30"
	maxlength="100"
     style="background-color:#FFFFFF;text-transform:uppercase;"     autocomplete='off'>
  												</td>
											</tr>
											<tr> 
												<td width="29%" nowrap title="Número do Endereço"> 
													<strong>Número:</strong> 
												</td>
												<td width="71%" nowrap>
													<a name="AN3"> 
														
  <input title="Número do Endereço" name="numero_novo"  type="text"     id="z01_numero"  value="<?=$z01_numero ?>"  size="7"
	maxlength="6"
     style="background-color:#FFFFFF;"     autocomplete='off'>
  														&nbsp; 
														<strong>Complemento:</strong>														
  <input title="Complemento" name="complemento_novo"  type="text"     id="z01_compl"  value="<?=$z01_compl ?>"  size="7"
	maxlength="30"
     style="background-color:#FFFFFF;text-transform:uppercase;"     autocomplete='off'>
  													</a> 
												</td>
											</tr>
											<tr> <br> <br>
												<td nowrap title="Município"> 
													<strong>Município:</strong>												</td>
												<td nowrap> 
													
  <input title="Município" name="municipio_novo"  type="text"     id="z01_munic"  value="<?=$z01_munic ?>"  size="20"
	maxlength="40"
     style="background-color:#FFFFFF;text-transform:uppercase;"     autocomplete='off'>
  												</td>
											</tr>
											<tr> 
												<td nowrap title="Unidade Federativa (Estado)"> 
													<strong>UF:</strong>												</td>
												<td nowrap> 
													
  <input title="Unidade Federativa (Estado)" name="estado_novo"  type="text"     id="z01_uf"  value="<?=$z01_uf ?>"  size="2"
	maxlength="2"
     style="background-color:#FFFFFF;text-transform:uppercase;"     autocomplete='off'>
  												</td>
											</tr>
											<tr> <br> <br>
												<td align="right" nowrap title="Bairro"> 
													<strong>Bairro:</strong>												</td>
												<td nowrap> 
													
  <input title="Bairro" name="bairro_novo"  type="text"     id="z01_bairro"  value="<?=$z01_bairro ?>"  size="25"
	maxlength="40"
     style="background-color:#FFFFFF;text-transform:uppercase;"     autocomplete='off'>
  

  												</td>
											</tr>
											<tr> 
												<td nowrap title="CEP"> 
													<strong>CEP:</strong>												</td>
												<td nowrap> 
													
  <input title="CEP" name="cep_novo"  type="text"     id="z01_cep"  value="<?=$z01_cep ?>"  size="9"
	maxlength="8"
     style="background-color:#FFFFFF;text-transform:uppercase;"     autocomplete='off'>
  												</td>
											</tr>
											  <br> <br>
       
											<tr> 
												<td nowrap title="E-mail"> 
													<strong>E-mail:</strong>												</td>
												<td nowrap> 
													
  <input title="E-mail" name="email_novo"  type="text"     id="z01_email"  value="<?=$z01_email ?>"  size="50"
	maxlength="40"
     style="background-color:#FFFFFF;"     autocomplete='off'>
  												</td>
											</tr>
											
					<tr> 
												<td nowrap title="Telefone"> 
													<strong>Telefone:</strong>												</td>						
					<td nowrap> 
													
  <input title="Telefone" name="telefone_novo"  type="text"     id="telefone_novo"  pattern=".{14,15}" size="14"
	maxlength="14"
     style="background-color:#FFFFFF;"     autocomplete='off'>


</td>
											</tr> <br> <br>
<tr> 
												<td nowrap title="Celular"> 
													<strong>Celular:</strong>												</td><td>

          <input  title="Celular" type="text" class="form-control"  id="celular_novo"   maxlength="15" name="celular_novo" pattern=".{15,16}"  required>

</td>
</tr>
 <tr> 
												<td nowrap title="Celular 2"> 
													<strong>Celular 2:</strong>												</td>
												<td nowrap> 
													
  <input title="Celular 2" name="celular2_novo"  type="text"     id="celular2_novo"  pattern=".{15,16}" size="15"
	maxlength="15"
     style="background-color:#FFFFFF;text-transform:uppercase;"     autocomplete='off'>
  												</td>
											</tr>

 <br> <br> <br> <tr><td> <center>  <input type="submit" name="acao" value="Salvar" ></center></td></tr>
        
      </form>
      
<?php 
          } ?>
        </div>
      </fieldset><center> 
      <p>
        <?php  
        }
      }   ?>
      </p>
      <p>&nbsp;</p>


       <div >
      <?  
	  if($matricula >= 5000000 && $matricula <= 5099999) {
	    echo "SERVIDOR(A), INFORME-SE SOBRE A LEI COMPLEMENTAR NR. 183 (PCC-SAÚDE) NO SITE DA PREFEITURA.";
		  } else { echo " "; }
      ?>
     </div>
     <p>&nbsp;</p>
    <iframe id="iframePortalServidor" name="iframe" src="centro_pref.php" width="100%" height="500px;" style="border:hidden;"></iframe>
         </td width="3%"> 
    <td>&nbsp;</td>       
  </tr>  
</table>
<? 
  } else if ($w13_permfornsemlog == "f") {
?>
 <table width="300" border="0" bordercolor="#cccccc" cellpadding="2" cellspacing="0" class="texto">
  <tr height="220">
   <td align="center">
    <img src="imagens/atencao.gif"><br>
    Para acessar suas informações, efetue login.
   </td>
  </tr>
 </table>
<?
}
?>
</body>
</html>
<script>
function imprimir(){
 jan=window.open('',
                 '',
                 'width='+(screen.availWidth-5)+',height='+(screen.availHeight-40)+',scrollbars=1,location=0');
                 
 jan.moveTo(0,0);
}

function js_voltar(id){
  var idusuario = id;
  document.location.href = 'cons_funcional.php?id_usuario='+idusuario;
}

function js_atualizaFrame( sOpcao ){

  var sQuery = '<?=$sUrl?>'; 
 
  if ( sOpcao == 'dadosCadastrais') {
    document.getElementById('iframePortalServidor').src = 'dadosfuncionario.php?'+sQuery;
  } else if (sOpcao == 'assentamentos') {
    document.getElementById('iframePortalServidor').src = 'dadosassentamentos.php?'+sQuery;
  } else if (sOpcao == 'averbacao') {
    document.getElementById('iframePortalServidor').src = 'dadosassentamentos.php?'+sQuery+'<?=$sUrlAverba?>';
  } else if (sOpcao == 'dependentes') {
    document.getElementById('iframePortalServidor').src = 'dependentesservidor.php?'+sQuery;
  } else if (sOpcao == 'ferias') {
    document.getElementById('iframePortalServidor').src = 'dadosfuncionario2.php?'+sQuery;
  } else if (sOpcao == 'comprovanteRendimentos') {
    document.getElementById('iframePortalServidor').src = 'comprovanterendimentosservidor.php?'+sQuery;
  } else if (sOpcao == 'fichaFinanceira') {
    document.getElementById('iframePortalServidor').src = 'fichafinanceiraservidor.php?'+sQuery;
  } else if (sOpcao == 'atualizadados') {
    document.getElementById('iframePortalServidor').src = 'atualiza_dados.php?'+sQuery;
  }  else if (sOpcao == 'cartilhasaae') {
    document.getElementById('iframePortalServidor').src = 'cartilhasaae.php?'+sQuery;
  }  else if (sOpcao == 'conveniosaae') {
    document.getElementById('iframePortalServidor').src = 'conveniosaae.php?'+sQuery;
  }
  

}
</script>
