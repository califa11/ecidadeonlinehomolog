<?php
session_start();
//error_reporting(E_ALL ^ E_NOTICE);
//ini_set('display_errors', 1);
global $HTTP_SESSION_VARS;

include_once ("functions/func_sys.php");
//include ("libs/db_conn.php");
include ("libs/db_conecta.php");
//include ("libs/menu.php");
include ("libs/db_stdlib.php");
include ("libs/db_sql.php");
include ("libs/db_utils.php");
include ("db_funcoes.php");
db_postmemory($HTTP_SERVER_VARS);
parse_str(base64_decode($HTTP_SERVER_VARS["QUERY_STRING"]));
$result4 = pg_query("select upper(trim(munic)) as munic from db_config where codigo = ".db_getsession("DB_instit"));
if (pg_num_rows($result4) > 0) {
	db_fieldsmemory($result4, 0);
}

$page = $_GET['page'].'.php';

//Carregar sem exibir html
if ($_POST['output'] == 't') {
	if (file_exists($page)) {
		require_once ($page);
		exit();
	} else {
		die("Arquivo não encontrado");
	}
}

require_once ('header.php');
require_once ('menu.php');
?>
<!-- Page Content -->
<div id="page-wrapper">
  <div class="container-fluid">
<?php
if ($_GET['page']) {
	if (file_exists($page)) {
		require_once ($page);
	} else {
		require_once ('home.php');
	}
} else {
	require_once ('home.php');
}
?>
</div>
</div>
<?php
require_once ('footer.php');

?>