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

//MODULO: prefeitura
//CLASSE DA ENTIDADE db_ouvidoria
class cl_db_ouvidoria { 
   // cria variaveis de erro 
   var $rotulo     = null; 
   var $query_sql  = null; 
   var $numrows    = 0; 
   var $erro_status= null; 
   var $erro_sql   = null; 
   var $erro_banco = null;  
   var $erro_msg   = null;  
   var $erro_campo = null;  
   var $pagina_retorno = null; 
   // cria variaveis do arquivo 
   var $tipo = null; 
   var $comentario = null; 
   var $email = null; 
   var $receb_noticias = null; 
   var $data_dia = null; 
   var $data_mes = null; 
   var $data_ano = null; 
   var $data = null; 
   var $revisado_dia = null; 
   var $revisado_mes = null; 
   var $revisado_ano = null; 
   var $revisado = null; 
   var $login = null; 
   var $texto = null; 
   var $id_ouvidoria = 0; 
   // cria propriedade com as variaveis do arquivo 
   var $campos = "
                 tipo = varchar(50) = Tipo 
                 comentario = text = Comentário 
                 email = varchar(50) = email 
                 receb_noticias = char(1) = Notícias 
                 data = date = data 
                 revisado = date = Revisado 
                 login = varchar(20) = login do usuario 
                 texto = text = texto 
                 id_ouvidoria = int4 = Ouvidoria 
                 ";
   //funcao construtor da classe 
   function cl_db_ouvidoria() { 
     //classes dos rotulos dos campos
     $this->rotulo = new rotulo("db_ouvidoria"); 
     $this->pagina_retorno =  basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"]);
   }
   //funcao erro 
   function erro($mostra,$retorna) { 
     if(($this->erro_status == "0") || ($mostra == true && $this->erro_status != null )){
        echo "<script>alert(\"".$this->erro_msg."\");</script>";
        if($retorna==true){
           echo "<script>location.href='".$this->pagina_retorno."'</script>";
        }
     }
   }
   // funcao para atualizar campos
   function atualizacampos($exclusao=false) {
     if($exclusao==false){
       $this->tipo = ($this->tipo == ""?@$GLOBALS["HTTP_POST_VARS"]["tipo"]:$this->tipo);
       $this->comentario = ($this->comentario == ""?@$GLOBALS["HTTP_POST_VARS"]["comentario"]:$this->comentario);
       $this->email = ($this->email == ""?@$GLOBALS["HTTP_POST_VARS"]["email"]:$this->email);
       $this->receb_noticias = ($this->receb_noticias == ""?@$GLOBALS["HTTP_POST_VARS"]["receb_noticias"]:$this->receb_noticias);
       if($this->data == ""){
         $this->data_dia = @$GLOBALS["HTTP_POST_VARS"]["data_dia"];
         $this->data_mes = @$GLOBALS["HTTP_POST_VARS"]["data_mes"];
         $this->data_ano = @$GLOBALS["HTTP_POST_VARS"]["data_ano"];
         if($this->data_dia != ""){
            $this->data = $this->data_ano."-".$this->data_mes."-".$this->data_dia;
         }
       }
       if($this->revisado == ""){
         $this->revisado_dia = @$GLOBALS["HTTP_POST_VARS"]["revisado_dia"];
         $this->revisado_mes = @$GLOBALS["HTTP_POST_VARS"]["revisado_mes"];
         $this->revisado_ano = @$GLOBALS["HTTP_POST_VARS"]["revisado_ano"];
         if($this->revisado_dia != ""){
            $this->revisado = $this->revisado_ano."-".$this->revisado_mes."-".$this->revisado_dia;
         }
       }
       $this->login = ($this->login == ""?@$GLOBALS["HTTP_POST_VARS"]["login"]:$this->login);
       $this->texto = ($this->texto == ""?@$GLOBALS["HTTP_POST_VARS"]["texto"]:$this->texto);
       $this->id_ouvidoria = ($this->id_ouvidoria == ""?@$GLOBALS["HTTP_POST_VARS"]["id_ouvidoria"]:$this->id_ouvidoria);
     }else{
       $this->id_ouvidoria = ($this->id_ouvidoria == ""?@$GLOBALS["HTTP_POST_VARS"]["id_ouvidoria"]:$this->id_ouvidoria);
     }
   }
   // funcao para inclusao
   function incluir ($id_ouvidoria){ 
      $this->atualizacampos();
     if($this->tipo == null ){ 
       $this->erro_sql = " Campo Tipo nao Informado.";
       $this->erro_campo = "tipo";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->comentario == null ){ 
       $this->erro_sql = " Campo Comentário nao Informado.";
       $this->erro_campo = "comentario";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->email == null ){ 
       $this->erro_sql = " Campo email nao Informado.";
       $this->erro_campo = "email";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->receb_noticias == null ){ 
       $this->erro_sql = " Campo Notícias nao Informado.";
       $this->erro_campo = "receb_noticias";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->data == null ){ 
       $this->erro_sql = " Campo data nao Informado.";
       $this->erro_campo = "data_dia";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->revisado == null ){ 
       $this->erro_sql = " Campo Revisado nao Informado.";
       $this->erro_campo = "revisado_dia";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->login == null ){ 
       $this->erro_sql = " Campo login do usuario nao Informado.";
       $this->erro_campo = "login";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->texto == null ){ 
       $this->erro_sql = " Campo texto nao Informado.";
       $this->erro_campo = "texto";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($id_ouvidoria == "" || $id_ouvidoria == null ){
       $result = @pg_query("select nextval('db_ouvidoria_id_ouvidoria_seq')"); 
       if($result==false){
         $this->erro_banco = str_replace("\n","",@pg_last_error());
         $this->erro_sql   = "Verifique o cadastro da sequencia: db_ouvidoria_id_ouvidoria_seq do campo: id_ouvidoria"; 
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false; 
       }
       $this->id_ouvidoria = pg_result($result,0,0); 
     }else{
       $result = @pg_query("select last_value from db_ouvidoria_id_ouvidoria_seq");
       if(($result != false) && (pg_result($result,0,0) < $id_ouvidoria)){
         $this->erro_sql = " Campo id_ouvidoria maior que último número da sequencia.";
         $this->erro_banco = "Sequencia menor que este número.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }else{
         $this->id_ouvidoria = $id_ouvidoria; 
       }
     }
     if(($this->id_ouvidoria == null) || ($this->id_ouvidoria == "") ){ 
       $this->erro_sql = " Campo id_ouvidoria nao declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $result = @pg_query("insert into db_ouvidoria(
                                       tipo 
                                      ,comentario 
                                      ,email 
                                      ,receb_noticias 
                                      ,data 
                                      ,revisado 
                                      ,login 
                                      ,texto 
                                      ,id_ouvidoria 
                       )
                values (
                                '$this->tipo' 
                               ,'$this->comentario' 
                               ,'$this->email' 
                               ,'$this->receb_noticias' 
                               ,".($this->data == "null" || $this->data == ""?"null":"'".$this->data."'")." 
                               ,".($this->revisado == "null" || $this->revisado == ""?"null":"'".$this->revisado."'")." 
                               ,'$this->login' 
                               ,'$this->texto' 
                               ,$this->id_ouvidoria 
                      )");
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "Ouvidoria ($this->id_ouvidoria) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "Ouvidoria já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "Ouvidoria ($this->id_ouvidoria) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusao Efetivada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->id_ouvidoria;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $resaco = $this->sql_record($this->sql_query_file($this->id_ouvidoria));
     if(($resaco!=false)||($this->numrows!=0)){
       $resac = pg_query("select nextval('db_acount_id_acount_seq') as acount");
       $acount = pg_result($resac,0,0);
       $resac = pg_query("insert into db_acountkey values($acount,1077,'$this->id_ouvidoria','I')");
       $resac = pg_query("insert into db_acount values($acount,188,1073,'','".pg_result($resaco,0,'tipo')."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = pg_query("insert into db_acount values($acount,188,1074,'','".pg_result($resaco,0,'comentario')."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = pg_query("insert into db_acount values($acount,188,574,'','".pg_result($resaco,0,'email')."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = pg_query("insert into db_acount values($acount,188,1075,'','".pg_result($resaco,0,'receb_noticias')."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = pg_query("insert into db_acount values($acount,188,566,'','".pg_result($resaco,0,'data')."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = pg_query("insert into db_acount values($acount,188,1076,'','".pg_result($resaco,0,'revisado')."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = pg_query("insert into db_acount values($acount,188,571,'','".pg_result($resaco,0,'login')."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = pg_query("insert into db_acount values($acount,188,1069,'','".pg_result($resaco,0,'texto')."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = pg_query("insert into db_acount values($acount,188,1077,'','".pg_result($resaco,0,'id_ouvidoria')."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
     }
     return true;
   } 
   // funcao para alteracao
   function alterar ($id_ouvidoria=null) { 
      $this->atualizacampos();
     $sql = " update db_ouvidoria set ";
     $virgula = "";
     if($this->tipo!="" || isset($GLOBALS["HTTP_POST_VARS"]["tipo"])){ 
       $sql  .= $virgula." tipo = '$this->tipo' ";
       $virgula = ",";
       if($this->tipo == null ){ 
         $this->erro_sql = " Campo Tipo nao Informado.";
         $this->erro_campo = "tipo";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if($this->comentario!="" || isset($GLOBALS["HTTP_POST_VARS"]["comentario"])){ 
       $sql  .= $virgula." comentario = '$this->comentario' ";
       $virgula = ",";
       if($this->comentario == null ){ 
         $this->erro_sql = " Campo Comentário nao Informado.";
         $this->erro_campo = "comentario";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if($this->email!="" || isset($GLOBALS["HTTP_POST_VARS"]["email"])){ 
       $sql  .= $virgula." email = '$this->email' ";
       $virgula = ",";
       if($this->email == null ){ 
         $this->erro_sql = " Campo email nao Informado.";
         $this->erro_campo = "email";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if($this->receb_noticias!="" || isset($GLOBALS["HTTP_POST_VARS"]["receb_noticias"])){ 
       $sql  .= $virgula." receb_noticias = '$this->receb_noticias' ";
       $virgula = ",";
       if($this->receb_noticias == null ){ 
         $this->erro_sql = " Campo Notícias nao Informado.";
         $this->erro_campo = "receb_noticias";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if($this->data!="" || isset($GLOBALS["HTTP_POST_VARS"]["data_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["data_dia"] !="") ){ 
       $sql  .= $virgula." data = '$this->data' ";
       $virgula = ",";
       if($this->data == null ){ 
         $this->erro_sql = " Campo data nao Informado.";
         $this->erro_campo = "data_dia";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }     else{ 
       if($this->data!="" || isset($GLOBALS["HTTP_POST_VARS"]["data"])){ 
         $sql  .= $virgula." data = null ";
         $virgula = ",";
         if($this->data == null ){ 
           $this->erro_sql = " Campo data nao Informado.";
           $this->erro_campo = "data_dia";
           $this->erro_banco = "";
           $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
           $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
           $this->erro_status = "0";
           return false;
         }
       }
     }
     if($this->revisado!="" || isset($GLOBALS["HTTP_POST_VARS"]["revisado_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["revisado_dia"] !="") ){ 
       $sql  .= $virgula." revisado = '$this->revisado' ";
       $virgula = ",";
       if($this->revisado == null ){ 
         $this->erro_sql = " Campo Revisado nao Informado.";
         $this->erro_campo = "revisado_dia";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }     else{ 
       if($this->revisado!="" || isset($GLOBALS["HTTP_POST_VARS"]["revisado"])){ 
         $sql  .= $virgula." revisado = null ";
         $virgula = ",";
         if($this->revisado == null ){ 
           $this->erro_sql = " Campo Revisado nao Informado.";
           $this->erro_campo = "revisado_dia";
           $this->erro_banco = "";
           $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
           $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
           $this->erro_status = "0";
           return false;
         }
       }
     }
     if($this->login!="" || isset($GLOBALS["HTTP_POST_VARS"]["login"])){ 
       $sql  .= $virgula." login = '$this->login' ";
       $virgula = ",";
       if($this->login == null ){ 
         $this->erro_sql = " Campo login do usuario nao Informado.";
         $this->erro_campo = "login";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if($this->texto!="" || isset($GLOBALS["HTTP_POST_VARS"]["texto"])){ 
       $sql  .= $virgula." texto = '$this->texto' ";
       $virgula = ",";
       if($this->texto == null ){ 
         $this->erro_sql = " Campo texto nao Informado.";
         $this->erro_campo = "texto";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if($this->id_ouvidoria!="" || isset($GLOBALS["HTTP_POST_VARS"]["id_ouvidoria"])){ 
       $sql  .= $virgula." id_ouvidoria = $this->id_ouvidoria ";
       $virgula = ",";
       if($this->id_ouvidoria == null ){ 
         $this->erro_sql = " Campo Ouvidoria nao Informado.";
         $this->erro_campo = "id_ouvidoria";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     $sql .= " where  id_ouvidoria = $this->id_ouvidoria
";
     $resaco = $this->sql_record($this->sql_query_file($this->id_ouvidoria));
     if($this->numrows>0){       $resac = pg_query("select nextval('db_acount_id_acount_seq') as acount");
       $acount = pg_result($resac,0,0);
       $resac = pg_query("insert into db_acountkey values($acount,1077,'$this->id_ouvidoria','A')");
       if(isset($GLOBALS["HTTP_POST_VARS"]["tipo"]))
         $resac = pg_query("insert into db_acount values($acount,188,1073,'".pg_result($resaco,0,'tipo')."','$this->tipo',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       if(isset($GLOBALS["HTTP_POST_VARS"]["comentario"]))
         $resac = pg_query("insert into db_acount values($acount,188,1074,'".pg_result($resaco,0,'comentario')."','$this->comentario',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       if(isset($GLOBALS["HTTP_POST_VARS"]["email"]))
         $resac = pg_query("insert into db_acount values($acount,188,574,'".pg_result($resaco,0,'email')."','$this->email',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       if(isset($GLOBALS["HTTP_POST_VARS"]["receb_noticias"]))
         $resac = pg_query("insert into db_acount values($acount,188,1075,'".pg_result($resaco,0,'receb_noticias')."','$this->receb_noticias',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       if(isset($GLOBALS["HTTP_POST_VARS"]["data"]))
         $resac = pg_query("insert into db_acount values($acount,188,566,'".pg_result($resaco,0,'data')."','$this->data',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       if(isset($GLOBALS["HTTP_POST_VARS"]["revisado"]))
         $resac = pg_query("insert into db_acount values($acount,188,1076,'".pg_result($resaco,0,'revisado')."','$this->revisado',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       if(isset($GLOBALS["HTTP_POST_VARS"]["login"]))
         $resac = pg_query("insert into db_acount values($acount,188,571,'".pg_result($resaco,0,'login')."','$this->login',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       if(isset($GLOBALS["HTTP_POST_VARS"]["texto"]))
         $resac = pg_query("insert into db_acount values($acount,188,1069,'".pg_result($resaco,0,'texto')."','$this->texto',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       if(isset($GLOBALS["HTTP_POST_VARS"]["id_ouvidoria"]))
         $resac = pg_query("insert into db_acount values($acount,188,1077,'".pg_result($resaco,0,'id_ouvidoria')."','$this->id_ouvidoria',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
     }
     $result = @pg_exec($sql);
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Ouvidoria nao Alterado. Alteracao Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->id_ouvidoria;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "Ouvidoria nao foi Alterado. Alteracao Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->id_ouvidoria;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Alteração Efetivada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->id_ouvidoria;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         return true;
       } 
     } 
   } 
   // funcao para exclusao 
   function excluir ($id_ouvidoria=null) { 
     $this->atualizacampos(true);
     $resaco = $this->sql_record($this->sql_query_file($this->id_ouvidoria));
     if(($resaco!=false)||($this->numrows!=0)){
       $resac = pg_query("select nextval('db_acount_id_acount_seq') as acount");
       $acount = pg_result($resac,0,0);
       $resac = pg_query("insert into db_acountkey values($acount,1077,'$this->id_ouvidoria','E')");
       $resac = pg_query("insert into db_acount values($acount,188,1073,'','".pg_result($resaco,0,'tipo')."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = pg_query("insert into db_acount values($acount,188,1074,'','".pg_result($resaco,0,'comentario')."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = pg_query("insert into db_acount values($acount,188,574,'','".pg_result($resaco,0,'email')."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = pg_query("insert into db_acount values($acount,188,1075,'','".pg_result($resaco,0,'receb_noticias')."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = pg_query("insert into db_acount values($acount,188,566,'','".pg_result($resaco,0,'data')."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = pg_query("insert into db_acount values($acount,188,1076,'','".pg_result($resaco,0,'revisado')."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = pg_query("insert into db_acount values($acount,188,571,'','".pg_result($resaco,0,'login')."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = pg_query("insert into db_acount values($acount,188,1069,'','".pg_result($resaco,0,'texto')."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = pg_query("insert into db_acount values($acount,188,1077,'','".pg_result($resaco,0,'id_ouvidoria')."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
     }
     $sql = " delete from db_ouvidoria
                    where ";
     $sql2 = "";
      if($this->id_ouvidoria != ""){
      if($sql2!=""){
        $sql2 .= " and ";
      }
      $sql2 .= " id_ouvidoria = $this->id_ouvidoria ";
}
     $result = @pg_exec($sql.$sql2);
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Ouvidoria nao Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$this->id_ouvidoria;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "Ouvidoria nao Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$this->id_ouvidoria;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão Efetivada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->id_ouvidoria;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         return true;
       } 
     } 
   } 
   // funcao do recordset 
   function sql_record($sql) { 
     $result = @pg_query($sql);
     if($result==false){
       $this->numrows    = 0;
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Erro ao selecionar os registros.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $this->numrows = pg_numrows($result);
      if($this->numrows==0){
        $this->erro_banco = "";
        $this->erro_sql   = "Dados do Grupo nao Encontrado";
        $this->erro_msg   = "Usuário: \n\n ".$this->erro_sql." \n\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }
   // funcao do sql 
   function sql_query ( $id_ouvidoria=null,$campos="*",$ordem=null,$dbwhere=""){ 
     $sql = "select ";
     if($campos != "*" ){
       $campos_sql = split("#",$campos);
       $virgula = "";
       for($i=0;$i<sizeof($campos_sql);$i++){
         $sql .= $virgula.$campos_sql[$i];
         $virgula = ",";
       }
     }else{
       $sql .= $campos;
     }
     $sql .= " from db_ouvidoria ";
     $sql2 = "";
     if($dbwhere==""){
       if($id_ouvidoria!=null ){
         $sql2 .= " where db_ouvidoria.id_ouvidoria = $id_ouvidoria "; 
       } 
     }else if($dbwhere != ""){
       $sql2 = " where $dbwhere";
     }
     $sql .= $sql2;
     if($ordem != null ){
       $sql .= " order by ";
       $campos_sql = split("#",$ordem);
       $virgula = "";
       for($i=0;$i<sizeof($campos_sql);$i++){
         $sql .= $virgula.$campos_sql[$i];
         $virgula = ",";
       }
     }
     return $sql;
  }
   // funcao do sql 
   function sql_query_file ( $id_ouvidoria=null,$campos="*",$ordem=null,$dbwhere=""){ 
     $sql = "select ";
     if($campos != "*" ){
       $campos_sql = split("#",$campos);
       $virgula = "";
       for($i=0;$i<sizeof($campos_sql);$i++){
         $sql .= $virgula.$campos_sql[$i];
         $virgula = ",";
       }
     }else{
       $sql .= $campos;
     }
     $sql .= " from db_ouvidoria ";
     $sql2 = "";
     if($dbwhere==""){
       if($id_ouvidoria!=null ){
         $sql2 .= " where db_ouvidoria.id_ouvidoria = $id_ouvidoria "; 
       } 
     }else if($dbwhere != ""){
       $sql2 = " where $dbwhere";
     }
     $sql .= $sql2;
     if($ordem != null ){
       $sql .= " order by ";
       $campos_sql = split("#",$ordem);
       $virgula = "";
       for($i=0;$i<sizeof($campos_sql);$i++){
         $sql .= $virgula.$campos_sql[$i];
         $virgula = ",";
       }
     }
     return $sql;
  }
}
?>