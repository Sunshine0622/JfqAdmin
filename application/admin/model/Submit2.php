<?php

namespace app\admin\model;

use think\Model;

class Submit2 extends Model
{
    // 表名
    protected $name = 'submit2';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    
    // 追加属性
    protected $append = [
        'is_mobile_text'
    ];
    

    
    public function getIsMobileList()
    {
        return ['2) unsigne' => __('2) unsigne')];
    }     


    public function getIsMobileTextAttr($value, $data)
    {        
        $value = $value ? $value : $data['is_mobile'];
        $list = $this->getIsMobileList();
        return isset($list[$value]) ? $list[$value] : '';
    }

     public function channel()
    {
        return $this->belongsTo('Channel', 'cpid', 'cpid', [], 'LEFT')->setEagerlyType(0);
    }

    public function advert()
    {
        return $this->belongsTo('Advert', 'appid', 'appid', [], 'LEFT')->setEagerlyType(0);
    }


}
