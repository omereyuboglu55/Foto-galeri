<?php
/**
 * Created by JetBrains PhpStorm.
 * User: sametatabasch
 * Date: 14.07.2013
 * Time: 03:16
 * To change this template use File | Settings | File Templates.
 */

class fotoGaleri
{
    function __construct(){
        $this->resimbelirle();
    }
    /*
     * Resimlerin tutulduğu dizi
     *
     * Array
     */
    public $resimler=array();
    /*
     * Belirlenen resmin uzantısı
     *
     * String
     */
    private  $uzanti;
    /*
     * İzin verilen uzuntıların saklandığı dizi
     *
     * Array
     */
    private $gecerliUzantilar=array('jpg','png','jpeg','JPG');
    /*
     * İmages dizinindeki 1-1, 2-1, 2-2 şeklinde isimlendirilmiş resimleri $resimler dizisine atar
     *
     * @return Array
     */
    private function resimbelirle()
    {
        include('dizinislemleri.class.php');
        $Directory = new DirectoryReader();
        $Directory->setDir(__DIR__ . '/images');
        foreach ($Directory->getFileList('a') as $dosya) {
            $this->uzanti = end(explode('.', $dosya));
            $isim = current(explode('.', $dosya, -1));
            if (in_array($this->uzanti,$this->gecerliUzantilar)) {
                $grup = current(explode('-', $isim));
                if (!is_array($resimler[$grup])) $resimler[$grup] = array();
                $this->resimler[$grup][] = $isim . '.' . $this->uzanti;
            }
        }
        return $resimler;
    }
}