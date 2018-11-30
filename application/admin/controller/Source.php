<?php

namespace app\admin\controller;
use think\cache\driver\Redis;
use app\common\controller\Backend;

/**
 * 点击记录
 *
 * @icon fa fa-circle-o
 */
class Source extends Backend
{
    
    /**
     * Source模型对象
     */
    protected $model = null;
    protected $Submit2Model =null;
    protected $SubmitModel =null;
    protected $AdvertModel =null;
    protected $relationSearch = true;
    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('Source');
        $this->Submit2Model = model('Submit2');
        $this->SubmitModel = model('Submit');
        $this->AdvertModel = model('Advert');
        $this->view->assign("isMobileList", $this->model->getIsMobileList());
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
        if ($this->request->isAjax())
        {   
             $AdminId    = $_SESSION['think']['admin']['id'];
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField'))
            {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $jjhData =array(1347663353,415606289,1097492828);

            if($AdminId==79){
            
                if(!empty(json_decode($_GET['filter'],true)) && in_array(json_decode($_GET['filter'],true)['appid'],$jjhData)){
                   
                   
                    $total = $this->model
                         ->with('channel')
                        ->where($where)
                        ->order($sort, $order)
                        
                       ->count();
                }else{
                  return false;
                }
            }else{
                 $total = '';
                  $date1 = strtotime(date("Y-m-d"))+60*60*16;
                  $date2 = strtotime(date("Y-m-d"))+60*60*17;
                  if(!empty(json_decode($_GET['filter'],true))){
                    
                    $now   =time();
                   // return $date1.'---'.$now.'---'.$date2;
                    if($now/$date1>1 && $now/$date2<1 && strstr($_GET['filter'],'idfa')){
                      $total  = 1000;
                      
                    }else if($now/$date1>1 && $now/$date2<1 && !strstr($_GET['filter'],'idfa')){
                      return false;
                    }else{
                      $total = $this->model
                         ->with('channel')
                        ->where($where)
                        ->order($sort, $order)
                        
                       ->count();
                    }
                    
                  }else{
                    
                    $total =1000;
                  }
            }
           

           //  $redis          = new Redis();
          	// $total          = $redis->get('total');
           
            // if(empty($total)){
            // 	$total = $this->model
            //         ->with('channel')
            //         ->where($where)
            //         ->order($sort, $order)

            //         ->count();
            //         $redis->set('total',$total,3600);
            //   }
           
            $list = $this->model
                    ->with('channel')
                    
                    ->where($where)
                    ->order($sort, $order)
                   
                     ->limit($offset, $limit)
                   
                    ->select();
            //return $this->model->getlastsql();
            $list = collection($list)->toArray();


            foreach($list as $k=>$val){
                    $app  = $this->AdvertModel->where(array('appid'=>$val['appid'],'cpid'=>$val['adid']))->find();
                // if($val['adid']!=1){
                //     $JhS  = $this->Submit2Model->where(array('appid'=>$val['appid'],'idfa'=>$val['idfa'],'cpid'=>$val['cpid']))->order('timestamp desc')->group('idfa')->select();
                // }else{
                //     $JhS  = $this->SubmitModel->where(array('appid'=>$val['appid'],'idfa'=>$val['idfa'],'cpid'=>$val['cpid']))->order('timestamp desc')->group('idfa')->select();
                // }
                

                
                if($val['type']==0){

                    $list[$k]['type']  = '未激活';
                     $list[$k]['app_name']  = $app['app_name'];
                    // $list[$k]['stime']  = $JhSA['timestamp'];
                }else if($val['type']==1){
                    $list[$k]['type']  = '上报激活';
                     $list[$k]['app_name']  = $app['app_name'];
                }else{
                	$list[$k]['type']  = '回调激活';
                     $list[$k]['app_name']  = $app['app_name'];
                }

            }

           
            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }
    

}
