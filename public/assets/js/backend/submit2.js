define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'submit2/index',
                    add_url: 'submit2/add',
                    edit_url: 'submit2/edit',
                    del_url: 'submit2/del',
                    multi_url: 'submit2/multi',
                    table: 'submit2',
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
                        {field: 'channel', title: '来源'},
                        {field: 'app_name', title: '应用名称'},
                        {field: 'appid', title: __('Appid')},
                        {field: 'idfa', title: __('Idfa')},
                        {field: 'keywords', title: __('Keywords')},
                        {field: 'timestamp', title: '激活时间', operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'type', title: '激活方式'},
                        // {field: 'is_mobile', title: __('Is_mobile'), visible:false, searchList: {"2) unsigne":__('2) unsigne')}},
                        // {field: 'is_mobile_text', title: __('Is_mobile'), operate:false},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
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