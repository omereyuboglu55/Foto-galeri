<?php
/**
* Dizin işlemlerini yürüten class
*
* @author Mehmet Şamlı
*/
class DirectoryReader
{
	private $dh;
	/**
   * işlem yapacak dizin bilgisi
   *
   * @var string
   */
   private $basedir;
   /**
   * dizin içerisinde yar alan dosya isimlerini tutar
   *
   * @var Array
   */
   private $fileNameArray=array();
   /**
   * dizin içerisinde yer alan klasör isimlerini  tutar
   *
   * @var Array
   */
   private $dirNameArray=array();
   /**
   * construc metodu
   *
   * @return void
   */
   public function __construct() {
   }
   /**
   * işlem yapılacak dizin bilgisi set edilir
   *
   * @param string dirname
   * @return void
   */
   public function setDir($dirname){
   	try{
   		$this->basedir =$dirname;
   		if(!$this->dh = @dir($dirname)) 
   			throw new Exception('Dizini Açamadım',1);
   		self::parseDirectory();
   	}catch (Exception $error){
   		trigger_error($error->getmessage(),E_USER_ERROR);
   	}
   }
   /**
   * belirtilen dizin içerisindeki  klasör  ve dosyaları $fileNameArray ile $dirNameArray sabit  değişkenlerine push eder
   *
   * @return void
   */
   private function parseDirectory(){
   	$filename = '';
   	while(false !== ($filename = $this->dh->read())) {
   		$fullpath = $this->basedir . '/' .$filename;
   		if(is_file($fullpath)) {
   			array_push($this->fileNameArray, $filename);
   		}else {
   			array_push($this->dirNameArray, $filename);
   		}
   	}
   }
   /**
   * destruct metodu
   *
   * @return void
   */
   public function __destruct(){
     	$this->dh->close();
   }
   /**
   * dizin ve dosya isimlerini sıralama yapar
   *
   * @param string $sort
   * @param array $array
   * @return array
   */
   private function sort($sort,Array $array){
   	switch($sort) {
   		case 'a':
   			sort($array);
   			break;
   		case 'z':
   			rsort($array);
   			break;
   	}
   	return (Array)$array;
   }
   /**
   * Belirtilen dizin içerisindeki dosyaları listeler
   *
   * @return array
   */
   public function getFileList($sort=null) {
   	return self::sort($sort,$this->fileNameArray);
   }
   /**
   * belirtilen dizin içerisindeki klasörleri listeler
   *
   * @return array 
   */
   public function getDirList($sort=null){
   	return self::sort($sort,$this->dirNameArray);
   }
   /**
   * dizin bilgisini get eder
   *
   * @return string
   */
   public function getDirPath(){
   	return $this->dh->path;
   }
}
?> 