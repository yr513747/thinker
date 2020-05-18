
// var umeditor_toolbar_minipro = [
//     'source | undo redo | bold italic underline strikethrough | superscript subscript | forecolor backcolor | removeformat |',
//     'insertorderedlist insertunorderedlist | selectall paragraph | fontfamily fontsize',
//     '| justifyleft justifycenter justifyright justifyjustify ',
//     '| image insertimage video',
//     '| horizontal'
// ];

var ueditor_toolbars_minipro = [[
    'source', '|', 'undo', 'redo', '|',
    'bold', 'italic', 'underline', 'fontborder', 'strikethrough', 'superscript', 'subscript', 'removeformat', '|', 'forecolor', 'backcolor', 'insertorderedlist', 'insertunorderedlist', '|',
    '|', 'customstyle', 'paragraph', 'fontfamily', 'fontsize', '|',
    'justifyleft', 'justifycenter', 'justifyright', 'justifyjustify', '|', 'imagenone', 'imageleft', 'imageright', 'imagecenter', '|',
    'insertimage'
]];

var v_single_imgpath = '';
function single_imgpath_call_back(fileurl_tmp)
{
    v_single_imgpath = fileurl_tmp;
}

/*
 * 上传图片
 * @access  public
 * @null int 一次上传图片张图
 * @elementid string 上传成功后返回路径插入指定ID元素内
 * @path  string 指定上传保存文件夹,默认存在public/upload/temp/目录
 * @callback string  回调函数(单张图片返回保存路径字符串，多张则为路径数组 )
 */
var layer_GetUploadify_minipro;
function GetUploadify_minipro(num,elementid,path,callback, v_source, v_indexs)
{
    if (layer_GetUploadify_minipro){
        layer.close(layer_GetUploadify_minipro);
    }
    if (num > 0) {
        var width = '85%';
        var height = '85%';
        var upurl = thinker_basefile+'?' + var_app + '=' + app_name + '&' + var_controller + '=Uploadify&' + var_action + '=upload&num='+num+'&input='+elementid+'&path='+path+'&func='+callback;
        layer_GetUploadify_minipro = layer.open({
            type: 2,
            title: '上传图片',
            shadeClose: false,
            shade: 0.3,
            maxmin: true, //开启最大化最小化按钮
            area: [width, height],
            content: upurl,
            end: function(layero, index){
                if ('' != v_single_imgpath) {
                    v_source[v_indexs] = v_single_imgpath;
                }
            },
            success: function(layero, index){
                v_single_imgpath = '';
            }
         });
    } else {
        layer.alert('允许上传0张图片', {icon:2, title:false});
        return false;
    }
}

(function () {

    // 解决火狐浏览器拖动新增tab
    document.body.ondrop = function (event) {
        event.preventDefault();
        event.stopPropagation();
    };

    // 默认数据
    var defaultData = {};

    // umeditor 实例
    var $umeditor = {};

    /***
     * 前端可视化diy
     * @constructor
     */
    function diyPhone(initalData, diyData, opts) {
        defaultData = initalData;
        this.init(diyData, opts);
    }

    diyPhone.prototype = {

        init: function (data, opts) {
            // 实例化Vue
            new Vue({
                el: '#app',
                data: {
                    // diy数据
                    diyData: data,
                    // 当前选中的元素（下标）
                    selectedIndex: -1,
                    // 当前选中的diy元素
                    curItem: {},
                    // 外部数据
                    opts: opts
                },

                error: '',

                methods: {

                    /**
                     * 新增Diy组件
                     * @param key
                     */
                    onAddItem: function (key) {
                        // 验证新增Diy组件
                        if (!this.onCheckAddItem(key)) {
                            return false;
                        }
                        // 复制默认diy组件数据
                        var data = $.extend(true, {}, defaultData[key]);
                        // 新增到diy列表数据
                        this.diyData.items.push(data);
                        // 编辑当前选中的元素
                        this.onEditer(this.diyData.items.length - 1);
                    },

                    /**
                     * 验证新增Diy组件
                     * @param key
                     */
                    onCheckAddItem: function (key) {
                        // 验证关注公众号组件只能存在一个
                        if (key === 'officialAccount') {
                            for (var index in this.diyData.items) {
                                if (this.diyData.items.hasOwnProperty(index)) {
                                    var item = this.diyData.items[index];
                                    if (item.type === 'officialAccount') {
                                        layer.msg('该组件最多存在一个', {anim: 6});
                                        return false;
                                    }
                                }
                            }
                        }
                        return true;
                    },

                    /**
                     * 拖动diy元素更新当前索引
                     * @param e
                     */
                    onDragItemEnd: function (e) {
                        this.onEditer(e.newIndex);
                    },

                    /**
                     * 编辑当前选中的Diy元素
                     * @param index
                     */
                    onEditer: function (index) {
                        // 记录当前选中元素的索引
                        this.selectedIndex = index;
                        // 当前选中的元素数据
                        this.curItem = this.selectedIndex === 'page' ? this.diyData.page
                            : this.diyData.items[this.selectedIndex];
                        // 注册编辑器事件
                        this.initEditor();
                    },

                    /**
                     * 删除diy元素
                     * @param index
                     */
                    onDeleleItem: function (index) {
                        var _this = this;
                        layer.confirm('确定要删除吗？', function (layIdx) {
                            _this.diyData.items.splice(index, 1);
                            _this.selectedIndex = -1;
                            layer.close(layIdx);
                        });
                    },

                    /**
                     * 编辑器：选择图片
                     * @param source
                     * @param index
                     */
                    onEditorSelectImage: function (source, index) {
                        GetUploadify_minipro(1,'','allimg','single_imgpath_call_back', source, index);
                        // source[index] = v_single_imgpath;
                        // $.fileLibrary({
                        //     type: 'image',
                        //     done: function (images) {
                        //         source[index] = images[0]['file_path'];
                        //     }
                        // });
                    },

                    /**
                     * 编辑器：选择小程序链接
                     * @param source
                     * @param page_links
                     */
                    onEditoSelectPathIndex: function (source, page_links) {
                        var index = source.pathconf.index;
                        var path = page_links[index]['path'];
                        source['pathconf']['value'] = '';
                        source['pathconf']['is_vars'] = page_links[index]['is_vars'];
                        source['url'] = path;
                    },

                    /**
                     * 编辑器：小程序链接的ID输入
                     * @param source
                     * @param page_links
                     */
                    onEditoJoinPathValue: function (source, page_links) {
                        var index = source.pathconf.index;
                        var value = source.pathconf.value;
                        var path = page_links[index]['path'];
                        var url = path;
                        if ($.trim(value) != '') {
                            url += value;
                        }
                        source['url'] = url;
                    },

                    /**
                     * 编辑器：重置颜色
                     * @param holder
                     * @param attribute
                     * @param color
                     */
                    onEditorResetColor: function (holder, attribute, color) {
                        holder[attribute] = color;
                    },

                    /**
                     * 编辑器：删除data元素
                     * @param index
                     * @param selectedIndex
                     */
                    onEditorDeleleData: function (index, selectedIndex) {
                        if (this.diyData.items[selectedIndex].data.length <= 1) {
                            layer.msg('至少保留一个', {anim: 6});
                            return false;
                        }
                        this.diyData.items[selectedIndex].data.splice(index, 1);
                    },

                    /**
                     * 编辑器：添加data元素
                     */
                    onEditorAddData: function () {
                        // 新增data数据
                        var newDataItem = $.extend(true, {}, defaultData[this.curItem.type].data[0]);
                        this.curItem.data.push(newDataItem);
                    },

                    /**
                     * 注册编辑器事件
                     */
                    initEditor: function () {
                        // 注册dom事件
                        this.$nextTick(function () {
                            // 销毁 umeditor 组件
                            if ($umeditor.hasOwnProperty('key')) {
                                $umeditor.destroy();
                            }
                            // 注册html组件
                            this.editorHtmlComponent();
                            // 富文本事件
                            if (this.curItem.type === 'richText') {
                                this.onRichText(this.curItem);
                            }
                        });
                    },

                    /**
                     * 编辑器事件：html组件
                     */
                    editorHtmlComponent: function () {
                        var $editor = $(this.$refs['diy-editor']);
                        // 单/多选框
                        $editor.find('input[type=checkbox], input[type=radio]').uCheck();
                        // select组件
                        // $editor.find('select').selected();
                    },

                    /**
                     * 编辑器事件：文档选择
                     * @param item
                     */
                    onSelectArchives: function (item) {
                        var aids = '';
                        var aidArr = [];
                        item.data.forEach(function (article) {
                            if (article.hasOwnProperty('aid')) {
                                aidArr.push(article.aid);
                            }
                        });
                        aids = aidArr.join(',');

                        var uris = {
                            article: thinker_basefile+'?' + var_app + '=' + app_name + '&' + var_controller + '=Minipro&' + var_action + '=ajaxArchivesList&assembly=article&aids='+aids
                        };
                        $.selectData({
                            title: '选择文档',
                            uri: uris[item.type],
                            duplicate: false,
                            dataIndex: 'aid',
                            done: function (data) {
                                data.forEach(function (itm) {
                                    item.data.push(itm)
                                });
                            },
                            getExistData: function () {
                                var existData = [];
                                item.data.forEach(function (article) {
                                    if (article.hasOwnProperty('aid')) {
                                        existData.push(article.aid);
                                    }
                                });
                                return existData;
                            }
                        });
                    },

                    /**
                     * 编辑器事件：拼团商品选择
                     * @param item
                     */
                    onSelectGoods: function (item) {
                        var aids = '';
                        var aidArr = [];
                        item.data.forEach(function (goods) {
                            if (goods.hasOwnProperty('aid')) {
                                aidArr.push(goods.aid);
                            }
                        });
                        aids = aidArr.join(',');

                        var uris = {
                            goods: thinker_basefile+'?' + var_app + '=' + app_name + '&' + var_controller + '=Minipro&' + var_action + '=ajaxArchivesList&channel=2&assembly=goods&aids='+aids
                            // , sharingGoods: 'sharing.goods/lists&status=10'
                            // , bargainGoods: 'bargain.goods/lists&status=10'
                            // , sharpGoods: 'sharp.goods/lists&status=10'
                        };
                        $.selectData({
                            title: '选择商品',
                            uri: uris[item.type],
                            duplicate: false,
                            dataIndex: 'aid',
                            done: function (data) {
                                data.forEach(function (itm) {
                                    item.data.push(itm)
                                });
                            },
                            getExistData: function () {
                                var existData = [];
                                item.data.forEach(function (goods) {
                                    if (goods.hasOwnProperty('aid')) {
                                        existData.push(goods.aid);
                                    }
                                });
                                return existData;
                            }
                        });
                    },

                    /**
                     * 选择线下门店
                     * @param item
                     */
                    onSelectShop: function (item) {
                        $.selectData({
                            title: '选择门店',
                            uri: 'shop/lists&status=1',
                            duplicate: false,
                            dataIndex: 'shop_id',
                            done: function (data) {
                                data.forEach(function (itm) {
                                    item.data.push(itm)
                                });
                            },
                            getExistData: function () {
                                var existData = [];
                                item.data.forEach(function (shop) {
                                    if (shop.hasOwnProperty('shop_id')) {
                                        existData.push(shop.shop_id);
                                    }
                                });
                                return existData;
                            }
                        });
                    },

                    /**
                     * 编辑器事件：富文本
                     */
                    onRichText: function (item) {
                        // $umeditor = UM.getEditor('ume-editor', {
                        //     initialFrameWidth: 375,
                        //     initialFrameHeight: 400,
                        //     toolbar: umeditor_toolbar_minipro
                        // });
                        // $umeditor.ready(function () {
                        //     // 写入编辑器内容
                        //     $umeditor.setContent(item.params.content);
                        //     $umeditor.addListener('contentchange', function () {
                        //         item.params.content = $umeditor.getContent();
                        //     });
                        // });
                        var $umeditor = UE.getEditor('ume-editor',{
                            serverUrl : thinker_basefile+'?' + var_app + '=' + app_name + '&' + var_controller + '=Ueditor&' + var_action + '=index&savepath=ueditor',
                            zIndex: 999,
                            initialFrameWidth: 375, //初化宽度
                            initialFrameHeight: 400, //初化高度            
                            focus: false, //初始化时，是否让编辑器获得焦点true或false
                            maximumWords: 99999,
                            removeFormatAttributes: 'class,style,lang,width,height,align,hspace,valign',//允许的最大字符数 'fullscreen',
                            pasteplain:false, //是否默认为纯文本粘贴。false为不使用纯文本粘贴，true为使用纯文本粘贴
                            autoHeightEnabled: false,
                            toolbars: ueditor_toolbars_minipro
                        });
                        $umeditor.ready(function () {
                            // 写入编辑器内容
                            $umeditor.setContent(item.params.content);
                            $umeditor.addListener('contentchange', function () {
                                item.params.content = $umeditor.getContent();
                            });
                        });
                    },

                    /**
                     * 提交后端保存
                     * @returns {boolean}
                     */
                    // onSubmit: function (e, type) {
                    //     if (this.diyData.items.length <= 0) {
                    //         layer.msg('至少增加一个组件', {anim: 6});
                    //         return false;
                    //     }
                    //     var data = JSON.stringify(this.diyData);
                    //     var timeStamp = parseInt(new Date().getTime() / 1000);
                    //     localStorage.setItem("diy_" + timeStamp, data);
                    //     var url = e.srcElement.dataset.url;
                    //     layer_loading('正在处理');
                    //     $.post(url, {data: data}, function (result) {
                    //         layer.closeAll();
                    //         if (result.code === 1) {
                    //             if ('save' == type) {
                    //                 $.show_success(result.msg, result.url);
                    //             } else {
                    //                 window.location.href = e.srcElement.dataset.auditurl;
                    //             }
                    //         } else {
                    //             $.show_error(result.msg);
                    //         }
                    //     });
                    // }

                }
            });


            // 实例化Vue
            new Vue({
                el: '#submit',
                data: {
                    // diy数据
                    diyData: data
                },

                error: '',

                methods: {

                    /**
                     * 提交后端保存
                     * @returns {boolean}
                     */
                    onSubmit: function (e, fmdo) {
                        if (this.diyData.items.length <= 0) {
                            layer.msg('至少增加一个组件', {anim: 6});
                            return false;
                        }
                        var data = JSON.stringify(this.diyData);
                        var timeStamp = parseInt(new Date().getTime() / 1000);
                        localStorage.setItem("diy_" + timeStamp, data);
                        var url = e.srcElement.dataset.url;
                        var authorizerstatus = parseInt(e.srcElement.dataset.authorizerstatus);
                        layer_loading('正在处理');
                        $.post(url, {data:data, fmdo:fmdo, authorizerstatus:authorizerstatus,_ajax:1}, function (result) {
                            if (result.code == 1) {
                                if ('preview' == fmdo) {
                                    layer.closeAll();
                                    if (0 < authorizerstatus) {
                                        layer.open({
                                            title: '小程序体验二维码',
                                            type: 1,
                                            skin: 'layui-layer-demo', //样式类名
                                            closeBtn: 1, //不显示关闭按钮
                                            anim: 2,
                                            shadeClose: false, //开启遮罩关闭
                                            content: "<img src='"+result.data.imgurl+"' width='230' height='230'/>"
                                        });
                                    } else {
                                        layer.confirm('微信规定，需要授权小程序后，才可显示二维码？', {
                                            title: false,
                                            btn: ['前往授权', '取消'] //按钮
                                        }, function () {
                                            window.location.href = result.data.url;
                                        });
                                    }
                                } else if ('audit' == fmdo) {
                                    layer.closeAll();
                                    if (0 < authorizerstatus) {
                                        layer.alert(result.msg, {
                                            title: false,
                                            icon: 6
                                        });
                                    } else {
                                        layer.confirm('审核发布，需要授权您的小程序。是否现在授权？', {
                                            title: false,
                                            btn: ['前往授权', '取消'] //按钮
                                        }, function () {
                                            window.location.href = result.data.url;
                                        });
                                    }
                                } else {
                                    layer.closeAll();
                                    $.show_success(result.msg, result.url);
                                }
                            } else {
                                layer.closeAll();
                                $.show_error(result.msg);
                            }
                        });
                    },

                    /**
                     * 删除授权
                     * @returns {boolean}
                     */
                    onAuthoriDel: function (e) {
                        var url = e.srcElement.dataset.url;
                        var authorizerstatus = parseInt(e.srcElement.dataset.authorizerstatus);
                        if (0 == authorizerstatus) {
                            return false;
                        }

                        layer_loading('正在处理');
                        $.post(url, {authorizerstatus:authorizerstatus,_ajax:1}, function (result) {
                            layer.closeAll();
                            if (result.code == 1) {
                                $.show_success(result.msg, result.url);
                            } else {
                                $.show_error(result.msg);
                            }
                        });
                    },

                    /**
                     * 下载小程序码
                     * @returns {boolean}
                     */
                    onDwonQrcode: function (e) {
                        var url = e.srcElement.dataset.url;
                        var authorizerstatus = parseInt(e.srcElement.dataset.authorizerstatus);
                        if (0 == authorizerstatus) {
                            $.show_error('需授权小程序后，才可下载小程序码。');
                            return false;
                        }

                        window.location.href = url;
                    }

                }
            });
        }

    };

    window.diyPhone = diyPhone;

})(window);