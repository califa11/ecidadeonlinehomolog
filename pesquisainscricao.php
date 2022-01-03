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

include("libs/db_conecta.php");
include("libs/db_stdlib.php");
include("libs/db_sql.php");
if(isset($inscr)){
  $result = pg_exec("select q02_numcgm from issbase where q02_inscr = $inscr");  
  if(pg_numrows($result) == 0){
    echo "<script>parent.js_erropesquisa($inscr)</script>";
    exit;
  }else{
    $q02_numcgm = pg_result($result,0,'q02_numcgm');
    $result = pg_exec ("select q03_descr from issbase inner join tabativ on q07_inscr = q02_inscr inner join ativid on q03_ativ = q07_ativ where q02_inscr = $inscr");
    $ativid = pg_result($result,0,'q03_descr');
    $result = pg_exec("select z01_cgccpf,z01_nome from cgm where z01_numcgm = $q02_numcgm");
    db_fieldsmemory($result,0);
    echo "<script>parent.js_preenchedados('$z01_cgccpf','$z01_nome','$ativid')</script>";
    exit;
  }
}
?>