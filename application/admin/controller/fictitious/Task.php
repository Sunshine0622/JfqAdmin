<?php

namespace app\admin\controller\fictitious;

use app\common\controller\Backend;
use think\Db;
/**
 * 积分墙虚拟任务表
 *
 * @icon fa fa-circle-o
 */
class Task extends Backend
{
    
    /**
     * FictitiousTask模型对象
     */
    protected $model = null;
    protected $relationSearch = true;
    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('FictitiousTask');

    }

    /**
     * 查看
     */
    public function index()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax())
        {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField'))
            {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $total = $this->model
                   
                    ->where($where)
                    ->order($sort, $order)

                    ->count();

            $list = $this->model
                    
                    ->where($where)
                    ->order($sort, $order)
                    ->limit($offset, $limit)
                    ->select();


            $list = collection($list)->toArray();
            foreach($list as $k=>$val){
                $appid = $val['appid'];
                 $json = json_decode(file_get_contents("https://itunes.apple.com/lookup?id=$appid"),true);
                if($json['resultCount']==0){
                    $json = json_decode(file_get_contents("https://itunes.apple.com/cn/lookup?id=$appid"),true);
                }
               

                $appname     = isset($json['results'][0]['trackName'])?$json['results'][0]['trackName']:'已下架';
                if(strstr($appname, '-')){
                    $appname = explode('-',$appname)[0];
                }
                 if(strstr($appname, '—')){
                    $appname = explode('—',$appname)[0];
                }
                if(strstr($appname, '·')){
                    $appname = explode('·',$appname)[0];
                }

                if(strstr($appname, '：')){
                    $appname = explode('：',$appname)[0];
                }
                $list[$k]['AppName'] =  $appname;
                $list[$k]['AppLogo'] = isset($json['results'][0]['artworkUrl100'])?$json['results'][0]['artworkUrl100']:'';
            }
            $UsedIdfas = 0;
            
            $idfas  = Db::name('fictitious_submit')->where('type = 0')->count();
            $result = array("total" => $total, "rows" => $list,"extend" => ['idfas' => $idfas]);

            return json($result);
        }
        return $this->view->fetch();
    }

     public function multi($ids = "")
    {
        $ids = $ids ? $ids : $this->request->param("ids");
        if ($ids) {
            // if ($this->request->has('params')) {
            //     parse_str($this->request->post("params"), $values);
            //     $values = array_intersect_key($values, array_flip(is_array($this->multiFields) ? $this->multiFields : explode(',', $this->multiFields)));
            //     if ($values) {
            //         // $adminIds = $this->getDataLimitAdminIds();
            //         // if (is_array($adminIds)) {
            //         //     $this->model->where($this->dataLimitField, 'in', $adminIds);
            //         // }
            //         $count = 0;
            //         $list = $this->model->where($this->model->getPk(), 'in', $ids)->select();
            //         foreach ($list as $index => $item) {
            //             $count += $item->allowField(true)->isUpdate(true)->save($values);
            //         }
            //         if ($count) {
            //             $this->success();
            //         } else {
            //             $this->error(__('No rows were updated'));
            //         }
            //     } else {
            //         $this->error(__('You have no permission'));
            //     }
            // }

             $params  = $this->request->post("params");
       
            $key     = explode('=',$params)[0];
            $value   = explode('=',$params)[1];
            $data[$key] = $value;
            $info = Db::name('fictitious_task')->where(array('id'=>$ids))->update($data);
            $this->success();
        }
        $this->error(__('Parameter %s can not be empty', 'ids'));
    }


    public function importidfa($ids){

       return json(array('code'=>1,'id'=>$ids));
    

    }

    public function importdata(){
        $task_id    = $this->request->get('id');
        $taskData   = Db::name('fictitious_task')->where(array('id'=>$task_id))->find();
        $appid      = $taskData['appid'];
        $adid       = $taskData['adid'];
        $keywords       = $taskData['keywords'];
        $startTime  = $taskData['start_time'];
        $endTime    = $taskData['end_time'];
        $data       = Db::name('fictitious_source')->alias('s')->join('fictitious_submit a','a.idfa=s.idfa','left')->field('FROM_UNIXTIME(s.timestamp) as start_time,FROM_UNIXTIME(a.timestamp) as end_time,s.keywords,s.device,s.os,s.ip,s.idfa')->where("a.appid = $appid and a.adid=$adid and s.task_id =$task_id  and s.submit=1 ")->order('s.timestamp asc')->group('s.idfa')->select();
        // return Db::name('fictitious_source')->getLastSql();
        
        $xlsCell  = array(
                array('idfa','IDFA'),
                array('os','系统版本号'),
               array('device','设备类型'),
                
                 array('keywords','关键词'),
                 array('ip','Ip'),
                array('start_time','点击时间'),
                array('end_time','完成时间'),
                
            );
        $app_name  = Db::name('advert')->where(array('appid'=>$appid,'cpid'=>$adid))->value('app_name');
        $file_name=$app_name.date('m-d',$startTime).'虚拟测试数据';
         $xlsName = $app_name.date('m-d',$startTime).'虚拟测试数据';
        exportExcel_data($xlsName,$xlsCell,$data,$file_name);
    }

    
    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */
    

}
