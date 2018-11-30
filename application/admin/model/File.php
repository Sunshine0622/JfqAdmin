<?php

namespace app\admin\model;

use think\Model;

class File extends Model
{
    // 表名
    protected $name = 'file';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    
    // 追加属性
    protected $append = [
        'upload_time_text',
        'status_text'
    ];
    

    
    public function getStatusList()
    {
        return ['2) unsigne' => __('2) unsigne')];
    }     


    public function getUploadTimeTextAttr($value, $data)
    {
        $value = $value ? $value : $data['upload_time'];
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


    public function getStatusTextAttr($value, $data)
    {        
        $value = $value ? $value : $data['status'];
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    protected function setUploadTimeAttr($value)
    {
        return $value && !is_numeric($value) ? strtotime($value) : $value;
    }

    public function sales()
    {
        return $this->belongsTo('Sales', 'sales_id', 'sales_id', [], 'LEFT')->setEagerlyType(0);
    }


}
