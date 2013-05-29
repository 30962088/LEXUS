<?php
//定义项目名称和路径
define('APP_DEBUG',TRUE); // 开启调试模式
define('APP_NAME', 'car');
define('APP_PATH', './');
define('APP_REAL_PATH', dirname(__FILE__)."/");
// define('WEB_HOST', "http://123.118.213.121/");
define('WEB_HOST', "http://ct200.1du1du.com/");
//授权改2处,weiboconfig和这里

//加载微博
include_once( 'Weibo/config.php' );
include_once( 'Weibo/saetv2.ex.class.php' );



require_once 'service.php';
require_once 'canvas.php';

header('P3P: CP="CAO PSA OUR"');


require( "../ThinkPHP/ThinkPHP.php");






//http://e.weibo.com/2960317105/app_3564132756?previev=1  //1931835691


//http://e.weibo.com/1931835691/app_3564132756 
/**
 * 账号：lexus_china@sina.cn
        密码：LEXUSlexus20130
 */
/**
 * 性别定义
 * 1代表男
 * 2代表女
 */

/**
 * 7套图定义:
 * 1为正面
 * 2为正斜面
 * 3为侧面
 * 4为侧斜左面
 * 5为侧斜右面
 * 6为后面
 * 7为后斜面
 */

/**
 * 车图的定义
 * 1为侧面
 * 2为侧斜左
 * 3为侧斜右面
 * 4为正面
 * 5为后面
 */ 
 

/**
 * 生成车图的定义:
 * 1为侧面
 * 2为侧斜左
 * 3为侧斜右
 */



