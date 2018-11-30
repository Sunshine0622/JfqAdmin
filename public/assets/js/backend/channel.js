define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'channel/index',
                    add_url: 'channel/add',
                    edit_url: 'channel/edit',
                    del_url: 'channel/del',
                    multi_url: 'channel/multi',
                    table: 'source_cpid',
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
                        {field: 'cpid', title: '渠道CPID',formatter:Table.api.formatter.search},
                        {field: 'name', title: '渠道名称',formatter:Table.api.formatter.search},
                        {field: 'key', title: '接口加密key',formatter: Table.api.formatter.label},
                        {field: 'note', title: '备注说明',formatter: Table.api.formatter.label},
                        
                        {field: 'ip', title: '服务器ip白名单',formatter: Table.api.formatter.label},
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