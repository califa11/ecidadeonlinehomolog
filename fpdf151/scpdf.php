<?
set_time_limit(0);
session_cache_limiter('none');
if ( session_id() == null ) 
   session_start();

if(!defined('DB_BIBLIOT')){
  include_once("libs/db_stdlib.php");
  db_postmemory($HTTP_POST_VARS);
  db_postmemory($HTTP_SERVER_VARS);
  define('FPDF_FONTPATH','fpdf151/font/');
  require('fpdf151/fpdf.php');
}

class scpdf extends fpdf {
//|00|//scpdf
//|10|//Esta classe é uma extensão da classe |fpdf|, não possui cabeçalho ou rodapé, é classe utilizada
//|10|//na geração de formularios tais como: carnês de parcelamento, recibos, alvarás, etc
function _Arc($x1, $y1, $x2, $y2, $x3, $y3)
   {
   $h = $this->h;
   $this->_out(sprintf('%.2f %.2f %.2f %.2f %.2f %.2f c ', $x1*$this->k, ($h-$y1)*$this->k,
   $x2*$this->k, ($h-$y2)*$this->k, $x3*$this->k, ($h-$y3)*$this->k));
   }
   
function RotatedText($x, $y, $txt, $angle)
	{
		//Text rotated around its origin
		$this->Rotate($angle,$x,$y);
		$this->Text($x,$y,$txt);
		$this->Rotate(0);
	}
	
	function Rotate($angle,$x=-1,$y=-1) { 

        if($x==-1) 
            $x=$this->x; 
        if($y==-1) 
            $y=$this->y; 
        if($this->angle!=0) 
            $this->_out('Q'); 
        $this->angle=$angle; 
        if($angle!=0) 

        { 
            $angle*=M_PI/180; 
            $c=cos($angle); 
            $s=sin($angle); 
            $cx=$x*$this->k; 
            $cy=($this->h-$y)*$this->k; 
             
            $this->_out(sprintf('q %.5f %.5f %.5f %.5f %.2f %.2f cm 1 0 0 1 %.2f %.2f cm',$c,$s,-$s,$c,$cx,$cy,-$cx,-$cy)); 
        } 
    } 
   
}
//|XX|//
?>
