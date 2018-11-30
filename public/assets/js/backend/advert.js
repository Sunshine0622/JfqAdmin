define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'advert/index',
                    add_url: 'advert/add',
                    apiwf_url: 'advert/apiwf',
                    edit_url: 'advert/edit',
                    del_url: 'advert/del',
                    import_url: 'advert/import',
                    multi_url: 'advert/multi',
                    table: 'advert',
                }
            });

            var table = $("#table");
            var id    = 'id';
            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        // {checkbox: true},
                        // {field: 'id', title: __('Id')},
                        {field: 'app_name', title: __('App_name'),formatter:Table.api.formatter.search,operate:'LIKE'},
                        // {field: 'AppLogo', title: 'Logo图', formatter: Table.api.formatter.image},
                        {field: 'cpid', title: __('Cpid'),formatter:Table.api.formatter.search},
                        // {field: 'charge', title: 'CP广告ID',formatter:Table.api.formatter.promptt},
                        {field: 'appid', title: __('Appid'),formatter:Table.api.formatter.search},
                        {field: 'channel.name', title: __('Channel'),formatter:Table.api.formatter.search,operate:'LIKE'},
                        {field: 'sales.sales_name', title: '负责人' ,formatter:Table.api.formatter.search,operate:'LIKE'},
                        // {
                        //     field: 'api_type',
                        //     width: "120px",
                        //     title: 'ce',
                        //     table: table,
                        //     events: Table.api.events.operate,
                        //     buttons: [
                        //         {
                        //             name: 'detail',
                        //             text: 'api_type',
                        //             title: 'api_type',
                        //             classname: 'btn btn-xs btn-primary btn-ajax',
                        //             // icon: 'fa fa-list',
                        //             url: 'example/bootstraptable/detail',
                        //             callback: function (data) {
                        //                 Layer.alert("接收到回传数据：" + JSON.stringify(data), {title: "回传数据"});
                        //             },
                        //             visible: function (row) {
                        //                 //返回true时按钮显示,返回false隐藏
                        //                 return true;
                        //             }
                        //         }
                        //       ],formatter: Table.api.formatter.buttons  
                        //   },      
                        
                        {field: "api_type", title: '接口模式',formatter:Table.api.formatter.label},
                        
                        {field: 'create_time', title: '添加时间', operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                         {field: 'is_disable', title: '状态', operate: false,formatter:Table.api.formatter.status,custom: {'禁用':'danger', '正常':'success'}},
                        
                    //  {field: 'is_disable', title: '状态', operate: false,editable: {
                    //     type: 'select',
                    //     pk: 1,
                    //     source: [
                    //         {value: '正常', text: '正常'},
                    //         {value: '禁用', text: '禁用'},
                    //     ]
                    // }},

                    
                       
                        // {field: 'is_disable_text', title: __('Is_disable'), operate:false},
                       
                        // {field: 'api_cat', title: __('Api_cat'), visible:false, searchList: {"2) unsigne":__('2) unsigne')}},
                        // {field: 'api_cat_text', title: __('Api_cat'), operate:false},
                        // {field: 'is_repeat', title: __('Is_repeat'), visible:false, searchList: {"2) unsigne":__('2) unsigne')}},
                        // {field: 'is_repeat_text', title: __('Is_repeat'), operate:false},
                        // {field: 'is_source', title: __('Is_source'), visible:false, searchList: {"2) unsigne":__('2) unsigne')}},
                        // {field: 'is_source_text', title: __('Is_source'), operate:false},
                        // {field: 'is_submit', title: __('Is_submit'), visible:false, searchList: {"2) unsigne":__('2) unsigne')}},
                        // {field: 'is_submit_text', title: __('Is_submit'), operate:false},
                       // {field: 'switch', title: __('Switch'), searchList: {"1": __('Yes'), "0": __('No')}, formatter: Table.api.formatter.toggle},
                        {field: 'operate', title: __('Operate'), table: table, buttons: [
                                {
                                    name: 'detail',
                                    text: __('测试'),
                                    title: __('测试'),
                                    classname: 'btn btn-xs btn-primary btn-ajax',
                                    // icon: 'fa fa-list',
                                    url: 'advert/ceg',
                                    success: function (data, ret) {
                                    
                                layer.prompt({title: '请输入测试关键词',value: ret.app_name,},function(val, index){
                                        
                             
                                layer.close(index);
                               $.get("advert/ceshi",{ids:ret.id,keywords:val},function(msg){
                                    var source_content;
                                    var repeat_content;
                                    var submit_content;
                                    if(msg.data.rt==1){
                                        var rc = "color:blue";
                                        repeat_content ='排重接口测试通过√';
                                    }else if(msg.data.rt==0){
                                        var rc = "color:red";
                                        repeat_content ='排重接口测试异常信息：'+msg.data.rt_error;
                                    }else{
                                        var rc = "color:gray";
                                        repeat_content ='排重接口未开通';
                                    }
                                    if(msg.data.st==1){
                                        var sc = "color:blue";
                                        source_content = '点击接口测试通过√';
                                    }else if(msg.data.st==0){
                                        var sc = "color:red";
                                        source_content ='点击接口测试异常信息：'+msg.data.st_error;
                                    }else{
                                        var sc = "color:gray";
                                       source_content ='点击接口未开通';
                                    }

                                    if(msg.data.ut==1){
                                        var uc = "color:blue";
                                        submit_content = '上报接口测试通过√';
                                    }else if(msg.data.ut==0){
                                        var uc = "color:red";
                                       submit_content ='上报接口测试异常信息：'+msg.data.ut_error;
                                    }else{
                                        var uc = "color:gray";
                                        submit_content ='上报接口未开通';
                                    }
                                    layer.open({
                                      type: 1,
                                      skin: 'layui-layer-rim', //加上边框
                                      area: ['420px', '420px'], //宽高
                                      content: '<div style="margin-top:10px;text-align:center;font-size:16px;font-weight:bold">'+msg.data.title+'</div><br/><div style="margin-top:10px;font-size:16px;text-align:center;'+rc+'">'+repeat_content+'</div><br/><div style="margin-top:10px;font-size:16px;text-align:center;'+sc+'">'+source_content+'</div><br/><div style="margin-top:10px;font-size:16px;text-align:center;'+uc+'">'+submit_content+'</div>'
                                    });
                                },'json')
                             
                              
                            });
                //如果需要阻止成功提示，则必须使用return false;
                //return false;
                                  },
                                    error: function (data, ret) {
                                    }
                                },

                                //{name: 'detail', text: '测试', title: '测试', icon: 'fa fa-list', classname: 'CE btn btn-xs btn-primary btn-button'},
                               // {name: 'detail', text: '测试', title: '测试', icon: 'fa fa-list', classname: 'CE btn btn-xs btn-primary btn-button', extend:'onclick="shoucang()"'},
                                 {name: 'name1', text: '接口外放', title: '接口外放', icon: 'fa fa-share-square-o', classname: 'btn btn-xs btn-primary btn-dialog', url: 'advert/apiwf', callback:function(data){alert(1111)}},
                                {name: 'detail', text: '复制', title: '复制',  classname: 'btn btn-xs btn-primary btn-ajax', url: 'advert/copy',success: function (data, ret) {table.bootstrapTable('refresh');}}
                                 
                            ],events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
        },
        apiwf: function () {
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});


function charge(id){
    layer.prompt({title: '配置CP广告id'},function(val, index){
         $.get("advert/ecpid",{ids:id,cpid:val},function(msg){
                 
            },'json')
  layer.msg('得到了'+val);                               
                             
        layer.close(index);
       
      
    });
}

$('#DelK').click(function(){
    var keywords=prompt("请输入关键词");
    
    if(keywords && keywords.length>0){
            $.get("advert/del_cache",{action:'del_cache',keyword:encodeURIComponent(keywords)},function(msg){
               
                  if(msg=='{\"code\":0}'){
                        alert('清除缓存成功');
                  }else{
                        alert('清除缓存失败');
                  }
            },'json')
    }
    
   
})





 $("#c-appid").on("input propertychange change",function(data){
    var value = $(this).val();
    $.get("advert/get_appname",{id:value},function(msg){
           
           
       if(msg.code==0){
        $("#c-app_name").val(msg.name);

       }
        
   
            
     
      },'json')
});
$("#add-form").click(function(e){
	var appid= $('#c-appid').val();
	var adid = $('#c-cpid').val();
    var id = $('#c-id').val();
	$.get('advert/app_info',{appid:appid,adid:adid,id:id},function(msg){
		if(msg.code==1){
			layer.alert('当前渠道已存在该产品，请先禁用！', {
			  icon: 2,
			  skin: 'layer-ext-moon' //该皮肤由layer.seaning.com友情扩展。关于皮肤的扩展规则，去这里查阅
			})
		}else{
			$('form').submit();
		}
	},'json')
  //alert(1111111);
  
});


$("#apiwf-form").click(function(e){
    var appid= $('#wf-appid').val();
    var adid = $('#wf-adid').val();
    var channel = $('#wf-channel').val();
    var key   =$('#key'+channel+'').attr('data-id');
    if(key.length==0){
        var keyData = '该渠道暂无key值';
    }else{
        var keyData = key;
    }
     $.get('advert/apiwf',{data:appid+'&adid='+adid+'&cpid='+channel},function(msg){
       layer.alert('链接生成成功！<br/><br/> <span style="color:black;font-size:16px">http://jfad.appubang.com/doc/wf?params='+msg.key+'</span><br/><br/><span >key值：'+keyData+'</span><br/><br/><span style="color:red">(注意：如果该渠道有key值则需要把key 值给外放渠道方否则 无法看到接口文档)</span>', {
              icon: 1,
              skin: 'layer-ext-moon' 
            })
    },'json')
    
 
  
});

