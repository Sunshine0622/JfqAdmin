define(['jquery', 'bootstrap', 'backend', 'table', 'form','editable'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'fictitious/task/index',
                    add_url: 'fictitious/task/add',
                    edit_url: 'fictitious/task/edit',
                    del_url: 'fictitious/task/del',
                    multi_url: 'fictitious/task/multi',
                    table: 'fictitious_task',
                }
            });

            var table = $("#table");
             table.on('load-success.bs.table', function (e, data) {
    //这里可以获取从服务端获取的JSON数据
    console.log(data);
    //这里我们手动设置底部的值
    $("#idfas").text(data.extend.idfas);
   // $("#price").text(data.extend.price);
});
            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        // {field: 'id', title: '任务ID'},
                        {field: 'adid', title: '广告id'},
                        {field: 'appid', title: __('Appid')},
                        {field: 'AppName', title: 'App名称'},
                         {field: 'AppLogo', title: 'Logo图', formatter: Table.api.formatter.images},
                        {field: 'keywords', title: '任务关键词',editable: true},
                        {field: 'total', title: '总数量'},
                        {field: 'rob_total', title: '已抢数量'},
                        {field: 'start_time', title:'开始时间', operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'end_time', title: '结束时间', operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'finish_total', title:'已完成'},
                         // {field: 'switch', title: '是否开启任务', searchList: {"1": __('Yes'), "0": __('No')}, formatter: Table.api.formatter.toggle},
                        {field: 'operate', title: __('Operate'), table: table,  buttons: [{name: 'detail', text: '导出', title: '导出数据',  classname: 'btn btn-xs btn-primary btn-ajax', url: 'fictitious/task/importidfa',success: function (data, ret) {
                
                window.location.href="http://jfqlk.appubang.com/admin/fictitious/task/importdata?id="+ret.id;
            }}],events: Table.api.events.operate, formatter: Table.api.formatter.operate}
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
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }

       
    };
    return Controller;
});

