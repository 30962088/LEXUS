<?php
return array(
    'URL_MODEL'			=>	3, // 如果你的环境不支持PATHINFO 请设置为3
    'DEFAULT_MODULE'	=>	'',
    //启用路由功能
	'URL_ROUTER_ON'		=>	true,
	//路由定义
	'URL_ROUTE_RULES'	=> 	array(
		'/^(\w+)(\?.*)?$/' 				=>	'Page/:1:2',//正则路由
		
	),
	'LAYOUT_ON'=>true,
    'LAYOUT_NAME'=>'layout',
);