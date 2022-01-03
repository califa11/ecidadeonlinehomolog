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

include("libs/db_stdlib.php");
include("libs/db_sql.php");
require_once("libs/db_utils.php");
require_once("std/DBLargeObject.php");
db_postmemory($HTTP_SERVER_VARS);


?>
<html>
<head>
<title>Licita��es</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" type="text/css" href="include/estilodai.css" >
<script language="JavaScript" src="scripts/db_script.js"></script>
<style type="text/css">
<?
db_estilosite()
?>
</style>
</head>
<body bgcolor="<?=$w01_corbody?>" >
<?
$sSqlParam  = " select l12_tipoliberacaoweb,    ";
$sSqlParam .= "        l12_qtdediasliberacaoweb "; 
$sSqlParam .= "   from licitaparam              ";
$sSqlParam .= "  where l12_instit = ".db_getsession('DB_instit');

$rsSqlParam = db_query($sSqlParam);
$iNumRows   = pg_num_rows($rsSqlParam);
if ($iNumRows > 0) {

  $l12_tipoliberacaoweb     = db_utils::fieldsmemory($rsSqlParam, 0)->l12_tipoliberacaoweb; 
  $l12_qtdediasliberacaoweb = db_utils::fieldsmemory($rsSqlParam, 0)->l12_qtdediasliberacaoweb;
}

$data = date("Y-m-d");

if (!isset($tipo)){
	echo "";
} else {

    /*
     * Valida se A op��o do menu for de licita��o aberta ou Julgada
     * Se for Julgada mostra os pempenhos se tiver.
     * 
     */

    if ($julgada == 0) {     
      $sJulgada = 'l11_licsituacao = 0';
    }else{     
      $sJulgada = 'l11_licsituacao = 1';
    }
    
  $sWhere = "where l29_datapublic <= '$data' 
             and l20_dataaber   >= '$data'  
             and l20_codtipocom  = $tipo";


	if ($l12_tipoliberacaoweb == 2) {
	
	  $sWhere  = "left join liclicitasituacao on l11_liclicita = l20_codigo "; 
	  $sWhere .= "      and l20_licsituacao =  l11_licsituacao ";
	  $sWhere .= "where l29_datapublic<='$data'                             ";
	  $sWhere .= "      and l20_codtipocom  = $tipo                         ";
	  //$sWhere .= "      and ((l11_licsituacao = 0)  or ( l11_licsituacao = 1  ";   // 0 = em aprovacao e 1 = aprovada
	  $sWhere .= "      and (($sJulgada ";
	  $sWhere .= "      and   l11_data +'$l12_qtdediasliberacaoweb days'::interval >='$data'))";
	}
	//die($sWhere);
  // mostra editais........mostrar somente os que for liberado para publica��o de acordo com a data de publica��o e retidar os que a data de abetrura ja passou
  $sql = "select * from (
   select distinct on(l20_codigo) l20_codigo,
                 l03_descr, 
                 l20_dataaber, 
                 l29_datapublic, 
                 l20_horaaber,  
                 l20_local, 
                 l20_objeto,  
                 l20_codtipocom,  
                 l20_numero, 
                 l29_contato, 
                 l29_email,  
                 l29_telefone,  
                 l29_obs, 
                 l29_liberaedital
  		      from liclicita
  		     inner join cflicita     on l03_codigo    = l20_codtipocom
  		     inner join liclicitaweb on l29_liclicita = l20_codigo
  		     $sWhere) as x order by l20_dataaber desc
  		     ";
	//echo $sql;	
  $result = pg_query($sql);
  $lin = pg_num_rows($result);
?>
  <table width="100%" border="0" align= "center">
	 <tr><td>&nbsp;</td></tr>
  </table>
  
  <table width="90%" border="0" align= "center" class="texto">
  	<form name="form1" method="post" action="">
	   <?
		  if ($lin>0) {
			
		    db_query($conn, "begin;");
			  for ($i = 0; $i < $lin; $i++) {
  				db_fieldsmemory($result, $i);
				
	  			$data = (db_formatar($l20_dataaber,"d"));
				
		  		echo "<tr bgcolor='$w01_corfundomenu'>
			  		      <td> <b>$l03_descr N� $l20_numero </b></td>
				        </tr>
				    	  <tr>
				  	      <td> Data de abertura: $data</td>
				  	    </tr>
  				   	  <tr>
	  			   	    <td> Hora: $l20_horaaber </td>
		  		   	  </tr>
			  	 	    <tr>
				   	      <td> Local: $l20_local </td>
				 	      </tr>";
				 	     
				  if ($l29_contato!="" || $l29_contato != null){
  					echo "<tr><td>Contato: $l29_contato</td></tr>";
	  			}
				
		  		if ($l29_telefone!="" || $l29_telefone != null){
			 	  	echo"<tr><td>Telefone: $l29_telefone</td></tr>";
				  }
				
				  if ($l29_email!="" || $l29_email != null){
				  	echo"<tr><td>Email: $l29_email</td></tr>";
				  }
				
				  if ($l29_obs!="" || $l29_obs != null){
				  	echo"<tr><td>Obs: $l29_obs</td></tr>";
				  }
				
		      echo "<tr><td>Objeto: $l20_objeto </td></tr>";
				
		      $sqledital= "select l20_codigo,
  		                        l27_arquivo, 
	  	                        l27_arqnome
		  					         from liclicita
			  				        inner join liclicitaedital on l20_codigo    = l27_liclicita
				  			        inner join liclicitaweb    on l29_liclicita = l20_codigo
					  		        where l20_codigo=$l20_codigo";  
				  $resultedital = pg_query($sqledital);
				  $linhaedital = pg_num_rows($resultedital);
	 				
  	 			// .....efetuar cadastro para baixar edital
	  			if (isset($l29_liberaedital) && $l29_liberaedital == 2) {	
		  	 		echo"<tr><td><a href='lic_cadastrobaixa.php?edital=$l20_codigo' class='links'> Baixar edital e anexos</a></td></tr>";
			  		 
			   	//......baixar edital direto...sem cadastro	
				  } elseif (isset($l29_liberaedital) && $l29_liberaedital== 1) {
  					echo"<tr><td>";
	  				for ($e = 0; $e < $linhaedital; $e++) { 
		  				db_fieldsmemory($resultedital,$e);
		  				if(DBLargeObject::leitura($l27_arquivo, "$DOCUMENT_ROOT/dbpref/tmp/$l27_arqnome")) {
  	  				 	echo"<a href='tmp/$l27_arqnome' target='_blank'> $l27_arqnome </a>";
  	 					  echo"&nbsp;&nbsp;&nbsp;";
		  				}
					}
						echo"</td></tr>";
				}
        /*
         * Verifica Se as licita��es julgadas possuem empenho
         * e as exibe no modelo:
         * numero/ano,
         */
      		if ($julgada == 1) {     
              $sSqlempenho = "SELECT distinct e60_codemp, 
                                              e60_anousu 
                                                from liclicitem 
                                              inner join empautitempcprocitem on e73_pcprocitem = l21_codpcprocitem 
                                              inner join empautoriza on e73_autori = e54_autori 
                                              inner join empempaut   on e61_autori = e54_autori 
                                              inner join empempenho  on e61_numemp = e60_numemp 
                                              where l21_codliclicita = $l20_codigo";
             
             $rsEmpenho = pg_exec($sSqlempenho); 
             $sLinhasempenho = pg_num_rows($rsEmpenho);
            // die($sLinhasempenho);
             if ($sLinhasempenho > 0){                               
                 echo"<tr><td>Empenhos :</td></tr>";
                 echo"<tr><td>";  
                 $sVirgula = ''; 
                 for ($iEmpenho = 0; $iEmpenho < $sLinhasempenho; $iEmpenho++){
                    
                    db_fieldsmemory($rsEmpenho,$iEmpenho);
                    if ($iEmpenho != 0) {
                      $sVirgula = ',  ';
                    }
                    echo $sVirgula.$e60_codemp."/".$e60_anousu; 
                     // echo $iEmpenho;
                 }
                 
                 echo "</td></tr>";
              
             }
            
      		}				
				
			}
			db_query($conn, "commit;");
		}
}
		?>
	</form>
</table>

</body>
</html>