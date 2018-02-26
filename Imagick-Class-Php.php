<?php
ob_start(); // Bunuda ne yaparsanız yapın
/*
 * KRYZ @ 2012
 * 
 * 
 *
*/
/*
 *  TODO Listem 
 *  
 *  Hatalı noktalar aranıyor, fark eder etmez düzelteceğim.
 *  Hata toplayıcı ve bu hataları loglıyacak sistem eklenecek - belki mailliyecek
 *  
 *  Kontrol Kodları Eklenecek
 *  Resim Döndürme Eklenecek
 *      
 *  P-i-P eklenecek, picture in picture
 *  Imagick İçin DPI seçeneği eklenecek
 *  Imagick İçin blur,noise gibi var olan filtreler eklenecek
 *  GMagick Kütüphanesi eklenecek
 *  Imagick Kütüphanesinde otomatik gif oluşturan kod eklenecek 
 *  Unuttuklarım varsa hatırlatın işte
* 	
*
*   Bu Nokta PHP.net Sitesinden alınmıştır. 
*   http://www.imagemagick.org/Usage/resize/
* LANCZOSFILTER_POINT took: 0.334532976151 seconds
* FILTER_BOX took: 0.777871131897 seconds
* FILTER_TRIANGLE took: 1.3695909977 seconds
* FILTER_HERMITE took: 1.35866093636 seconds
* FILTER_HANNING took: 4.88722896576 seconds
* FILTER_HAMMING took: 4.88665103912 seconds
* FILTER_BLACKMAN took: 4.89026689529 seconds
* FILTER_GAUSSIAN took: 1.93553304672 seconds
* FILTER_QUADRATIC took: 1.93322920799 seconds
* FILTER_CUBIC took: 2.58396601677 seconds
* FILTER_CATROM took: 2.58508896828 seconds
* FILTER_MITCHELL took: 2.58368492126 seconds
* FILTER_LANCZOS took: 3.74232912064 seconds
* FILTER_BESSEL took: 4.03305602074 seconds
* FILTER_SINC took: 4.90098690987 seconds
* PHP.NET ten alınmıştır.
*
*
*/
// namespace kernel; // TODO Ne yazıkki eskileri kapsamalıyım. :( 

class Image {

		public $classc;
		public $tip;
		public $width;
		public $height;
		public $resimAdi;
		public $dpi;
		public $tumKutuphane=array(
								array("oncelik"=>"1","kutuphaneAdi"=>"gd","aciklama"=>"Standart PHP Resim Kütüphanesi"),
								array("oncelik"=>"2","kutuphaneAdi"=>"imagick","aciklama"=>"ImageMagick Resim Kütüphanesi"),
	/*TODO For Now Not Supported */	array("oncelik"=>"3","kutuphaneAdi"=>"gmagick","aciklama"=>"Gmagick Resim Kütüphanesi"),
								);
		
		
		public $filterImagick=array("Imagick::FILTER_LANCZOS","Imagick::LANCZOSFILTER_POINT","Imagick::FILTER_BOX",
									"Imagick::FILTER_TRIANGLE","Imagick::FILTER_HERMITE","Imagick::FILTER_HANNING",
									"Imagick::FILTER_BLACKMAN","Imagick::FILTER_GAUSSIAN","Imagick::FILTER_QUADRATIC",
									"Imagick::FILTER_CUBIC","Imagick::FILTER_CATROM","Imagick::FILTER_MITCHELL",
									"Imagick::FILTER_BESSEL","Imagick::FILTER_SINC");

		public $standartFilter=8; //0 YOU CAN WHAT YOU WANT BEBEĞİM benim. hehehe kopyala yapıştır sandınız dimi :P Resme ait filtreyi seçer.Bu Yavaş ama daha nazik miş . 
		
		public $secilenKutuphane;
		public $toplayiciClass;
		
		
		
	function __construct() // TESTED CONFIRMED
	{
		//print_r($this->tumKutuphane);
		if (extension_loaded($this->tumKutuphane["1"]["kutuphaneAdi"]))
		{
			$this->classc="Imagick";
			
			//$this->classc="Gd"; /* Test 1 -*2 -3*/
			
			$this->toplayiciClass=new Imagick();
		}
		else
		{
			$this->classc="Gd";
			$this->toplayiciClass="GEREKLİ DEĞİL // NOT NECESSARY";
		}
	}
	
	/* TR: Bu fonksiyon  Kümesi daha sonrası için bilgi toplayacak kümededir
	 * EN: This functions get Information about image 
	 *  MAIN CLASSES for background;
	 */
	
	function getInfGd($_source) // TESTED CONFIRMED
	{
		$info=getimagesize($_source);
		$this->tip=$info['mime'];
		$this->height=$info[1];
		$this->width=$info[0];
	}
	
	function getInfImagick($_source) // TESTED CONFIRMED
	{
		$res=$this->toplayiciClass;
		$res->readimage($_source);
		
		$this->tip=$res->getImageFormat();
		$this->width=$res->getimagewidth();
		$this->height=$res->getimageheight();	
		$res->destroy();
	}
	
	
	function reSizeGd($_source,$_width,$_height) // TESTED CONFIRMED
	{
		$res="";
		$tmp="";

		switch ($this->tip)
		{
			case 'image/jpeg':
				
				$res=imagecreatefromjpeg($_source);
				$resimCikis = 'imagejpeg';
				
			break;
			case 'image/png':
				
				$res=imagecreatefrompng($_source);
				
				$resimCikis = 'imagepng';
				
				ImageAlphaBlending($res,true);
				ImageSaveAlpha($res,true);
								
			break;
			case 'image/gif':

				$res=imagecreatefromgif($_source);
				$resimCikis = 'imagegif';

			break;
		}


		$tmp=imagecreatetruecolor($_width, $_height);
		imagecopyresized($tmp,$res,0,0,0,0,$_width,$_height,$this->width, $this->height);
		
		
		$resimCikis($tmp); // Functions biliyorsun kardaş açım. Alah seni inandırsın Starbucks ada bak a.q bir kahve ısmarlarsan birde şu bordo cheesecake tan ne isteirm başka
		$imageFile = ob_get_contents();
		
		imagedestroy($res);
		imageDestroy($tmp);
		ob_end_clean();
	
		
		return $imageFile;
	
	}
	
	
	function reSizeImagick($_source,$_width,$_height) // TESTED CONFIRMED
	{

		$res=$this->toplayiciClass;
		$res->readimage($_source);

		$res->resizeimage($_width,$_height,(int)$this->filterImagick[$this->standartFilter],1); // Daha nazik olması için standart Blur ekledim.
		
		return $res;
		$res->destroy(); // SET ME FREEEEEE bayılırım bu parçaya.  Birde Nickelback denen grup var dinleyin klasik a.q önerin varsa yukarıdan bul beni ;)
	}
	
	
	function cropImageGd($_source,$_width,$_height,$_x=0,$_y=0) // TESTED CONFIRMED
	{
		
	$res="";
	$tmp="";
	
	if (($this->width<($_width+$_x))) {$_x=0;} // Sabitler çünkü o gerekli
	if (($this->height<($_height+$_y))) {$_y=0;} // Sabitler
	
	if ($this->width<$_width){$_width=100;}
	if ($this->height<$_height){$_height=100;}

		switch ($this->tip)
		{
			case 'image/jpeg':
		
				$res=imagecreatefromjpeg($_source);
				$resimCikis = 'imagejpeg';
		
				break;
			case 'image/png':
		
				$res=imagecreatefrompng($_source);
		
				$resimCikis = 'imagepng';
		
				ImageAlphaBlending($res,true);
				ImageSaveAlpha($res,true);
		
				break;
			case 'image/gif':
		
				$res=imagecreatefromgif($_source);
				$resimCikis = 'imagegif';
		
				break;
		}
		
		//echo $_x;
		$tmp=imagecreatetruecolor($_width, $_height);
		imagecopy($tmp,$res,0,0,$_x,$_y,$_width,$_height);
		
		//
		
		$resimCikis($tmp); 
		$imageFile = ob_get_contents();
		
		
		return $imageFile;
		
		imagedestroy($res);
		imageDestroy($tmp);
		
		ob_end_clean();
		
	}
	
	function cropImageImagick($_source,$_width,$_height,$_x,$_y) // TESTED CONFIRMED
	{
	
		$res=$this->toplayiciClass;
		$res->readimage($_source);
	
		
		if ($this->tip=='GIF')
		{
			foreach($res as $frame) {$res->cropImage($_width, $_height, $_x, $_y);}
		}else
		{
			$res->cropImage($_width, $_height, $_x, $_y);
		}
		
		return $res;
		$res->destroy();
		ob_end_clean();
		
	}
	
	
	function setImageTextGd($_source,$_text,$_x=0,$_y=0,$_color='black',$_dondur=0,$_size=12,$_font='arial.ttf')
	{
		
			
		
		$res="";
		$tmp="";

		
		$resimTop=14;
		$resimLeft=2;
		
		$resimCenter=ceil(($this->width-ceil(strlen($_text)/1.65*$_size))/2);
		
		$resimMiddle=ceil(($this->height-ceil(strlen($_size)/2))/2);
		$resimRight=$this->width-ceil(strlen($_text)/1.65*$_size);
		
		$resimBottom=$this->height-ceil($_size/2);
		
		
		
		if ($_x==="right"){$_x=$resimRight;}
		if ($_x==="center"){$_x=$resimCenter;}
		if ($_x==="left"){$_x=$resimLeft;}
		
		
		if ($_y==="top"){$_y=$resimTop;}
		if ($_y==="middle"){$_y=$resimMiddle;}
		if ($_y==="bottom"){$_y=$resimBottom;}
		
		
		
		ob_start();
		
		if (is_file($_source))
		{
			$func="imagecreatefrom";
			$t=1;
		}else
		{	
			$func="imagecreatefromstring";$t=0;
			//$res=imagecreatefromstring($_source);
		}
		
		switch ($this->tip)
		{
			case 'image/jpeg':
				$resimCikis = 'imagejpeg';
				if ($t>0) {$func=$func."jpeg";}
				$res=$func($_source);
			break;
			case 'image/png':
				$resimCikis = 'imagepng';
				if ($t>0) {$func=$func."png";}
				$res=$func($_source);
				ImageAlphaBlending($res,true);
				ImageSaveAlpha($res,true);		
				break;
			case 'image/gif':
				if ($t>0) {$func=$func."gif";}
				$res=$func($_source);
				$resimCikis = 'imagegif';
				break;
		}

		
		$_font = 'arial.ttf';
		$_color = imagecolorallocate($res, 0, 0, 0);

		imagettftext($res, $_size, $_dondur, $_x, $_y, $_color, $_font, $_text);
		
		$resimCikis($res);
		//ob_end_clean();
		$imageFile= ob_get_contents();
		
		ob_clean();
		
		imagedestroy($res);
		return $imageFile;		
		
	}
	
	function setImageTextImagick($_source,$_text,$_x=0,$_y=0,$_color='#000000',$_dondur=0,$_size=12,$_font='arial.ttf')
	{
		
		$res=$this->toplayiciClass;
		
		if (is_file($_source))
		{
			$res->readimage($_source);
		}else
		{
			$res->readimageblob($_source);
		}

		
		$ciziktir = new ImagickDraw();
		$ciziktir->setFillColor($_color);
		$ciziktir->setFont($_font);
		$ciziktir->setFontSize($_size);
		$resimTop=14;
		$resimLeft=2;
		
		$resimCenter=ceil(($this->width-ceil(strlen($_text)/1.65*$_size))/2);
		
		$resimMiddle=ceil(($this->height-ceil(strlen($_size)/2))/2);
		$resimRight=$this->width-ceil(strlen($_text)/1.65*$_size);
		
		$resimBottom=$this->height-ceil($_size/2);
		
		
		
		if ($_x==="right"){$_x=$resimRight;}
		if ($_x==="center"){$_x=$resimCenter;}
		if ($_x==="left"){$_x=$resimLeft;}
		
		
		if ($_y==="top"){$_y=$resimTop;}
		if ($_y==="middle"){$_y=$resimMiddle;}
		if ($_y==="bottom"){$_y=$resimBottom;}
		
		
		$res->annotateImage($ciziktir, $_x, $_y, $_dondur, $_text);
		
		$ciziktir->clear();
		$ciziktir->destroy();
		
		return $res;
		
		
	}
	function reSizeWithRateGd($_source,$_maxWidth=120,$_maxHeight=120,$thumb='yes',$_background="#FFFFFF")
	{
		
		

		ob_start();
		
		if (is_file($_source))
		{
			$func="imagecreatefrom";
			$t=1;
		}else
		{	
			$func="imagecreatefromstring()";$t=0;
			//$res=imagecreatefromstring($_source);
		}
		
		switch ($this->tip)
		{
			case 'image/jpeg':
				$resimCikis = 'imagejpeg';
				if ($t>0) {$func=$func."jpeg";}
				$res=$func($_source);
			break;
			case 'image/png':
				$resimCikis = 'imagepng';
				if ($t>0) {$func=$func."png";}
				$res=$func($_source);
				ImageAlphaBlending($res,true);
				ImageSaveAlpha($res,true);		
				break;
			case 'image/gif':
				if ($t>0) {$func=$func."gif";}
				$res=$func($_source);
				$resimCikis = 'imagegif';
				break;
		}
		
		/*Bak işte burayı yeniden yazmadım. Ne var a.q bizde insanız. */
		
		
		$forCeilx = $this->width  / $_maxWidth;
		$forCeily = $this->width / $_maxHeight;
		
		
		$en    = $this->width;
		$boy   = $this->height;
		$max_en=$_maxWidth;
		$max_boy= $_maxHeight;
			
		$x_oran = $max_en  / $en;
		$y_oran = $max_boy / $boy;
		
		if (($en <= $max_en) and ($boy <= $max_boy)){
			$son_en  = $en;
			$son_boy = $boy;
		}
		else if (($x_oran * $boy) < $max_boy){
			$son_en  = $max_en;
			$son_boy = ceil($x_oran * $boy);
		}
		else {
			$son_en  = ceil($y_oran * $en);
			$son_boy = $max_boy;
		}
		
		
		 
		$araBoslukY=0;
		$araBoslukX=0;
		
		
		if ($thumb=='yes')
		{
			if ($son_boy<$_maxHeight)
			{
				$araBoslukY=floor(($_maxHeight-$son_boy)/2);
			}
			if ($son_en<$_maxWidth)
			{
				$araBoslukX=floor(($_maxWidth-$son_en)/2);
			}
		}
		else
		{
			$_maxHeight=$son_boy;
			$_maxWidth=$son_en;
		}
		
		$tmp=imagecreatetruecolor($_maxWidth,$_maxHeight); // image Pointer
		$_backgroundColor=imagecolorallocate($tmp,hexdec('0x' . $_background{1} . $_background{2}), hexdec('0x' . $_background{3} . $_background{4}), hexdec('0x' . $_background{5} . $_background{6}));
		imagefill($tmp,0,0,$_backgroundColor);
		
		imagecopyresampled($tmp,$res,$araBoslukX,$araBoslukY,0,0,$son_en,$son_boy,$this->width,$this->height);
		
		$resimCikis($tmp); // 
		$imageFile = ob_get_contents();
		
		imagedestroy($res);
		imageDestroy($tmp);
		ob_end_clean();
		
		
		return $imageFile;
		
		/* Bak işte burayı yeniden yazmadım. Ne var a.q bizde insanız. Yüzyıl önce düzelttiğimiz kod şimdi yazıyoz işte ;)*/
		
	}
	
	function reSizeWithRateImagick($_source,$_maxWidth=120,$_maxHeight=120,$thumb='yes',$_background="#FFFFFF")
	{
		
		$forCeilx = $this->width  / $_maxWidth;
		$forCeily = $this->width / $_maxHeight;
		
		
		$en    = $this->width;
		$boy   = $this->height;
		$max_en=$_maxWidth;
		$max_boy= $_maxHeight;
			
		$x_oran = $max_en  / $en;
		$y_oran = $max_boy / $boy;
		
		if (($en <= $max_en) and ($boy <= $max_boy)){
			$son_en  = $en;
			$son_boy = $boy;
		}
		else if (($x_oran * $boy) < $max_boy){
			$son_en  = $max_en;
			$son_boy = ceil($x_oran * $boy);
		}
		else {
			$son_en  = ceil($y_oran * $en);
			$son_boy = $max_boy;
		}
		
		
			
		$araBoslukY=0;
		$araBoslukX=0;
		
		
		if ($thumb=='yes')
		{
			if ($son_boy<$_maxHeight)
			{
				$araBoslukY=floor(($_maxHeight-$son_boy)/2);
			}
			if ($son_en<$_maxWidth)
			{
				$araBoslukX=floor(($_maxWidth-$son_en)/2);
			}
		}
		else
		{
			$_maxHeight=$son_boy;
			$_maxWidth=$son_en;
		}
			
		
		
		$ye=new Imagick();
		$ye->newimage($_maxWidth, $_maxHeight,  new ImagickPixel($_background));
		
		
		
		$res=$this->toplayiciClass;
		
		if (is_file($_source))
		{
			$res->readimage($_source);
		}else
		{
			$res->readimageblob($_source);
		}
		
		$res->resizeimage($son_en, $son_boy,(int)$this->filterImagick[$this->standartFilter],1);
		
		$ye->compositeImage($res, imagick::COMPOSITE_ATOP, $araBoslukX,$araBoslukY);
		
		$ye->setImageFormat($this->tip);

		return $ye;
			
	}
	
	
	public function resimoyna($_kaynak,$parametres=array("kontrol"=>"0"))
	{
		
		/* Sıkıyosa çevirin a.q  */
		$f="getInf".$this->classc;
		$this->$f($_kaynak);
		 
			switch ($parametres["prosess"])
			{
				case "resize":
					$f="reSize".$this->classc;
					$returner=$this->$f($_kaynak,$parametres["width"],$parametres["height"]);
				break;
				case "crop":
					$f="cropImage".$this->classc;					
					$returner=$this->$f($_kaynak,$parametres["width"],$parametres["height"],$parametres["cropx"],$parametres["cropy"]);
				break;
				case "rate":
					$f="reSizeWithRate".$this->classc;	
					$returner=$this->$f($_kaynak,$parametres["width"],$parametres["height"],$thumb='yes',$parametres["background"]);
				break;
			}
		
		if ($parametres["tonp"]=="yes")
		{
			
			$f="setImageText".$this->classc;
			$returner=$this->$f($returner,$parametres["text"],$parametres["textx"],$parametres["texty"],$parametres["textcolor"]); // İstersen döndürebilirsiniz, TIFF verebilirsiniz
			
		}		
		
		
		return $returner;
	}
	
	
	
}

/*
 * 
 * EN BASİT KULLANIM ÖRNEĞİ
header('Content-type: image/jpeg');

$a=new Image();

$parametres=array();


$parametres["width"]="300"; // integer Anla işte sayı
$parametres["height"]="250";  // integer Anla İşte Sayı iki

$parametres["prosess"]="resize"; // rateSize, reSize , CropResize
$parametres["prosess"]="crop"; // rateSize, reSize , CropResize
$parametres["prosess"]="rate"; // rateSize, reSize , CropResize

$parametres["cropx"]="200";
$parametres["cropy"]="200";



$parametres["tonp"]="yes"; // no | yes Text on Picture 
$parametres["text"]="http://www.eklenti.org"; // string
$parametres["textcolor"]="#000000"; //#FFFFFF
$parametres["textx"]="left"; // LEFT , RIGHT , CENTER , integer
$parametres["texty"]="middle"; /// BOTTOM, MIDDLE, TOP  , integer


$parametres["background"]="#FFFFFF"; //#FFFFFF 



echo $a->resimoyna("img/urun/buyuk/gumus-dugun-paketi_15.jpg",$parametres);
*/
?>