<?php
class Canvas {

    public static function statickFile($sticker,$type){
        $car_name = $sticker['car_name'];
        $sticker1 = self::defaultPathname($sticker['sticker'][1]);
        $sticker4 = self::defaultPathname($sticker['sticker'][4]);
        $sticker5 = self::defaultPathname($sticker['sticker'][5]);
        return APP_REAL_PATH."upload/sticker/${car_name}/${sticker1}/${sticker4}/${sticker5}/${type}.png";
    }

    private static function defaultPathname($string){
        return $string?$string:"empty";
    }

    public static function pairFile($sticker1,$sticker2,$type){
       
        $car_name = $sticker1['car_name']."_".$sticker2['car_name'];
        $sticker_1 = self::defaultPathname($sticker1['sticker'][1])."_".self::defaultPathname($sticker2['sticker'][1]);
        $sticker_4 = self::defaultPathname($sticker1['sticker'][4])."_".self::defaultPathname($sticker2['sticker'][4]);
        $sticker_5 = self::defaultPathname($sticker1['sticker'][5])."_".self::defaultPathname($sticker2['sticker'][5]);
        return APP_REAL_PATH."upload/pair/${car_name}/${sticker_1}/${sticker_4}/${sticker_5}/${type}.png";
    }
    
    public static function drawCommon3($sticker,$width,$height){
        $styles = array(
            "canvas"=>array("width"=>1543,"height"=>768),
            3=>array("width"=>1455,"height"=>898,"x"=>508,"y"=>-299),
            2=>array("width"=>1331,"height"=>822,"x"=>-336,"y"=>-245),
            1=>array("width"=>1700,"height"=>1050,"x"=>-129,"y"=>38)
        );
        $car = self::generate3CarImg($sticker,$styles);
        self::resize($car, $width, $height);
        return $car;
    }
    
    public static function drawPair($sticker1,$sticker2){
        $img1 = Canvas::drawCommon3($sticker1,508,261);
        $img2 = Canvas::drawCommon3($sticker2,508,261);
        $canvas = imagecreatetruecolor(508, 522);
        imagesavealpha($canvas, true);
        $white = imagecolorallocate($canvas, 255, 255, 255);
        $grey = imagecolorallocate($canvas, 128, 128, 128);
        $black = imagecolorallocate($canvas, 0, 0, 0);
        imagefilledrectangle($canvas, 0, 0, 150, 25, $black);
        $trans_colour = imagecolorallocatealpha($canvas, 0, 0, 0, 127);
        imagefill($canvas, 0, 0, $trans_colour);
        imagecopy($canvas, $img1, 0, 0, 0, 0, imagesx($img1), imagesy($img1));
        imagecopy($canvas, $img2, 0, 261, 0, 0, imagesx($img1), imagesy($img1));
        return $canvas;
    }

    public static function drawPostcard($postcar=array("name"=>"2","style"=>array("width"=>"509","height"=>269)),
                $car=array("sticker"=>array("car_name"=>"lux_white_pearl_base","sticker"=>array("1"=>1,"4"=>1,"5"=>1)),"dir"=>2,"style"=>array("width"=>196,"height"=>89,"x"=>31,"y"=>100))
                ,$string=array("text"=>"我擦擦才啊爱吃擦aac擦爱吃\r\n哈哈啊啊 速度","style"=>array("x"=>222,"y"=>128))){
        
        // var_export($car);
        // echo $car;
        $img_postcard = imagecreatefromjpeg("images/postcard/${postcar['name']}.jpg");
        
        // self::resize($img_postcard, $postcar['style']['width'], $postcar['style']['height']);
        
        $img_car = self::drawDirCar($car['dir'],$car['sticker']);
        
        self::resize($img_car, $car['style']['width'], $car['style']['height']);
        
        imagecopyresampled($img_postcard, $img_car, $car['style']['x'], $car['style']['y'], 0, 0,imagesx($img_car),imagesy($img_car) ,imagesx($img_car), imagesy($img_car));
        
        
        
        $y = $string['style']['y'];
        $color = imagecolorallocate($img_postcard, 0, 0, 0);
        
        foreach (explode("\r\n", $string['text'])  as $text) {
            imagettftext($img_postcard, 10, 0, $string['style']['x'], $y, $color, "fonts/MSYH.TTC", $text);
            $y += 35;
        }
        
        
        imagesavealpha($img_postcard, true);
        return $img_postcard;
        
    }

    

    public static function drawDirCar($dir,$sticker){
        
        if($dir == 1){
            $car = Canvas::generateCarImg1($sticker['car_name'], $sticker['sticker']);
            $car = Canvas::styleCar($car, array("canvas" => array("width" => 426, "height" => 149), "car" => array("width" => 644, "height" => 410, "x" => -120, "y" => -138)));
        }else if($dir == 2){
            $car = Canvas::generateCarImg2($sticker['car_name'], $sticker['sticker']);
            $car = Canvas::styleCar($car, array("canvas" => array("width" => 362, "height" => 164), "car" => array("width" => 644, "height" => 398, "x" => -162, "y" => -119)));
        }else if($dir == 3){
            $car = Canvas::generateCarImg3($sticker['car_name'], $sticker['sticker']);
            $car = Canvas::styleCar($car, array("canvas" => array("width" => 295, "height" => 132), "car" => array("width" => 604, "height" => 373, "x" => -136, "y" => -132)));
        }
        return $car;
    }

    private static function imagestringwrap($image, $font, $text, $color) {

        $fontwidth = imagefontwidth($font);
        $fontheight = imagefontheight($font);

        $words = str_word_count($text);
        if ($words > 1) {
            $string = array(strtok($text, ' '));
            for ($i = 1; $i <= $words; $i++) {
                $string = array_merge($string, array($i => strtok(' ')));
            }
        } else
            $string = $text;
        $vspace = 4;
        $y = ((imagesy($image) - ($fontheight * $words) - ($words * $vspace)) / 2);
        // echo $string;
        foreach (str_split($string) as $st) {

            $x = ((imagesx($image) - ($fontwidth * strlen($st))) / 2);
            echo $y . "<br>";
            imagestring($image, $font, $x, $y, $st, $color);
            $y += ($fontheight + $vspace);
        }
    }

    public static function generatePost($postname, $car, $string) {
        $img_postcard = imagecreatefrompng("images/postcard/" . $postname);
        Canvas::imagemerge($img_postcard, $car, 50, 50, 0, 0);
        $img_text = imagecreate(100, 300);
        $textcolor = imagecolorallocate($img_postcard, 255, 255, 255);
        self::imagestringwrap($img_postcard, 14, "adsadsadsadsadsadsadsadsadsadsadasdsadsasd", $textcolor);
        Canvas::imagemerge($img_postcard, $img_text, 450, 450, 0, 0);
        return $img_postcard;
    }

    public static function generate3CarImg($data, $styles = array(
    "canvas"=>array("width"=>478,"height"=>261),
    3=>array("width"=>482,"height"=>298,"x"=>152,"y"=>-73),
    2=>array("width"=>444,"height"=>274,"x"=>-108,"y"=>-56),
    1=>array("width"=>510,"height"=>315,"x"=>-24,"y"=>34)
    )) {

        $car_name = $data['car_name'];
        $sticker = $data['sticker'];

        $cars = array(1 => self::generateCarImg1($car_name, $sticker), 2 => self::generateCarImg2($car_name, $sticker), 3 => self::generateCarImg3($car_name, $sticker));

        return self::compositeCar($cars, $styles);
    }

    public static function compositeCar($cars, $styles) {
        $canvas = imagecreatetruecolor($styles['canvas']['width'], $styles['canvas']['height']);
        imagesavealpha($canvas, true);
        $white = imagecolorallocate($canvas, 255, 255, 255);
        $grey = imagecolorallocate($canvas, 128, 128, 128);
        $black = imagecolorallocate($canvas, 0, 0, 0);
        imagefilledrectangle($canvas, 0, 0, 150, 25, $black);
        $trans_colour = imagecolorallocatealpha($canvas, 0, 0, 0, 127);
        imagefill($canvas, 0, 0, $trans_colour);
        foreach ($cars as $i => $car) {
            $style = $styles[$i];

            self::resize($car, $style['width'], $style['height']);
            imagecopyresized($canvas, $car, $style['x'], $style['y'], 0, 0, $style['width'], $style['height'], imagesx($car), imagesy($car));
            // imagecopyresized($dst_image, $src_image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h)
        }

        return $canvas;

    }

    public static function styleCar($car, $styles = array("canvas"=>array("width"=>478,"height"=>261),"car"=>array("width"=>482,"height"=>298,"x"=>152,"y"=>-73))) {
        $canvas = imagecreatetruecolor($styles['canvas']['width'], $styles['canvas']['height']);
        imagesavealpha($canvas, true);
        $white = imagecolorallocate($canvas, 255, 255, 255);
        $grey = imagecolorallocate($canvas, 128, 128, 128);
        $black = imagecolorallocate($canvas, 0, 0, 0);
        imagefilledrectangle($canvas, 0, 0, 150, 25, $black);
        $trans_colour = imagecolorallocatealpha($canvas, 0, 0, 0, 127);
        imagefill($canvas, 0, 0, $trans_colour);

        $style = $styles["car"];

        self::resize($car, $style['width'], $style['height']);
        imagecopyresized($canvas, $car, $style['x'], $style['y'], 0, 0, $style['width'], $style['height'], imagesx($car), imagesy($car));

        return $canvas;

    }

    public static function generateCarImg1($car_name, $ticker) {

        $img_car = imagecreatefrompng("images/car/1/${car_name}.png");
        if (isset($ticker[1])) {
            $tickername = $ticker[1];
            $img_sticker = imagecreatefrompng("images/sticker/3/${tickername}.png");
            // Canvas::imagemerge($img_car, $img_sticker, 0, 0, 0, 0);
            imagecopy($img_car, $img_sticker, 0, 0, 0, 0, imagesx($img_sticker), imagesy($img_sticker));
            // imagecopymerge($img_car, $img_sticker, 0, 0, 0, 0, imagesx($img_sticker), imagesy($img_sticker), 100);
        }
        imagesavealpha($img_car, true);
        return $img_car;
    }

    public static function generateCarImg2($car_name, $ticker) {
        
        $img_car = imagecreatefrompng("images/car/2/${car_name}.png");
        if (isset($ticker[1])) {
            $tickername = $ticker[1];
            $img_sticker = imagecreatefrompng("images/sticker/4/${tickername}.png");
            imagecopy($img_car, $img_sticker, 0, 0, 0, 0, imagesx($img_sticker), imagesy($img_sticker));
        }
        if (isset($ticker[5])) {
            $tickername = $ticker[5];
            $img_sticker = imagecreatefrompng("images/sticker/7/${tickername}.png");
            imagecopy($img_car, $img_sticker, 0, 0, 0, 0, imagesx($img_sticker), imagesy($img_sticker));
        }
        imagesavealpha($img_car, true);
        return $img_car;
    }

    public static function generateCarImg3($car_name, $ticker) {
        $img_car = imagecreatefrompng("images/car/3/${car_name}.png");
        if (isset($ticker[4])) {
            $tickername = $ticker[4];
            $img_sticker = imagecreatefrompng("images/sticker/2/${tickername}.png");
            imagecopy($img_car, $img_sticker, 0, 0, 0, 0, imagesx($img_sticker), imagesy($img_sticker));
        }
        if (isset($ticker[1])) {
            $tickername = $ticker[1];
            $img_sticker = imagecreatefrompng("images/sticker/5/${tickername}.png");
            imagecopy($img_car, $img_sticker, 0, 0, 0, 0, imagesx($img_sticker), imagesy($img_sticker));
        }
        imagesavealpha($img_car, true);
        return $img_car;
    }

    /**
     * @param carname name(1.png)
     * @param type 1->3
     * @param sticker array(2=>1.png,3=>2.png,4=>2.png,5=>2.png,7=>2.png)
     */
    public static function generateCar($carname, $type, $sticker) {

        switch ($type) {
            case 1 :
                return self::generateCar1($carname, $sticker);

            case 2 :
                return self::generateCar2($carname, $sticker);
            case 3 :
                return self::generateCar3($carname, $sticker);
        }
    }

    private static function generateCar1($carname, $sticker) {
        $img_car = imagecreatefrompng("images/car/1/" . $carname);
        $img_sticker = imagecreatefrompng("images/sticker/3/" . $sticker[3]);

        Canvas::imagemerge($img_car, $img_sticker, 50, 50, 0, 0);
        imagesavealpha($img_car, true);
        return $img_car;
    }

    private static function generateCar2($carname, $sticker) {

        $img_car = imagecreatefrompng("images/car/2/" . $carname);
        $img_sticker4 = imagecreatefrompng("images/sticker/4/" . $sticker[4]);
        Canvas::imagemerge($img_car, $img_sticker4, 20, 20, 0, 0);
        $img_sticker2 = imagecreatefrompng("images/sticker/2/" . $sticker[2]);
        Canvas::imagemerge($img_car, $img_sticker2, 50, 50, 0, 0);
        imagesavealpha($img_car, true);
        self::resize($img_car, 200, 150);

        return $img_car;
    }

    private static function generateCar3($carname, $sticker) {
        $img_car = imagecreatefrompng("images/car/3/" . $carname);
        $img_sticker7 = imagecreatefrompng("images/sticker/7/" . $sticker[7]);
        Canvas::imagemerge($img_car, $img_sticker7, 50, 50, 0, 0);
        $img_sticker5 = imagecreatefrompng("images/sticker/5/" . $sticker[5]);
        Canvas::imagemerge($img_car, $img_sticker5, 150, 50, 0, 0);

        imagesavealpha($img_car, true);
        return $img_car;
    }

    private static function resize(&$image, $width, $height) {
        $thumb = imagecreatetruecolor($width, $height);
        // imagecolortransparent($new, imagecolorallocatealpha($new, 0, 0, 0, 127));
        imagealphablending($thumb, false);
        imagesavealpha($thumb, true);
        imagecopyresampled ($thumb, $image, 0, 0, 0, 0, $width, $height, imagesx($image), imagesy($image));

        $image = $thumb;
    }

    public static function imagemerge($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y) {
        self::imagecopymerge_alpha($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, imagesx($src_im), imagesy($src_im), 100);
    }

    public static function imagecopymerge_alpha($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct) {
        if (!isset($pct)) {
            return false;
        }
        $pct /= 100;
        // Get image width and height
        $w = imagesx($src_im);
        $h = imagesy($src_im);
        // Turn alpha blending off
        imagealphablending($src_im, false);
        // Find the most opaque pixel in the image (the one with the smallest alpha value)
        $minalpha = 127;
        for ($x = 0; $x < $w; $x++)
            for ($y = 0; $y < $h; $y++) {
                $alpha = (imagecolorat($src_im, $x, $y)>>24) & 0xFF;
                if ($alpha < $minalpha) {
                    $minalpha = $alpha;
                }
            }
        //loop through image pixels and modify alpha for each
        for ($x = 0; $x < $w; $x++) {
            for ($y = 0; $y < $h; $y++) {
                //get current alpha value (represents the TANSPARENCY!)
                $colorxy = imagecolorat($src_im, $x, $y);
                $alpha = ($colorxy>>24) & 0xFF;
                //calculate new alpha
                if ($minalpha !== 127) {
                    $alpha = 127 + 127 * $pct * ($alpha - 127) / (127 - $minalpha);
                } else {
                    $alpha += 127 * $pct;
                }
                //get the color index with new alpha
                $alphacolorxy = imagecolorallocatealpha($src_im, ($colorxy>>16) & 0xFF, ($colorxy>>8) & 0xFF, $colorxy & 0xFF, $alpha);
                //set pixel with the new color + opacity
                if (!imagesetpixel($src_im, $x, $y, $alphacolorxy)) {
                    return false;
                }
            }
        }
        // The image copy
        imagecopy($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h);
    }

}
