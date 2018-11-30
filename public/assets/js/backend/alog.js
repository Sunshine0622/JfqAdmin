define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'alog/index',
                    add_url: 'alog/add',
                    edit_url: 'alog/edit',
                    del_url: 'alog/del',
                    multi_url: 'alog/multi',
                    table: 'submit_log',
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
                        {checkbox: true},
                        // {field: 'id', title: __('Id')},
                        // {field: 'cpid', title: __('Cpid')},
                        // {field: 'adid', title: __('Adid')},
                        {field: 'appid', title: __('Appid')},
                        {field: 'adid', title: '广告id'},
                        {field: 'idfa', title: __('Idfa')},
                        {field: 'keywords', title: '关键词'},
                        {field: 'timestamp', title: '报错时间', operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        // {field: 'type', title: __('Type')},
                        {field: 'json', title: '报错内容'}
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