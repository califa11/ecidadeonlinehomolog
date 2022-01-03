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

require_once("libs/db_conecta.php");
require_once("libs/db_stdlib.php");
require_once("libs/db_sql.php");
require_once("libs/db_utils.php");
require_once("dbforms/db_funcoes.php");
require_once("libs/JSON.php");

$oPost    = db_utils::postMemory($_POST);
$oJson    = new services_json();

$lErro    = false;
$sMsgErro = '';

$dirmes = array();

$dir = $dirbasesaae.$oPost->anousu.'/';

if ( $oPost->tipo == "consultaMes" ) {

  if (is_dir($dir)) {
    if ($dh = scandir($dir,1)) {
        foreach ($dh as $file){
          if($file != "." && $file != ".."){
            $dirmes[] = array('codigo'=>$file,'descr'=>formatames($file));
          }
        }
        closedir($dh);
    }
  }

  $aRetorno = $dirmes;
  $contmes = count($dirmes);

  if ($contmes > 0 ) {
    $lErro = false;
  } else {
    $sMsgErro = 'Não foram encontrados arquivos para essa data';
    $lErro = true;
  }
  
  if ( $lErro ) {
    $aRetorno  = array( "sMsg" =>urlencode($sMsgErro),
                        "lErro"=>true );    
  } else {
    $aRetorno  = array( "aLista"=>$aRetorno,
                        "lErro" =>false );
  }

  echo $oJson->encode($aRetorno); 


} else if ( $oPost->tipo == "consultaDocumento" ) {

  $iMatric  = $oPost->matric;
  $iInstit = $oPost->instit;
  $iAno = $oPost->anousu;
  $iMes = $oPost->mesusu;

  $sSqlDadosServidor = "select distinct cgm.z01_cgccpf from rhpessoal
        join cgm on rh01_numcgm = cgm.z01_numcgm
        where rhpessoal.rh01_regist = {$iMatric} and rhpessoal.rh01_instit = {$iInstit}";

  $rsDadosServidor = db_query($sSqlDadosServidor);
  $iContDados = pg_num_rows($rsDadosServidor);
  $oDadosServidor  = db_utils::fieldsMemory($rsDadosServidor,0);



  if ($iContDados > 0 ) {
    $lErro = false;
  } else {
    $sMsgErro = 'Não foram encontrados arquivos para essa data';
    $lErro = true;
  }

  if ( $lErro ) {
    $aRetorno  = array( "sMsg" =>urlencode($sMsgErro),
                        "lErro"=>true ); 
  } else {
    $aRetorno  = array( "sSrc" =>"http://10.1.1.5/ecidadeonlinehomolog/documentos/convenio-saae/{$iAno}/{$iMes}/{$oDadosServidor->z01_cgccpf}.pdf","lErro"=>false ); 
  }

  echo $oJson->encode($aRetorno); 

}

function formatames($mes){
  $descrmes = '';

  if ($mes == '01') { $descrmes = 'Janeiro'; }
  else if ($mes == '02') { $descrmes = 'Fevereiro'; }
  else if ($mes == '03') { $descrmes = 'Marco'; }
  else if ($mes == '04') { $descrmes = 'Abril'; }
  else if ($mes == '05') { $descrmes = 'Maio'; }
  else if ($mes == '06') { $descrmes = 'Junho'; }
  else if ($mes == '07') { $descrmes = 'Julho'; }
  else if ($mes == '08') { $descrmes = 'Agosto'; }
  else if ($mes == '09') { $descrmes = 'Setembro'; }
  else if ($mes == '10') { $descrmes = 'Outubro'; }
  else if ($mes == '11') { $descrmes = 'Novembro'; }
  else if ($mes == '12') { $descrmes = 'Dezembro'; }
  else {$descrmes = $mes;}

  return $descrmes;

}
  
?>