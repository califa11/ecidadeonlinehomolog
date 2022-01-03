<?
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

function db_getNomeSecretaria(){
	$nomeSecretaria = "SECRETARIA DA FAZENDA";
  $sqlparag   = " select db02_texto ";
  $sqlparag  .= "   from db_documento ";
  $sqlparag  .= "        inner join db_docparag  on db03_docum   = db04_docum ";
  $sqlparag  .= "        inner join db_tipodoc   on db08_codigo  = db03_tipodoc ";
  $sqlparag  .= "        inner join db_paragrafo on db04_idparag = db02_idparag ";
  $sqlparag  .= " where db03_tipodoc = 1017 ";
  $sqlparag  .= "   and db03_instit = ".db_getsession("DB_instit")." ";
  $sqlparag  .= " order by db04_ordem ";
  $resparag  = pg_query($sqlparag);
  if (pg_numrows($resparag) > 0) {
    $nomeSecretaria = pg_result($resparag,0,'db02_texto');
  }
	return $nomeSecretaria;  
}
  

function db_getcadbancobranca($arretipo,$ip,$datahj,$instit,$tipomod){

   $sSql  = "  select k48_sequencial,                                                                                                                   ";
   $sSql .= "         k48_cadconvenio,                                                                                                                  ";  
   $sSql .= "         ar12_cadconveniomodalidade                                                                                                        ";    
   $sSql .= "    from modcarnepadrao                                                                                                                    ";      
   $sSql .= "         inner join cadconvenio                on cadconvenio.ar11_sequencial                  = modcarnepadrao.k48_cadconvenio            ";
   $sSql .= "         inner join cadtipoconvenio            on cadtipoconvenio.ar12_sequencial              = cadconvenio.ar11_cadtipoconvenio          ";
   $sSql .= "         left  join conveniocobranca           on conveniocobranca.ar13_cadconvenio            = cadconvenio.ar11_sequencial               ";
   $sSql .= "         left  join modcarnepadraotipo         on modcarnepadraotipo.k49_modcarnepadrao        = modcarnepadrao.k48_sequencial             ";
   $sSql .= "         left  join modcarneexcessao           on modcarneexcessao.k36_modcarnepadrao          = modcarnepadrao.k48_sequencial             ";
   $sSql .= "         left  join modcarnepadraocadmodcarne  on modcarnepadraocadmodcarne.m01_modcarnepadrao = modcarnepadrao.k48_sequencial             ";
   $sSql .= "         left  join cadmodcarne                on cadmodcarne.k47_sequencial                   = modcarnepadraocadmodcarne.m01_cadmodcarne ";  
   $sSql .= "         left  join modcarnepadraolayouttxt    on modcarnepadraolayouttxt.m02_modcarnepadrao   = modcarnepadrao.k48_sequencial             ";
   $sSql .= "         left  join db_layouttxt               on db_layouttxt.db50_codigo                     = modcarnepadraolayouttxt.m02_db_layouttxt  ";
   $sSql .= "   where k48_dataini  <= '{$datahj}'                                                                                                       ";
   $sSql .= "     and k48_datafim  >= '{$datahj}'                                                                                                       ";
   $sSql .= "     and k48_instit     = {$instit}                                                                                                        ";
   $sSql .= "     and k48_cadtipomod = {$tipomod}                                                                                                       ";
   $sSql .= "     and ar12_cadconveniomodalidade = 1                                                                                                    ";
  
  if (!empty($iArretipo)) {
    $sSql .= "   and case                                                                 ";    
    $sSql .= "        when k49_tipo is not null then k49_tipo = {$arretipo} else true     ";
    $sSql .= "       end                                                                  ";    
  }
  
  if (!empty($sIp)) {
    $sSql .= "   and case                                                                 ";
    $sSql .= "          when k36_ip is not null then k36_ip = '{$ip}' else true           ";
    $sSql .= "       end                                                                  ";
  }

  $rsConsultaRegra = pg_query($sSql);
  $iNroLinhas      = pg_num_rows($rsConsultaRegra);
  
  if ( $iNroLinhas > 0 ) {
    db_fieldsmemory($rsConsultaRegra,0);
    return true;
  } else {
    return false;      
  }   

  
}
?>