<?php

namespace app\admin\controller;

use app\common\controller\Backend;

/**
 * 
 *
 * @icon fa fa-file
 */
class File extends Backend
{
    
    /**
     * File模型对象
     */
    protected $model = null;
     protected $SalesModel = null;
     protected  $AdvertModel =null;
      protected $relationSearch = true;
    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('File');
        $this->SalesModel = model('Sales');
        $this->AdvertModel = model('Advert');
        $this->view->assign("statusList", $this->model->getStatusList());
    }
    
    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */

     /**
     * 查看
     */
    public function index()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        //$redis  = new Redis();
        if ($this->request->isAjax())
        {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField'))
            {
                return $this->selectpage();
            }
           
            
             list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $total = $this->model
                    ->with('sales')
                    
                    ->where($where)
                    ->order($sort, $order)
                    ->count();

            $list = $this->model
                    ->with('sales')
                    
                    ->where($where)
                    ->order($sort, $order)
                    ->limit($offset, $limit)
                    ->select();
          
            $list = collection($list)->toArray();
                 
           
            foreach($list as $k=>$val){
               if($val['status']==0){
                    $list[$k]['status']   ='待处理';
                    $As                   = $this->AdvertModel->where(array('appid'=>$val['appid']))->order('id desc')->find();
                    $list[$k]['app_name'] = $As['app_name'];
               }else{
                    $As                   = $this->AdvertModel->where(array('appid'=>$val['appid']))->order('id desc')->find();
                    $list[$k]['app_name'] = $As['app_name'];
                    $list[$k]['status']   = '已完成';
               }
            }
            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }

        return $this->view->fetch();
    }
    

}