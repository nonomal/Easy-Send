<?php include_once dirname(__FILE__) . "/../header.php" ?>

<body class="pear-container">
        <div class="pear-main">
            <fieldset class="layui-elem-field">
                <legend>搜索信息</legend>
                <div style="margin: 10px 10px 10px 10px">
                    <form class="layui-form layui-form-pane" action="">
                        <div class="layui-form-item">
                            <div class="layui-inline">
                                <label class="layui-form-label">提取码</label>
                                <div class="layui-input-inline">
                                    <input type="text" name="gkey" autocomplete="off" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-inline">
                                <label class="layui-form-label">文件名称</label>
                                <div class="layui-input-inline">
                                    <input type="text" name="origin" autocomplete="off" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-inline">
                                <label class="layui-form-label">剩余次数</label>
                                <div class="layui-input-inline">
                                    <input type="text" name="times" autocomplete="off" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-inline">
                                <label class="layui-form-label">uid</label>
                                <div class="layui-input-inline">
                                    <input type="number" name="uid" autocomplete="off" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-inline">
                                <label class="layui-form-label">到期时间</label>
                                <div class="layui-input-inline">
                                    <input type="text" name="tillday" autocomplete="off" class="layui-input" id="tillday" placeholder="yyyy-MM-dd">
                                </div>
                            </div>
                            <div class="layui-inline">
                                <button type="submit" class="pear-btn pear-btn-primary" lay-submit lay-filter="data-search-btn"><i class="layui-icon"></i> 搜 索</button>
                            </div>
                        </div>
                    </form>
                </div>
            </fieldset>

            <script type="text/html" id="left-toolbar">
                <div class="pear-btn-container">
                    <button class="pear-btn pear-btn-primary pear-btn-md" lay-event="refresh"><i class="layui-icon layui-icon-refresh"></i> 刷新 </button>
                    <button class="pear-btn pear-btn-md pear-btn-danger data-delete-btn" lay-event="delete"><i class="layui-icon layui-icon-delete"></i> 删除 </button>
                </div>
            </script>

            <table class="layui-hide" id="main-table" lay-filter="main-table-filter"></table>

            <script type="text/html" id="each-tool">
                <a class="pear-btn pear-btn-normal pear-btn-xs data-count-edit" lay-event="edit">编辑</a>
                <a class="pear-btn pear-btn-xs pear-btn-danger data-count-delete" lay-event="delete">删除</a>
            </script>
        </div>
<script>
    layui.use(['form', 'table', 'laypage', 'laydate', 'admin'], function() {
        var $ = layui.jquery,
            form = layui.form,
            laypage = layui.laypage,
            laydate = layui.laydate,
            admin = layui.admin,
            table = layui.table;
        laydate.render({
            elem: "#tillday"
        })
        table.render({
            elem: '#main-table',
            url: '/admin/api/data/file.php',
            toolbar: '#left-toolbar',
            defaultToolbar: ['filter', 'exports', 'print', {
                title: '提示',
                layEvent: 'LAYTABLE_TIPS',
                icon: 'layui-icon-tips'
            }],
            where: {
                action: "get"
            },
            cols: [
                [{
                        type: "checkbox",
                        width: 50
                    },
                    {
                        field: 'id',
                        title: 'ID'
                    },
                    {
                        field: 'origin',
                        minWidth: 150,
                        title: '文件名称'
                    },
                    {
                        field: 'times',
                        minWidth: 100,
                        title: '剩余次数',
                        align: "center"
                    },
                    {
                        field: 'uid',
                        minWidth: 70,
                        title: 'uid',
                        align: "center"
                    },
                    {
                        field: 'tillday',
                        minWidth: 100,
                        title: '到期时间',
                        align: "center"
                    },
                    {
                        title: '操作',
                        minWidth: 150,
                        toolbar: '#each-tool',
                        align: "center"
                    }
                ]
            ],
            limits: [10, 25, 50, 100],
            limit: 10,
            page: true,
            text: {
                none: '暂无相关数据'
            },
            skin: 'line'
        });

        // 监听搜索操作
        form.on('submit(data-search-btn)', function(data) {
            table.reload('main-table', {
                page: {
                    curr: 1
                },
                where: {
                    "action": "search",
                    "data": JSON.stringify(data.field)
                },
            });

            return false;
        });

        /**
         * toolbar监听事件
         */
        table.on('toolbar(main-table-filter)', function(obj) {
            if (obj.event === 'delete') { // 监听删除操作
                var checkStatus = table.checkStatus('main-table'),
                    data = checkStatus.data;

                layer.confirm('真的删除这' + data.length + '行么', function(index) {
                    $.ajax({
                        type: "POST",
                        url: "/admin/api/data/file.php",
                        data: {
                            "type": "some",
                            "action": "delete",
                            "data": JSON.stringify(data)
                        },
                        dataType: "json",
                        async: false,
                        success: function(res) {
                            layer.close(index);
                            if (res.code == 200) {
                                layer.msg(res.tip, {
                                    icon: 1,
                                    shade: 0.3,
                                    time: 2000
                                });
                            } else {
                                layer.msg(res.tip, {
                                    icon: 2,
                                    shade: 0.3,
                                    time: 2000
                                });
                            }
                            table.reload('main-table', {})
                        },
                        error: function() {
                            layer.close(index);
                            layer.msg("程序出错", {
                                icon: 2,
                                shade: 0.3,
                                time: 2000
                            })
                        }
                    })
                });
            }
            if (obj.event === "LAYTABLE_TIPS") {
                layer.open({
                    type: 0,
                    content: "为保护用户隐私，此处不会显示提取码，仅可通过搜索框获取指定提取码对应的数据。<br>搜索功能中“提取码”、“uid”和“剩余次数”为全词匹配搜索，不搜索时请留空。<br>多个条件搜索则匹配同时符合所有搜索条件的结果。<br>uid为0代表游客上传的数据",
                    shade: 0.3,
                    title: "说明"
                })
            }
            if (obj.event == 'refresh') {
                table.reload('main-table', {})
            }
        });

        table.on('tool(main-table-filter)', function(obj) {
            var data = obj.data;
            if (obj.event === 'edit') {
                parent.layui.admin.addTab("edit_file_data_" + data.id,"编辑文件数据-id" + data.id,"/admin/view/data/file_edit.php?id=" + data.id);
                return false;
            } else if (obj.event === 'delete') {
                layer.confirm('真的删除此行么', function(index) {
                    $.ajax({
                        type: "POST",
                        url: "/admin/api/data/file.php",
                        data: {
                            "type": "one",
                            "action": "delete",
                            "id": obj.data.id
                        },
                        dataType: "json",
                        async: false,
                        success: function(res) {
                            layer.close(index);
                            layer.msg(res.tip, {
                                icon: 1,
                                shade: 0.3,
                                time: 2000
                            });
                            table.reload('main-table', {})
                        },
                        error: function() {
                            layer.close(index);
                            layer.msg("删除失败", {
                                icon: 2,
                                shade: 0.3,
                                time: 2000
                            })
                        }
                    })
                });
            }
        });

    });
</script>
</body>

</html>