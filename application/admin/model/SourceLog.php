<?php

namespace app\admin\model;

use think\Model;

class SourceLog extends Model
{
    // 表名
    protected $name = 'source_log';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    
    // 追加属性
    protected $append = [
        'adid_text',
        'is_mobile_text'
    ];
    

    
    public function getAdidList()
    {
        return ['4) unsigne' => __('4) unsigne')];
    }     

    public function getIsMobileList()
    {
        return ['2) unsigne' => __('2) unsigne')];
    }     


    public function getAdidTextAttr($value, $data)
    {        
        $value = $value ? $value : $data['adid'];
        $list = $this->getAdidList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getIsMobileTextAttr($value, $data)
    {        
        $value = $value ? $value : $data['is_mobile'];
        $list = $this->getIsMobileList();
        return isset($list[$value]) ? $list[$value] : '';
    }




}
