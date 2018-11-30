define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'wfqk/index',
                    add_url: 'wfqk/add',
                    edit_url: 'wfqk/edit',
                    del_url: 'wfqk/del',
                    multi_url: 'wfqk/multi',
                    table: 'wfqk',
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
                        {field: 'advert.app_name', title: '应用名称'},
                        {field: 'appid', title: __('Appid')},

                        {field: 'adid', title: __('Adid')},
                       {field: 'status', title: '状态', formatter:Table.api.formatter.status,searchList: {'normal': '正常', 'locked': '禁用'}, style: 'min-width:100px;color:red;'},
                       // {field: 'is_del_text', title: __('Is_del'), operate:false},
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