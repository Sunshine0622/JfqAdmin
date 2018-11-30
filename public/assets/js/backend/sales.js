define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'sales/index',
                    add_url: 'sales/add',
                    edit_url: 'sales/edit',
                    del_url: 'sales/del',
                    multi_url: 'sales/multi',
                    table: 'sales',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'sales_id',
                sortName: 'sales_id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'sales_id', title: __('Sales_id')},
                        {field: 'sales_name', title: __('Sales_name')},
                        // {field: 'email', title: '邮箱'},
                        {field: 'create_time', title: '添加时间', operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
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