<?php

namespace app\admin\model;

use think\Model;

class Fzsource extends Model
{
    // 表名
    protected $name = 'fzsource';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    
    // 追加属性
    protected $append = [
        'statusdata_text'
    ];
    

    
    public function getStatusdataList()
    {
        return ['100' => __('Statusdata 100'),'101' => __('Statusdata 101')];
    }     


    public function getStatusdataTextAttr($value, $data)
    {        
        $value = $value ? $value : $data['statusdata'];
        $list = $this->getStatusdataList();
        return isset($list[$value]) ? $list[$value] : '';
    }




}
