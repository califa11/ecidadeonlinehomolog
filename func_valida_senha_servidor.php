<?php
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

error_reporting('** FAVOR FA�A SEU PEDIDO DE SENHA! **');
include("libs/db_conecta.php");
include("libs/db_stdlib.php");
include("libs/db_utils.php");
include("dbforms/db_funcoes.php");
include("libs/db_sql.php");

$oGet     = db_utils::postmemory($_GET);
$sMatric  = $oGet->nummatric;
$datanasc = formataDataNasc($oGet->z01_nasc);
$cpf      = formataCpf($oGet->z01_cgccpf);
$conf     = true;

if($oGet->chave == 't'){
	if(isset($oGet->sqms) && $oGet->sqms == 't'){
		   $sErroUsuario = "t";
		   // verifica numcgm
		   $sqlCgmUsuario = " select z01_nome,
		                             z01_numcgm 
		                        from cgm 
		                       where z01_cgccpf = '{$cpf}' ";    
		   //die($sqlCgmUsuario);
		   $queryCgm = pg_query($sqlCgmUsuario);
		   $totalCgm = pg_num_rows($queryCgm);

	        if($totalCgm > 0){
            db_fieldsmemory($queryCgm,0);   
          }		   
		   
       //verifica se usu�rio j� � cadastrado
       $sqlUsuario = " select login 
                         from db_usuarios 
                        where nome   = '{$z01_nome}' 
                          and login  = '{$z01_numcgm}' 
                          and usuext = '1' ";
       //die($sqlUsuario);
       $queryUsuario = pg_query($sqlUsuario);
       $total = pg_num_rows($queryUsuario);		 

       if($total > 0){
       	  $sErroUsuario = "f";      
	       	$sql = " select rh01_regist,
	                      z01_numcgm,
	                      z01_nome,
	                      z01_cgccpf,
	                      z01_nasc,
	                      z01_mae,
	                      z01_email
	                 from rhpessoal 
	                      inner join cgm on rh01_numcgm = z01_numcgm 
	                      left join db_usuarios on db_usuarios.nome  =  cgm.z01_nome  
	                where rh01_regist = '{$sMatric}'
	                  and z01_cgccpf  = '{$cpf}' limit 1 ";
	        //die($sql);           
	        $result = pg_query($sql);
	        $total  = pg_num_rows($result);
	    
	        if($total > 0){
	          db_fieldsmemory($result,0);   
	        }
       	
       }
       
	} else {
		  $sErroUsuario = "f";
		  $sql = " select rh01_regist,
		                  z01_numcgm,
		                  z01_nome,
		                  z01_cgccpf,
		                  z01_nasc,
		                  z01_mae,
		                  z01_email
		             from rhpessoal 
		                  inner join cgm on rh01_numcgm = z01_numcgm 
		                  left join db_usuarios on db_usuarios.nome  =  cgm.z01_nome  
		            where rh01_regist = '{$sMatric}'
		              and z01_cgccpf  = '{$cpf}' limit 1 ";
		
		    //die($sql);           
		    $result = pg_query($sql);
		    $total  = pg_num_rows($result);
		
		    if($total > 0){
		      db_fieldsmemory($result,0);   
		    }
		    
	}
}
    
function formataDataNasc($sData){

$data         = str_replace("/", "", $sData);
$datanasc_dia = substr($data, -8,2);
$datanasc_mes = substr($data, -6,2);
$datanasc_ano = substr($data, -4);  
$datanasc     = $datanasc_ano."-".$datanasc_mes."-".$datanasc_dia;

return $datanasc;
}

function formataCpf($sCpf){

$cpf = str_replace(".", "", $sCpf);
$cpf = str_replace("-", "", $cpf);

return $cpf;
}

 if(@$z01_nasc != ""){
  $z01_nasc_dia = substr($z01_nasc,8,2);
  $z01_nasc_mes = substr($z01_nasc,5,2);
  $z01_nasc_ano = substr($z01_nasc,0,4);
 }

if($sMatric             == $rh01_regist      && $rh01_regist  != '' &&
   trim($cpf)           == trim($z01_cgccpf) && $z01_cgccpf   != '') {
?>
<script>  
str = "<span><font color='#E9000'> SUA SENHA DEVE CONTER NO M�NIMO 6 CARACTERES, LETRAS E N�MEROS! </font></span>";

   var msgerro = str; 

   parent.document.form1.matricula.value     = '<?= $oGet->nummatric; ?>';
   parent.document.form1.nome.value          = '<?= $oGet->z01_nome; ?>';
   parent.document.form1.cpf.value           = '<?= $oGet->z01_cgccpf; ?>';
   parent.document.form1.z01_nasc_dia.value  = '<?= $z01_nasc_dia; ?>';
   parent.document.form1.z01_nasc_mes.value  = '<?= $z01_nasc_mes; ?>';
   parent.document.form1.z01_nasc_ano.value  = '<?= $z01_nasc_ano; ?>';
   //parent.document.form1.nomemae.value       = '<?= $oGet->z01_mae; ?>';
   parent.document.form1.emailsrv.value      = '<?= $oGet->email; ?>';
   
   parent.document.form1.matricula.disabled     = true;   
   parent.document.form1.nome.disabled          = true;
   parent.document.form1.cpf.disabled           = true;
   parent.document.form1.z01_nasc_dia.disabled  = true;
   parent.document.form1.z01_nasc_mes.disabled  = true;
   parent.document.form1.z01_nasc_ano.disabled  = true;
   parent.document.form1.dtjs_z01_nasc.disabled = true;
   //parent.document.form1.nomemae.disabled       = true;      
   parent.document.form1.btnlmp.disabled        = true;
   
   parent.document.getElementById("rdsenha").style.display       = '';
   parent.document.getElementById("confrdsenha").style.display   = '';
   parent.document.getElementById("msgerro").innerHTML           = '';
   parent.document.getElementById("btnsub").style.display        = 'none';   
   parent.document.getElementById("btnenviar").style.display     = '';
   parent.document.getElementById('msgerro').style.display       = 'none';
   parent.document.getElementById('msgerro').innerHTML           = '';
   parent.document.getElementById('msgerrosenha').style.display  = '';
   parent.document.getElementById('msgerrosenha').innerHTML      = msgerro;
     
</script>
<?
} else {
	if(isset($sErroUsuario) && $sErroUsuario == 't'){
?>
<script> 
   str  = "<span><font color='#E9000'> OS DADOS DIGITADOS EST�O INCORRETOS! TENTE NOVAMENTE </font><BR />";
   str += "<font color='#E9000'> FAVOR FA�A SEU PEDIDO DE SENHA! </font></span>";
   
   var msgerro = str; 
 
   parent.document.getElementById('msgerro').style.display = '';
   parent.document.getElementById('msgerro').innerHTML = msgerro;
   parent.document.getElementById('msgerrosenha').innerHTML  = '';
</script>
<?
  } else {
?>
<script> 
   str  = "<span><font color='#E9000'> OS DADOS DIGITADOS S�O INCONSISTENTES! </font></span>";
   
   var msgerro = str; 
 
   parent.document.getElementById('msgerro').style.display = '';
   parent.document.getElementById('msgerro').innerHTML = msgerro;
   parent.document.getElementById('msgerrosenha').innerHTML  = '';
</script>
<?
  }
}
?>
