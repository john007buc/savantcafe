<?php
namespace John\SavantBundle\MyClasses;
use Symfony\Component\HttpFoundation\Session\Session;
class Captcha
{

    public static function generate($sess)
    {
        //header("Content-type: image/jpg");
        //header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Some time in the past
       // header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
       // header("Cache-Control: no-store, no-cache, must-revalidate");
       // header("Cache-Control: post-check=0, pre-check=0", false);
       // header("Pragma: no-cache");
        $text=rand(1000,9999);
        //Session::start();
        //Session::set("secure_code",$text);
        /*Cand se folosesc sesiuni*/
        //session_start();
        //$_SESSION['secure']=rand(1000,9999);



        /*$session = new Session();
        $session->start();
        $session->set("secure_code",$text);*/
        $sess->set("secure_code",$text);

       $font="fonts/verdana.ttf";
       // ../web/fonts/verdana.ttf
       // $font='../web/fonts/verdana.ttf';

        $width=130;
        $height=50;
        $font_size=30;
        $letter_width=imagefontwidth($font_size);
        $img=imagecreate($letter_width*4*3,$height);
        imagecolorallocate($img,255,255,255);
        $foreground=imagecolorallocate($img,0,0,0);
        $letters=str_split($text,1);
        $i=0;
        foreach($letters as $letter)
        {
            if($i==0){
                $random_orientation=-5;
            }else{
                $random_orientation=rand(-10,20);
            }

            $x_pos=$letter_width*3*$i;
            imagettftext($img,$font_size, $random_orientation,$x_pos,40,$foreground,$font,$letter);
            $i++;
        }
        //generare de imagini
        for($x=1;$x<=40;$x++)
        {
            $x1=rand(1,100);
            $x2=rand(1,100);
            $y1=rand(1,100);
            $y2=rand(1,100);
            imageline($img,$x1,$y1,$x2,$y2,$foreground);
        }
        return $img;
      //imagejpeg($img);
        //put echo in front if it doesn't work
        //echo dirname(__FILE__);
    }
}