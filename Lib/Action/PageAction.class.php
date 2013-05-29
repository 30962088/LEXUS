<?php
class PageAction extends Action {
    public function index() {

        if (($_GET['cid']) || preg_match("/http:\/\/e.weibo.com|http:\/\/api.weibo.com/", $_SERVER['HTTP_REFERER']) || isset($_GET['from'])) {

        } else {
            header("Location: http://e.weibo.com/" . WB_DKEY . "/app_3564132756");
        }

        $this -> display();
    }

    public function getImage() {
        header("Content-Type: image/png");
        header("Cache-Control: public");
        header("Pragma: cache");
        $offset = 30 * 60 * 60 * 24;
        // cache 1 month
        $ExpStr = "Expires: " . gmdate("D, d M Y H:i:s", time() + $offset) . " GMT";
        header($ExpStr);
        $sticker = json_decode($_GET['sticker'], true);
        // print_r($sticker);
        $file = Canvas::statickFile($sticker, $_GET['type']);

        if (file_exists($file)) {
            $img = imagecreatefrompng($file);
            imagesavealpha($img, true);
            imagepng($img, NULL);
            return NULL;
        }
        if ($_GET['type'] == "publish") {//发布图
            $img = Canvas::drawCommon3($sticker, 1015, 503);

        } else if ($_GET['type'] == "confirm") {//确认图
            $img = Canvas::drawCommon3($sticker, 508, 261);

        } else if ($_GET['type'] == "car3") {//配对图
            // $sticker = array("car_name"=>"lux_white_pearl_base","sticker"=>array(1=>"1",4=>"1",5=>"1"));
            $img = Canvas::generateCarImg3($sticker['car_name'], $sticker['sticker']);
            $img = Canvas::styleCar($img, array("canvas" => array("width" => 268, "height" => 117), "car" => array("width" => 539, "height" => 333, "x" => -120, "y" => -120)));

        } else if ($_GET['type'] == "car4") {//组合图
            $img = Canvas::generate3CarImg($sticker, array("canvas" => array("width" => 222, "height" => 120), 3 => array("width" => 194, "height" => 120, "x" => 80, "y" => -33), 2 => array("width" => 194, "height" => 120, "x" => -46, "y" => -31), 1 => array("width" => 264, "height" => 163, "x" => -20, "y" => 7)));

        } else if ($_GET['type'] == "car5_3") {//明信片的缩略图3
            $img = Canvas::drawDirCar("3", $sticker);

        } else if ($_GET['type'] == "car5_2") {//明信片的缩略图2
            $img = Canvas::drawDirCar("2", $sticker);

        } else if ($_GET['type'] == "car5_1") {//明信片的缩略图2
            $img = Canvas::drawDirCar("1", $sticker);

        }
        imagepng($img, null);
        mkdir(dirname($file), 0700, true);
        imagepng($img, $file, 0);
    }

    public function getPair() {
        header("Content-Type: image/png");
        header("Cache-Control: public");
        header("Pragma: cache");
        $offset = 30 * 60 * 60 * 24;
        // cache 1 month
        $ExpStr = "Expires: " . gmdate("D, d M Y H:i:s", time() + $offset) . " GMT";
        header($ExpStr);
        $sticker1 = json_decode($_GET['sticker1'], true);
        $sticker2 = json_decode($_GET['sticker2'], true);
        $file = Canvas::pairFile($sticker1, $sticker2, $_GET['type']);

        if (file_exists($file)) {
            $img = imagecreatefrompng($file);
            imagesavealpha($img, true);
            imagepng($img, NULL);
            return NULL;
        }

        $img = Canvas::drawPair($sticker1, $sticker2);
        imagepng($img, null);
        mkdir(dirname($file), 0700, true);
        imagepng($img, $file, 0);

    }

    public function getPostcard() {
        header("Content-Type: image/png");
        header("Cache-Control: public");
        header("Pragma: cache");
        $offset = 30 * 60 * 60 * 24;
        // cache 1 month
        $ExpStr = "Expires: " . gmdate("D, d M Y H:i:s", time() + $offset) . " GMT";
        header($ExpStr);
        $sticker = json_decode($_GET['sticker'], true);
        $styles = array("2" => array("width" => 232, "height" => 105, "y" => 197, "x" => 47), "3" => array("width" => 232, "height" => 104, "y" => 197, "x" => 47), "1" => array("width" => 270, "height" => 94, "y" => 200, "x" => 37));
        $postcar = array("name" => $_GET['postcard']);
        $car = array("sticker" => $sticker, "dir" => $_GET['dir'], "style" => $styles[$_GET['dir']]);
        $string = array("text" => $_GET['text'], "style" => array("x" => 351, "y" => 183));

        $img = Canvas::drawPostcard($postcar, $car, $string);

        imagepng($img, null);
    }

    public function sticker() {
        $sex = @$_REQUEST['sex'];

        if (!isset($sex)) {
            $sex = 1;
            //默认男
        }
        if ($sex == "1") {
            $sf = "male";
            $theme = "blue";
        } else {
            $sf = "female";
            $theme = "pink";
        }
        $this -> assign("car_name", "lux_white_pearl_base");
        $this -> assign("car_dirs", array("4", "1", "5"));
        $this -> assign("sticker_names", self::getFileNames(APP_PATH . "images/sticker/${sf}//1/"));
        $this -> assign('sticker_dirs', array("1", "3", "6"));
        $this -> assign("theme", $theme);
        $this -> assign("sex", $sex);
        $this -> display();
    }

    public function pair() {
        $sticker = session('sticker');
        $params['sex'] = $sticker['sex'] == 1 ? 2 : 1;
        $params['limit'] = "0,20";
        $result = Service::popcar($params);
        if ($result['count'] == 1) {
            $result['datas'] = array($result['datas']);
        }
        foreach ($result['datas'] as &$data) {
            $data['sticker'] = self::buildSticker($data);
        }
        $this -> assign("mysticker", json_encode(array("car_name" => $sticker['car_name'], "sticker" => $sticker['sticker'])));
        $this -> assign("count", $result['count']);
        $this -> assign("datas", $result['datas']);
        $this -> assign("sex", $params['sex']);
        $this -> assign("mysex", $sticker['sex']);
        $this -> display();
    }

    public function confirm2() {
        $type = $_REQUEST['type'];
        $sticker = session("sticker");
        if ($type == "pair") {//从配对过来
            $pid = $_REQUEST['pid'];
            Service::pair(array("car1" => session("id"), "car2" => $pid));
        } else if ($type == "confirm") {//从贴图过来
            $token = $this -> getToken();
            $newCar = array("uid" => $token['uid'], "nickname" => $token['nickname'], "sex" => $sticker['sex'], "car_name" => $sticker['car_name'], "sticker_1" => $sticker['sticker'][1], "sticker_4" => $sticker['sticker'][4], "sticker_5" => $sticker['sticker'][5]);
            if (!isset($sticker['saved'])) {
                $id = Service::add($newCar);
                session("id", $id);
                $sticker['saved'] = 1;
                session("sticker", $sticker);
            }
        }
        $this -> assign("mysticker", json_encode(array("car_name" => $sticker['car_name'], "sticker" => $sticker['sticker'])));
        $this -> display();
    }

    public function confirm() {
        $request = $_REQUEST['data'];

        if (isset($request)) {
            $sticker = json_decode($_REQUEST['data'], true);
            session("sticker", $sticker);
        } else {
            $sticker = session("sticker");
        }

        $this -> assign("sticker", $sticker);
        $this -> assign("mysticker", json_encode(array("car_name" => $sticker['car_name'], "sticker" => $sticker['sticker'])));
        $this -> display();
    }

    public function postcard() {
        $sticker = session("sticker");
        $this -> assign("postcard_names", self::getFileNames(APP_PATH . "images/postcard/"));
        $this -> assign("mysticker", json_encode(array("car_name" => $sticker['car_name'], "sticker" => $sticker['sticker'])));
        $this -> display();
    }

    public function popcar() {
        $this -> display();
    }

    public function poppair() {
        $this -> display();
    }

    //验证  -> authorizeCallback -> index
    public function authorize() {

        session("ref", $_SERVER['HTTP_REFERER']);

        $o = new SaeTOAuthV2(WB_AKEY, WB_SKEY);
        $code_url = $o -> getAuthorizeURL(WB_CALLBACK_URL);
        header("Location: ${code_url}");
    }

    //微博验证回调
    public function authorizeCallback() {

        $o = new SaeTOAuthV2(WB_AKEY, WB_SKEY);

        if (isset($_GET['code'])) {
            $keys = array();
            $keys['code'] = $_GET['code'];
            $keys['redirect_uri'] = WB_CALLBACK_URL;
            try {
                $token = $o -> getAccessToken('code', $keys);
            } catch (OAuthException $e) {
                // print_r($e);
            }
        }

        if ($token) {
            $c = new SaeTClientV2(WB_AKEY, WB_SKEY, $token['access_token']);
            $user = $c -> show_user_by_id($token['uid']);
            $token['nickname'] = $user['name'];
            session('token', $token);
            // $_SESSION['token'] = $token;
            // setcookie( 'weibojs_'.$o->client_id, http_build_query($token) );
            cookie('weibojs_' . $o -> client_id, http_build_query($token));

            $this -> redirect("index?from=app");

        }

        // $this->redirect("index");
        // header('Location: '.session('ref'));
    }

    public function page2() {
    }

    public function getToken() {
        $token = session("token");
        if ($token) {
            return session("token");
        }
        $cookie = cookie("weibojs_" . WB_AKEY);
        $token = array();
        foreach (explode("&", $cookie) as $pair) {
            $pair = explode("=", $pair);
            $token[$pair[0]] = $pair[1];
        };
        $c = new SaeTClientV2(WB_AKEY, WB_SKEY, $token['access_token']);
        $user = $c -> show_user_by_id($token['uid']);
        $token['nickname'] = $user['name'];
        session('token', $token);
        return $token;
    }

    public static function getFileNames($dir) {
        $files = array();
        if ($handle = opendir($dir)) {
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != "..") {
                    $files[] = preg_replace("/\.[\w]+/", "", $file);
                }
            }
            closedir($handle);
        }
        return $files;
    }

    private static function buildSticker($car) {
        return json_encode(array("car_name" => $car['car_name'], "sticker" => array(1 => $car['sticker_1'], 4 => $car['sticker_4'], 5 => $car['sticker_5'])));
    }

}
