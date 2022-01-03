<?php
//echo '<pre>';
//var_dump($HTTP_POST_VARS);
//echo '</pre>';

if ((isset($HTTP_POST_VARS["numpre_unica"]) && $HTTP_POST_VARS["numpre_unica"] != "") || (isset($HTTP_POST_VARS["geracarne"]) && !isset($HTTP_POST_VARS["calculavalor"]))) {

  include_once("cai3_gerfinanc033.php"); // entra aki quando clica no emitir recibo
  exit;
}
//parse_str($HTTP_SERVER_VARS['QUERY_STRING']);
parse_str(base64_decode($_GET['key']));

//se for submit, ele cria o recibo
if (isset($HTTP_POST_VARS["ver_matric"]) && !isset($HTTP_POST_VARS["calculavalor"])) {

  global $HTTP_SESSION_VARS;
  if (isset($db_datausu)) {
    if (!checkdate(substr($db_datausu, 5, 2), substr($db_datausu, 8, 2), substr($db_datausu, 0, 4))) {
      echo "Data para cálculo inválida. <br><br>";
      echo "Data deverá ser superior a: ".date('Y-m-d', @$HTTP_SESSION_VARS["DB_datausu"]);
    }
    if (mktime(0, 0, 0, substr($db_datausu, 5, 2), substr($db_datausu, 8, 2), substr($db_datausu, 0, 4)) < mktime(0, 0, 0, date('m', @$HTTP_SESSION_VARS["DB_datausu"]), date('d', @$HTTP_SESSION_VARS["DB_datausu"]), date('Y', @$HTTP_SESSION_VARS["DB_datausu"]))) {
      echo "Data não permitida para cálculo. <br><br>";
      echo "Data deverá ser superior a: ".date('Y-m-d', @$HTTP_SESSION_VARS["DB_datausu"]);
    }
    $DB_DATACALC = mktime(0, 0, 0, substr($db_datausu, 5, 2), substr($db_datausu, 8, 2), substr($db_datausu, 0, 4));
  } else {
    $DB_DATACALC = @$HTTP_SESSION_VARS["DB_datausu"];
  }
  if (isset($var_vcto)) {
    $DB_DATACALC = mktime(0, 0, 0, substr($var_vcto, 4, 2), substr($var_vcto, 6, 2), substr($var_vcto, 0, 4));
  }

  include("fpdf151/scpdf.php");
  include("cai3_gerfinanc003.php");
  exit;
}


?>
<div class="row">
  <div class="col-lg-12">
    <h1 class="page-header">Imóveis</h1>
    <ol class="breadcrumb">
      <li><a href="index.php">Home</a></li>
      <li><a href="#">Tributos</a></li>
      <li><a href="index.php?page=imoveis">Imóveis</a></li>
      <li><a href="index.php?page=imoveis">Débitos Imóvel</a></li>
      <li class="active">Lista Débitos</li>
    </ol>
  </div>
</div>
<!-- /.col-lg-12 -->
<div class="row">
  <div class="col-lg-12" >


    <?php
    if(isset($suspensao)){
        //cai3_gerfinanc008
    }else if( isset($tipo) && $tipo == 3){
        //cai3_gerfinanc002
    }else if( isset($tipo) &&  $tipo == 19){
        //cai3_gerfinanc040
    }else if( isset($tipo) && $tipo == 34){
      $inicial = true;
        //cai3_gerfinanc050
    }else {

      //cai3_gerfinanc002
      include_once("classes/db_issvarlancval_classe.php");
      include_once("classes/db_arrehistip_classe.php");
      include_once("classes/db_arrehist_classe.php");
      include_once("classes/db_parissqn_classe.php");
      include_once("classes/db_arrecant_classe.php");

      ?>
      <div class="panel panel-default" onLoad="js_somatudo()">
      <div class="panel-heading">Débitos Encontrados </div>
      <div class="panel-body">
      <script type="text/javascript" src="scripts/prototype.js"></script>
      <div id='int_perc1' align="left"
      style="position:absolute;top:60%;left:35%; float:left; width:200; background-color:#ECEDF2; padding:5px; margin:0px; border:1px #C2C7CB solid; margin-left:10px; font-size:80%; visibility:hidden">
      <div style="border:1px #ffffff solid; margin:8px 3px 3px 3px;">
        <div id='int_perc2' style="width:0%; background-color:#888888;">&nbsp;</div>
      </div>
    </div>
    <script>
      document.getElementById('int_perc1').style.visibility='visible';
      document.getElementById('int_perc2').style.width='15%';
    </script>
    <?php

    $k00_recibodbpref = 1;
    $sqlmostra = "select k00_tipo, k00_descr,k00_recibodbpref from arretipo where k00_tipo = $tipo";
    $resultmostra = db_query($sqlmostra);
    $linhasmostra = pg_num_rows($resultmostra);
    if($linhasmostra>0){
      db_fieldsmemory($resultmostra,0);
    // echo "<br> $k00_descr  = $k00_recibodbpref..tipo. $k00_tipo";
    }

    $lExibe = true;
    $clissvarlancval = new cl_issvarlancval;
    $clarrehistip = new cl_arrehistip;
    $clarrehist = new cl_arrehist;
    $clparissqn = new cl_parissqn;
    $clarrecant = new cl_arrecant;

    $vt = $HTTP_POST_VARS;
    $tam = sizeof($vt);

    reset($vt);
    $j = 0;

    $int_guia = $HTTP_POST_VARS["calculavalor"];
    $str_movimento = $HTTP_POST_VARS["sem_movimento$int_guia"];

    $int_guia_nr = 0;

    for ($i = 0; $i < $tam; $i ++) {
      if (db_indexOf(key($vt), "VAL") > 0) {
        $valores[$j ++] = $vt[key($vt)];
     // echo"<br>valores ".key($vt)."=".$vt[key($vt)];
        if (key($vt) == ("VAL".$int_guia)) {
          $int_guia_nr = $j -1;
        }
      }
      next($vt);
    }

    $j = 0;
    reset($vt);

    for ($i = 0; $i < $tam; $i ++) {
      if (db_indexOf(key($vt), "CHECK") > 0){

        $numpres[$j ++] = $vt[key($vt)];
     // echo"<br>numpres ".key($vt)."=".$vt[key($vt)];
      }
      next($vt);

    }


    if (isset($valores)) {

      if (sizeof($valores) != sizeof($numpres)) {

        db_erro("<br> Matriz inválida!", 1);

      }
      $tam = sizeof($valores);

      $sqlerro = false;

      db_inicio_transacao();
      //for($i = 0;$i < $tam;$i++) {

      // Lançamento sem movimento - parte I
      if (!empty($str_movimento) and $str_movimento != 'Sem Movimento') {
        // ######################### TEM QUE INCLUIR NA cancdebitos, cancdebitosproc #########################
        include_once("classes/db_cancdebitos_classe.php");
        include_once("classes/db_cancdebitosproc_classe.php");
        include_once("classes/db_cancdebitosreg_classe.php");
        include_once("classes/db_cancdebitosprocreg_classe.php");
        include_once("classes/db_issvarsemmov_classe.php");
        include_once("classes/db_issvarsemmovreg_classe.php");
        $clcancdebitos        = new cl_cancdebitos;
        $clcancdebitosproc    = new cl_cancdebitosproc;
        $clcancdebitosreg     = new cl_cancdebitosreg;
        $clcancdebitosprocreg = new cl_cancdebitosprocreg;
        $clissvarsemmov       = new cl_issvarsemmov;
        $clissvarsemmovreg    = new cl_issvarsemmovreg;
        $clcancdebitos -> k20_hora    = date("H:i");
        $clcancdebitos -> k20_data    = date("Y-m-d");
        $clcancdebitos -> k20_usuario = empty($id_usuario) ? 'null' : $id_usuario;
        $clcancdebitos -> k20_descr   = 'ISS cancelado pelo Dbpref.';
        $clcancdebitos -> incluir(null);
        if ($clcancdebitos->erro_status == 0) {
          $sqlerro = true;
          $msgerro = $clcancdebitos ->erro_msg ;
          db_msgbox($msgerro);
        }

        $clcancdebitosproc -> k23_data   = date("Y-m-d");
        $clcancdebitosproc -> k23_hora   = date("H:i");
        $clcancdebitosproc -> k23_usuario= empty($id_usuario) ? 'null' : $id_usuario;
        $clcancdebitosproc -> k23_obs    = $str_movimento;
        $clcancdebitosproc -> incluir(null);
        if ($clcancdebitosproc->erro_status == 0) {
          $sqlerro = true;
          $msgerro = $clcancdebitosproc ->erro_msg ;
          db_msgbox($msgerro);
        }

        $clissvarsemmov-> q08_usuario  = empty($id_usuario) ? 'null' : $id_usuario;
        $clissvarsemmov-> q08_data     = date("Y-m-d");
        $clissvarsemmov-> q08_hora     = date("H:i");
        $clissvarsemmov-> q08_tipolanc = 1;
        $clissvarsemmov-> incluir(null) ;
        if ($clissvarsemmov->erro_status == 0) {
          $sqlerro = true;
          $msgerro = $clissvarsemmov ->erro_msg ;
          db_msgbox($msgerro);
        }


      }

      for ($i = $int_guia_nr; $i <= $int_guia_nr; $i ++) {
        $mat = split("P", $numpres[$i]);
        $numpre = $mat[0];
        $numpar = $mat[1];
        $valores[$i] = $valores[$i] + 0;

        //atualiza valor no issvar
        $sql = "update issvar set q05_vlrinf = ".$valores[$i]." where q05_numpre = $numpre and q05_numpar = $numpar";
       // echo "<br>$sql";
        db_query($sql) or die("Erro(37) atualizando issvar: ".pg_errormessage());

        //grava informações dos valores

        $str_sql = "select *
        from issvar
        where q05_numpre = $numpre
        and q05_numpar = $numpar";

        $res_issvar = $clissvarlancval->sql_record($str_sql);

        db_fieldsmemory($res_issvar, 0);

        $clissvarlancval->q50_codigo = $q05_codigo;
        $clissvarlancval->q50_numpre = $q05_numpre;
        $clissvarlancval->q50_numpar = $q05_numpar;
        $clissvarlancval->q50_valor  = $q05_valor;
        $clissvarlancval->q50_ano    = $q05_ano;
        $clissvarlancval->q50_mes    = $q05_mes;
        $clissvarlancval->q50_histor = $q05_histor;
        $clissvarlancval->q50_aliq   = $q05_aliq;
        $clissvarlancval->q50_bruto  = $q05_bruto;
        $clissvarlancval->q50_vlrinf = $q05_vlrinf;
        $clissvarlancval->q50_ip     = $_SERVER["REMOTE_ADDR"];
        $clissvarlancval->q50_data   = date("Y-m-d");
        $clissvarlancval->q50_hora   = date("G:i:s");
        $clissvarlancval->q50_idusuario = empty($id_usuario) ? 'null' : $id_usuario;
        $clissvarlancval->q50_arquivo = $_SERVER["REQUEST_URI"];
        $clissvarlancval->incluir(null);
      //  echo "<br>inclui na issvarlancval<br>";
        if ($clissvarlancval->erro_status == "0") {
          $clissvarlancval->erro(true, false);
        }

        // Lançamento sem movimento - parte II
        if (!empty($str_movimento) and $str_movimento != 'Sem Movimento') {
          //echo "<br>se for sem movimentooooooooooo<br>";
          $clarrehist->k00_numpre     = $q05_numpre;
          $clarrehist->k00_numpar     = $q05_numpar;
          $res_parissqn = $clparissqn->sql_record($clparissqn->sql_query_file("", "q60_histsemmov"));
          db_fieldsmemory($res_parissqn, 0);
          $clarrehist->k00_hist       = $q60_histsemmov;
          $clarrehist->k00_dtoper     = date("Y-m-d");
          $clarrehist->k00_hora       = date("G:i");
          $clarrehist->k00_id_usuario = $id_usuario;
          $clarrehist->k00_histtxt    = $str_movimento;
          $clarrehist->incluir(null);
          if ($clarrehist->erro_status == 0) {
            $sqlerro = true;
            $msgerro = $clarrehist ->erro_sql ;
            db_msgbox($msgerro);
          }
//echo "<br> id_hist = $clarrehist->k00_idhist <br>id_usuario=$id_usuario";

          $clarrecant->incluir_arrecant($q05_numpre, $q05_numpar);
          if ($clarrecant->erro_status == 0) {
            $sqlerro = true;
          }
          if ($sqlerro==false){
            $clarrehistip->k45_ip  = $_SERVER["REMOTE_ADDR"];
            $clarrehistip->k45_obs = $_SERVER["REQUEST_URI"];
            $clarrehistip->incluir($clarrehist->k00_idhist);
            $clarrehistip->erro(true, false);
            if ($clarrehistip->erro_status == 0) {
              $sqlerro = true;
              $msgerro = $clarrehistip ->erro_sql ;
              db_msgbox($msgerro);
            }
          }
          $clissvarsemmovreg-> q15_issvarsemmov = $clissvarsemmov->q08_sequencial;
          $clissvarsemmovreg-> q15_issvar       = $q05_codigo ;
          $clissvarsemmovreg-> incluir(null) ;
          if ($clissvarsemmovreg->erro_status == 0) {
           $sqlerro = true;
           $msgerro = $clissvarsemmovreg ->erro_msg ;
           db_msgbox($msgerro);
         }



          // ######################### TEM QUE INCLUIR NA cancdebitosreg, cancdebitosprocreg #########################

         $sqlrec = "select k00_receit, k00_valor from arrecant where k00_numpre = $q05_numpre and k00_numpar = $q05_numpar";

         $resultrec = db_query($sqlrec);
         $linhasrec = pg_num_rows($resultrec);
         if($linhasrec>0){
          for($r=0;$r<$linhasrec;$r++){

            db_fieldsmemory($resultrec, $r);
            $clcancdebitosreg -> k21_codigo = $clcancdebitos->k20_codigo;
            $clcancdebitosreg -> k21_numpre = $q05_numpre;
            $clcancdebitosreg -> k21_numpar = $q05_numpar;
            $clcancdebitosreg -> k21_receit = $k00_receit;
            $clcancdebitosreg -> k21_data   = date("Y-m-d");
            $clcancdebitosreg -> k21_hora   = date("H:i");
            $clcancdebitosreg -> k21_obs    = $str_movimento;
            $clcancdebitosreg -> incluir(null);
            if ($clcancdebitosreg->erro_status == 0) {
              $sqlerro = true;
              $msgerro = $clcancdebitosreg ->erro_msg ;
              db_msgbox($msgerro);
            }

            $clcancdebitosprocreg -> k24_codigo         = $clcancdebitosproc->k23_codigo;
            $clcancdebitosprocreg -> k24_cancdebitosreg = $clcancdebitosreg->k21_sequencia;
            $clcancdebitosprocreg -> k24_vlrhis         = '0';
            $clcancdebitosprocreg -> k24_vlrcor         = $k00_valor;
            $clcancdebitosprocreg -> k24_juros          = '0';
            $clcancdebitosprocreg -> k24_multa          = '0';
            $clcancdebitosprocreg -> k24_desconto       = '0';
            $clcancdebitosprocreg -> incluir(null);
            if ($clcancdebitosprocreg->erro_status == 0) {
              $sqlerro = true;
              $msgerro = $clcancdebitosprocreg ->erro_msg ;
              db_msgbox($msgerro);
            }

          }
        }

      }

      db_fim_transacao($sqlerro);
      db_redireciona($_SERVER["REQUEST_URI"]);
    }

    $tipo = 3;

  }

  if (isset($db_datausu)) {
    if (!checkdate(substr($db_datausu, 5, 2), substr($db_datausu, 8, 2), substr($db_datausu, 0, 4))) {
      echo "Data para Cálculo Inválida. <br><br>";
      echo "Data deverá ser superior a: ".date('Y-m-d', db_getsession("DB_datausu"));
    }
    if (mktime(0, 0, 0, substr($db_datausu, 5, 2), substr($db_datausu, 8, 2), substr($db_datausu, 0, 4)) < mktime(0, 0, 0, date('m', db_getsession("DB_datausu")), date('d', db_getsession("DB_datausu")), date('Y', db_getsession("DB_datausu")))) {
      echo "Data não permitida para cálculo. <br><br>";
      echo "Data deverá ser superior a: ".date('Y-m-d', db_getsession("DB_datausu"));
    }
    $DB_DATACALC = mktime(0, 0, 0, substr($db_datausu, 5, 2), substr($db_datausu, 8, 2), substr($db_datausu, 0, 4));
  } else {
    $DB_DATACALC = db_getsession("DB_datausu");
  }
  ?>
  <script>
    document.getElementById('int_perc2').style.width='40%';
  </script>
  <script>
      function js_emiteunica(numpre){

                  var mostraemite = <?=$k00_recibodbpref?>;

                  if(mostraemite==2){
                        alert('Este tipo de debito não permite emitir recibo.');
                        document.getElementById("enviar").disabled = true;//botao emite recibo
                        return false;
                  }else{
                        document.getElementById("enviar").disabled = false;//botao emite recibo
                  }

                  document.form1.numpre_unica.value = numpre;
                  jan = window.open('','reciboweb2','width=790,height=530,scrollbars=1,location=0');
                  jan.moveTo(0,0);
                  document.form1.submit();
                  document.form1.numpre_unica.value = "";
      }

      function js_somatudo(){

       var F = document.form1;
       var valor = 0;
       var valorcorr = 0;
       var juros = 0;
       var multa = 0;
       var desconto = 0;
       var total = 0;
       var emrec = 0;
       for(var i = 0;i < F.length;i++){
        if((F.elements[i].type == "checkbox" || F.elements[i].type == "submit")){
          var indi = js_parse_int(F.elements[i].id);
          valor += new Number(document.getElementById('valor'+indi).value.replace(",",""));
          valorcorr += new Number(document.getElementById('valorcorr'+indi).value.replace(",",""));
          juros += new Number(document.getElementById('juros'+indi).value.replace(",",""));
          multa += new Number(document.getElementById('multa'+indi).value.replace(",",""));
          desconto += new Number(document.getElementById('desconto'+indi).value.replace(",",""));
          total += new Number(document.getElementById('total'+indi).value.replace(",",""));
        }
      }
      document.getElementById('valor1').innerHTML     = valor.toFixed(2);
      document.getElementById('valorcorr1').innerHTML = valorcorr.toFixed(2);
      document.getElementById('juros1').innerHTML     = juros.toFixed(2);
      document.getElementById('multa1').innerHTML     = multa.toFixed(2);
      document.getElementById('desconto1').innerHTML  = desconto.toFixed(2);
      document.getElementById('total1').innerHTML     = total.toFixed(2);
    }

function js_soma(linha,dt_agrupadebito) {

  //alert(dt_agrupadebito);
  linha = ((typeof(linha)=="undefined") || (typeof(linha)=="object")?2:linha);
  var F = document.form1;
  var numpres=0;
  var valor = 0;
  var valorcorr = 0;
  var juros = 0;
  var multa = 0;
  var desconto = 0;
  var total = 0;
  var emrec = 0;
  var vcto_atraso = false;
  var data_hoje = "<?=date('Ymd',db_getsession('DB_datausu'))?>";
  var var_vcto = "";
  var vcto_calc = "";
  var mostraemite = '<?=$k00_recibodbpref?>';


  var mostraemitedaeb = '<?=$w16_recibodbpref?>';
  var mostramsgdaeb   = '<?=$mostramsgdaeb?>';

  if((mostramsgdaeb == 'true') && (mostraemitedaeb == '2')){
      alert('Emissão não disponível. Favor dirigir-se ao Setor de Cadastro e Atendimento ao Público do DAEB.');
      document.getElementById("enviar").disabled = true;//botao emite recibo
      return false;
  }else if(mostraemite==2){
    alert('Este tipo de debito não permite emitir recibo.');
    document.getElementById("enviar").disabled = true;//botao emite recibo
    return false;
  }else{
    document.getElementById("enviar").disabled = false;//botao emite recibo
  }




  /*if(mostraemite==2){
    alert('Este tipo de debito não permite emitir recibo.');
    parent.document.getElementById("enviar").disabled = true;//botao emite recibo
  }else{
    parent.document.getElementById("enviar").disabled = false;//botao emite recibo
  }/*/



  var cont = 0;
  for(var i = 0;i < F.length;i++){
    if((F.elements[i].type == "checkbox" || F.elements[i].type == "submit") && (F.elements[i].checked == true )){
      var indi = js_parse_int(F.elements[i].id);

      valor     += new Number(document.getElementById('valor'+indi).value.replace(",",""));
      valorcorr += new Number(document.getElementById('valorcorr'+indi).value.replace(",",""));
      juros     += new Number(document.getElementById('juros'+indi).value.replace(",",""));
      multa     += new Number(document.getElementById('multa'+indi).value.replace(",",""));
      desconto  += new Number(document.getElementById('desconto'+indi).value.replace(",",""));
      total     += new Number(document.getElementById('total'+indi).value.replace(",",""));
      numpres += 'N'+document.getElementById('CHECK'+indi).value ;
      document.getElementById('numpres').value = numpres;
      if (document.getElementById('debito')) {
        document.getElementById('debito').disabled = false;
      }

      //data do vencimento
      var_vcto = document.getElementById('vcto_parcela'+indi).innerHTML;
      var_vcto2 = var_vcto.substr(6,4)+var_vcto.substr(3,2)+var_vcto.substr(0,2);
      cont++;

      if(var_vcto2 < data_hoje){
       vcto_atraso = true;
       vcto_calc = data_hoje;
     }else{
       //vcto_atraso = false;
       vcto_calc = var_vcto2;
     }
     ///fim vcto
   }
 }

 //alert('cont = '+cont+' vcto_atraso='+vcto_atraso+' vcto_calc='+vcto_calc);
  if(cont>1 && vcto_atraso==true){
    vcto_calc = data_hoje;
  }else if(cont==1 && vcto_atraso==false){
     vcto_calc = var_vcto2;
  }else if(cont>1 && vcto_atraso==false){
    vcto_calc = data_hoje;
  }
  if(cont==0){
    vcto_calc = data_hoje;
  }

//alert(valor.toFixed(2));
  document.getElementById('dia_vcto').value = vcto_calc.substr(6,2);
  document.getElementById('mes_vcto').value = vcto_calc.substr(4,2);
  document.getElementById('ano_vcto').value = vcto_calc.substr(0,4);
  document.getElementById('valorg'+linha).innerHTML     = valor.toFixed(2);
  document.getElementById('valorcorrg'+linha).innerHTML = valorcorr.toFixed(2);
  document.getElementById('jurosg'+linha).innerHTML     = juros.toFixed(2);
  document.getElementById('multag'+linha).innerHTML     = multa.toFixed(2);
  document.getElementById('descontog'+linha).innerHTML  = desconto.toFixed(2);
  document.getElementById('totalg'+linha).innerHTML     = total.toFixed(2);

  if(linha == 2){

    valor     = Number(document.getElementById('valorg1').innerHTML) - valor;
    valorcorr = Number(document.getElementById('valorcorrg1').innerHTML) - valorcorr;
    juros     = Number(document.getElementById('jurosg1').innerHTML) - juros;
    multa     = Number(document.getElementById('multag1').innerHTML) - multa;
    desconto  = Number(document.getElementById('descontog1').innerHTML) - desconto;
    total     = Number(document.getElementById('totalg1').innerHTML) - total;

    document.getElementById('valorg3').innerHTML     = valor.toFixed(2);
    document.getElementById('valorcorrg3').innerHTML = valorcorr.toFixed(2);
    document.getElementById('jurosg3').innerHTML     = juros.toFixed(2);
    document.getElementById('multag3').innerHTML     = multa.toFixed(2);
    document.getElementById('descontog3').innerHTML  = desconto.toFixed(2);
    document.getElementById('totalg3').innerHTML     = total.toFixed(2);

  }

  if(emrec == 't') {
    var aux = 0;
    for(i = 0;i < F.length;i++) {
      if(F.elements[i].type == "checkbox")
       if(F.elements[i].checked == true)
        aux = 1;
    }
    if(aux == 0) {

      document.getElementById("enviar").disabled = true;
      document.getElementById('marca').innerHTML = "M";
      document.getElementById('btmarca').value = "Marcar Todas";
    }
  }

 if(Number(document.getElementById('totalg2').innerHTML)==0)
  document.getElementById("enviar").disabled = true; //Comentado

  document.getElementById('dt_agrupadebitos').value = 0;
  var G = document.getElementById('tabdebitos').rows;
  var check = "";
  for(i = 0;i < G.length;i++) {
    check = 'CHECK'+i;
    if(document.getElementById(check) && document.getElementById(check).checked == true ) {
      dataLida = 'vcto_parcela'+i;
    }
  }
 var aData = (document.getElementById(dataLida).innerHTML.split('-'));
 var dt_agrupadebitos = aData[2]+'-'+aData[1]+'-'+aData[0];
 document.getElementById('dt_agrupadebitos').value = dt_agrupadebitos;


}

function js_marca() {
  var ID = document.getElementById('marca');
  if(!ID)
    return false;
  var F = document.form1;
  if(ID.innerHTML == 'M') {
    var dis = true;
    ID.innerHTML = 'D';
  } else {
    var dis = false;
    ID.innerHTML = 'M';
  }
  for(i = 0;i < F.elements.length;i++) {
    if(F.elements[i].type == "checkbox"){
      if(F.elements[i].style.visibility!="hidden"){
        if(F.elements[i].name == "NM") { // se o name do checkbox for NM(Não Marcar) não deixa somar os valor tbem
          F.elements[i].checked = false;
        }else {
          F.elements[i].checked = dis;
        }
      }
    }
  }
  js_soma(this);
}

function pop_valorbruto(id,inscr, id_usuario){
 if(document.getElementById('enviar').value == 'Agrupar'){
  document.getElementById('confirm_guia_nro').value = id;
 // js_OpenJanelaIframe('corpo','db_iframe_teste','calcula_issqn2.php','issqn',true);
 window.open('calcula_issqn.php?id='+id+'&inscr='+inscr+'&id_usuario='+id_usuario,'pop','top=150,left=150,width=600,height=300,toolbar=no,menubar=no,resizable=no,scrollbars=no');
}else{
  if(confirm("Valores já foram Agrupados!\n\nDeseja atualizar valores informados?")){
   parent.location.reload();
   return true;
 }else{
   return false;
 }
}
}

function MM_reloadPage(init) {  //reloads the window if Nav4 resized
  if (init==true) with (navigator) {if ((appName=="Netscape")&&(parseInt(appVersion)==4)) {
    document.MM_pgW=innerWidth; document.MM_pgH=innerHeight; onresize=MM_reloadPage; }}
    else if (innerWidth!=document.MM_pgW || innerHeight!=document.MM_pgH) location.reload();
  }
  MM_reloadPage(true);

  function msgNaoLiberada(id) {
    var instit = '<?=$DB_INSTITUICAO?>';
    document.getElementById(id).checked = false;
    if(instit == '4') {
      alert('Emissão não disponível. Favor dirigir-se ao Setor de Cadastro e Atendimento ao Público do DAEB.');
    } else {
      alert('Débito não disponível para emissão nesta data.');
    }
  }
</script>
<script> document.getElementById('int_perc2').style.width='60%'; </script>
<?php

//verifica se clicou no link da matricula ou inscrição
  if (isset($inscricao) && !empty($inscricao)) { //die("incr= $inscricao xx $inscr");
  $inscr = $inscricao;
  $tipo = $tipo2;
}
if (isset($matricula) && !empty($matricula)) {
  $matric = $matricula;
  $tipo = $tipo2;
}
//verifica o tipo e da o select dependendo se é numcgm, matric numpre ou inscr
if (isset($tipo)) {

  if ($tipo == 3) {

    if (isset($inscr) && !empty($inscr)) {
      if (($result = debitos_inscricao_var($inscr, 0, $tipo, $DB_DATACALC, db_getsession("DB_anousu")))) {
        echo "<script> inscr = '$inscr'; </script>\n";
      } else {
        db_redireciona("cai3_gerfinanc007.php?erro1=1");
      }
    }
    elseif (isset($numcgm) && !empty($numcgm)) {
      if (($result = debitos_numcgm_var($numcgm, 0, $tipo, $DB_DATACALC, db_getsession("DB_anousu")))) {
        echo "<script> numcgm = '$numcgm'; </script>\n";
      } else {
        db_redireciona("cai3_gerfinanc007.php?erro1=1");
      }
    } else
    if (isset($numpre) && !empty($numpre)) {
      if (($result = debitos_numpre_var($numpre, 0, $tipo, $DB_DATACALC, db_getsession("DB_anousu")))) {
        echo "<script> numpre = '$numpre'; </script>\n";
      } else {
        db_redireciona("cai3_gerfinanc007.php?erro1=1");
      }
    }
  } else {

    if (isset($numcgm) && !empty($numcgm) && (isset($matric) && empty($matric)) && (isset($inscr) && empty($inscr))) {

      if (($result = debitos_numcgm($numcgm, 0, $tipo, $DB_DATACALC, db_getsession("DB_anousu")))) {

        echo "<script> numcgm = '$numcgm'; </script>\n";

      } else
      db_redireciona("cai3_gerfinanc007.php?erro1=1");
    } else

    if (isset($matric) && !empty($matric)) {

      if (($result = debitos_matricula($matric, 0, $tipo, $DB_DATACALC, db_getsession("DB_anousu")))){
        echo "<script> matric = '$matric'; </script>\n";

      }else{
        db_redireciona("cai3_gerfinanc007.php?erro1=1");
      }
    } else
    if (isset($inscr) && !empty($inscr)) {
      if (($result = debitos_inscricao($inscr, 0, $tipo, $DB_DATACALC, db_getsession("DB_anousu")))) {
        echo "<script> inscr = '$inscr'; </script>\n";
      } else
      db_redireciona("cai3_gerfinanc007.php?erro1=1");
    } else
    if (isset($numpre) && !empty($numpre)) {
      if (($result = debitos_numpre($numpre, 0, $tipo, $DB_DATACALC, db_getsession("DB_anousu"))))
        echo "<script> numpre = '$numpre'; </script>\n";
      else
        db_redireciona("cai3_gerfinanc007.php?erro1=1");
    }

  }

  ?>
  <script> document.getElementById('int_perc2').style.width='85%'; </script>
  <?php
  $numrows = pg_numrows($result);
  echo "<form name=\"form1\" id=\"form1\" method=\"post\" target=\"reciboweb2\">\n";
  echo "<input type=\"hidden\" name=\"output\" value=\"t\">\n";
  echo "<input type=\"hidden\" name=\"H_ANOUSU\" value=\"".db_getsession("DB_anousu")."\">\n";
  echo "<input type=\"hidden\" name=\"H_DATAUSU\" value=\"".$DB_DATACALC."\">\n";
  if ($numrows > 0) {
    echo "<input type=\"hidden\" id=\"ver_matric\" name=\"ver_matric\" value=\"".@pg_result($result, 0, "k00_matric")."\">\n";
    echo "<input type=\"hidden\" id=\"ver_inscr\" name=\"ver_inscr\" value=\"".@pg_result($result, 0, "k00_inscr")."\">\n";
    echo "<input type=\"hidden\" id=\"ver_numcgm\" name=\"ver_numcgm\" value=\"".@pg_result($result, 0, "k00_numcgm")."\">\n";
  }
  $result_k03_tipo = db_query("select cadtipo.k03_tipo,k03_parcelamento,k03_permparc
   from arretipo
   inner join cadtipo on arretipo.k03_tipo = cadtipo.k03_tipo
   where k00_tipo = $tipo");

  db_fieldsmemory($result_k03_tipo, 0);

  echo "<input type=\"hidden\" name=\"tipo_debito\" value=\"".$tipo."\">\n";
  echo "<input type=\"hidden\" name=\"k03_tipo\" value=\"".$k03_tipo."\">\n";
  echo "<input type=\"hidden\" name=\"k03_parcelamento\" value=\"".$k03_parcelamento."\">\n";
  echo "<input type=\"hidden\" name=\"k03_permparc\" value=\"".$k03_permparc."\">\n";
  echo "<table border=\"0\" cellspacing=\"0\" cellpadding=\"3\" id=\"tabdebitos\" class=\"table table-striped\">\n";
    //cria o cabeçalho
  echo "<tr class=\"active\">\n";
  echo "<th title=\"Parcela\">P</th>\n";
  echo "<th title=\"Total de Parcela\">T</th>\n";
  echo "<th title=\"Data de Vencimento\">Dt. Venc.</th>\n";
  echo "<th title=\"Histórico do Lançamento\">Histórico</th>\n";
    //Verifica se agrupado por numpre, cria link pra passar pro nivel 2, mostrando todos os numpres
  if (!empty($inscr))
    $arg = "inscr=".$inscr;
  else
    if (!empty($numcgm))
      $arg = "numcgm=".$numcgm;
    else
      if (!empty($matric))
        $arg = "matric=".$matric;
      else
        if (!empty($numpre))
          $arg = "numpre=".$numpre;
        if (@$agnum == 't') {
          //echo "<th title=\"Lista por parcela\" ><a href=\"cai3_gerfinanc002.php?".$arg."&tipo=$tipo&verificaagrupar=1&agnump=f&agpar=t&emrec=".$emrec."&db_datausu=".date("Y-m-d", $DB_DATACALC)."&inscr=".@$inscr."&matric=".@$matric."&numpre=".@$numpre."&numcgm=".@$numcgm."\">Rec</a></th>\n";
          $link   = "index.php?page=listadebitos&key=".base64_encode("&".$arg."&tipo=$tipo&verificaagrupar=1&agnump=f&agpar=t&emrec=".$emrec."&db_datausu=".date("Y-m-d", $DB_DATACALC)."&inscr=".@$inscr."&matric=".@$matric."&numpre=".@$numpre."&numcgm=".@$numcgm);
          echo "<th title=\"Lista por parcela\" ><a href=\"".$link."\">Rec</a></th>\n";
        } else {
          echo "<th title=\"Receita\" >Rec</th>\n";
        }

        echo "<th title=\"Descrição Receita\" >Receita</th>\n";
        echo "<th title=\"Valor Lançado\">Val.</th>\n";
        echo "<th title=\"Valor Corrigido\">Val Cor.</th>\n";
        echo "<th title=\"Valor Juros\">Jur.</th>\n";
        echo "<th title=\"Valor Multa\">Mul.</th>\n";
        echo "<th title=\"Valor Desconto\">Desc.</th>\n";
        echo "<th title=\"Total a Pagar\">Tot.</th>\n";
        echo "<th title=\"Marca/Desmarca Todas\"><a id=\"marca\" href=\"\" style=\"color:black\" onclick=\"js_marca();return false\">M</a>
        <input type=\"hidden\" name=\"numpre_unica\" id=\"numpre_unica\"></th>\n";
        echo "</tr>\n";

        //verifica se foi clicado no link agrupar e recria as variaveis do QUERY_STRING pra atualizar o agnump e agpar
        if (isset($verificaagrupar)) {
          parse_str($HTTP_SERVER_VARS['QUERY_STRING']);
        }

        // calcular totregistros...
        $j = 0;
        $elementos2[0] = "";
        for($i = 0;$i < $numrows;$i++) {
          if(!in_array(pg_result($result,$i,"k00_numpre"),$elementos2)) {
            $elementos2[$j++] = pg_result($result,$i,"k00_numpre");

          }
        }

        $totregistros = 0;

        for($i = 0;$i < sizeof($elementos2);$i++) {

          for($j = 0;$j < $numrows;$j++) {
            if($elementos2[$i] == pg_result($result,$j,"k00_numpre")) {
              if(pg_result($result,$j,"k00_numpar") != @pg_result($result,$j+1,"k00_numpar") || ($elementos2[$i] != @pg_result($result,$j+1,"k00_numpre")) ) {
                $totregistros++;

              }
            }
          }

        }

        echo "<input type=\"hidden\" name=\"totregistros\" value=\"".@$totregistros."\">\n";
//if com 3 partes. Primeiro se é pra agrupar por numpre, segundo se é pra agrupar por parcela e terceiro mostra o default
    //agrupar por numpre
        if (@$agnum == 't') {

          /******************************************************************************************/
      //cria um array com os elementos não repetidos
          $j = 0;
          $vlrtotal = 0;
          $elementos[0] = "";
          for ($i = 0; $i < $numrows; $i ++) {
            if (!in_array(pg_result($result, $i, "k00_numpre"), $elementos)) {
              $REGISTRO[$j] = pg_fetch_array($result, $i);
              $elementos[$j ++] = pg_result($result, $i, "k00_numpre");
            }
          }
      //faz a mao...

          $sqlconfigdbprefarretipo = "select w17_dtini, w17_dtfim from configdbprefarretipo where w17_arretipo = $tipo and w17_instit = $DB_INSTITUICAO";
          $rsconfigdbprefarretipo  = db_query($sqlconfigdbprefarretipo);
          if(pg_num_rows($rsconfigdbprefarretipo) > 0) {
            db_fieldsmemory($rsconfigdbprefarretipo, 0);
          }

          for ($i = 0; $i < sizeof($elementos); $i ++) {
            $valor = 0;
            $valorcorr = 0;
            $juros = 0;
            $multa = 0;
            $desconto = 0;
            $total = 0;
            $separador = "";
            $numpres = "";

            for ($j = 0; $j < $numrows; $j ++) {
              if ($elementos[$i] == pg_result($result, $j, "k00_numpre")) {
                if (pg_result($result, $j, "k00_numpar") != @pg_result($result, $j +1, "k00_numpar") || ($elementos[$i] != @pg_result($result, $j +1, "k00_numpre"))) {
                  $numpres .= $separador.pg_result($result, $j, "k00_numpre")."P".pg_result($result, $j, "k00_numpar");
                  $separador = "N";
                }
                $valor += (float) pg_result($result, $j, "vlrhis");
                $valorcorr += (float) pg_result($result, $j, "vlrcor");
                $juros += (float) pg_result($result, $j, "vlrjuros");
                $multa += (float) pg_result($result, $j, "vlrmulta");
                $desconto += (float) pg_result($result, $j, "vlrdesconto");
                $total += (float) pg_result($result, $j, "total");
              }
            }

            /**************************/
            $vlrtotal += $REGISTRO[$i]["total"];
            $dtoper = $REGISTRO[$i]["k00_dtoper"];
            $dtoper = mktime(0, 0, 0, substr($dtoper, 5, 2), substr($dtoper, 8, 2), substr($dtoper, 0, 4));
            $corDtoper = "";
            $dtvenc = $REGISTRO[$i]["k00_dtvenc"];

            $dtvenc = mktime(23, 59, 0, substr($dtvenc, 5, 2), substr($dtvenc, 8, 2), substr($dtvenc, 0, 4));
        if ($dtvenc < $DB_DATACALC) //time())
$corDtvenc = "red";
else {
          if (date("d/m/Y", $dtvenc) == date("d/m/Y", $DB_DATACALC)) //time())
$corDtvenc = "blue";
else
  $corDtvenc = "";
}
        //*****CABEÇALHO  ;border:none

        // unica

$sql_resultunica = "select *,
substr(fc_calcula,2,13)::float8 as uvlrhis,
substr(fc_calcula,15,13)::float8 as uvlrcor,
substr(fc_calcula,28,13)::float8 as uvlrjuros,
substr(fc_calcula,41,13)::float8 as uvlrmulta,
substr(fc_calcula,54,13)::float8 as uvlrdesconto,
(substr(fc_calcula,15,13)::float8+
 substr(fc_calcula,28,13)::float8+
 substr(fc_calcula,41,13)::float8-
 substr(fc_calcula,54,13)::float8) as utotal
from (
 select r.k00_numpre,r.k00_dtvenc as dtvencunic, r.k00_dtoper as dtoperunic,r.k00_percdes,
 fc_calcula(r.k00_numpre,0,0,r.k00_dtvenc,r.k00_dtvenc,".db_getsession("DB_anousu").")
 from recibounica r
 where r.k00_numpre = ".$elementos[$i]." and r.k00_dtvenc >= '".date('Y-m-d',$DB_DATACALC)."'::date
 ) as unica";
        //die($sql_resultunica);
$resultunica = db_query($sql_resultunica);
for ($unicont = 0; $unicont < pg_numrows($resultunica); $unicont ++) {
  db_fieldsmemory($resultunica, $unicont);
  if ($dtvencunic >= date('Y-m-d', $DB_DATACALC)) {
    $dtvencunic = db_formatar($dtvencunic, 'd');
    $dtoperunic = db_formatar($dtoperunic, 'd');
    $corunica = "success";
    $uvlrcorr = 0;

    $histdesc = "";
    $resulthist = db_query("select k00_dtoper as dtlhist,k00_hora, login,substr(k00_histtxt,0,80) as k00_histtxt
      from arrehist
      left outer join db_usuarios on id_usuario = k00_id_usuario
      where k00_numpre = ".$elementos[$i]." and k00_numpar = 0");
    if (pg_numrows($resulthist) > 0) {
      for ($di = 0; $di < pg_numrows($resulthist); $di ++) {
        db_fieldsmemory($resulthist, $di);
        $histdesc .= $dtlhist." ".$k00_hora." ".$login." ".$k00_histtxt."\n";
      }
    }

    echo "<tr class=\"$corunica\">\n";
    echo "<td >00</td>\n";
    echo "<td >00</td>\n";
    echo "<td  id=\"vcto_parcela$i\" name=\"vcto_parcela$i\"  >".$dtvencunic."</td>\n";
    echo "<td colspan=\"3\"  >Parcela Única com $k00_percdes% desconto</td>\n";
    echo "<td  >".number_format($uvlrhis, 2, ".", ",")."</td>\n";
    echo "<td  >".number_format($uvlrcorr, 2, ".", ",")."</td>\n";
    echo "<td  >".number_format($uvlrjuros, 2, ".", ",")."</td>\n";
    echo "<td  >".number_format($uvlrmulta, 2, ".", ",")."</td>\n";
    echo "<td  >".number_format($uvlrdesconto, 2, ".", ",")."</td>\n";
    echo "<td  >".number_format($utotal, 2, ".", ",")."</td>\n";
    echo "<td   align=\"center\" >
      <!--<input type=\"button\" style=\"border:none;background-color:write\" name=\"unica\" onclick=\"js_emiteunica('$k00_numpre')\" value=\"U\"> -->";
    echo '<button type="button" name="unica" class="btn btn-default btn-sm" onclick="js_emiteunica('.$k00_numpre.')">U</button>  ';
    echo "</td>\n</tr>";
  }
}


$noti_sql = "select k53_numpre
from notidebitos
where k53_numpre = ".$REGISTRO[$i]["k00_numpre"]."
limit 1";
$noti_result = db_query($noti_sql);
$temnoti = false;
if (pg_numrows($noti_result)) {
  $temnoti = true;

}

$dtVctoInicial  = str_replace("-", "", $w17_dtini);
$dtVctoFinal    = str_replace("-", "", $w17_dtfim);
$dtVctoParcela  = str_replace("-", "", date("Y-m-d", $dtvenc));

if(($dtVctoFinal  > $dtVctoParcela) OR (pg_num_rows($rsconfigdbprefarretipo) == 0)) {
  $checkLibera = true;
}else {
  $checkLibera = false;
}

      //echo "<label for=\"CHECK$i\"><tr style=\"cursor: hand\" bgcolor=\"". (@$cor = (@$cor == "#E4F471" ? "#EFE029" : "#E4F471"))."\">\n";
      echo "<label for=\"CHECK$i\"><tr style=\"cursor: hand\" >\n";
      echo "<td   ><input type=\"hidden\" id=\"parc$i\" value=\"0#".$REGISTRO[$i]["k00_numtot"]."#".$REGISTRO[$i]["k00_numpre"]."\">0 </td>\n";
      echo "<td   >".$REGISTRO[$i]["k00_numtot"]."</td>\n";
      echo "<td  id=\"vcto_parcela$i\" name=\"vcto_parcela$i\"  ". ($corDtvenc == "" ? "" : "bgcolor=$corDtvenc")." >".date("d-m-Y", $dtvenc)."</td>\n";
      echo "<td   >". (trim($REGISTRO[$i]["k01_descr"]) == "" ? "&nbsp" : $REGISTRO[$i]["k01_descr"])."</td>\n";
      $link   = "index.php?page=listadebitos&key=".base64_encode("&numpre=".$elementos[$i]."&tipo=$tipo&verificaagrupar=1&agnump=f&agpar=t&emrec=".$emrec."&db_datausu=".date("Y-m-d", $DB_DATACALC)."&inscr=".@$inscr."&matric=".@$matric."&numpre=".@$numpre."&numcgm=".@$numcgm);
      echo "<td title=\"Lista por parcela\"   ><a href=\"".$link."\"><b>Parcelas</b></a></td>\n";
      echo "<td   >". (trim($REGISTRO[$i]["k02_descr"]) == "" ? "&nbsp" : $REGISTRO[$i]["k02_descr"])."</td>\n";
      echo "<td   ><input type=\"hidden\" id=\"valor$i\" value=\"".$valor."\">".number_format($valor, 2, ".", ",")."</td>\n";
      echo "<td   ><input type=\"hidden\" id=\"valorcorr$i\" value=\"".$valorcorr."\">".number_format($valorcorr, 2, ".", ",")."</td>\n";
      echo "<td   ><input type=\"hidden\" id=\"juros$i\" value=\"".$juros."\">".number_format($juros, 2, ".", ",")."</td>\n";
      echo "<td   ><input type=\"hidden\" id=\"multa$i\" value=\"".$multa."\">".number_format($multa, 2, ".", ",")."</td>\n";
      echo "<td   ><input type=\"hidden\" id=\"desconto$i\" value=\"".$desconto."\">".number_format($desconto, 2, ".", ",")."</td>\n";
      echo "<td   ><input type=\"hidden\" id=\"total$i\" value=\"".$total."\">".number_format($total, 2, ".", ",")."</td>\n";

        if($checkLibera == true) {
          echo "<td   id=\"coluna$i\" >". ($tipo == 3 ? "<input type=\"submit\" name=\"calculavalor\" id=\"calculavalor$i\" value=\"Calcular\">" : "")."<input style=\"visibility:'visible'\" type=\"". ($tipo == 3 ? "hidden" : "checkbox")."\" value=\"".$numpres."\" onclick=\"js_soma(2)\" id=\"CHECK$i\" name=\"CHECK$i\" ". ((abs($REGISTRO[$i]["k00_valor"]) != 0 && $tipo == 3) ? "disabled" : "")."></td>\n";
        }else {
          echo "<td   id=\"coluna$i\" >". ($tipo == 3 ? "<input type=\"submit\" name=\"calculavalor\" id=\"calculavalor$i\" value=\"Calcular\">" : "")."<input style=\"visibility:'visible'\" type=\"". ($tipo == 3 ? "hidden" : "checkbox")."\" value=\"\" onclick=\"msgNaoLiberada(this.id)\" id=\"CHECK$i\" name=\"NM\" ". ((abs($REGISTRO[$i]["k00_valor"]) != 0 && $tipo == 3) ? "disabled" : "")."></td>\n";
        }
        echo "</tr></label>\n";
/***************************/
        //soma totais divida
@$total_valor += $valor;
@$total_valorcor += $valorcorr;
@$total_juros += $juros;
@$total_multa += $multa;
@$total_desconto += $desconto;
@$total_total += $total;

}
?>
<tr>
  <td colspan="15" class="texto">
    <span class="label label-default">Clique em <b>Parcelas</b> para visualizar os parcelamentos.</span>
  </td>
</tr>
<?

      //agrupar por parcela
} else
if ($agpar == 't') {

  /**********************************************************************************************/
  //cria um array com os numpres não repetidos
  $j = 0;
  $elementos_numpres[0] = "";
  for ($i = 0; $i < $numrows; $i ++) {
    if (!in_array(pg_result($result, $i, "k00_numpre"), $elementos_numpres)) {
      $elementos_numpres[$j ++] = pg_result($result, $i, "k00_numpre");
    }
  }
  //contador unico para nomear os inputs
  $ContadorUnico = 0;
  $bool = 1;
  //faz a mao..
  for ($x = 0; $x < sizeof($elementos_numpres); $x ++) {
    //cria um array com as parcelas do numpre não repetidos
    if ($bool == 0) {
      $ConfCor1 = "#77EE20";
      $ConfCor2 = "#A9F471";
      $bool = 1;
    } else {
      $ConfCor1 = "#EFE029";
      $ConfCor2 = "#E4F471";
      $bool = 0;
    }
    $f = 0;
    $vlrtotal = 0;
    if (isset($elementos_parcelas))
      unset ($elementos_parcelas);
    $elementos_parcelas[0] = "";
    for ($r = 0; $r < $numrows; $r ++) {
      if ($elementos_numpres[$x] == pg_result($result, $r, "k00_numpre"))
        if (!in_array(pg_result($result, $r, "k00_numpar"), $elementos_parcelas)) {
          $REGISTRO[$f] = pg_fetch_array($result, $r);
          $elementos_parcelas[$f ++] = pg_result($result, $r, "k00_numpar");
        }
      }

      $sqlconfigdbprefarretipo = "select w17_dtini, w17_dtfim from configdbprefarretipo where w17_arretipo = $tipo and w17_instit = $DB_INSTITUICAO";
      $rsconfigdbprefarretipo  = db_query($sqlconfigdbprefarretipo);
      if(pg_num_rows($rsconfigdbprefarretipo) > 0) {
        db_fieldsmemory($rsconfigdbprefarretipo, 0);
      }


      for ($i = 0; $i < sizeof($elementos_parcelas); $i ++) {
        $numpres = "";
        $separador = "";
        $valor = 0;
        $valorcorr = 0;
        $juros = 0;
        $multa = 0;
        $desconto = 0;
        $total = 0;
        for ($j = 0; $j < $numrows; $j ++) {
          if ($elementos_parcelas[$i] == pg_result($result, $j, "k00_numpar") && $elementos_numpres[$x] == pg_result($result, $j, "k00_numpre")) {
            if (pg_result($result, $j, "k00_numpar") != @pg_result($result, $j +1, "k00_numpar") || ($elementos_numpres[$x] != @pg_result($result, $j +1, "k00_numpre"))) {
              $numpres .= $separador.$elementos_numpres[$x]."P".$elementos_parcelas[$i];
              $separador = "N";
            }
            $valor += (float) pg_result($result, $j, "vlrhis");
            $valorcorr += (float) pg_result($result, $j, "vlrcor");
            $juros += (float) pg_result($result, $j, "vlrjuros");
            $multa += (float) pg_result($result, $j, "vlrmulta");
            $desconto += (float) pg_result($result, $j, "vlrdesconto");
            $total += (float) pg_result($result, $j, "total");
          }
        }
        /**************************/
        @$vlrtotal += $REGISTRO[$i]["total"];
        @$dtoper = $REGISTRO[$i]["k00_dtoper"];
        @$dtoper = mktime(0, 0, 0, substr($dtoper, 5, 2), substr($dtoper, 8, 2), substr($dtoper, 0, 4));
        @$corDtoper = "";
        @$dtvenc = $REGISTRO[$i]["k00_dtvenc"];

        @$dtvenc = mktime(23, 59, 0, substr($dtvenc, 5, 2), substr($dtvenc, 8, 2), substr($dtvenc, 0, 4));
            if ($dtvenc < $DB_DATACALC) //time())
$corDtvenc = "red";
else {
              if (date("d/m/Y", $dtvenc) == date("d/m/Y", $DB_DATACALC)) //time())
$corDtvenc = "blue";
else
  $corDtvenc = "";
}
            // unica
if ($elementos_parcelas[$i] == 1) {



  $resultunica = db_query("select k00_numpre,dtvencunic,dtoperunic,k00_percdes,
    substr(fc_calcula,2,13)::float8 as uvlrhis,
    substr(fc_calcula,15,13)::float8 as uvlrcor,
    substr(fc_calcula,28,13)::float8 as uvlrjuros,
    substr(fc_calcula,41,13)::float8 as uvlrmulta,
    substr(fc_calcula,54,13)::float8 as uvlrdesconto,
    (substr(fc_calcula,15,13)::float8+
      substr(fc_calcula,28,13)::float8+
      substr(fc_calcula,41,13)::float8-
      substr(fc_calcula,54,13)::float8) as utotal
  from (
    select r.k00_numpre,r.k00_dtvenc as dtvencunic, r.k00_dtoper as dtoperunic,r.k00_percdes,
    fc_calcula(k00_numpre,0,0,k00_dtvenc,k00_dtvenc,".db_getsession("DB_anousu").")
    from recibounica r
    where r.k00_numpre = ".$elementos_numpres[$x]." and r.k00_dtvenc >= '".date('Y-m-d',$DB_DATACALC)."'
    ) as unica");
  for ($unicont = 0; $unicont < pg_numrows($resultunica); $unicont ++) {
    db_fieldsmemory($resultunica, $unicont);
    if ($dtvencunic >= date('Y-m-d', $DB_DATACALC)) {
      $dtvencunic = db_formatar($dtvencunic, 'd');
      $dtoperunic = db_formatar($dtoperunic, 'd');
      $corunica = "success";
      $uvlrcorr = 0;
      $histdesc = "";
      $resulthist = db_query("select k00_dtoper as dtlhist,k00_hora, login,substr(k00_histtxt,0,80) as k00_histtxt
       from arrehist
       left outer join db_usuarios on id_usuario = k00_id_usuario
       where k00_numpre = ".$elementos_numpres[$x]." and k00_numpar = 0");
      if (pg_numrows($resulthist) > 0) {
        for ($di = 0; $di < pg_numrows($resulthist); $di ++) {
          db_fieldsmemory($resulthist, $di);
          $histdesc .= $dtlhist." ".$k00_hora." ".$login." ".$k00_histtxt."\n";
        }
      }
                //echo "aki 4444";
      echo "<tr class=\"$corunica\">\n";
      echo "<td   >00</td>\n";
      echo "<td   >00</td>\n";
      echo "<td  id=\"U$i\" name=\"U$i\"  >".$dtvencunic."</td>\n";
      echo "<td colspan=\"3\" >Parcela Única com $k00_percdes% desconto</td>\n";
      echo "<td   >".number_format($uvlrhis, 2, ".", ",")."</td>\n";
      echo "<td   >".number_format($uvlrcorr, 2, ".", ",")."</td>\n";
      echo "<td   >".number_format($uvlrjuros, 2, ".", ",")."</td>\n";
      echo "<td   >".number_format($uvlrmulta, 2, ".", ",")."</td>\n";
      echo "<td   >".number_format($uvlrdesconto, 2, ".", ",")."</td>\n";
      echo "<td   >".number_format($utotal, 2, ".", ",")."</td>\n";
      echo "<td   align=\"center\" >
                  <!-- <input type=\"button\" style=\"border:none;background-color:write\" name=\"unica\" onclick=\"js_emiteunica(".$k00_numpre.")\" value=\"U\"> -->";
      echo '<button type="button" name="unica" class="btn btn-default btn-sm" onclick="js_emiteunica('.$k00_numpre.')">U</button>  ';
      echo "  </td>\n
                  </tr>";
  }
}
}
            //
$noti_sql = "select k53_numpre
from notidebitos
where k53_numpre = ".$REGISTRO[$i]["k00_numpre"]." and
k53_numpar = ".$REGISTRO[$i]["k00_numpar"]."
limit 1";
$noti_result = db_query($noti_sql);
$temnoti = false;
if (pg_numrows($noti_result)) {
  $temnoti = true;
}


$dtVctoInicial  = str_replace("-", "", $w17_dtini);
$dtVctoFinal    = str_replace("-", "", $w17_dtfim);
$dtVctoParcela  = str_replace("-", "", date("Y-m-d", $dtvenc));

            if(($dtVctoFinal  > $dtVctoParcela) OR (pg_num_rows($rsconfigdbprefarretipo) == 0)) {
              $checkLibera = true; // liberado
            }else {
              $checkLibera = false;
            }

            //echo "<label for=\"CHECK$ContadorUnico\">
           //           <tr style=\"cursor: hand\" bgcolor=\"". ($cor = (@$cor == $ConfCor2 ? $ConfCor1 : $ConfCor2))."\">\n";
          echo "<label for=\"CHECK$ContadorUnico\">
                      <tr style=\"cursor: hand\">\n";
            echo "<td><input type=\"hidden\" id=\"parc$i\" value=\"".$elementos_parcelas[$i]."#".$REGISTRO[$i]["k00_numtot"]."#".$elementos_numpres[$x]."\">".$elementos_parcelas[$i]."</td>\n";
            echo "<td>".$REGISTRO[$i]["k00_numtot"]."</td>\n";
            echo "<td id=\"vcto_parcela$i\" name=\"vcto_parcela$i\"  ". ($corDtvenc == "" ? "" : "bgcolor=$corDtvenc")." >".date("d-m-Y", $dtvenc)."</td>\n";
            echo "<td>". (trim($REGISTRO[$i]["k01_descr"]) == "" ? "&nbsp" : $REGISTRO[$i]["k01_descr"])."</td>\n";
            $link   = "index.php?page=listadebitos&key=".base64_encode("&".$arg."&tipo=$tipo&agnump=f&agpar=f&emrec=".$emrec."&db_datausu=".date("Y-m-d", $DB_DATACALC)."&numcgm=".@$numcgm."&inscr=".@$inscr."&matric=".@$matric."&numpre=".@$numpre);
            echo "<td title=\"Lista por receita\" ><a href=\"".$link."\">DE</a></td>\n";
            echo "<td>". (trim($REGISTRO[$i]["k02_descr"]) == "" ? "&nbsp" : $REGISTRO[$i]["k02_descr"])."</td>\n";
            echo "<td><input type=\"hidden\" id=\"valor$ContadorUnico\" value=\"".$valor."\">".number_format($valor, 2, ".", ",")."</td>\n";
            echo "<td><input type=\"hidden\" id=\"valorcorr$ContadorUnico\" value=\"".$valorcorr."\">".number_format($valorcorr, 2, ".", ",")."</td>\n";
            echo "<td><input type=\"hidden\" id=\"juros$ContadorUnico\" value=\"".$juros."\">".number_format($juros, 2, ".", ",")."</td>\n";
            echo "<td><input type=\"hidden\" id=\"multa$ContadorUnico\" value=\"".$multa."\">".number_format($multa, 2, ".", ",")."</td>\n";
            echo "<td><input type=\"hidden\" id=\"desconto$ContadorUnico\" value=\"".$desconto."\">".number_format($desconto, 2, ".", ",")."</td>\n";
            echo "<td><input type=\"hidden\" id=\"total$ContadorUnico\" value=\"".$total."\">".number_format($total, 2, ".", ",")."</td>\n";

            if($checkLibera == true) {
              echo "<td   id=\"coluna$ContadorUnico\" >". ($tipo == 3 ? "<input type=\"submit\" name=\"calculavalor\" id=\"calculavalor$ContadorUnico\" value=\"Calcular\">" : "")."<input style=\"visibility:'visible'\" type=\"". ($tipo == 3 ? "hidden" : "checkbox")."\" value=\"".$numpres."\" onclick=\"js_soma(2)\" id=\"CHECK$ContadorUnico\" name=\"CHECK".$ContadorUnico ++."\" ". ((abs($REGISTRO[$i]["k00_valor"]) != 0 && $tipo == 3) ? "disabled" : "")."></td>\n";
            }else {

              echo "<td   id=\"coluna$ContadorUnico\" >";
              echo "".($tipo == 3 ? "<input type=\"submit\" name=\"calculavalor\" id=\"calculavalor$ContadorUnico\" value=\"Calcular\">" : "");
              echo "<input style=\"visibility:'visible'\" type=\"". ($tipo == 3 ? "hidden" : "checkbox")."\" value=\"\" onclick=\"msgNaoLiberada(this.id)\" id=\"CHECK$ContadorUnico\" name=\"NM\" ". ((abs($REGISTRO[$i]["k00_valor"]) != 0 && $tipo == 3) ? "disabled" : "")."></td>\n";
            }

            echo "</tr></label>\n";

            /***************************/
            //soma totais
            @$total_valor += $valor;
            @$total_valorcor += $valorcorr;
            @$total_juros += $juros;
            @$total_multa += $multa;
            @$total_desconto += $desconto;
            @$total_total += $total;
            ////
          }
        }
      } else {
  //NIVEL NORMAL
        /**************************************************************************************************************/
  //cria um array com os numpres não repetidos
        $j = 0;
        $elementos_numpres[0] = "";
        for ($i = 0; $i < $numrows; $i ++) {
          if (!in_array(pg_result($result, $i, "k00_numpre"), $elementos_numpres))
            $elementos_numpres[$j ++] = pg_result($result, $i, "k00_numpre");
        }
        for ($i = 0; $i < sizeof($elementos_numpres); $i ++) {
          $auxValor = 0;
          $auxValorcorr = 0;
          $auxJuros = 0;
          $auxMulta = 0;
          $auxDesconto = 0;
          $auxTotal = 0;
          for ($j = 0; $j < $numrows; $j ++) {
            if ($elementos_numpres[$i] == pg_result($result, $j, "k00_numpre")) {
              if (pg_result($result, $j, "k00_numpar") == @pg_result($result, ($j +1), "k00_numpar")) {
                $auxValor += (float) pg_result($result, $j, "vlrhis");
                $auxValorcorr += (float) pg_result($result, $j, "vlrcor");
                $auxJuros += (float) pg_result($result, $j, "vlrjuros");
                $auxMulta += (float) pg_result($result, $j, "vlrmulta");
                $auxDesconto += (float) pg_result($result, $j, "vlrdesconto");
                $auxTotal += (float) pg_result($result, $j, "total");
                $SomaDasParcelasValor[$elementos_numpres[$i]][pg_result($result, $j, "k00_numpar")] = $auxValor;
                $SomaDasParcelasValorcorr[$elementos_numpres[$i]][pg_result($result, $j, "k00_numpar")] = $auxValorcorr;
                $SomaDasParcelasJuros[$elementos_numpres[$i]][pg_result($result, $j, "k00_numpar")] = $auxJuros;
                $SomaDasParcelasMulta[$elementos_numpres[$i]][pg_result($result, $j, "k00_numpar")] = $auxMulta;
                $SomaDasParcelasDesconto[$elementos_numpres[$i]][pg_result($result, $j, "k00_numpar")] = $auxDesconto;
                $SomaDasParcelasTotal[$elementos_numpres[$i]][pg_result($result, $j, "k00_numpar")] = $auxTotal;
                //echo $elementos_numpres[$i]." == ".pg_result($result,$j,"k00_numpar")." == ".@pg_result($result,$j+1,"k00_numpar")." == ".$aux."<br>";
              } else {
                $auxValor = 0;
                $auxValorcorr = 0;
                $auxJuros = 0;
                $auxMulta = 0;
                $auxDesconto = 0;
                $auxTotal = 0;
                @$SomaDasParcelasValor[$elementos_numpres[$i]][pg_result($result, $j, "k00_numpar")] += pg_result($result, $j, "vlrhis");
                @$SomaDasParcelasValorcorr[$elementos_numpres[$i]][pg_result($result, $j, "k00_numpar")] += pg_result($result, $j, "vlrcor");
                @$SomaDasParcelasJuros[$elementos_numpres[$i]][pg_result($result, $j, "k00_numpar")] += pg_result($result, $j, "vlrjuros");
                @$SomaDasParcelasMulta[$elementos_numpres[$i]][pg_result($result, $j, "k00_numpar")] += pg_result($result, $j, "vlrmulta");
                @$SomaDasParcelasDesconto[$elementos_numpres[$i]][pg_result($result, $j, "k00_numpar")] += pg_result($result, $j, "vlrdesconto");
                @$SomaDasParcelasTotal[$elementos_numpres[$i]][pg_result($result, $j, "k00_numpar")] += pg_result($result, $j, "total");
              }
            } else {
              continue;
            }
          }
        }
        $vlrtotal = 0;
        $verf_parc = "";
        $cont = 0;
        $cont2 = 0;
        $bool = 0;
        $bool2 = 0;
        $ConfCor1 = "#EFE029";
        $ConfCor2 = "#E4F471";
        $listaunica = true;

        $sqlconfigdbprefarretipo = "select w17_dtini, w17_dtfim from configdbprefarretipo where w17_arretipo = $tipo and w17_instit = $DB_INSTITUICAO";
        $rsconfigdbprefarretipo  = db_query($sqlconfigdbprefarretipo);
        if(pg_num_rows($rsconfigdbprefarretipo) > 0) {
          db_fieldsmemory($rsconfigdbprefarretipo, 0);
        }


        for ($i = 0; $i < $numrows; $i ++) {
          if ($elementos_numpres[$cont] != pg_result($result, $i, "k00_numpre")) {
            $listaunica = true;
            $cont ++;
            if ($bool == 0) {
              $ConfCor1 = "#77EE20";
              $ConfCor2 = "#A9F471";
              $bool = 1;
            } else {
              $ConfCor1 = "#EFE029";
              $ConfCor2 = "#E4F471";
              $bool = 0;
            }
          }
          $vlrtotal += pg_result($result, $i, "total");
          $dtoper = pg_result($result, $i, "k00_dtoper");
          $dtoper = mktime(0, 0, 0, substr($dtoper, 5, 2), substr($dtoper, 8, 2), substr($dtoper, 0, 4));
          $corDtoper = "";
          $dtvenc = pg_result($result, $i, "k00_dtvenc");
          $dtvenc = mktime(23, 59, 0, substr($dtvenc, 5, 2), substr($dtvenc, 8, 2), substr($dtvenc, 0, 4));
          if ($dtvenc < $DB_DATACALC)
            $corDtvenc = "red";
          else {
            if (date("d/m/Y", $dtvenc) == date("d/m/Y", $DB_DATACALC))
              $corDtvenc = "blue";
            else
              $corDtvenc = "";
          }
          if (pg_result($result, $i, "k00_numpar") == @$salva_parcela) {
            $cor = $ConfCor1;
          } else {
            $cor = $ConfCor2;
            if (pg_result($result, $i, "k00_numpar") == @pg_result($result, $i +1, "k00_numpar"))
              $salva_parcela = "";
            else
              $salva_parcela = @pg_result($result, $i +1, "k00_numpar");
          }
    // unica
          if ($tipo != 3) {
            if (($elementos_numpres[$cont] == pg_result($result, $i, "k00_numpre")) && $listaunica) {
              $listaunica = false;

              $resultunica = db_query("select *,
               substr(fc_calcula,2,13)::float8 as uvlrhis,
               substr(fc_calcula,15,13)::float8 as uvlrcor,
               substr(fc_calcula,28,13)::float8 as uvlrjuros,
               substr(fc_calcula,41,13)::float8 as uvlrmulta,
               substr(fc_calcula,54,13)::float8 as uvlrdesconto,
               (substr(fc_calcula,15,13)::float8+
                 substr(fc_calcula,28,13)::float8+
                 substr(fc_calcula,41,13)::float8-
                 substr(fc_calcula,54,13)::float8) as utotal
              from (
               select r.k00_numpre,r.k00_dtvenc as dtvencunic, r.k00_dtoper as dtoperunic, r.k00_percdes,
               fc_calcula(r.k00_numpre,0,0,r.k00_dtvenc,r.k00_dtvenc,".db_getsession("DB_anousu").")
               from recibounica r
               where r.k00_numpre = ".pg_result($result, $i, "k00_numpre")." and r.k00_dtvenc >= '".date('Y-m-d',$DB_DATACALC)."'
               ) as unica");
              for ($unicont = 0; $unicont < pg_numrows($resultunica); $unicont ++) {
                db_fieldsmemory($resultunica, $unicont);
                if ($dtvencunic >= date('Y-m-d', $DB_DATACALC)) {
                  $dtvencunic = db_formatar($dtvencunic, 'd');
                  $dtoperunic = db_formatar($dtoperunic, 'd');
                  $corunica = "success";
                  $uvlrcorr = 0;
                  $histdesc = "";
                  $resulthist = db_query("select k00_dtoper as dtlhist,k00_hora, login,substr(k00_histtxt,0,80) as k00_histtxt
                   from arrehist
                   left outer join db_usuarios on id_usuario = k00_id_usuario
                   where k00_numpre = ".pg_result($result, $i, "k00_numpre")." and k00_numpar = 0");
                  if (pg_numrows($resulthist) > 0) {
                    for ($di = 0; $di < pg_numrows($resulthist); $di ++) {
                      db_fieldsmemory($resulthist, $di);
                      $histdesc .= $dtlhist." ".$k00_hora." ".$login." ".$k00_histtxt."\n";
                    }
                  }
                //echo "aki 666";
                  echo "<tr class=\"$corunica\">\n";
                  echo "<td >00</td>\n";
                  echo "<td >00</td>\n";
                  echo "<td id=\"vcto_parcela$i\" name=\"vcto_parcela$i\">".$dtvencunic."</td>\n";
                  echo "<td colspan=\"3\">Parcela Única com $k00_percdes% desconto</td>\n";
                  echo "<td>".number_format($uvlrhis, 2, ".", ",")."</td>\n";
                  echo "<td>".number_format($uvlrcorr, 2, ".", ",")."</td>\n";
                  echo "<td>".number_format($uvlrjuros, 2, ".", ",")."</td>\n";
                  echo "<td>".number_format($uvlrmulta, 2, ".", ",")."</td>\n";
                  echo "<td>".number_format($uvlrdesconto, 2, ".", ",")."</td>\n";
                  echo "<td>".number_format($utotal, 2, ".", ",")."</td>\n";
                  echo "<td> <!-- <input type=\"button\" style=\"border:none;background-color:write\" name=\"unica\" onclick=\"js_emiteunica('$k00_numpre')\" value=\"U\"> -->";
                  echo '<button type="button" name="unica" class="btn btn-default btn-sm" onclick="js_emiteunica('.$k00_numpre.')">U</button>  ';
                  echo "  </td>\n
                          </tr>";
              }
            }
          }
        }
    //
        $noti_sql = "select k53_numpre
        from notidebitos
        where k53_numpre = ".pg_result($result, $i, "k00_numpre")." and
        k53_numpar = ".pg_result($result, $i, "k00_numpar")."
        limit 1";
        $noti_result = db_query($noti_sql);
        $temnoti = false;
        if (pg_numrows($noti_result)) {
          $temnoti = true;
        }

        //echo "<label for=\"CHECK$i\"><tr bgcolor=\"".$cor."\">\n";
        echo "<label for=\"CHECK$i\"><tr >\n";
        echo "<td>". (trim(pg_result($result, $i, "k00_numpar")) == "" ? "&nbsp" : pg_result($result, $i, "k00_numpar"))."</td>\n";
        echo "<td>". (trim(pg_result($result, $i, "k00_numtot")) == "" ? "&nbsp" : pg_result($result, $i, "k00_numtot"))."</td>\n";
        echo "<td id=\"vcto_parcela$i\" name=\"vcto_parcela$i\"   ". ($corDtvenc == "" ? "" : "bgcolor=$corDtvenc")." >".date("d-m-Y", $dtvenc)."</td>\n";
        echo "<td>". (trim(pg_result($result, $i, "k01_descr")) == "" ? "&nbsp" : pg_result($result, $i, "k01_descr"))."</td>\n";
        echo "<td>". (trim(pg_result($result, $i, "k00_receit")) == "" ? "&nbsp" : pg_result($result, $i, "k00_receit"))."</td>\n";
        echo "<td>". (trim(pg_result($result, $i, "k02_descr")) == "" ? "&nbsp" : pg_result($result, $i, "k02_descr"))."</td>\n";
        echo "<td title=\"Clique aqui para informar o valor.\"><input type=\"hidden\" id=\"valor$i\" value=\"".$SomaDasParcelasValor[pg_result($result, $i, "k00_numpre")][pg_result($result, $i, "k00_numpar")]."\">". (trim(pg_result($result, $i, "vlrhis")) == "" ? "&nbsp" : ((abs(pg_result($result, $i, "k00_valor")) == 0 && $tipo == 3) ? "<input style=\"height: 20px;font-size=11px; text-align:right; cursor:hand\" readonly type=\"text\" onclick=\"pop_valorbruto('$i','$inscr', '$id_usuario')\" name=\"VAL".$i."\" value=\"".abs(pg_result($result, $i, "valor_variavel"))."\" size=\"10\">" : number_format(pg_result($result, $i, "vlrhis"), 2, ".", ",")))."</td>\n";
        echo "<td><input type=\"hidden\" id=\"valorcorr$i\" value=\"".$SomaDasParcelasValorcorr[pg_result($result, $i, "k00_numpre")][pg_result($result, $i, "k00_numpar")]."\">". (trim(pg_result($result, $i, "vlrcor")) == "" ? "&nbsp" : number_format(pg_result($result, $i, "vlrcor"), 2, ".", ","))."</td>\n";
        echo "<td><input type=\"hidden\" id=\"juros$i\" value=\"".$SomaDasParcelasJuros[pg_result($result, $i, "k00_numpre")][pg_result($result, $i, "k00_numpar")]."\">". (trim(pg_result($result, $i, "vlrjuros")) == "" ? "&nbsp" : number_format(pg_result($result, $i, "vlrjuros"), 2, ".", ","))."</td>\n";
        echo "<td><input type=\"hidden\" id=\"multa$i\" value=\"".$SomaDasParcelasMulta[pg_result($result, $i, "k00_numpre")][pg_result($result, $i, "k00_numpar")]."\">". (trim(pg_result($result, $i, "vlrmulta")) == "" ? "&nbsp" : number_format(pg_result($result, $i, "vlrmulta"), 2, ".", ","))."</td>\n";
        echo "<td><input type=\"hidden\" id=\"desconto$i\" value=\"".$SomaDasParcelasDesconto[pg_result($result, $i, "k00_numpre")][pg_result($result, $i, "k00_numpar")]."\">". (trim(pg_result($result, $i, "vlrdesconto")) == "" ? "&nbsp" : number_format(pg_result($result, $i, "vlrdesconto"), 2, ".", ","))."</td>\n";
        echo "<td><input type=\"hidden\" id=\"total$i\" value=\"".$SomaDasParcelasTotal[pg_result($result, $i, "k00_numpre")][pg_result($result, $i, "k00_numpar")]."\">". (trim(pg_result($result, $i, "total")) == "" ? "&nbsp" : number_format(pg_result($result, $i, "total"), 2, ".", ","));
        echo ($tipo == 3 ? "<input type=\"hidden\" name=\"sem_movimento$i\" id=\"sem_movimento$i\" value=\"Sem Movimento\">" : "");
        echo "</td>\n";

//soma totais
        @$total_valor += number_format(pg_result($result, $i, "vlrhis"), 2, ".", ",");
        @$total_valorcor += number_format(pg_result($result, $i, "vlrcor"), 2, ".", ",");
        @$total_juros += number_format(pg_result($result, $i, "vlrjuros"), 2, ".", ",");
        @$total_multa += number_format(pg_result($result, $i, "vlrmulta"), 2, ".", ",");
        @$total_desconto += number_format(pg_result($result, $i, "vlrdesconto"), 2, ".", ",");
        @$total_total += number_format(pg_result($result, $i, "total"), 2, ".", ",");
    ////
    //if ($elementos_numpres[$cont2] == pg_result($result, $i, "k00_numpre")) {


        $iAnoOperacao = date('Y',strtotime(pg_result($result, $i, "k00_dtoper")));


    ////colocar validação da data de emissao aqui


        $dtVctoInicial  = str_replace("-", "", $w17_dtini);
        $dtVctoFinal    = str_replace("-", "", $w17_dtfim);
        $dtVctoParcela  = str_replace("-", "", date("Y-m-d", $dtvenc));

        if(($dtVctoFinal  > $dtVctoParcela) OR (pg_num_rows($rsconfigdbprefarretipo) == 0)) {
      $checkLibera = true; // liberado
    }else {
      $checkLibera = false;
    }

//    echo " | 1 : ". $verf_parc ." --  | 2 : " .pg_result($result, $i, "k00_numpar").$iAnoOperacao ."<br> " ;
    $dt_agrupadebitosrecibo = date("Y-m-d",$dtvenc);
    if ($verf_parc != str_pad(pg_result($result, $i, "k00_numpar"),2,"0",STR_PAD_LEFT).$iAnoOperacao) {
      if($checkLibera == true) {
        echo "<td id=\"coluna$i\" >". ($tipo == 3 ? "<input type=\"submit\" name=\"calculavalor\" id=\"calculavalor$i\" style=\"visibility:hidden\"  value=\"C\"  >" : "")."<input style=\"visibility:'visible'\" type=\"". ($tipo == 3 ? "hidden" : "checkbox")."\" value=\"".pg_result($result, $i, "k00_numpre")."P".pg_result($result, $i, "k00_numpar")."\" onclick=\"js_soma(2,'$dt_agrupadebitosrecibo');\" id=\"CHECK$i\" name=\"CHECK$i\" ". ((abs(pg_result($result, $i, "k00_valor")) != 0 && $tipo == 3) ? "disabled" : "")."></td>\n";
        $verf_parc = str_pad(pg_result($result, $i, "k00_numpar"),2,"0",STR_PAD_LEFT) . $iAnoOperacao;
      }else {//caso parcela n esteja dentro da data de liberação para emissão
        echo "<td id=\"coluna$i\" >";
        echo "".($tipo == 3 ? "<input type=\"submit\" name=\"calculavalor\" id=\"calculavalor$i\" style=\"visibility:hidden\"  value=\"C\"  >" : "");
        echo "<input style=\"visibility:'visible'\" type=\"". ($tipo == 3 ? "hidden" : "checkbox")."\" value=\"s\" onclick=\"msgNaoLiberada(this.id)\" id=\"CHECK$i\" name=\"NM\" ". ((abs(pg_result($result, $i, "k00_valor")) != 0 && $tipo == 3) ? "disabled" : "").">";
        echo "</td>\n";
      }
    } else {
      $verf_parc = str_pad(pg_result($result, $i, "k00_numpar"),2,"0",STR_PAD_LEFT) . $iAnoOperacao;
      echo "<td   id=\"coluna$i\" >&nbsp; </td>\n";

    }
        //  } else {        //    $cont2 ++;
        //    $verf_parc = pg_result($result, $i, "k00_numpar");
        //    echo "<td   id=\"coluna$i\" >". ($tipo == 3 ? "<input type=\"submit\" name=\"calculavalor\" id=\"calculavalor$i\"  disabled value=\"C5555\">" : "")."<input style=\"visibility:'visible'\" type=\"". ($tipo == 3 ? "hidden" : "checkbox")."\" value=\"".pg_result($result, $i, "k00_numpre")."P".pg_result($result, $i, "k00_numpar")."\" onclick=\"js_soma(2)\" id=\"CHECK$i\" name=\"CHECK$i\" ". ((abs(pg_result($result, $i, "k00_valor")) != 0 && $tipo == 3) ? "disabled" : "")."></td>\n";        //echo "<td   id=\"coluna$i\" >". ($tipo == 3 ? "<input type=\"submit\" onclick=\"this.form.target = ''\" name=\"calculavalor\" id=\"calculavalor$i\" value=\"Calcular4444\">" : "")."<input style=\"visibility:'visible'\" type=\"". ($tipo == 3 ? "hidden" : "checkbox")."\" value=\"".pg_result($result, $i, "k00_numpre")."P".pg_result($result, $i, "k00_numpar")."\" onclick=\"js_soma(2)\" id=\"CHECK$i\" name=\"CHECK$i\" ". ((abs(pg_result($result, $i, "k00_valor")) != 0 && $tipo == 3) ? "disabled" : "")."></td>\n";
        //  }
    echo "</tr></label>\n";
  }
} ////////****************************************************************************************/
?>
<input type="hidden" name="var_vcto">
<input type="hidden" name="dt_agrupadebitos" id="dt_agrupadebitos" value="0">
<?php

echo "</table>\n</form>\n";
}
?>
<script>
  document.getElementById('int_perc2').style.width='95%';

</script>

<?php

}//fim cai3_gerfinanc002

include_once("classes/db_iptubase_classe.php");
include_once("classes/db_issbase_classe.php");
?>


<Table width="100%" border="0" cellpadding="0" cellspacing="0">
 <tr>
  <td align="center" valign="middle" id="processando" onclick="document.getElementById('processando').style.visibility='hidden'"></td>
</tr>
</Table>
<table width="100%" border="0" cellspacing="0" cellpadding="3" align="center">
 <tr>
  <td align="center">
    <font size="1"> <b>Obs: Valores abaixo calculados sem considerar a(s) parcela(s) única(s). </b></font>
    <table class="table table-hover table-bordered">
      <tr class="active ">
       <th>Valor</th>
       <th>Valor Corr.</th>
       <th>Juros</th>
       <th>Multa</th>
       <th>Desconto</th>
       <th>Total</th>
     </tr>
     <tr>
       <td class="tabcols1"><font id="valorg1"><?=number_format(@$total_valor,2,'.','')?></font>&nbsp;</td>
       <td class="tabcols1"><font id="valorcorrg1"><?=number_format(@$total_valorcor,2,'.','')?></font>&nbsp;</td>
       <td class="tabcols1"><font id="jurosg1"><?=number_format(@$total_juros,2,'.','')?></font>&nbsp;</td>
       <td class="tabcols1"><font id="multag1"><?=number_format(@$total_multa,2,'.','')?></font>&nbsp;</td>
       <td class="tabcols1"><font id="descontog1"><?=number_format(@$total_desconto,2,'.','')?></font>&nbsp;</td>
       <td class="tabcols1"><font id="totalg1"><?=number_format(@$total_total,2,'.','')?></font>&nbsp;</td>
     </tr>
     <tr>
       <td class="tabcols1"><font id="valorg2">0.00</font>&nbsp;</td>
       <td class="tabcols1"><font id="valorcorrg2">0.00</font>&nbsp;</td>
       <td class="tabcols1"><font id="jurosg2">0.00</font>&nbsp;</td>
       <td class="tabcols1"><font id="multag2">0.00</font>&nbsp;</td>
       <td class="tabcols1"><font id="descontog2">0.00</font>&nbsp;</td>
       <td class="tabcols1"><font id="totalg2">0.00</font>&nbsp;</td>
     </tr>
     <tr>
       <td class="tabcols1"><font id="valorg3"><?=number_format(@$total_valor,2,'.','')?></font>&nbsp;</td>
       <td class="tabcols1"><font id="valorcorrg3"><?=number_format(@$total_valorcor,2,'.','')?></font>&nbsp;</td>
       <td class="tabcols1"><font id="jurosg3"><?=number_format(@$total_juros,2,'.','')?></font>&nbsp;</td>
       <td class="tabcols1"><font id="multag3"><?=number_format(@$total_multa,2,'.','')?></font>&nbsp;</td>
       <td class="tabcols1"><font id="descontog3"><?=number_format(@$total_desconto,2,'.','')?></font>&nbsp;</td>
       <td class="tabcols1"><font id="totalg3"><?=number_format(@$total_total,2,'.','')?></font>&nbsp;</td>
     </tr>
   </table>
 </td>
</tr>
<tr>
  <td height="24" align="center" class="texto">
   <input type="hidden" name="confirm_guia_nro" id="confirm_guia_nro" value="">
   <input type="hidden" name="confirm_guia" id="confirm_guia" value="false">
   <b>Vencimento:</b>
   <input type="text" name="dia_vcto" id="dia_vcto" size="2" maxlength="2" value="<?=date('d')?>">
   <input type="text" name="mes_vcto" id="mes_vcto" size="2" maxlength="2" value="<?=date('m')?>">
   <input type="text" name="ano_vcto" id="ano_vcto" size="4" maxlength="4" value="<?=date('Y')?>">
   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
   <!-- <input type="button" value="Voltar" onclick="js_voltar();" class="botao"> -->
   <!-- <input type="button" name="enviar" id="enviar" value="Emite Guia" class="botao" onClick="return js_emiterecibo()" disabled > -->
   <button type="button" name="enviar" id="enviar" value="Emite Guia" class="btn btn-primary" onClick="return js_emiterecibo()" disabled>Emite Guia</button>

   <input type='hidden' name='debito' id='debito' value=''>
   <?
     /*
     if ($inscr!=""){
       echo"<input type=\"button\" id=\"debito\" value=\"Debito em conta\" name=\"debito\" onclick=\"js_debito('$inscr','inscr')\" disabled class=\"botao\">";
     }elseif($matric!=""){
      echo"<input type=\"button\" id=\"debito\" value=\"Debito em conta\" name=\"debito\" onclick=\"js_debito('$matric','matric')\" disabled class=\"botao\">";
     }
    */
     ?>


     <input type="hidden" value="" name="numpres" id ="numpres" size="60">
   </td>
 </tr>
</table>
<br/>
<script>
  agrupadebrecibos  = false;
  debitos           = false;

  function js_MudaLink(nome) {
   if(navigator.appName == "Netscape") {
    TIPO = document.getElementById(nome).childNodes[1].firstChild.nodeValue;
  } else {
    TIPO = document.getElementById(nome).innerText;
  }
  for(i = 0;i < document.links.length;i++) {
    var L = document.links[i].id;
    if(L!=""){
      document.getElementById(L).style.backgroundColor = '#CCCCCC';
      document.getElementById(L).hideFocus = true;
    }
  }
  document.getElementById(nome).style.backgroundColor = '#E8EE6F';
  if(nome.indexOf("tiposemdeb") != -1) {
    document.getElementById('enviar').disabled = true;
    document.getElementById('btmarca').disabled = true;
  } else {
   document.getElementById('btmarca').disabled = false;
 }

 document.getElementById('valor1').innerHTML = "0.00";
 document.getElementById('valorcorr1').innerHTML = "0.00";
 document.getElementById('juros1').innerHTML = "0.00";
 document.getElementById('multa1').innerHTML = "0.00";
 document.getElementById('desconto1').innerHTML = "0.00";
 document.getElementById('total1').innerHTML = "0.00";

 document.getElementById('valor2').innerHTML = "0.00";
 document.getElementById('valorcorr2').innerHTML = "0.00";
 document.getElementById('juros2').innerHTML = "0.00";
 document.getElementById('multa2').innerHTML = "0.00";
 document.getElementById('desconto2').innerHTML = "0.00";
 document.getElementById('total2').innerHTML = "0.00";

 document.getElementById('valor3').innerHTML = "0.00";
 document.getElementById('valorcorr3').innerHTML = "0.00";
 document.getElementById('juros3').innerHTML = "0.00";
 document.getElementById('multa3').innerHTML = "0.00";
 document.getElementById('desconto3').innerHTML = "0.00";
 document.getElementById('total3').innerHTML = "0.00";
}

function js_emiterecibo(){

  var emissao = false;

  if(document.getElementById('enviar').value != 'Agrupar'){

    var dia_vcto = document.getElementById('dia_vcto').value;
    var mes_vcto = document.getElementById('mes_vcto').value;
    var ano_vcto = document.getElementById('ano_vcto').value;
    if(dia_vcto==""||mes_vcto==""||ano_vcto==""){
     alert("Informe o vencimento.");
     return false;
   }
    //verifica data vcto
    data_vcto = ano_vcto+mes_vcto+dia_vcto;
    data_hoje = "<?=date('Ymd',db_getsession('DB_datausu'))?>";
    if(data_vcto < data_hoje){
     alert("Vencimento deve ser maior ou igual a data atual.");
     document.getElementById('dia_vcto').focus();
     return false;
   }
    //Aqui testo se emite aviso sim ou não
    //se true fazer chamada ajax para verificar se existem debitos e emitir alerta
    js_verificaDebitos();

    if(agrupadebrecibos){
      if(debitos){
       emissao = false;
       if(!confirm('\nExistem Débitos Vencidos que serão Agrupados a esse recibo! \n\n Emitir mesmo assim?\n')){
        return false;
      }
    }
  }

  if(emissao){
    alert('\nParcela não Liberada para Emissão de Recibos!\n');
    return false;
  }

  document.form1.var_vcto.value = data_vcto;
    //
    var F = document.form1.elements;
    jan = window.open('','reciboweb2','width='+(screen.availWidth-5)+',height='+(screen.availHeight-40)+',scrollbars=1,location=0 ');
    jan.moveTo(0,0);
    document.form1.submit();
    if((elem = document.getElementById("geracarne")))
      elem.parentNode.removeChild(elem);

  }else{
    var tab = document.getElementById('tabdebitos');
    for(i = 1;i < tab.rows.length;i++) {
      var num = new Number(tab.rows[i].cells[10].childNodes[1].nodeValue);
      num = Math.abs(num);
    }
    var cor = "";
    for(i = 1;i < tab.rows.length;i++) {
      cor = (cor=="#E4F471")?"#EFE029":"#E4F471";
      tab.rows[i].bgColor = cor;
      if(tab.rows[i].cells[12].childNodes[0].attributes["type"].nodeValue == "submit") {
        var elem = document.getElementById(tab.rows[i].cells[12].childNodes[0].attributes["id"].nodeValue);
        elem.parentNode.removeChild(elem);
      }
      if(tab.rows[i].cells[12].childNodes[0].attributes["type"].nodeValue == "hidden") {
        var inp = document.createElement("INPUT");
        inp.setAttribute("type","checkbox");
        inp.setAttribute("name",tab.rows[i].cells[12].childNodes[0].attributes["name"].nodeValue);
        inp.setAttribute("id",tab.rows[i].cells[12].childNodes[0].attributes["id"].nodeValue);
        inp.setAttribute("value",tab.rows[i].cells[12].childNodes[0].attributes["value"].nodeValue);
        if(navigator.appName == "Netscape")
          inp.addEventListener("click",js_soma,false);
        else
          inp.onclick = js_soma;
        tab.rows[i].cells[12].appendChild(inp);
        var elem = document.getElementById(tab.rows[i].cells[12].childNodes[0].attributes["id"].nodeValue);
        elem.parentNode.removeChild(elem);
      }
    }
    document.getElementById("enviar").value = 'Emite Recibo';
    document.getElementById("enviar").disabled = true;
  }

}
function js_verificaDebitos(){

  var G = document.getElementById('tabdebitos').rows;
  var check = "";
  var virgula = "";
  var numpres = "";

  for(i = 0;i < G.length;i++) {

    check = 'CHECK'+i;
    if(document.getElementById(check) && document.getElementById(check).checked == true ) {
      if(document.getElementById(check).value != "") {
        numpres += virgula + document.getElementById(check).value;
        virgula = ",";
      }
    }

  }

  var sUrlRPC = 'cai3_gerfinanc003RPC.php';
  var sQuery = "";
  sQuery += 'numcgm='+'<?=$numcgm?>'+'&matric='+'<?=$matric?>'+'&inscr='+'<?=@$inscr?>'+'&tipo='+'<?=$tipo?>';
  var datausu = document.form1.dt_agrupadebitos.value;
  sQuery += '&db_datausu='+datausu;
  sQuery += '&ver_matric='+document.form1.ver_matric.value;
  sQuery += '&ver_inscr='+document.form1.ver_inscr.value;
  sQuery += '&ver_numcgm='+document.form1.ver_numcgm.value;
  sQuery += '&numpre_unica='+document.form1.numpre_unica.value;
  <? if (isset($inicial)) { ?>
    sQuery += '&inicial=true';
    <? } ?>
    sQuery += '&num_pres='+numpres;

  //alert("antes ajax : "+sQuery);
  js_divCarregando('Aguarde, verificando os dados.', "msgBox");
  var oAjax       = new Ajax.Request(
    sUrlRPC,
    {
     asynchronous : false,
     method    : 'post',
     parameters: sQuery,
     onComplete: js_retornoAgrupaDebitos
   }
   );
}

function js_retornoAgrupaDebitos(oAjax) {

  js_removeObj("msgBox");
  var oRetorno = eval("("+oAjax.responseText+")");
  emissao = '';
  if (oRetorno.debitos == 1) { debitos = true; } else { debitos = false; }
  if (oRetorno.emissao == 1) { emissao = true; } else { emissao = false; }
}

function js_debito(iss,iv){
  var numpres = document.getElementById('numpres').value;

  location.href='debito.php?numcgm='+<?=$numcgm?>+'&'+iv+'='+iss+'&tipo='+<?=$tipo?>+'&numpres='+numpres;
}
<?php
echo "
function js_voltar(){
  location.href = 'opcoesdebitospendentes.php?matricula=".trim(@$matric).
  "&inscricao=".trim(@$inscr).
  "&id_usuario=".trim(@$id_usuario).
  "&opcao=".trim(@$opcao).
  "&cgccpf=".trim(@$cgccpf).
  "&cgc=".trim(@$cgccpf).
  "&lVoltar=true';
}";

/*
  location.href = 'opcoesdebitospendentes.php?".base64_encode("matricula1=$matric&opcao=m&id_usuario=$id_usuario")."';

*/
  ?>

  window.document.getElementById('enviar').disabled = true;
  var tipo = <?php echo !isset($tipo) ? -1 : trim($tipo); ?>;
  if(tipo == 3) {
    document.getElementById('enviar').value = 'Agrupar';
    document.getElementById('enviar').disabled = false;
  }
  if(document.getElementById('confirm_guia').value == 'true'){
   document.getElementById('enviar').click();
   campo = 'CHECK'+document.getElementById('confirm_guia_nro').value;
   document.getElementById(campo).click();
   document.getElementById('enviar').click();
 }
 function js_submit(obj){
  document.getElementById(obj).style.visibility = 'visible';
  document.getElementById(obj).click();
}

document.getElementById('int_perc1').style.visibility='hidden';
</script>
<?php
require_once ("classes/db_configdbpref_classe.php");
$clconfigdbpref = new cl_configdbpref();
$rs_agrupadebitos = $clconfigdbpref->sql_record($clconfigdbpref->sql_query_file(db_getsession('DB_instit'),"w13_agrupadebrecibos"));
if($clconfigdbpref->numrows > 0){
  db_fieldsmemory($rs_agrupadebitos,0);
  if($w13_agrupadebrecibos == 't'){
    ?>
    <script type="text/javascript">
      agrupadebrecibos = true;
    </script>
    <?php
  }

}
//echo 'FIM';
?>

</div>
</div>
</div>
</div>