<?php

namespace app\api\controller;
use think\Db;
use app\common\controller\Api;

/**
 * 首页接口
 */
class Index extends Api
{

    protected $noNeedLogin = ['*'];
    protected $noNeedRight = ['*'];

    /**
     * 首页
     * 
     */
	public function home1(){
		$this->success('返回成功');
	}
    public function index()
    {
        $data                 = Db::name('error')->select();
        foreach($data as $k=>$val){
            if($val['source_request_count']!==0){
                    $data[$k]['source_rate']   = round($val['source_error_count']/$val['source_request_count'],2);
            }
            // if($val['repeat_request_count']!==0){
            //          $data[$k]['repeat_rate']   = round($val['repeat_error_count']/$val['repeat_request_count'],2);
            // }
           if($val['submit_request_count']!==0){
             $data[$k]['submit_rate']   = round($val['submit_error_count']/$val['submit_request_count'],2);
           }
           
        }
        $error_info='';
        
        foreach($data as $i=>$j){
            $info  =  Db::name('advert')->where(array('appid'=>$j['appid'],'cpid'=>$j['adid']))->find();
            $salesId  = $info['salesman'];
            $appname  = $info['app_name'];
            $email    = Db::name('admin')->where(array('id'=>$salesId))->find()['email'];
            //$repeat_info='';
            $source_info='';
            $submit_info='';
           
            if(isset($j['source_rate'])){
                    if($j['source_rate']>=0.9){
                
                $source_info  .='点击错误率'.($j['source_rate']*100).'%';
                //sendMail('liuchao@aiyingli.com','','广告：'.$appname.'，appid：'.$j['appid'].'，广告ID：'.$j['adid'].' 接口异常请及时处理');
                 }
            }
            // if(isset($j['repeat_rate'])){
            //         if($j['repeat_rate']>=1){
                
            //     $repeat_info  .='每分钟排重错误率'.($j['repeat_rate']*100).'%';
               
            //         }

            // }
             
             if(isset($j['submit_rate'])){
                    if($j['submit_rate']>=0.7){
                
                $submit_info  .='上报错误率'.($j['submit_rate']*100).'%';
                //sendMail('liuchao@aiyingli.com','','广告：'.$appname.'，appid：'.$j['appid'].'，广告ID：'.$j['adid'].' 接口异常请及时处理');
                 }
             }
             if($source_info!='' || $submit_info!=''){
                $error_info .='(广告：'.$appname.'  appid：'.$j['appid'].'  广告ID：'.$j['adid'].'    '.$source_info.'   '.$submit_info.'),';
                sendMail($email,'','你的产品  (广告：'.$appname.'  appid：'.$j['appid'].'  广告ID：'.$j['adid'].'    '.$source_info.'   '.$submit_info.')请及时处理！');
                
             }
            
           

            
        }
        $error_info = rtrim($error_info,',');
        if( $error_info!=''){
                sendMail('liuchao@aiyingli.com','',$error_info.'请及时处理！');
               sendMail('zilong.wang@aiyingli.com','',$error_info.'请及时处理！');
            }
       
        Db::name('error')->where("1=1")->delete();
        //sendMail('liuchao@aiyingli.com','','点击接口异常记录超过1000次,请及时处理!');
       $this->success('返回成功');
      
    }

    public function DelError(){
        Db::name('error')->where("1=1")->delete();

         $this->success('返回成功');
    }
    public function getRandIdfa(){
    	 $data = Db::query('SELECT idfa FROM `aso_fictitious_submit` where type = 0 ORDER BY RAND() LIMIT 0,1 ');

    	 return $data[0]['idfa'];

    }

    public function getTasking($count){
         $data = Db::query("SELECT * FROM `aso_fictitious_source` where submit = 0 ORDER BY RAND() LIMIT 0,$count ");

         return $data;

    }



    public function Set_Idfa_click(){
      
    	
 
    //执行插入操作
    	while(1){
        
        $NowTime    = time();
        $tasks      =  Db::name('fictitious_task')->where("start_time < $NowTime and $NowTime< end_time and switch = 1")->select();
        foreach($tasks as $a=>$b){
        	if($b['finish_total']>=$b['total']){
        		unset($tasks[$a]);
        	}
        	if($b['rob_total']>=$b['total']){
        		unset($tasks[$a]);
        	}
        }
        if(!empty($tasks)){
           
        $task_ids   = array();

        foreach($tasks as $k=>$val){
            $task_ids[] = $val['id'];
        }

        $id         = $task_ids[array_rand($task_ids,1)];
        $taskData   = Db::name('fictitious_task')->where("id = $id")->find();
        $appid      = $taskData['appid'];
        $adid       = $taskData['adid'];
        $keywords   = $taskData['keywords'];
        $idfa       = $this->getRandIdfa();
        $ip         = sjIP();
        $devices    = array('iPhone7,1','iPhone7,2','iPhone8,1','iPhone8,2','iPhone8,4','iPhone9,1','iPhone9,2','iPhone9,3' ,'iPhone9,4','iPhone10,1','iPhone10,2','iPhone10,4','iPhone10,5','iPhone10,3','iPhone10,6','iPhone11,8','iPhone11,2');
        $oss        = array('12.0','12.1','11.4.1','12.0.1','11.4','11.1.2','11.2','11.2.6','11.4.2','11.0.3');
        $os         = $oss[array_rand($oss,1)];
        $device     =  $devices[array_rand($devices,1)];

         $RepeatInfo= json_decode(request_get("http://asoapi.appubang.com/api/aso_IdfaRepeat/cpid/666/?adid=$adid&appid=$appid&idfa=$idfa&ip={$ip}"),true);

        if($RepeatInfo[$idfa]=='1'){
            $SourceInfo = json_decode(request_get("http://asoapi.appubang.com/api/aso_source/cpid/666/?adid={$adid}&appid={$appid}&idfa=$idfa&ip={$ip}&timestamp=$NowTime&reqtype=0&device=$device&os=$os&isbreak=0&keywords={$keywords}"),true);

            if($SourceInfo['code']==0){
                $insertData['adid']  = $adid;
                $insertData['appid'] = $appid;
                $insertData['idfa']  = $idfa;
                $insertData['ip']    = $ip;
                $insertData['os']    =$os;
                $insertData['keywords']    =$keywords;
                $insertData['device'] = $device;
                $insertData['timestamp'] =$NowTime;
                DB::name('fictitious_source')->insert($insertData);
                Db::name('fictitious_submit')->where(array('idfa'=>$idfa))->update(array('type'=>1));
                Db::name('fictitious_task')->where(array('id'=>$taskData['id']))->update(array('rob_total'=>$taskData['rob_total']+1));
                 
                
            	}
        	}
       
        }

        sleep(rand(1, 3));
         
        }


    
    }


    public function Active_idfa(){
       while(1){

        $NowTime       = time();  
        //随机获取正在进行的任务
        $tasks         = Db::name('fictitious_source')->where("submit=0")->order('id asc')->find();

        $taskInfo      = Db::name('fictitious_task')->where(array('appid'=>$tasks['appid'],'adid'=>$tasks['adid'],'keywords'=>$tasks['keywords']))->find();

        if($taskInfo['finish_total']<$taskInfo['total']){
        	
        $source_time   = $tasks['timestamp'];
        $ztime         = $source_time+180;
        $wtime         = $source_time+600;

        $appid   =  $tasks['appid'];
        $adid   =  $tasks['adid'];
        $keywords   = $tasks['keywords'];
        $ip      =  $tasks['ip'];
         $idfa   =  $tasks['idfa'];
        if($ztime < $NowTime && $NowTime< $wtime ){

             $submitData     = json_decode(request_get("http://asoapi.appubang.com/api/aso_Submit/cpid/666/?adid={$adid}&appid={$appid}&idfa=$idfa&ip={$ip}&keywords={$keywords}"),true);

             if($submitData['code']==0){
                   
                $submit['appid'] = $tasks['appid'];
                 $submit['adid'] = $tasks['adid'];
                $submit['ip'] = $tasks['ip'];
                $submit['os'] = $tasks['os'];
                 $submit['device'] = $tasks['device'];
                 $submit['keywords'] = $tasks['keywords'];
                 $submit['timestamp'] = time();
                

                 $nowFinishTotal = Db::name('fictitious_task')->where(array('id'=>$taskInfo['id']))->value('finish_total');
                  Db::name('fictitious_task')->where(array('id'=>$taskInfo['id']))->update(array('finish_total'=>$nowFinishTotal+1));
                Db::name('fictitious_submit')->where(array('idfa'=>$idfa))->update($submit);
                Db::name('fictitious_source')->where(array('id'=>$tasks['id']))->update(array('submit'=>1));
               
             }
            
       	 	}elseif($NowTime > $wtime){
                Db::name('fictitious_source')->where(array('id'=>$tasks['id']))->update(array('submit'=>2));
                Db::name('fictitious_submit')->where(array('idfa'=>$idfa))->update(array('type'=>0));
                Db::name('fictitious_task')->where(array('id'=>$taskInfo['id']))->update(array('rob_total'=>$taskInfo['rob_total']-1));
            }
   		 }
     
		 sleep(rand(1, 3));
   		} 
    }


    public function SetClick(){
      
        
 
    //执行插入操作
        while(1){
        
        $NowTime    = time();
        $tasks      =  Db::name('fictitious_task')->where("start_time < $NowTime and $NowTime< end_time and switch = 1")->select();
        foreach($tasks as $a=>$b){
            if($b['finish_total']>=$b['total']){
                unset($tasks[$a]);
            }
            if($b['rob_total']>=$b['total']){
                unset($tasks[$a]);
            }
        }
        if(!empty($tasks)){
           
        $task_ids   = array();

        foreach($tasks as $k=>$val){
            $task_ids[] = $val['id'];
        }

        $id         = $task_ids[array_rand($task_ids,1)];
        $taskData   = Db::name('fictitious_task')->where("id = $id")->find();
        $appid      = $taskData['appid'];
        $adid       = $taskData['adid'];
        $keywords   = $taskData['keywords'];
        $idfa       = $this->getRandIdfa();
        $ip         = sjIP();
        $devices    = array('iPhone7,1','iPhone7,2','iPhone8,1','iPhone8,2','iPhone8,4','iPhone9,1','iPhone9,2','iPhone9,3' ,'iPhone9,4','iPhone10,1','iPhone10,2','iPhone10,4','iPhone10,5','iPhone10,3','iPhone10,6','iPhone11,8','iPhone11,2');
        $oss        = array('12.0','12.1','11.4.1','12.0.1','11.4','11.1.2','11.2','11.2.6','11.4.2','11.0.3');
        $os         = $oss[array_rand($oss,1)];
        $device     =  $devices[array_rand($devices,1)];

         $RepeatInfo= json_decode(request_get("http://asoapi.appubang.com/api/aso_IdfaRepeat/cpid/666/?adid=$adid&appid=$appid&idfa=$idfa&ip={$ip}"),true);

        if($RepeatInfo[$idfa]=='1'){
            $SourceInfo = json_decode(request_get("http://asoapi.appubang.com/api/aso_source/cpid/666/?adid={$adid}&appid={$appid}&idfa=$idfa&ip={$ip}&timestamp=$NowTime&reqtype=0&device=$device&os=$os&isbreak=0&keywords={$keywords}"),true);

            if($SourceInfo['code']==0){
                $insertData['adid']  = $adid;
                $insertData['appid'] = $appid;
                $insertData['idfa']  = $idfa;
                $insertData['ip']    = $ip;
                $insertData['os']    =$os;
                $insertData['keywords']    =$keywords;
                $insertData['device'] = $device;
                $insertData['task_id'] = $id;
                $insertData['timestamp'] =$NowTime;
                DB::name('fictitious_source')->insert($insertData);
                Db::name('fictitious_submit')->where(array('idfa'=>$idfa))->update(array('type'=>1));
                Db::name('fictitious_task')->where(array('id'=>$taskData['id']))->update(array('rob_total'=>$taskData['rob_total']+1));
                 
                
                }
            }
       
        }

        sleep(rand(1, 3));
         
        }


    
    }


     public function SetActive(){
        while(1){
        $SetCount      = rand(1,5);
        $NowTime       = time();  
        //随机获取正在进行的任务
        $TaskDatas     = $this->getTasking($SetCount);
      
        //$tasks         = Db::name('fictitious_source')->where("submit=0")->order('id asc')->find();
        foreach($TaskDatas as $k =>$val){
        $taskInfo      = Db::name('fictitious_task')->where(array('id'=>$val['task_id']))->find();

        if($taskInfo['finish_total']<$taskInfo['total']){
            
        $source_time   = $val['timestamp'];
        $ztime         = $source_time+180;
        $wtime         = $source_time+1800;

        $appid   =  $val['appid'];
        $adid   =  $val['adid'];
        $keywords   = $val['keywords'];
        $ip      =  $val['ip'];
         $idfa   =  $val['idfa'];
        if($ztime < $NowTime && $NowTime< $wtime ){

             $submitData     = json_decode(request_get("http://asoapi.appubang.com/api/aso_Submit/cpid/666/?adid={$adid}&appid={$appid}&idfa=$idfa&ip={$ip}&keywords={$keywords}"),true);

             if($submitData['code']==0){
                   
                $submit['appid'] = $val['appid'];
                 $submit['adid'] = $val['adid'];
                $submit['ip'] = $val['ip'];
                $submit['os'] = $val['os'];
                 $submit['device'] = $val['device'];
                 $submit['keywords'] = $val['keywords'];
                 $submit['timestamp'] = time();
                

                 $nowFinishTotal = Db::name('fictitious_task')->where(array('id'=>$taskInfo['id']))->value('finish_total');
                  Db::name('fictitious_task')->where(array('id'=>$taskInfo['id']))->update(array('finish_total'=>$nowFinishTotal+1));
                Db::name('fictitious_submit')->where(array('idfa'=>$idfa))->update($submit);
                Db::name('fictitious_source')->where(array('id'=>$val['id']))->update(array('submit'=>1));
               
               
             }else{
                 Db::name('fictitious_source')->where(array('id'=>$val['id']))->update(array('submit'=>3));
                
                Db::name('fictitious_submit')->where(array('idfa'=>$idfa))->update(array('type'=>0));
                Db::name('fictitious_task')->where(array('id'=>$taskInfo['id']))->update(array('rob_total'=>$taskInfo['rob_total']-1));
             }
            
            }elseif($NowTime > $wtime){
                Db::name('fictitious_source')->where(array('id'=>$val['id']))->update(array('submit'=>2));
                
                Db::name('fictitious_submit')->where(array('idfa'=>$idfa))->update(array('type'=>0));
                Db::name('fictitious_task')->where(array('id'=>$taskInfo['id']))->update(array('rob_total'=>$taskInfo['rob_total']-1));
                 //$this->success('返回成功',$TaskDatas);
            }
         }
        }
        
         sleep(rand(1,5));
        }
    }


    public function getRandTime(){
            $rands = rand(1,10); 

            if($rands<=7){ 

                return rand(0,60);

            }elseif($rands==8){ 

                return rand(120,180);

            }else{ 

                 return rand(60,120);
            }
    }

}
