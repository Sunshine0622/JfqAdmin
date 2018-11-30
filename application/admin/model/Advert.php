<?php

namespace app\admin\model;

use think\Model;

class Advert extends Model
{
    // 表名
    protected $name = 'advert';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = false;
    
    // 追加属性
    protected $append = [
        'is_advert_text',
        'is_disable_text',
        'api_cat_text',
        'is_repeat_text',
        'is_source_text',
        'is_submit_text'
    ];
    

    
    public function getIsAdvertList()
    {
        return ['2) unsigne' => __('2) unsigne')];
    }     

    public function getIsDisableList()
    {
        return ['0' => __('Is_disable 0'),'1' => __('Is_disable 1')];
    }     

    public function getApiCatList()
    {
        return ['2) unsigne' => __('2) unsigne')];
    }     

    public function getIsRepeatList()
    {
        return ['2) unsigne' => __('2) unsigne')];
    }     

    public function getIsSourceList()
    {
        return ['2) unsigne' => __('2) unsigne')];
    }     

    public function getIsSubmitList()
    {
        return ['2) unsigne' => __('2) unsigne')];
    }     


    public function getIsAdvertTextAttr($value, $data)
    {        
        $value = $value ? $value : $data['is_advert'];
        $list = $this->getIsAdvertList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getIsDisableTextAttr($value, $data)
    {        
        $value = $value ? $value : $data['is_disable'];
        $list = $this->getIsDisableList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getApiCatTextAttr($value, $data)
    {        
        $value = $value ? $value : $data['api_cat'];
        $list = $this->getApiCatList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getIsRepeatTextAttr($value, $data)
    {        
        $value = $value ? $value : $data['is_repeat'];
        $list = $this->getIsRepeatList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getIsSourceTextAttr($value, $data)
    {        
        $value = $value ? $value : $data['is_source'];
        $list = $this->getIsSourceList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getIsSubmitTextAttr($value, $data)
    {        
        $value = $value ? $value : $data['is_submit'];
        $list = $this->getIsSubmitList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    public function sales()
    {
        return $this->belongsTo('Sales', 'salesman', 'sales_id', [], 'LEFT')->setEagerlyType(0);
    }

     public function channel()
    {
        return $this->belongsTo('Channel', 'channel', 'cpid', [], 'LEFT')->setEagerlyType(0);
    }




}
