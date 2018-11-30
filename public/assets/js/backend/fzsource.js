define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'fzsource/index',
                    add_url: 'fzsource/add',
                    edit_url: 'fzsource/edit',
                    del_url: 'fzsource/del',
                    multi_url: 'fzsource/multi',
                    table: 'fzsource',
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
                        {field: 'appid', title: __('Appid')},
                        {field: 'image', title: __('Image'), formatter: Table.api.formatter.image},
                        {field: 'mp3_file', title: __('Mp3_file')},
                        {field: 'source_file', title: __('Source_file')},
                        {field: 'statusdata', title: __('Statusdata'), visible:false, searchList: {"100":__('Statusdata 100'),"101":__('Statusdata 101')}},
                        {field: 'statusdata_text', title: __('Statusdata'), operate:false},
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