    <?php
include_once ("classes/db_imobil_classe.php");
include_once ("classes/db_iptubase_classe.php");
require_once ("classes/db_configdbpref_classe.php");

$climobil     = new cl_imobil;
$cliptubase = new cl_iptubase;

parse_str(base64_decode($HTTP_SERVER_VARS["QUERY_STRING"]));

db_mensagem("opcoesmatricula_cab", "opcoesmatricula_rod");

db_postmemory($_POST);
db_postmemory($_GET);

$db_datausu = date("Y-m-d");

//Montando numero de referencia anterior
if(empty($matricula) && (empty($dist) || empty($zona) || empty($setor) || empty($quadra) || empty($lote) || empty($unidade)) ){
    //$sMensagem = "Aviso: Informe o número da matrícula ou os campos da referência do imóvel.";
    $sMensagem  = 3;
    db_redireciona("index.php?page=imoveis&erroscripts=".base64_encode("$sMensagem"));
}

if(empty($matricula)){

      $refAnterior  =   $dist.$zona.$setor.$quadra.$lote.$unidade;

      $sql = "select  j40_matric as matricula
                        from iptuant
                        left join iptubase  on iptubase.j01_matric=iptuant.j40_matric
                        where j40_refant = '{$refAnterior}'  ";

      $rsValidaMatricula    =   db_query($sql);
      $sResultadoValMat   =   pg_num_rows($rsValidaMatricula);

      if ($sResultadoValMat == 0 && (!isset($imob) || $imob == false)) {
            //$sMensagem = "Aviso: Os dados informados não conferem. Verifique os dados e tente novamente.";
            $sMensagem  = 4;
            db_redireciona("index.php?page=imoveis&erroscripts=".base64_encode("$sMensagem"));
      }else{
            $matricula = trim(pg_result($rsValidaMatricula, 0, 'matricula'));
      }

}


// valida se o número da matrícula fornecida é válida
if (isset($matricula)) {

  $sWhere = "";
  $sJoin  = "";
  $sCampo = "";

  $sql = "select  j01_matric
     from iptubase
     left  join issbase   on q02_numcgm = j01_numcgm
{$sJoin}
     where j01_matric = {$matricula} {$sWhere}";

  $rsValidaMatricula = db_query($sql);
  $sResultadoValMat  = pg_num_rows($rsValidaMatricula);

  if ($sResultadoValMat == 0 && (!isset($imob) || $imob == false)) {
    //$sMensagem = "Aviso: Os dados informados não conferem. Verifique o número da matrícula ou o CPF/CNPJ indicado!";
    $sMensagem  = 4;
    db_redireciona("index.php?page=imoveis&erroscripts=".base64_encode("$sMensagem"));
  }

}


$sql    = "select * from iptubase,cgm where j01_matric = $matricula and j01_numcgm = z01_numcgm";
$result = db_query($sql) or die("Erro: ".pg_ErrorMessage($conn));
$cgccpf = trim(pg_result($result, 0, 'z01_cgccpf'));

$linhasexe = pg_num_rows($result);

if ($linhasexe == 0) {
	db_logs("$matricula", "", 0, "Dados Inconsistentes. Numero : $matricula");

	$script = false;
} else {
	db_logs("$matricula", "", 0, "Matricula Pesquisada. Numero: $matricula");
	if (pg_num_rows($result) > 0) {
		db_fieldsmemory($result, 0);
		if (@$codigo_cgm == "") {
			@$codigo_cgm = $z01_numcgm;
		}
	}
}

?>
<div class="row">
  <div class="col-lg-12">
    <h1 class="page-header">Imóveis</h1>
    <ol class="breadcrumb">
      <li><a href="index.php">Home</a></li>
      <li><a href="#">Tributos</a></li>
      <li><a href="index.php?page=imoveis">Imóveis</a></li>
      <li class="active">Débitos Imóvel</li>
    </ol>
  </div>
</div>
<!-- /.col-lg-12 -->
<div class="row">
  <div class="col-lg-12">
    <div class="panel panel-default">
      <div class="panel-heading">Buscar Imóvel</div>

      <?php //echo utf8_decode($DB_mens1);?>
      <?php

$sSqlInner = "inner join arrematric on arrematric.k00_numpre = arresusp.k00_numpre";
$sSqlWhere = "k00_matric = {$matricula}";
$result    = debitos_tipos_matricula($matricula); //Adicionado na função filtro de tipo de débito 46 (IPTU 2015)
$chave     = "matricula=$matricula";
$valor     = $matricula;

$sSqlVerificaSuspensao = " select k00_sequencial      ";
$sSqlVerificaSuspensao .= "   from arresusp            ";
$sSqlVerificaSuspensao .= "        inner join suspensao on ar18_sequencial = arresusp.k00_suspensao ";
$sSqlVerificaSuspensao .= "      {$sSqlInner}        ";
$sSqlVerificaSuspensao .= "  where {$sSqlWhere}        ";
$sSqlVerificaSuspensao .= "    and ar18_situacao = 1   ";
$rsVerificaDebitosSuspensos = db_query($sSqlVerificaSuspensao);
$iNroLinhasDebitosSuspensos = pg_num_rows($rsVerificaDebitosSuspensos);

//if ($result == true){
$linhas = pg_num_rows($result);

?>


<ul class="list-group">
 <li class="list-group-item "><b>CNPJ/CPF:</b>
                              <span class="bold3">
<?php
echo (trim($cgccpf) == ''?@$mostraCGCCPF:$cgccpf);
?>
</span> <br/>
<?php if (@$inscricao != "") {?> <b>Inscrição:</b>&nbsp; <span class="bold3"><?=@$inscricao?></span>
	<?php } else if (@$matricula != "") {?> <b>Matrícula:</b>&nbsp;
						                                 <span class="bold3"><?=@$matricula?></span><br>
      <?php } else if (@$codigo_cgm != "") {?> <b>CGM:</b>&nbsp; <span class="bold3"><?=@$codigo_cgm?></span><br>
	<?php }?>
  <span class="label label-default">Clique em uma das <b>opções</b> abaixo.</span>
</li>

<?php

$sSqlInner = "inner join arrematric on arrematric.k00_numpre = arresusp.k00_numpre";
$sSqlWhere = "k00_matric = {$matricula}";
$result    = debitos_tipos_matricula($matricula);
$chave     = "matricula=$matricula";
$valor     = $matricula;

$sSqlVerificaSuspensao = " select k00_sequencial      ";
$sSqlVerificaSuspensao .= "   from arresusp            ";
$sSqlVerificaSuspensao .= "        inner join suspensao on ar18_sequencial = arresusp.k00_suspensao ";
$sSqlVerificaSuspensao .= "      {$sSqlInner}        ";
$sSqlVerificaSuspensao .= "  where {$sSqlWhere}        ";
$sSqlVerificaSuspensao .= "    and ar18_situacao = 1   ";
$rsVerificaDebitosSuspensos = db_query($sSqlVerificaSuspensao);
$iNroLinhasDebitosSuspensos = pg_num_rows($rsVerificaDebitosSuspensos);

//if ($result == true){
$linhas = pg_num_rows($result);

                    $aK00_tipo = array();
                    for($x = 0; $x < $linhas; $x ++) {
                      db_fieldsmemory($result,$x);
                      $k00_recibodbpref = 1;
                      $sqlmostra = "select k00_tipo, k00_descr,k00_recibodbpref from arretipo where k00_tipo = $k00_tipo";
                      $resultmostra = db_query ( $sqlmostra );
                      $linhasmostra = pg_num_rows ( $resultmostra );
                      if ($linhasmostra > 0) {
                        db_fieldsmemory ( $resultmostra, 0 );
                      // echo "<br> $k00_descr  = $k00_recibodbpref..tipo. $k00_tipo";
                      }

                      if (@ $k00_tipo == 3){ $k00_agnum = "nivel3"; }



                      if ($k00_recibodbpref != "3") {
                        $lExibe = true;

                        $iTipo = pg_result(db_query("select coalesce(w10_tipo,0) from db_confplan"),0,0);
                        if ($k00_tipo == $iTipo and $linhasmenuretido == 0) {
                        // não mostra este item
                        } else if($lExibe){
                          $aK00_tipo[] = $k00_tipo;
                          ?>
                          <li class="list-group-item">
<?php
$link = "index.php?page=listadebitos".
"&key=".base64_encode(
"&numcgm=".@$k00_numcgm.
"&matric=".@$matricula.
"&tipo=".@$k00_tipo.
"&emrec=".@$k00_emrec.
"&agnum=".@$k00_agnum.
"&agpar=".@$k00_agpar.
"&inscr=".@$q02_inscr.
"&db_datausu=".date('Y-m-d', db_getsession('DB_datausu')).
//"&id_usuario=".@$id_usuario.
"&opcao=".$opcao.
"&cgccpf=".$cgccpf);
?>
                            <a class="links" href="<?php echo $link;?>">
                              <img src="imagens/busca.png" border="0"> <?=$k00_descr?>
                            </a>
                          </li>

                          <?
                        }
                      }
                    }
                    ?>
                    <li class="list-group-item">
                      <a class='links' href="index.php?page=imovelinfo&key=<?php echo base64_encode('matricula='.$matricula.'&cgccpf='.$cgccpf); ?>">
                        <img src="imagens/casa.png" border="0"> Informações do imóvel</a>
                      </li>

                    </ul>
                  </table>


              </div>
            </div>

          </div>
