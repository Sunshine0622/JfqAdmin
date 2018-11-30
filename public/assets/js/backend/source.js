define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'source/index',
                    add_url: 'source/add',
                    edit_url: 'source/edit',
                    del_url: 'source/del',
                    multi_url: 'source/multi',
                    table: 'source',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        // {checkbox: true},
                        // {field: 'id', title: __('Id')},
                        {field: 'channel.name', title: '平台'},
                        {field: 'app_name', title: 'App名称'},
                        {field: 'appid', title: 'Appid'},
                         {field: 'adid', title: '广告id'},
                        {field: 'idfa', title: 'Idfa'},
                        {field: 'ip', title: '客户端Ip'},
                        {field: 'keywords', title: '关键词'},
                         {field: 'device', title:'型号'},
                        {field: 'os', title: '系统'},
                        // {field: 'status', title: '状态', formatter:Table.api.formatter.status},
                        {field: 'type', title: '状态', formatter: Table.api.formatter.status, searchList: {'0': '未激活', '1': '上报激活','2': '回调激活'},custom: {'未激活':'danger', '上报激活':'success','回调激活':'success'}, style: 'min-width:100px;'},
                        //{field: 'id', title: '类型', formatter: Table.api.formatter.status, searchList: {'normal': __('Normal'), 'hidden': __('Hidden')}, style: 'min-width:100px;'},
                       {field: 'timestamp', title: '任务时间', operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},

                        {field: 'activetime', title: '激活时间', operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime}
                       // {field: 'stime', title: '激活时间', operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        // {field: 'sign', title: __('Sign')},
                        // {field: 'session_id', title: __('Session_id')},
                        // {field: 'type', title: __('Type')},
                        // {field: 'reqtype', title: __('Reqtype')},
                       
                        // {field: 'isbreak', title: __('Isbreak')},
                        //{field: 'callback', title: '回调地址'},
                        // {field: 'callback', title: '回调地址', formatter: Table.api.formatter.url},
                        // {field: 'backtype', title: __('Backtype')},
                        // {field: 'submit', title: __('Submit')},
                       
                        
                        // {field: 'is_mobile', title: __('Is_mobile'), visible:false, searchList: {"2) unsigne":__('2) unsigne')}},
                        // {field: 'is_mobile_text', title: __('Is_mobile'), operate:false},
                        // {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
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