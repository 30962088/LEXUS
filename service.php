<?php
require_once "mysql.php";
class Service{
    
    //1->1 10->1 11->2 20 ->2
    public static function getTotalPage($count,$range){
        
        $total = floor($count/$range);
        
        
        if($count%$range>0){
            $total++;
            
        }
        return $total;
    }
    
    public static function searchPopcar($name,$sex,$offset=0,$total=2){
        $oMySQL = new MySQL();
        $where = "where sex = ${sex}";
        if(isset($name)){
            $where .= " and (nickname = '${name}' or uid='${name}')";
        }
        $res = array();
        $result = $oMySQL->ExecuteSQL("select count(*) as count from car ${where} ");
        $res['count']=$result['count'];
        $res['datas']=array();
        $result = $oMySQL->ExecuteSQL("select * from car ${where} order by total desc limit ${offset},${total}");
        // echo "select * from car ${where} order by total desc limit ${offset},${total}";
        if($result){
            if(array_key_exists("id", $result)){
                $res['datas'] = array($result);
            }else{
                $res['datas'] = $result;
            }
        }
         // echo "select * from car ${where} order by total desc limit ${offset},${total}";
        return $res;
    }

    public static function searchPair($name,$offset=0,$total=3){
        $oMySQL = new MySQL();
        $sql = "select * from pair";
        $where = "";
        if(isset($name)){
            $where = "where id in (select distinct p.id from car c,pair p where (c.id = p.car1 or p.car2 = c.id) and (c.nickname='${name}' or c.uid='${name}'))";
        }
        
        $res = array();
        $result = $oMySQL->ExecuteSQL("select count(*) as count from pair ${where}");
        // echo "select count(*) as count from pair ${where}";
        $res['count']=$result['count'];
        
        $tempArr = $oMySQL->ExecuteSQL("select * from pair ${where} order by total desc limit ${offset},${total}");
        // echo "select * from pair ${where} order by total desc limit ${offset},${total}";
        if($tempArr){
            if(array_key_exists("id", $tempArr)){
                $tempArr = array($tempArr);
            }
        }else{
            $tempArr=array();
        }
        $res['datas']=array();
        foreach ($tempArr as $temp) {
            $obj = array();
            $obj['total']=$temp['total'];    
            $obj['id']=$temp['id'];
            $car1 = $oMySQL->ExecuteSQL("select * from car where id=".$temp['car1']);
            $obj['pair'][$car1['sex']]=$car1;
            $car2 = $oMySQL->ExecuteSQL("select * from car where id=".$temp['car2']);
            $obj['pair'][$car2['sex']]=$car2;
            $res['datas'][]=$obj;
        }
        return $res;
    }
    
    public static function add($params){
        $newCar = array();
        $newCar['uid']=$params['uid'];
        $newCar['nickname']=$params['nickname'];
        $newCar['sex']=$params['sex'];
        $newCar['car_name']=$params['car_name'];
        $newCar['sticker_1']=$params['sticker_1'];
        $newCar['sticker_4']=$params['sticker_4'];
        $newCar['sticker_5']=$params['sticker_5'];
        $newCar['total']=0;
        if($newCar['uid']&&$newCar['nickname']){
            $oMySQL = new MySQL();
            $oMySQL->Insert($newCar, 'car');
            $inserid = mysql_insert_id();
        }
        
        return $inserid;
    }
    public static function vote($id){
        $oMySQL = new MySQL();
        
        $car = $oMySQL->Select("car",array("id"=>$id));
        
        $total = $car['total'];
        $total ++;
        $oMySQL->Update("car", array("total"=>$total), array("id"=>$id));
    }
    public static function pair($params){
        $oMySQL = new MySQL();
        $id1 = $params['car1'];
        $id2 = $params['car2'];
        
        
        $car1 = $oMySQL->Select("car",array("id"=>$id1));
        $car2 = $oMySQL->Select("car",array("id"=>$id2));
        if($car1['sex']==$car2['sex']){
            echo "配对性别不能一样!";
            return;
        }
        $newPair = array();
        $newPair['car1']=$id1;
        $newPair['car2']=$id2;
        $newPair['total']=0;
        if($newPair['car1']&&$newPair['car2']){
            $oMySQL->Insert($newPair, "pair");
        }
        
    }
    
    
    public static function votepair($id){
        $oMySQL = new MySQL();
        $car = $oMySQL->ExecuteSQL("select * from pair where id = ${id}");
        $total = $car['total'];
        $total ++;
        $oMySQL->Update("pair", array("total"=>$total), array("id"=>$id));
    }
    public static function popcar($params){
        $queryCount = "select count(*) as count from car";
        
        $where = "";
        if(@$params['sex']!=null){
            $where .= " sex = ".$params['sex']." ";
        }
        $queryCount .= " where ".$where;

        $oMySQL = new MySQL();
        $res = $oMySQL->ExecuteSQL($queryCount);
        $query = "select * from car";
        $limit="0,5";
        if(@$params['limit']){
            $limit = $params['limit'];
        }
        $query.=" where ".$where." order by total desc limit ".$limit;
        $res['datas']=$oMySQL->ExecuteSQL($query);
        return $res;
    }
    
    public static function poppair($params){
        $queryCount = "select count(*) as count from pair";
        $oMySQL = new MySQL();
        $res = $oMySQL->ExecuteSQL($queryCount);
        $limit = "0,5";
        if($limit){
            $limit = $params['limit'];
        }
        $query = "select * from pair order by total desc limit ".$limit;
        $tempArr = $oMySQL->ExecuteSQL($query);
        $res['data']=array();
        foreach ($tempArr as $temp) {
            $obj = array();
            $obj['total']=$temp['total'];    
            $car1 = $oMySQL->ExecuteSQL("select * from car where id=".$temp['car1']);
            $obj['pair'][$car1['sex']]=$car1;
            $car2 = $oMySQL->ExecuteSQL("select * from car where id=".$temp['car2']);
            $obj['pair'][$car2['sex']]=$car2;
            $res['data'][]=$obj;
        }
        return $res;
    }
    
    
    
}
