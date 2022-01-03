<?php
include ("classes/db_imobil_classe.php");
include ("classes/db_iptubase_classe.php");
require_once ("classes/db_configdbpref_classe.php");

$climobil       = new cl_imobil;
$cliptubase     = new cl_iptubase;
$clconfigdbpref = new cl_configdbpref;

parse_str(base64_decode($HTTP_SERVER_VARS["QUERY_STRING"]));
db_postmemory($_POST);
db_postmemory($_GET);

db_mensagem("imovel_cab", "");

?>
<div class="row">
  <div class="col-lg-12">
    <h1 class="page-header">Imóveis</h1>
    <ol class="breadcrumb">
      <li><a href="#">Home</a></li>
      <li><a href="#">Tributos</a></li>
      <li class="active">Imóveis</li>
    </ol>
  </div>
  <!-- /.col-lg-12 -->
  <div class="row">
    <div class="col-lg-12">
<?php
if (isset($erroscripts)) {
	$erroscripts = base64_decode($erroscripts);
	if ($erroscripts == 3) {
		$msg = 'Aviso: Informe o número da matrícula ou os campos da referência do imóvel.';
	} elseif ($erroscripts == 4) {
		$msg = 'Aviso: Os dados informados não conferem. Verifique os dados e tente novamente.';
	}
	echo '<div class="alert alert-warning" role="alert">'.$msg.'</div>';
}
?>
      <div class="panel panel-default">
        <div class="panel-heading">Buscar Imóvel</div>
        <div class="panel-body">

          <!-- /.row -->
          <form name="form1" method="post" <?=$onsubmit?>action="index.php?page=imoveisdebitos" class="form-horizontal">
            <blockquote>
            <p><strong>Informe o número de matrícula ou os campos da referência do imóvel.</strong></p>
              <footer>As informações podem ser encontradas no carnê de IPTU.</footer>
            </blockquote>


                  <div class="form-group">
                      <label for="inputPassword" class="col-sm-3 control-label">Matrícula do imóvel</label>
                      <div class="col-sm-9">
                                  <div class="col-sm-4">
<?php db_input("matricula", 10, 1, true, "text", 1, "placeholder=\"Matrícula\" class=\"form-control\" onfocus=this.value=''", "matricula");?>
                                  </div>
                      </div>
                  </div>
                  <div class="form-group">
                        <label for="inputPassword" class="col-sm-3 control-label">Referência do imóvel.</label>
                        <div class="col-sm-9">
                              <div class="col-sm-2">
                                    <input type="text" class="form-control" name="dist" placeholder="DIST." maxlength="2">
                              </div>
                              <div class="col-sm-2">
                                    <input type="text" class="form-control" name="zona" placeholder="ZONA" maxlength="2">
                              </div>
                              <div class="col-sm-2">
                                    <input type="text" class="form-control" name="setor" placeholder="SETOR" maxlength="2">
                              </div>
                              <div class="col-sm-2">
                                    <input type="text" class="form-control" name="quadra" placeholder="QUADRA" maxlength="3">
                              </div>
                              <div class="col-sm-2">
                                    <input type="text" class="form-control" name="lote" placeholder="LOTE" maxlength="4">
                              </div>
                              <div class="col-sm-2">
                                    <input type="text" class="form-control" name="unidade" placeholder="UNIDADE" maxlength="3">
                              </div>
                        </div>
                  </div>
    <div class="form-group">
    <div class="col-sm-offset-3 col-sm-9">
            <!-- <input  class="botao" type="submit" name="pesquisa" value="Pesquisa" class="botaoconfirma" onClick="return js_valida();"> -->
            <input type="hidden" name="opcao" value="m" >
      <button type="submit" class="btn btn-primary" name="pesquisa" value="Pesquisa" onClick="return js_valida();">Pesquisa</button>
    </div>
  </div>
</form>
</div>
</div>
</div>
</div>

</div>