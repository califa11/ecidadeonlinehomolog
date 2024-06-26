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

require_once("fpdf151/impcarne.php");
require_once("fpdf151/scpdf.php");

require_once("libs/db_utils.php");
require_once("libs/db_libpessoal.php");

require_once("classes/db_cgm_classe.php");
require_once("classes/db_iptubase_classe.php");
require_once("classes/db_issbase_classe.php");
require_once("classes/db_rhemitecontracheque_classe.php");
require_once("classes/db_cfpess_classe.php");

$oPost = db_utils::postMemory($_POST);

$oDaoCfpess = new cl_cfpess;

/**
 * Modelo de impress�o de relat�rio contra cheque
 * Retorna false caso der erro na consulta
 */   
$iTipoRelatorio = $oDaoCfpess->buscaCodigoRelatorio('contracheque', db_anofolha(), db_mesfolha());
if(!$iTipoRelatorio) {
  db_redireciona('db_erros.php?fechar=true&db_erro=Modelo de impress�o invalido, verifique parametros.');
}

validaUsuarioLogado();

db_postmemory($_POST);
parse_str($HTTP_SERVER_VARS['QUERY_STRING']);

if($tipocalc  == 'r14' && $anocalc == 2018 &&  $mescalc == 1 ){
   echo 'Contra Cheque Indispon�vel';
    db_redireciona('db_erros.php?fechar=true&db_erro=Contra Cheque Indispon�vel.');
   die();
}else{ 

$clrhemitecontracheque = new cl_rhemitecontracheque();

$filtro     = 'M';
//$msg        = 'Conforme parecer da Procuradoria e recomenda��o da Corregedoria, a Lei n� 1031/64 encontra-se revogada desde dezembro/1990, assim a partir de abril/2014 a gratifica��o de especializa��o ser� suprimida.';
$msg        = '';
$local      = '';
$tipo_local = 's';
$num_vias   = 1;
$ordem      = 'M';
$sIp        = $HTTP_SERVER_VARS['REMOTE_ADDR'];

$ano  	   = $anocalc;
$mes 	     = $mescalc;
$matricula = $iMatric;
$dados     = $iMatric;

 



if ( $tipocalc == 'r14') {        	  
  $sOpcao = 'salario';   
} else if ( $tipocalc == 'r22' ) {
  $sOpcao = 'adiantamento';
} else if ( $tipocalc == 'r35' ) {
  $sOpcao = '13salario';
} else if ( $tipocalc == 'r20' ) {
  $sOpcao = 'rescisao';
} else if ( $tipocalc == 'r48' ) {
  $sOpcao = 'complementar';
}

$opcao = $sOpcao;



/*
$matricula = 2870;
$ano = 2005;
$mes = 7;
$opcao = 'salario';

*/



$sql = "select  db_config.*,
                rh02_instit as institservidor 
          from  db_config 
                inner join rhpessoalmov on rh02_instit = codigo
                                       and rh02_anousu = $ano 
                                       and rh02_mesusu = $mes
          where rh02_regist = $dados ";
                                       
                                       
$result = pg_exec($sql);
db_fieldsmemory($result,0);

$xtipo = "'x'";
$qualarquivo = '';
if ( $opcao == 'salario' ){
  $sigla   = 'r14_';
  $arquivo = 'gerfsal';
  $qualarquivo = 'SAL�RIO';
}elseif ( $opcao == 'ferias' ){
  $sigla   = 'r31_';
  $arquivo = 'gerffer';
  $xtipo   = ' r31_tpp ';
  $qualarquivo = 'F�RIAS';
}elseif ( $opcao == 'rescisao' ){
  $sigla   = 'r20_';
  $arquivo = 'gerfres';
  $xtipo   = ' r20_tpp ';
  $qualarquivo = 'RESCIS�O';
}elseif ($opcao == 'adiantamento'){
  $sigla   = 'r22_';
  $arquivo = 'gerfadi';
  $qualarquivo = 'ADIANTAMENTO';
}elseif ($opcao == '13salario'){
  $sigla   = 'r35_';
  $arquivo = 'gerfs13';
  $qualarquivo = '13o. SAL�RIO';
}elseif ($opcao == 'complementar'){
  $sigla   = 'r48_';
  $arquivo = 'gerfcom';
  $qualarquivo = 'COMPLEMENTAR';
}elseif ($opcao == 'fixo'){
  $sigla   = 'r53_';
  $arquivo = 'gerffx';
  $qualarquivo = 'FIXO';
}elseif ($opcao == 'previden'){
  $sigla   = 'r60_';
  $arquivo = 'previden';
  $qualarquivo = 'AJUSTE DA PREVID�NCIA';
}elseif ($opcao == 'irf'){
  $sigla   = 'r61_';
  $arquivo = 'ajusteir';
  $qualarquivo = 'AJUSTE DO IRRF';
}
$txt_where="1=1";
if (isset($filtro)&&$filtro!='N'){
  if ($filtro=='M'){
    $campo=$sigla."regist";
  }else if ($filtro=='L'){
    $campo=$sigla."lotac";
  }else if ($filtro=='T'){
    $campo= "rh56_localtrab";
  }
  if (isset($dados)&&$dados!=""){
    $txt_where = " $campo in ($dados) ";
  }elseif (isset($codini) && $codini != "" && $codfim != ""){
    $txt_where = " $campo between $codini and $codfim ";
  }
}
if(isset($local) && trim($local) != ""){
  if($txt_where!=""){
     $txt_where.= " and ";
  }
  if($tipo_local == 's'){
    $txt_where.= " rh56_localtrab = ".$local;
  }else{
    $txt_where.= " rh56_localtrab <> ".$local;
  }
}

$wheresemest = "";
if(isset($semest) && trim($semest) != 0){
  $wheresemest = " and r48_semest = ".$semest;
}


$sql1= "select distinct
       		z01_nome,
       		rhpessoal.*,
       		rhpessoalmov.*,
          rhpesbanco.*,
       		rh37_descr,
       		r70_descr,
      		substr(r70_estrut,1,7) as estrut,
	       	".$sigla."regist as regist,
	        substr(db_fxxx(".$sigla."regist,rh02_anousu,rh02_mesusu,rh02_instit),111,11) as f010, 
          substr(db_fxxx(".$sigla."regist,rh02_anousu,rh02_mesusu,rh02_instit),144, 11) as f013,
        	substr(db_fxxx(".$sigla."regist,rh02_anousu,rh02_mesusu,rh02_instit),210,8) as padrao
          from (select distinct ".$sigla."regist,
                                ".$sigla."anousu,
                                ".$sigla."mesusu,
                                ".$sigla."lotac
	              from ".$arquivo." ".bb_condicaosubpesproc( $sigla,$ano."/".$mes,$institservidor).$wheresemest." 
               ) as ".$arquivo."
       	  inner join rhpessoal     on rh01_regist = ".$sigla."regist 
		      inner join rhpessoalmov  on rh02_regist = rh01_regist
                                  and rh02_anousu = $ano 
		  	                          and rh02_mesusu = $mes 
     		  inner join cgm           on rh01_numcgm  = z01_numcgm
     		  left join rhfuncao       on rh37_funcao = rh02_funcao
		                              and rh37_instit = rh02_instit
     		  left join rhlota         on r70_codigo  = rh02_lota
				                          and r70_instit  = rh02_instit	
          left join rhpescargo     on rh20_seqpes = rh02_seqpes
		                              and rh20_instit = rh02_instit
          left join rhpesbanco     on rh44_seqpes = rh02_seqpes
          left join rhcargo     	 on rh04_codigo = rh20_cargo
		                              and rh04_instit = rh02_instit
          left join rhpeslocaltrab on rh56_seqpes = rh02_seqpes
                                  and rh56_princ  = true
	        where $txt_where
	        ";

$sql = "select * from ($sql1) as xxx, generate_series(1,$num_vias) order by ";

if($ordem == "L"){
  $sql .= " estrut,z01_nome, rh01_regist ";
}else if($ordem == "N"){
  $sql .= " z01_nome , rh01_regist";
}else if($ordem == "T"){
  $sql .= " rh56_localtrab , z01_nome , rh01_regist ";
}else{
  $sql .= " rh01_regist ";
}

//-------------- and ".$sigla."regist = $matricula------------------

// ------------- busca url do site do cliente ----------------------
$sqlDbConfig = " select url from db_config where prefeitura = true ";

$rsDbConfig  = pg_query($sqlDbConfig);
$iDbConfig   = pg_numrows($rsDbConfig);

if ($iDbConfig > 0) {
  $oDbConfig = db_utils::fieldsMemory($rsDbConfig, 0);
  $sDbConfig = $oDbConfig->url;
} else {
  $sDbConfig = "";
}
//------------------------------------------------------------------


$res = pg_query($sql);
//db_criatabela($res);
$num = pg_numrows($res);
if ($num == 0){
   db_redireciona('db_erros.php?fechar=true&db_erro=N�o existe C�lculo no per�odo de '.$mes.' / '.$ano);
}
  global $pdf;
  $pdf = new scpdf();
  $pdf->setautopagebreak(false,0.05);
  $pdf->Open();
  $pdf1 = new db_impcarne($pdf, $iTipoRelatorio);
//  $pdf1->modelo		  = 16;
  $pdf1->logo             = $logo;
  $pdf1->prefeitura       = $nomeinst;
  $pdf1->enderpref        = $ender.(isset($numero)?(', '.$numero):"");
  $pdf1->cgcpref          = $cgc;
  $pdf1->municpref        = $munic;
  $pdf1->telefpref        = $telef;
  $pdf1->emailpref        = $email;
  $pdf1->ano        	    = $ano;
  $pdf1->mes          	  = $mes;
  $pdf1->mensagem         = $msg;
  $pdf1->qualarquivo     = $qualarquivo;
 
  $lin = 1;
  
for($i=0;$i<$num;$i++){
	
  db_fieldsmemory($res,$i);

  $rsSeqContraCheque = db_query("select nextval('rhemitecontracheque_rh85_sequencial_seq') as sequencial");
  $oSeqContraCheque  = db_utils::fieldsMemory($rsSeqContraCheque,0);
  $iSequencial       = str_pad($oSeqContraCheque->sequencial,6,'0',STR_PAD_LEFT);
  
  $iMes       = str_pad($mes,2,'0',STR_PAD_LEFT);
  $iMatricula = str_pad($regist,6,'0',STR_PAD_LEFT);
  $iMod1      = db_CalculaDV($iMatricula);
  $iMod2      = db_CalculaDV($iMatricula.$iMod1.$iMes.$ano.$iSequencial); 
     
  $iCodAutent = $iMatricula.$iMod1.$iMes.$iMod2.$ano.$iSequencial;
  
  $clrhemitecontracheque->rh85_sequencial  = $iSequencial;
  $clrhemitecontracheque->rh85_regist      = $regist;
  $clrhemitecontracheque->rh85_anousu      = $ano;
  $clrhemitecontracheque->rh85_mesusu      = $mes;
  $clrhemitecontracheque->rh85_sigla       = substr($sigla,0,3);
  $clrhemitecontracheque->rh85_codautent   = $iCodAutent;
  $clrhemitecontracheque->rh85_dataemissao = date('Y-m-d',db_getsession('DB_datausu'));
  $clrhemitecontracheque->rh85_horaemissao = db_hora();
  $clrhemitecontracheque->rh85_ip          = $sIp;
  $clrhemitecontracheque->rh85_externo     = 'true';

  $clrhemitecontracheque->incluir($iSequencial);
  
  if ( $clrhemitecontracheque->erro_status == 0 ) {
  	db_redireciona('db_erros.php?fechar=true&db_erro='.$clrhemitecontracheque->erro_msg);
  }
  
  if($lin == 1){
    $lin = 0;
    $pdf1->seq = 0;
  }else{
    $lin = 1;
    $pdf1->seq = 1;
  }
  
  $sql = "
  select ".$sigla."rubric as rubrica,
       round(".$sigla."valor,2) as valor,
       round(".$sigla."quant,2) as quant, 
       rh27_descr, 
       ".$xtipo." as tipo , 
       case when ".$sigla."pd = 3 then 'B' 
            else case when ".$sigla."pd = 1 then 'P' 
	         else 'D' 
	    end 
       end as provdesc
 
  from ".$arquivo." 
     inner join rhrubricas on rh27_rubric = ".$sigla."rubric 
                          and rh27_instit = $institservidor
  where ".$sigla."regist = $regist
    and ".$sigla."anousu = $ano 
    and ".$sigla."mesusu = $mes
    and ".$sigla."instit = $institservidor
    $wheresemest
  order by ".$sigla."rubric  ";

//  echo $sql;  
  $res_env = pg_exec($sql);
//  db_criatabela($res_env);exit;
  
  $pdf1->f013             = $f013;
  $pdf1->registro	        = $rh01_regist;
  $pdf1->nome		          = $z01_nome;
  $pdf1->descr_funcao	    = $rh37_descr;
  $pdf1->descr_lota       = $estrut.'-'.$r70_descr;
  $pdf1->f010          	  = $f010;
  $pdf1->padrao        	  = $padrao;
  $pdf1->banco         	  = $rh44_codban;
  $pdf1->agencia       	  = trim($rh44_agencia).'-'.trim($rh44_dvagencia);
  $pdf1->conta         	  = trim($rh44_conta).'-'.trim($rh44_dvconta);
  $pdf1->lotacao	        = $estrut;
  $pdf1->recordenvelope   = $res_env;
  $pdf1->linhasenvelope	  = pg_numrows($res_env);
  $pdf1->valor		        = 'valor';
  $pdf1->quantidade	      = 'quant';
  $pdf1->tipo		          = 'provdesc';
  $pdf1->rubrica	        = 'rubrica';
  $pdf1->descr_rub	      = 'rh27_descr';
  $pdf1->numero	  	      = $i+1;
  $pdf1->total	  	      = $num;
  $pdf1->codautent        = $iCodAutent;
  $pdf1->url              = $sDbConfig;
  $pdf1->instit           = $institservidor;
  
 
  
  $pdf1->imprime();
  
}



//$arq = tempnam("tmp","pdf").".pdf";
//$pdf1->objpdf->output($arq);

$pdf->ln(25);
$pdf->SetFontSize(10);
/*
$pdf->Cell(0,4,'Est� dispon�vel no site da Prefeitura de Sete Lagoas -  www.setelagoas.mg.gov.br , acesso intranet, impress�o',0,1,'C');
$pdf->Cell(0,4,'de contracheque, a atualiza��o cadastral para preenchimento obrigat�rio. Para acesso dever� ser utilizado login e senha,  ',0,1,'C');
$pdf->Cell(0,4,'caso ainda n�o possua, dever� fazer a solicita��o utilizando matr�cula e CPF.',0,1,'C');
$pdf->Cell(0,4,'Lembramos que o prazo para cadastro ser� ate o dia 20 de maio de 2014.',0,1,'C');
*/

if( ($regist == 25042 && $ano == 2017 && $mes == 1) || ($regist == 26730 && $ano == 2017 && $mes == 5) ){
	$pdf1->objpdf->SetTextColor(255,0,0);
	$pdf1->objpdf->SetFontSize(100);
	$pdf1->objpdf->RotatedText(35,190,'CANCELADO',45);
	
}

$pdf1->objpdf->output();

}
?>