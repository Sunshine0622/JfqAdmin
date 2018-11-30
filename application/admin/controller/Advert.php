<?php

namespace app\admin\controller;

use app\common\controller\Backend;
use think\cache\driver\Redis;
/**
 * APP接口
 *
 * @icon fa fa-circle-o
 */
class Advert extends Backend
{
    
    /**
     * Advert模型对象
     */
    protected $model = null;
    protected $SalesModel = null;
    protected $ChannelModel = null;
    protected $FileModel = null;
    protected $SubmitModel = null;
    protected $relationSearch = true;
   protected $multiFields="is_disable";
    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('Advert');
        $this->SalesModel = model('Sales');
        $this->ChannelModel = model('Channel');
         $this->SubmitModel = model('Submit');
         $this->FileModel = model('File');
        $this->view->assign("isAdvertList", $this->model->getIsAdvertList());
        $this->view->assign("isDisableList", $this->model->getIsDisableList());
        $this->view->assign("apiCatList", $this->model->getApiCatList());
        $this->view->assign("isRepeatList", $this->model->getIsRepeatList());
        $this->view->assign("isSourceList", $this->model->getIsSourceList());
        $this->view->assign("isSubmitList", $this->model->getIsSubmitList());
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
                    ->with('sales,channel')
                    
                    ->where($where)
                    ->order($sort, $order)
                    ->count();

            $list = $this->model
                    ->with('sales,channel')
                    
                    ->where($where)
                    ->order($sort, $order)
                    ->limit($offset, $limit)
                    ->select();
          
            $list = collection($list)->toArray();
                 
           
            foreach($list as $k=>$val){
            	 $ApiType   = '';
            	if($list[$k]['is_repeat']==1){
            		$ApiType   .= '排重 ';
            	}
            	if($list[$k]['is_source']==1){
            		$ApiType   .= '点击 ';
            	}
            	if($list[$k]['is_submit']==1){
            		$ApiType   .= '上报 ';
            	}
            	if($list[$k]['is_advert']==1){
            		$ApiType   .= '回调 ';
            	}
                if($list[$k]['is_disable']=='0'){
                    $list[$k]['is_disable']='正常';
                    $list[$k]['status']    = '禁用';
                }else{
                    $list[$k]['is_disable']="禁用";
                     $list[$k]['status']    = '启用';
                }

                if($list[$k]['charge']==0){
                    $list[$k]['charge'] = '待配置';
                }
            	$list[$k]['api_type']  = $ApiType;

                // $json = json_decode(file_get_contents("https://itunes.apple.com/lookup?id={$val['appid']}"),true);
                // if($json['resultCount']==0){
                //     $json = json_decode(file_get_contents("https://itunes.apple.com/cn/lookup?id={$val['appid']}"),true);
                // }

                // $list[$k]['AppLogo'] = isset($json['results'][0]['artworkUrl100'])?$json['results'][0]['artworkUrl100']:'';


            }
            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }

        return $this->view->fetch();
    }


     /**
     * 编辑
     */
    public function edit($ids = NULL)
    {
        $row = $this->model->get($ids);
        $ChannelInfo   =  $this->ChannelModel->where(array('cpid'=>$row['channel']))->find();
      
        $row['cNote']  =  $ChannelInfo['note'];
        if (!$row)
            $this->error(__('No Results were found'));
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds))
        {
            if (!in_array($row[$this->dataLimitField], $adminIds))
            {
                $this->error(__('You have no permission'));
            }
        }
        
        $sales_list = $this->SalesModel->select();
       
        $channel_list = $this->ChannelModel->select();


        if ($this->request->isPost())
        {
            $params = $this->request->post("row/a");
            $params['repeat_value']   = $params['repeat_respone_params'].'%'.$params['repeat_respone_code'].'%'.$params['repeat_request'].'%'.$params['repeat_keywords'].'%'.$params['norepeat_respone_code'];
            $params['source_value']   = $params['source_respone_params'].'%'.$params['source_respone_code'].'%'.$params['source_request'].'%'.$params['source_keywords'];
            $params['submit_value']   = $params['submit_respone_params'].'%'.$params['submit_respone_code'].'%'.$params['submit_request'].'%'.$params['submit_keywords'];
           
             
            $params['source_url']  = str_replace('×','&times',$params['source_url']);
          
            
          
             $params['IdfaRepeat_url']  = str_replace('×','&times',$params['IdfaRepeat_url']);
           
           
             $params['submit_url']  = str_replace('×','&times',$params['submit_url']);
          
            $params['source_url']   = trim($params['source_url']);
            $params['IdfaRepeat_url']   = trim($params['IdfaRepeat_url']);
            $params['submit_url']   = trim($params['submit_url']);
            if ($params)
            {
                try
                {
                    //是否采用模型验证
                    if ($this->modelValidate)
                    {
                        $name = basename(str_replace('\\', '/', get_class($this->model)));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.edit' : true) : $this->modelValidate;
                        $row->validate($validate);
                    }
                    $result = $row->allowField(true)->save($params);
                    if ($result !== false)
                    {
                        $this->success();
                    }
                    else
                    {
                        $this->error($row->getError());
                    }
                }
                catch (\think\exception\PDOException $e)
                {
                    $this->error($e->getMessage());
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        	
            $row['repeat_request']         = '';
            $row['repeat_respone_params']  = '';
            $row['repeat_respone_code']    = '';
            $row['repeat_keywords']        = '';
           
            $row['norepeat_respone_code']    = '';

             $row['source_request']         = '';
        	$row['source_respone_params']  = '';
        	$row['source_respone_code']    = '';
            $row['source_keywords']        = '';

        	 $row['submit_request']         = '';
        	$row['submit_respone_params']  = '';
       		 $row['submit_respone_code']    = '';
              $row['submit_keywords']        = '';

        if($row['api_cat']==0 && $row['repeat_value']!=''){
        	$row['repeat_request']         = explode('%',$row['repeat_value'])[2];
            $row['repeat_respone_params']  = explode('%',$row['repeat_value'])[0];
            $row['repeat_respone_code']    = explode('%',$row['repeat_value'])[1];
            $row['repeat_keywords']        = explode('%',$row['repeat_value'])[3];
           
            $row['norepeat_respone_code']    = !empty(explode('%',$row['repeat_value'])[4])?explode('%',$row['repeat_value'])[4]:'';
        }
        
        if($row['api_cat']==0 && $row['source_value']!=''){
        	 $row['source_request']         = explode('%',$row['source_value'])[2];
        	$row['source_respone_params']  = explode('%',$row['source_value'])[0];
        	$row['source_respone_code']    = explode('%',$row['source_value'])[1];
            $row['source_keywords']        = explode('%',$row['source_value'])[3];
        }

         if($row['api_cat']==0 && $row['submit_value']!=''){
         	 $row['submit_request']         = explode('%',$row['submit_value'])[2];
        	$row['submit_respone_params']  = explode('%',$row['submit_value'])[0];
       		 $row['submit_respone_code']    = explode('%',$row['submit_value'])[1];

             $row['submit_keywords']        = explode('%',$row['submit_value'])[3];
         }
       

      
        $this->view->assign("sales",$sales_list);
        $this->view->assign("channel",$channel_list);
        $this->view->assign("row", $row);
        return $this->view->fetch();
    }

    public function apiwf($ids = NULL)
    {

        if(!is_numeric($ids)){
             return json(array('key'=>base64_encode($_GET['data'])));

        }
        $row = $this->model->get($ids);
        $ChannelInfo   =  $this->ChannelModel->where(array('cpid'=>$row['channel']))->find();
      
       
        if (!$row)
            $this->error(__('No Results were found'));
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds))
        {
            if (!in_array($row[$this->dataLimitField], $adminIds))
            {
                $this->error(__('You have no permission'));
            }
        }
        
       
       
        $channel_list = $this->ChannelModel->select();


       

      
       
        $this->view->assign("channel",$channel_list);
        $this->view->assign("row", $row);
        return $this->view->fetch();
    }


    public function images(){
    $QR="https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1537961970420&di=8891552692bd027a7d684387eb785dda&imgtype=0&src=http%3A%2F%2Fimg.atobo.com%2FProductImg%2FEWM%2FUWeb%2F8%2F1%2F3%2F5%2F373%2F8135373%2F1.gif";

    $logo   = "https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1537961970420&di=8891552692bd027a7d684387eb785dda&imgtype=0&src=http%3A%2F%2Fimg.atobo.com%2FProductImg%2FEWM%2FUWeb%2F8%2F1%2F3%2F5%2F373%2F8135373%2F1.gif";
    $path = '/www/web/fastadmin/public/uploads/demo/';
    if ($logo !== FALSE) {
        $QR = imagecreatefromstring(file_get_contents($QR));
        $logo = imagecreatefromstring(file_get_contents($logo));
        $QR_width = imagesx($QR);//二维码图片宽度
        $QR_height = imagesy($QR);//二维码图片高度
        $logo_width = imagesx($logo);//logo图片宽度
        $logo_height = imagesy($logo);//logo图片高度
        $logo_qr_width = $QR_width / 5;
        $scale = $logo_width/$logo_qr_width;
        $logo_qr_height = $logo_height/$scale;
        $from_width = ($QR_width - $logo_qr_width) / 2;
//重新组合图片并调整大小
        imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width,
            $logo_qr_height, $logo_width, $logo_height);
    }
    //输出图片
    $QIMG = $path.rand(100000,999999).time().".jpg";
    return imagepng($QR, $QIMG);
    }

     /**
     * 添加
     */
    public function add()
    {
        if ($this->request->isPost())
        {
            $params = $this->request->post("row/a");
            $params['repeat_value']   = $params['repeat_respone_params'].'%'.$params['repeat_respone_code'].'%'.$params['repeat_request'].'%'.$params['repeat_keywords'].$params['norepeat_respone_code'];
            $params['source_value']   = $params['source_respone_params'].'%'.$params['source_respone_code'].'%'.$params['source_request'].'%'.$params['source_keywords'];
            $params['submit_value']   = $params['submit_respone_params'].'%'.$params['submit_respone_code'].'%'.$params['submit_request'].'%'.$params['submit_keywords'];
            $params['create_time']    = time();
            $params['source_url']   = trim($params['source_url']);
            $params['IdfaRepeat_url']   = trim($params['IdfaRepeat_url']);
            $params['submit_url']   = trim($params['submit_url']);
            if ($params)
            {
                if ($this->dataLimit && $this->dataLimitFieldAutoFill)
                {
                    $params[$this->dataLimitField] = $this->auth->id;
                }
                try
                {
                    //是否采用模型验证
                    if ($this->modelValidate)
                    {
                        $name = basename(str_replace('\\', '/', get_class($this->model)));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.add' : true) : $this->modelValidate;
                        $this->model->validate($validate);
                    }
                    $result = $this->model->allowField(true)->save($params);
                    if ($result !== false)
                    {
                        $this->success();
                    }
                    else
                    {
                        $this->error($this->model->getError());
                    }
                }
                catch (\think\exception\PDOException $e)
                {
                    $this->error($e->getMessage());
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }

        $sales_list = $this->SalesModel->select();
       
        $channel_list = $this->ChannelModel->order('id asc')->select();

        $this->view->assign("sales",$sales_list);
        $this->view->assign("channel",$channel_list);
        return $this->view->fetch();
    }

    public function ceg($ids){
       $row    = $this->model->get($ids)->getData();

       return json(array('code'=>1,'id'=>$ids,'app_name'=>explode('-',$row['app_name'])[0]));
    }


     public function app_info($appid,$adid,$id){
       $row    = $this->model->where(array('appid'=>$appid,'cpid'=>$adid,'is_disable'=>0))->find();

       if($row && $row['id']!=$id){
            return json(array('code'=>1));
       }else{
        return json(array('code'=>0));
       }
    }


    /**
     * 复制
     */
    public function copy($ids = NULL)
    {

        $row    = $this->model->get($ids)->getData();
        
        $row['create_time']  = time();
        $row['is_disable']   = '1';
       
        unset($row['id']);
        
          
            if ($row)
            {
                if ($this->dataLimit && $this->dataLimitFieldAutoFill)
                {
                    $row[$this->dataLimitField] = $this->auth->id;
                }
                try
                {
                    //是否采用模型验证
                    if ($this->modelValidate)
                    {
                        $name = basename(str_replace('\\', '/', get_class($this->model)));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.copy' : true) : $this->modelValidate;
                        $this->model->validate($validate);
                    }
                    $result = $this->model->allowField(true)->insert($row);
                    if ($result !== false)
                    {
                        $this->success();
                    }
                    else
                    {
                        $this->error($this->model->getError());
                    }
                }
                catch (\think\exception\PDOException $e)
                {
                    $this->error($e->getMessage());
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
      


        
    }

    /**
     * 测试
     */
    public function ceshi($ids = NULL)
    {

        $row    = $this->model->get($ids)->getData();
        $keywords  = trim($_GET['keywords']);
       $appid   = $row['appid'];
       $adid    = $row['cpid'];
       $idfa    = randomkeys(8).'-'.randomkeys(4).'-'.randomkeys(4).'-'.randomkeys(4).'-'.randomkeys(12);
       $ip    = sjIP();
       $time    = time();
         $devices    = array('iPhone7,1','iPhone7,2','iPhone8,1','iPhone8,2','iPhone8,4','iPhone9,1','iPhone9,2','iPhone9,3' ,'iPhone9,4','iPhone10,1','iPhone10,2','iPhone10,4','iPhone10,5','iPhone10,3','iPhone10,6','iPhone11,8','iPhone11,2');
        $oss        = array('12.0','12.1','11.4.1','12.0.1','11.4','11.1.2','11.2','11.2.6','11.4.2','11.0.3');
        $os         = $oss[array_rand($oss,1)];
        $device     =  $devices[array_rand($devices,1)];
        
        $repeat_status  =json_decode(request_get("http://jfad.appubang.com/api/aso_IdfaRepeat/cpid/666/?adid=$adid&appid=$appid&idfa=$idfa&ip={$ip}&keywords={$keywords}"),true);

        $source_status  = json_decode(request_get("http://jfad.appubang.com/api/aso_source/cpid/666/?adid={$adid}&appid={$appid}&idfa=$idfa&ip={$ip}&timestamp=$time&reqtype=0&device=$device&os=$os&isbreak=0&keywords={$keywords}&callback=http%3A%2F%2Fwww.imoney.one%2Fdiamonds%2Fcallback%2FintegralWall%2Fios%2Faipu%3Fsnuid%3DHU11BkmaxtMExwNu3zjiQCFwl2YxxMXAa4Gjs1JHNPaIS3fYJd5cCF0dht5rRpzuZsLd_icOLg64MJofbCsX3YrUgiLADKH654gpDCZXhyV7UxEkZgChuYL0oJDyGUDhyKHHGuwf00d0uOkwC5toGg&sign=539327b3d8452ff9639c4b03cb09be27"),true);
        //echo "http://jfad.appubang.com/api/aso_source/cpid/666/?adid={$adid}&appid={$appid}&idfa=$idfa&ip={$ip}&timestamp=$time&reqtype=0&device=iphone&os=9.3.2&isbreak=0&keywords={$keywords}&callback=http%3A%2F%2Fwww.imoney.one%2Fdiamonds%2Fcallback%2FintegralWall%2Fios%2Faipu%3Fsnuid%3DHU11BkmaxtMExwNu3zjiQCFwl2YxxMXAa4Gjs1JHNPaIS3fYJd5cCF0dht5rRpzuZsLd_icOLg64MJofbCsX3YrUgiLADKH654gpDCZXhyV7UxEkZgChuYL0oJDyGUDhyKHHGuwf00d0uOkwC5toGg&sign=539327b3d8452ff9639c4b03cb09be27";

        $submit_status     = json_decode(request_get("http://jfad.appubang.com/api/aso_Submit/cpid/666/?adid={$adid}&appid={$appid}&idfa=$idfa&timestamp=$time&ip={$ip}&sign=e3662ccb8d8220588b660094e891e953&keywords={$keywords}&device=$device&os=$os"),true);
        $istr     = $idfa;
        $repeat_info = isset($repeat_status['message'])?$repeat_status['message']:'';
        $source_info = isset($source_status['message'])?$source_status['message']:'';
        $submit_info = isset($submit_status['message'])?$submit_status['message']:'';
        if($row['is_repeat']==1){
            if(isset($repeat_status[$idfa]) && $repeat_status[$idfa]=='1'){
                $rstr="排重测试通过√".$repeat_info;
                $data['rt']  = 1;
            }else{
                $rstr="排重测试异常X".$repeat_info;
                $data['rt']  = 0;
                $data['rt_error']=$repeat_status['ErrorInfo'];
            }   
        }else{
                $rstr ='未开通排重接口';
                $data['rt']  = 2;
        }
        
        if($row['is_source']==1){
            if(isset($source_status['code']) && $source_status['code']==0){
                $sstr="点击测试通过√".$source_info;
                $data['st']  = 1;
            }else{
                $sstr="点击测试异常X".$source_info;
                $data['st']  = 0; 
                $data['st_error'] =$source_status['ErrorInfo'];
            }
        }else{
             $sstr ='未开通点击接口';
             $data['st']  = 2;
        }
        
        if($row['is_submit']==1){
            if(isset($submit_status['code']) && $submit_status['code']==0){
                $bstr='上报测试通过√'.$submit_info;
                $data['ut']  = 1;
            }else{
                $bstr="上报测试异常X".$submit_info;
                $data['ut']  = 0;
                $data['ut_error']=$submit_status['ErrorInfo'];
            }
        }else{
             $bstr ='未开通上报接口';
             $data['ut']  = 2;
        }
        
        $data['title']  = $istr;
        $data['repeat']  = $rstr;
        $data['source']  = $sstr;
        $data['submit']  = $bstr;

        return json(array('code'=>1,'data'=> $data));
        //return '<div style="text-align:center;line-height: 100%;height:600px"><div style="text-align:left;">'.'<div style="margin-top:10px;">'.$istr.'</div><br/><div style="margin-top:10px;">'.$rstr.'</div><br/><div style="margin-top:10px;">'.$sstr.'</div><br/><div style="margin-top:10px;">'.$bstr.'</div></div></div>';
            // if ($row)
            // {
            //     if ($this->dataLimit && $this->dataLimitFieldAutoFill)
            //     {
            //         $row[$this->dataLimitField] = $this->auth->id;
            //     }
            //     try
            //     {
            //         //是否采用模型验证
            //         if ($this->modelValidate)
            //         {
            //             $name = basename(str_replace('\\', '/', get_class($this->model)));
            //             $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.copy' : true) : $this->modelValidate;
            //             $this->model->validate($validate);
            //         }
            //         $result = $this->model->allowField(true)->insert($row);
            //         if ($result !== false)
            //         {
            //             $this->success();
            //         }
            //         else
            //         {
            //             $this->error($this->model->getError());
            //         }
            //     }
            //     catch (\think\exception\PDOException $e)
            //     {
            //         $this->error($e->getMessage());
            //     }
            // }
            // $this->error(__('Parameter %s can not be empty', ''));
      


        
    }
    /*
    *清除关键词缓存
    */
    public function del_cache(){
        $keywords      = $_GET['keyword'];

        $file_contents = json_decode(request_get("http://rank.noenc.com/as_keyword/apiv2.php?action=del_cache&keyword=$keywords"),true);


        return json_encode(array('code'=>$file_contents['status']));

    }

    public function get_appname(){
        $appid      = $_GET['id'];

        $json = json_decode(request_get("https://itunes.apple.com/lookup?id=$appid"),true);
        if($json['resultCount']==0){
            $json = json_decode(request_get("https://itunes.apple.com/cn/lookup?id=$appid"),true);
        }
        
        if($json['resultCount']==0){
            return json(array('code'=>1,'message'=>'产品已下架'));
        }
        $appname     = $json['results'][0]['trackName'];
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
       return json(array('code'=>0,'name'=>$appname));

    }


    /*
    *本地数据查询
    */
    public function local_data(){

        $AppId      = $_GET['appid'];

        
        $total = $this->SubmitModel->where(array('appid'=>$AppId))->count();


        return json(array('code'=>0,'total'=>$total));

    }


    /**
     * 导入
     */
    public function import()
    {
         

        
        set_time_limit(0);
        $file = $this->request->request('file');

         $DirPath = ROOT_PATH . 'public' . DS.explode('/',$file)[1].'/'.explode('/',$file)[2];

        if (!$file)
        {
            $this->error(__('Parameter %s can not be empty', 'file'));
        }
        $filePath = ROOT_PATH . DS . 'public' . DS . $file;
        if (!is_file($filePath))
        {
            $this->error(__('No results were found'));
        }
        $PHPReader = new \PHPExcel_Reader_Excel2007();
        if (!$PHPReader->canRead($filePath))
        {
            $PHPReader = new \PHPExcel_Reader_Excel5();
            if (!$PHPReader->canRead($filePath))
            {
                $PHPReader = new \PHPExcel_Reader_CSV();
                if (!$PHPReader->canRead($filePath))
                {
                    $this->error(__('Unknown data format'));
                }
            }
        }

        //导入文件首行类型,默认是注释,如果需要使用字段名称请使用name
        $importHeadType = isset($this->importHeadType) ? $this->importHeadType : 'comment';

        $table = $this->model->getQuery()->getTable();
        $database = \think\Config::get('database.database');
        $fieldArr = [];
        $list = db()->query("SELECT COLUMN_NAME,COLUMN_COMMENT FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = ? AND TABLE_SCHEMA = ?", [$table, $database]);


        foreach ($list as $k => $v)
        {
            if ($importHeadType == 'comment')
            {
                $fieldArr[$v['COLUMN_COMMENT']] = $v['COLUMN_NAME'];
            }
            else
            {
                $fieldArr[$v['COLUMN_NAME']] = $v['COLUMN_NAME'];
            }
        }

        $PHPExcel = $PHPReader->load($filePath); //加载文件
        $currentSheet = $PHPExcel->getSheet(0);  //读取文件中的第一个工作表
        $allColumn = $currentSheet->getHighestDataColumn(); //取得最大的列号
        $allRow = $currentSheet->getHighestRow(); //取得一共有多少行
        $maxColumnNumber = \PHPExcel_Cell::columnIndexFromString($allColumn);
        $appid ='';//定义appid变量
        for ($currentRow = 1; $currentRow <= 1; $currentRow++)
        {
            for ($currentColumn = 0; $currentColumn < $maxColumnNumber; $currentColumn++)
            {
                // $val = $currentSheet->getCellByColumnAndRow($currentColumn, $currentRow)->getValue();
                // $fields[] = $val;
                $appid = $currentSheet->getCellByColumnAndRow($currentColumn, $currentRow)->getValue();
            }
        }
        if(!is_numeric($appid) || $appid==''){
        	return 'appid error';
        }
        $is_exist   = $this->model->where(array('appid'=>$appid,'cpid'=>1))->find();
        $AdminId    = $_SESSION['think']['admin']['id'];
        if(!$is_exist){
            $itunesMessage = json_decode(file_get_contents("http://itunes.apple.com/lookup?id=$appid"),true);
            
            //var_dump($itunesMessage);
            if($itunesMessage['results']){
                $trackName = $itunesMessage['results'][0]['trackName'];
            }else{
                $itunesMessage = json_decode(file_get_contents("http://itunes.apple.com/cn/lookup?id=$appid"),true);
                if($itunesMessage['results']){
                    $trackName = $itunesMessage['results'][0]['trackName'];
                }else{
                    $trackName ='本地排重';
                }
               
            }
            $addData['appid'] = $appid;
            $addData['cpid']  = 1;
            $addData['app_name']  = $trackName;
            $addData['channel'] = 207;
            $addData['salesman']  = $AdminId;
            $addData['is_advert']  = 0;
            $addData['is_submit']  = 1;
            $addData['is_source']  = 1;
            $addData['is_repeat']  = 1;
            $addData['api_cat']  = 1;
            $addData['create_time']  = time();
            $this->model->insert($addData);
            unset($itunesMessage);
            unset($trackName);
        }
        $data_al['appid']       = $appid;
        $data_al['sales_id']    = $AdminId;
        $data_al['upload_time'] = time();
        $data_al['status']      = 0;
        $data_al['path']        = $DirPath;
       	$this->FileModel->insert($data_al);
       // $output = exec("python /www/web/default/python/ImportData.py {$appid} {$DirPath}",$out,$res);

       //  deldir($DirPath);

       //  return 'success';
       
    }

     /**
     * 导入
     */
    public function import2()
    {
         

        
        set_time_limit(0);
        $file = $this->request->request('file');

         $DirPath = ROOT_PATH . 'public' . DS.explode('/',$file)[1].'/'.explode('/',$file)[2];

        if (!$file)
        {
            $this->error(__('Parameter %s can not be empty', 'file'));
        }
        $filePath = ROOT_PATH . DS . 'public' . DS . $file;
        if (!is_file($filePath))
        {
            $this->error(__('No results were found'));
        }
        $PHPReader = new \PHPExcel_Reader_Excel2007();
        if (!$PHPReader->canRead($filePath))
        {
            $PHPReader = new \PHPExcel_Reader_Excel5();
            if (!$PHPReader->canRead($filePath))
            {
                $PHPReader = new \PHPExcel_Reader_CSV();
                if (!$PHPReader->canRead($filePath))
                {
                    $this->error(__('Unknown data format'));
                }
            }
        }

        //导入文件首行类型,默认是注释,如果需要使用字段名称请使用name
        $importHeadType = isset($this->importHeadType) ? $this->importHeadType : 'comment';

        $table = $this->model->getQuery()->getTable();
        $database = \think\Config::get('database.database');
        $fieldArr = [];
        $list = db()->query("SELECT COLUMN_NAME,COLUMN_COMMENT FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = ? AND TABLE_SCHEMA = ?", [$table, $database]);


        foreach ($list as $k => $v)
        {
            if ($importHeadType == 'comment')
            {
                $fieldArr[$v['COLUMN_COMMENT']] = $v['COLUMN_NAME'];
            }
            else
            {
                $fieldArr[$v['COLUMN_NAME']] = $v['COLUMN_NAME'];
            }
        }

        $PHPExcel = $PHPReader->load($filePath); //加载文件
        $currentSheet = $PHPExcel->getSheet(0);  //读取文件中的第一个工作表
        $allColumn = $currentSheet->getHighestDataColumn(); //取得最大的列号
        $allRow = $currentSheet->getHighestRow(); //取得一共有多少行
        $maxColumnNumber = \PHPExcel_Cell::columnIndexFromString($allColumn);
        $appid ='';//定义appid变量
        for ($currentRow = 1; $currentRow <= 1; $currentRow++)
        {
            for ($currentColumn = 0; $currentColumn < $maxColumnNumber; $currentColumn++)
            {
                // $val = $currentSheet->getCellByColumnAndRow($currentColumn, $currentRow)->getValue();
                // $fields[] = $val;
                $appid = $currentSheet->getCellByColumnAndRow($currentColumn, $currentRow)->getValue();
            }
        }
        $is_exist   = $this->model->where(array('appid'=>$appid,'cpid'=>1))->find();
        $AdminId    = $_SESSION['think']['admin']['id'];
        if(!$is_exist){
            $itunesMessage = json_decode(file_get_contents("http://itunes.apple.com/lookup?id=$appid"),true);
            
            //var_dump($itunesMessage);
            if($itunesMessage['results']){
                $trackName = $itunesMessage['results'][0]['trackName'];
            }else{
                $trackName = '本地排重';
            }
            $addData['appid'] = $appid;
            $addData['cpid']  = 1;
            $addData['app_name']  = $trackName;
            $addData['channel'] = 207;
            $addData['salesman']  = $AdminId;
            $addData['is_advert']  = 0;
            $addData['is_submit']  = 1;
            $addData['is_source']  = 1;
            $addData['is_repeat']  = 1;
            $addData['api_cat']  = 1;
            $addData['create_time']  = time();
            $this->model->insert($addData);
            unset($itunesMessage);
            unset($trackName);
        }
       
       $output = exec("python /www/web/default/python/ImportData.py {$appid} {$DirPath}",$out,$res);

        deldir($DirPath);

        return '文件上传成功，正在排队处理，请10分钟后在排重文件列表里看处理结果';
       
    }


     /**
     * 导入
     */
    public function import1()
    {
        set_time_limit(0);
        $file = $this->request->request('file');

        if (!$file)
        {
            $this->error(__('Parameter %s can not be empty', 'file'));
        }
        $filePath = ROOT_PATH . DS . 'public' . DS . $file;
        if (!is_file($filePath))
        {
            $this->error(__('No results were found'));
        }
        $PHPReader = new \PHPExcel_Reader_Excel2007();
        if (!$PHPReader->canRead($filePath))
        {
            $PHPReader = new \PHPExcel_Reader_Excel5();
            if (!$PHPReader->canRead($filePath))
            {
                $PHPReader = new \PHPExcel_Reader_CSV();
                if (!$PHPReader->canRead($filePath))
                {
                    $this->error(__('Unknown data format'));
                }
            }
        }

        //导入文件首行类型,默认是注释,如果需要使用字段名称请使用name
        $importHeadType = isset($this->importHeadType) ? $this->importHeadType : 'comment';

        $table = $this->model->getQuery()->getTable();
        $database = \think\Config::get('database.database');
        $fieldArr = [];
        $list = db()->query("SELECT COLUMN_NAME,COLUMN_COMMENT FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = ? AND TABLE_SCHEMA = ?", [$table, $database]);


        foreach ($list as $k => $v)
        {
            if ($importHeadType == 'comment')
            {
                $fieldArr[$v['COLUMN_COMMENT']] = $v['COLUMN_NAME'];
            }
            else
            {
                $fieldArr[$v['COLUMN_NAME']] = $v['COLUMN_NAME'];
            }
        }

        $PHPExcel = $PHPReader->load($filePath); //加载文件
        $currentSheet = $PHPExcel->getSheet(0);  //读取文件中的第一个工作表
        $allColumn = $currentSheet->getHighestDataColumn(); //取得最大的列号
        $allRow = $currentSheet->getHighestRow(); //取得一共有多少行
        $maxColumnNumber = \PHPExcel_Cell::columnIndexFromString($allColumn);
        $appid ='';//定义appid变量
        for ($currentRow = 1; $currentRow <= 1; $currentRow++)
        {
            for ($currentColumn = 0; $currentColumn < $maxColumnNumber; $currentColumn++)
            {
                // $val = $currentSheet->getCellByColumnAndRow($currentColumn, $currentRow)->getValue();
                // $fields[] = $val;
                $appid = $currentSheet->getCellByColumnAndRow($currentColumn, $currentRow)->getValue();
            }
        }
       
        $insert = [];
        for ($currentRow = 2; $currentRow <= $allRow; $currentRow++)
        {
            //$values = [];
            for ($currentColumn = 0; $currentColumn < $maxColumnNumber; $currentColumn++)
            {
                $val = $currentSheet->getCellByColumnAndRow($currentColumn, $currentRow)->getValue();
                $insert[$currentRow]['idfa'] = is_null($val) ? '' : $val;
                if($val){
                    $insert[$currentRow]['idfa'] = is_null($val) ? '' : $val;
                    $insert[$currentRow]['appid'] = $appid;
                    $insert[$currentRow]['cpid'] = 207;
                    $insert[$currentRow]['timestamp'] = 1;
                    $insert[$currentRow]['type'] = 1;
                }                

            }
            // $row = [];
            // $temp = array_combine($fields, $values);
            // foreach ($temp as $k => $v)
            // {
            //     if (isset($fieldArr[$k]) && $k !== '')
            //     {
            //         $row[$fieldArr[$k]] = $v;
            //     }
            // }
            // if ($row)
            // {
            //     $insert[] = $row;
            // }
        }
        if (!$insert)
        {
            $this->error(__('No rows were updated'));
        }
        try
        {
            $is_exist   = $this->model->where(array('appid'=>$appid,'cpid'=>1))->find();
            $AdminId    = $_SESSION['think']['admin']['id'];
            if(!$is_exist){
                $itunesMessage = json_decode(file_get_contents("http://itunes.apple.com/lookup?id=$appid"),true);
                
                //var_dump($itunesMessage);
                if($itunesMessage['results']){
                    $trackName = $itunesMessage['results'][0]['trackName'];
                }else{
                    $trackName = '本地排重';
                }
                $addData['appid'] = $appid;
                $addData['cpid']  = 1;
                $addData['app_name']  = $trackName;
                $addData['channel'] = 207;
                $addData['salesman']  = $AdminId;
                $addData['is_advert']  = 0;
                $addData['is_submit']  = 1;
                $addData['is_source']  = 1;
                $addData['is_repeat']  = 1;
                $addData['api_cat']  = 1;
                $addData['create_time']  = time();
                $this->model->insert($addData);
                unset($itunesMessage);
                unset($trackName);
            }
            $this->SubmitModel->saveAll($insert);

        }
        catch (\think\exception\PDOException $exception)
        {
            $this->error($exception->getMessage());
        }

       $this->success();
    }


    

}
