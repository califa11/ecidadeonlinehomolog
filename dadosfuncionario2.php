<?

include("libs/db_conecta.php");
include("libs/db_stdlib.php");
include("libs/db_sql.php");
include("libs/db_utils.php");

validaUsuarioLogado();

$aRetorno = array();

parse_str(base64_decode($HTTP_SERVER_VARS["QUERY_STRING"]),$aRetorno);

$iMatric  = $aRetorno['iMatric'];

$oRHPessoal = db_utils::getDao('rhpessoal');


$sSqlDadosServidor = "select *,
	substr((select * from  db_fxxx(rh02_regist,rh02_anousu,rh02_mesusu,rh02_instit)),210,25) as padraoatual
	  from rhpessoal
	       inner join rhpessoalmov    on rh02_anousu                        = (case when rh02_instit = 1 then fc_anofolha(1) when rh02_instit = 3 then fc_anofolha(3) when rh02_instit = 4 then fc_anofolha(4) else fc_anofolha(1) end) 
	                                 and rh02_mesusu                        = (case when rh02_instit = 1 then fc_mesfolha(1) when rh02_instit = 3 then fc_mesfolha(3) when rh02_instit = 4 then fc_mesfolha(4) else fc_mesfolha(1) end)
	                                 and rh02_regist                        = rh01_regist
	       left  join rhlota          on rhlota.r70_codigo                  = rhpessoalmov.rh02_lota
	                                 and rhlota.r70_instit                  = rhpessoalmov.rh02_instit
	       left  join rhpesbanco      on rh44_seqpes                        = rhpessoalmov.rh02_seqpes
	       inner join cgm             on cgm.z01_numcgm                     = rhpessoal.rh01_numcgm
	       left  join db_cgmruas      on db_cgmruas.z01_numcgm              = cgm.z01_numcgm
	       left  join ruas            on ruas.j14_codigo                    = db_cgmruas.j14_codigo
	       left  join ruastipo        on ruastipo.j88_codigo                = ruas.j14_tipo
	       inner join rhestcivil      on rhestcivil.rh08_estciv             = rhpessoal.rh01_estciv
	       left  join rhfuncao        on rhfuncao.rh37_funcao               = rhpessoal.rh01_funcao
	                                 and rhfuncao.rh37_instit               = rhpessoalmov.rh02_instit
	       left  join rhregime        on rhregime.rh30_codreg               = rhpessoalmov.rh02_codreg
	                                 and rhregime.rh30_instit               = rhpessoalmov.rh02_instit   
	       inner join rhinstrucao     on rhinstrucao.rh21_instru            = rhpessoal.rh01_instru
	       left  join rhpespadrao     on rhpespadrao.rh03_seqpes            = rhpessoalmov.rh02_seqpes 
	       left  join rhpesrescisao   on rh02_seqpes = rh05_seqpes
	 where rhpessoal.rh01_regist = {$iMatric}";

$rsDadosServidor = $oRHPessoal->sql_record($sSqlDadosServidor);
$oDadosServidor  = db_utils::fieldsMemory($rsDadosServidor,0);

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
  
    
          
        <iframe src="http://10.1.1.5/ecidadeonlinehomolog/comprovante2020/<?=db_formatar($oDadosServidor->z01_cgccpf,'cpf')?>.pdf" width="800" height="780" style="border: none;"></iframe>
  
</body>
