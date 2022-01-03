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

include("libs/db_conecta.php");
include("libs/db_stdlib.php");
include("libs/db_sql.php");
include("libs/db_utils.php");
include("dbforms/db_funcoes.php");

validaUsuarioLogado();

$aRetorno = array();
parse_str(base64_decode($HTTP_SERVER_VARS["QUERY_STRING"]),$aRetorno);

$iMatric = $aRetorno['iMatric'];
$iInstit = $aRetorno['iInstit'];

$dir = $dirbasesaae;

$dirano = array();

  if (is_dir($dir)) {
    if ($dh = scandir($dir,1)) {
        foreach ($dh as $file){
          if($file != "." && $file != ".."){
            $dirano[$file] = $file;
          }
        }
        closedir($dh);
    }
}

$iNroCalculoAnos = count($dirano);  

?>
<html>
<head>
<title><?=$w01_titulo?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="config/estilos.css"        rel="stylesheet" type="text/css">
<link href="config/portalservidor.css" rel="stylesheet" type="text/css">
<script language="JavaScript" src="scripts/scripts.js"  ></script>
<script language="JavaScript" src="scripts/strings.js"  ></script>
<script language="JavaScript" src="scripts/db_script.js"></script>
<script language="JavaScript" src="scripts/prototype.js"></script>
</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" bgcolor="<?=$w01_corbody?>" onLoad="" <?mens_OnHelp()?>>
  <form name="form1" method="post"  target="iframeArqConvenio" >
    <table align="center" width="100%">
      <tr>  
        <td class="tituloForm">
           Convênio Saúde - SAAE
        </td>
      </tr>
      <tr>  
        <td>
          <fieldset>
          <?
           
         if ( $iNroCalculoAnos > 0 ) {
          
          ?>
          <table  class="tableForm"  align="center">
            <tr>
              <td class="labelForm">
                Ano Base:
              </td>
              <td class="dadosForm">
                <?
                   if ( $iNroCalculoAnos > 0 ) {
                     db_select('anocalc',$dirano,true,1,'onchange="js_consultaMes();"','','');
                   }
                   
                   db_input('iMatric',10,'',true,'hidden',1,'');
                   db_input('iInstit',10,'',true,'hidden',1,'');
                ?>
              </td>
              <td class="labelForm">
                Mês:
              </td>
              <td class="dadosForm">
                <select id="selMes"      name="mescalc" >
                </select>
              </td>
              <td colspan="2">
                <input type="button" name="pesquisar" id="pesquisar" value="Pesquisar"              onClick="js_atualizaFrame()">
              </td>
            </tr>
          </table>
          <?
              
            } else {
          ?>      
          <table  class="tableForm" align="center">
            <tr>
              <td class="labelForm">      
                <b>Nenhum Registro Encontrado</b>
              </td>
            </tr>
          </table>          
          <?
            }
          ?>  
          </fieldset>
        </td>
      </tr>
      <tr>
        <td>
          <iframe id="iframeArqConvenio" name="iframeArqConvenio" src="" width="100%" height="400px;" style="border:hidden;"></iframe>
        </td>
      </tr>
    </table>
  </form>
</body>
<script>
  
  var sUrl = 'conveniosaaeRPC.php';
    
  function js_consultaMes(){
    $('iframeArqConvenio').src = '';

    js_divCarregando('Aguarde...','msgBox');
    
    var sQuery  = 'tipo=consultaMes';
        sQuery += '&matric='+$F('iMatric');
        sQuery += '&anousu='+$F('anocalc');
        sQuery += '&mesusu='+$F('selMes');
        sQuery += '&instit='+$F('iInstit');

    var oAjax   = new Ajax.Request( sUrl, {
                                           method: 'post', 
                                           parameters: sQuery, 
                                           onComplete: js_retornoMes
                                          }
                                  );          
  }
  
  function js_retornoMes(oAjax){

    js_removeObj('msgBox');
    
    var aRetorno = eval("("+oAjax.responseText+")");

    if ( aRetorno.lErro ) {
      alert(aRetorno.sMsg)
    } else {
      js_montaSelect('selMes',aRetorno.aLista);
    }
    
  } 
  

  function js_montaSelect( idObj, aLista ){
  
    var iLinhas = aLista.length;
    var sOpcoes = '';

    $(idObj).innerHTML = '';
    
    if ( iLinhas > 0 ) {
     
      for ( var iInd=0; iInd < iLinhas; iInd++ ) {
        with ( aLista[iInd] ) {
          $(idObj).options[iInd] = new Option();
          $(idObj).options[iInd].value = codigo; 
          $(idObj).options[iInd].text  = descr.urlDecode();
        }        
      }
      
      $(idObj).options[0].selected = true;
                
    } else {
      $(idObj).innerHTML = '';
    }
  
  }  
  
  function js_atualizaFrame(){
    $('iframeArqConvenio').src = '';

    js_divCarregando('Aguarde...','msgBox');
    
    var sQuery  = 'tipo=consultaDocumento';
        sQuery += '&matric='+$F('iMatric');
        sQuery += '&anousu='+$F('anocalc');
        sQuery += '&mesusu='+$F('selMes');
        sQuery += '&instit='+$F('iInstit');

    var oAjax   = new Ajax.Request( sUrl, {
                                           method: 'post', 
                                           parameters: sQuery, 
                                           onComplete: js_retornoDocumento
                                          }
                                  );    
  }

  function js_retornoDocumento(oAjax){
    js_removeObj('msgBox');
    
    var aRetorno = eval("("+oAjax.responseText+")");

    if ( aRetorno.lErro ) {
      alert(aRetorno.sMsg)
    } else {
      $('iframeArqConvenio').src = aRetorno.sSrc;
    }
  }
  
  js_consultaMes();
   
</script>