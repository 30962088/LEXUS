<?php

class ajaxAction extends Action{
    public function getPopcar(){
        header('Content-Type: application/json;charset="utf-8"');
        $name = $_GET['name'];
        if(isset($name)){
            $name = urldecode($name);
        }
        
        $page = $_GET['page'];
        $sex = $_GET['sex'];
        $total = 2;
        $offset = ($page-1)*$total;
        
        $res = Service::searchPopcar($name, $sex,$offset,$total);
        $res['total']=Service::getTotalPage($res['count'], $total);

        echo json_encode($res);
    }
    
    public function getPoppair(){
        header('Content-Type: application/json;charset="utf-8"');
        $name = $_GET['name'];
        
        if(isset($name)){
            $name = urldecode($name);
        }
        
        $page = $_GET['page'];
        $total = 3;
        $offset = ($page-1)*$total;
        
        $res = Service::searchPair($name,$offset,$total);
        $res['total']=Service::getTotalPage($res['count'], $total);

        echo json_encode($res);
    }
    
    public function votePair(){
        $id = $_POST['id'];
        
        Service::votepair($id);
    }
    
    public function vote(){
        $id = $_POST['id'];
        // echo $id;
        Service::vote($id);
    }
    
}