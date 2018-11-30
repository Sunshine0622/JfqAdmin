define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'repeat/index',
                    add_url: 'repeat/add',
                    edit_url: 'repeat/edit',
                    del_url: 'repeat/del',
                    multi_url: 'repeat/multi',
                    table: 'IdfaRepeat_log',
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
                        {field: 'id', title: __('Id')},
                        {field: 'cpid', title: __('Cpid')},
                        {field: 'adid', title: __('Adid')},
                        {field: 'appid', title: __('Appid')},
                        {field: 'idfa', title: __('Idfa')},
                        {field: 'ip', title: __('Ip')},
                        {field: 'json', title: __('Json')},
                        {field: 'date', title: __('Date')},
                        {field: 'is_mobile', title: __('Is_mobile'), visible:false, searchList: {"2) unsigne":__('2) unsigne')}},
                        {field: 'is_mobile_text', title: __('Is_mobile'), operate:false},
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