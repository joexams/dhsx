/**===================================================================
 * @fileoverview Ga JS公用框架工具
 * @author chenyabin
 * @version v1.0.0
 * ===================================================================
 */

window.console = window.console || {
    log: function() {}
};
var Ga = window.Ga || {};

Ga.dialog = {
    timeout: 2,
    show: function(message, title, level) {
        title = title || '系统提示';
        level = level || 0;
        message = (level == 1 && message.length > 0) ? message : '<p>' + message + '</p>';
        var strHtml = [
            '<div class="modal fade ' + (level == 1 ? ' text-center' : '') + '" id="notifyModal" tabindex="-1" role="dialog" aria-labelledby="notifyModalLabel" aria-hidden="true">',
            '<div class="modal-dialog' + (level == 1 ? ' modal-lg' : ' modal-sm') + '"  ' + (level == 1 ? ' style="display: inline-block; width: auto;"' : '') + '>',
            '<div class="modal-content">',
            '<div class="modal-header">',
            '<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>',
            '<h4 class="modal-title" id="notifyModalLabel">' + title + '</h4>',
            '</div>'
        ].join('');
        if (level == 1) {
            strHtml += message;
        } else {
            strHtml += [
                '<div class="modal-body">',
                message,
                '</div>'
            ].join('');
        }
        strHtml += [
            '</div>',
            '</div>',
            '</div>'
        ].join('');
        this.remove();
        $('body').prepend(strHtml);
        $('#notifyModal').modal('show');
        if (level != 1) {
            setTimeout(Ga.dialog.remove, Ga.dialog.timeout * 1000);
        }
    },
    remove: function() {
        $('#notifyModal').remove();
        $('.modal-backdrop').remove();
    }
};

Ga.common = {
    load: function(options) {
        var self = {
            area: options.area || 'container',
            url: options.url || '',
            data: options.data || '',
            callback: options.callback || ''
        };
        Ga.mask.show(self.area);
        $('#' + self.area).load(self.url, self.data, function(data) {
            Ga.mask.clear(self.area);
            if (typeof(self.callback) == 'function') {
                self.callback(data);
            }
        }).fadeIn(800);
    },
    ajax: function(options, force) {
        var self = {
            maskEid: options.maskEid || 'container',
            url: options.url || '',
            dataType: options.dataType || 'json',
            queryData: options.queryData || '',
            type: options.type || 'get',
            alwayscall: options.alwayscall || '',
            callback: options.callback || ''
        };
        Ga.mask.show(self.maskEid);
        $.ajax({
            dataType: self.dataType,
            url: self.url,
            data: self.queryData,
            type: self.type,
            success: function(data) {
                var type = self.type;
                if (typeof(force) == 'undefined') {
                    Ga.common.show(data);
                } else if (type.toLowerCase() == 'post') {
                    if (data.status == 1) {
                        Ga.common.show(data);
                    } else {
                        $('#' + self.maskEid).append('<div class="alert alert-danger" role="alert"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button><p>' + data.text + '</p></div>');
                        setTimeout(function() {
                            $(".alert").alert('close');
                        }, 3000);
                    }
                }
                if (typeof(options.callback) == 'function') {
                    self.callback(data);
                }

                if (data.forward != undefined && data.forward.length > 0) {
                    Ga.common.pageload(data.forward);
                }
            },
            complete: function() {
                Ga.mask.clear(self.maskEid);
            },
            always: function() {
                if (typeof(options.alwayscall) == 'function') {
                    self.alwayscall();
                }
            },
            error: function(e) {

            }
        });
    },
    show: function(data) {
        var alertclassname = '';
        switch (data.status) {
            case 0:
                alertclassname = 'text-danger';
                break;
            case 1:
                alertclassname = 'text-success';
                break;
            default:
                alertclassname = 'text-warning';
                break;
        }
        Ga.dialog.show('<p class="' + alertclassname + '">' + data.text + '</p>');
    },
    gethash: function(key, url) {
        var hash;
        if (!!url) {
            hash = url.replace(/^.*?[#](.+?)(?:\?.+)?$/, "$1");
            hash = (hash == url) ? "" : hash;
        } else {
            hash = self.location.hash;
        }

        hash = "" + hash;
        hash = hash.replace(/^[?#]/, '');
        hash = "&" + hash;
        var val = hash.match(new RegExp("[\&]" + key + "=([^\&]+)", "i"));
        if (val == null || val.length < 1) {
            return null;
        } else {
            return decodeURIComponent(val[1]);
        }
    },
    pageload: function(hash) {
        var url = '';
        if (hash) {
            url = (hash.indexOf("#") == -1) ? hash : hash.substr(1);
        } else {
            if ($('#sidebar .submenu a').length > 0) {
                url = $('#sidebar .submenu a').eq(0).attr('href');
                url = url.substr(1);
            }
        }
        if (url) {
            Ga.common.load({
                url: base + url,
                callback: function(data){
                    if (data.code == undefined || data.code != 500 || data.code != 404) {
                        url = '#' + url;
                        $('#sidebar .submenu a').each(function() {
                            if (url.indexOf($(this).attr('href')) >= 0) {
                                $('.active', $('#sidebar')).removeClass('active');
                                $(this).parent().addClass('active');
                                $(this).parent().parent().parent().addClass('active open');
                            }
                        });
                    }
                }
            });
            // $('html,body').animate({
            //     scrollTop: '0px'
            // }, 800);
        }
    }
};

Ga.mask = {
    prefix: 'mask',
    show: function(id, extra) {
        var that = this;
        var style = function() {
            if ($('#' + id).length > 0) {
                return {
                    width: $('#' + id).width(),
                    height: $('#' + id).height(),
                    offset: $('#' + id).offset(),
                    padding: $('#' + id).css('padding')
                };
            }
            return null;
        }();
        if (style) {
            style.padding = id == 'container' ? style.padding+20 : style.padding;
            $('<div id="' + that.prefix + id + '"><i class="i_loading"></i>&nbsp;数据加载中...</div>').css({
                height: style.height + 'px',
                left: (style.offset.left) + 'px',
                position: 'absolute',
                padding: style.padding,
                'padding-top': '80px',
                top: style.offset.top + 'px',
                'text-align': 'center',
                width: style.width + 'px',
                background: '#FFF',
                'opacity': 0.4,
                'z-index': 1029
            }).appendTo('body');
        }
    },
    clear: function(id) {
        if (id && $('#' + this.prefix + id).length > 0) {
            $('#' + this.prefix + id).remove();
        } else {
            $('div[id^="' + this.prefix + '"]').each(function() {
                $(this).remove();
            });
        }
    }
};


Ga.handle = {
    create: function(options){
        var title = options.title || '添加信息',
            url = options.url,
            query = options.query || '';
        Ga.common.ajax({
          url: url,
          dataType: 'html',
          queryData: query,
          callback: function(data){
            Ga.dialog.show(data, title, 1);
          }
        }, 1);
    },
    modify: function(options){
        var title = options.title || '修改信息',
            url = options.url,
            query = options.query || '';
        Ga.common.ajax({
          url: url,
          dataType: 'html',
          queryData: query,
          callback: function(data){
            Ga.dialog.show(data, title, 1);
          }
        }, 1);
    },
    remove: function(options){
        var msg = options.msg || '您确定删除此信息吗？',
            url = options.url,
            query = options.query || '',
            callback = options.callback || '';
        if (confirm(msg)) {
          Ga.common.ajax({
            url: url,
            dataType: 'json',
            queryData: query,
            callback: callback
          }, 1);
        }
    },
    submit: function(options){
        var maskEid = options.mask || 'modal-mask',
            url = options.url,
            query = options.query || '',
            callback = options.callback || '';
        Ga.common.ajax({
          maskEid: maskEid,
          url: url,
          queryData: query,
          type: 'POST',
          callback: callback
        }, 1);
    }
};

function checknode(obj) {
    var chk = $("input[type='checkbox']");
    var count = chk.length;
    var num = chk.index(obj);
    var level_top = level_bottom = chk.eq(num).attr('level')
    for (var i = num; i >= 0; i--) {
        var le = chk.eq(i).attr('level');
        if (eval(le) < eval(level_top)) {
            chk.eq(i).attr("checked", 'checked');
            var level_top = level_top - 1;
        }
    }
    for (var j = num + 1; j < count; j++) {
        var le = chk.eq(j).attr('level');
        if (chk.eq(num).attr("checked") == 'checked') {
            if (eval(le) > eval(level_bottom)) chk.eq(j).attr("checked", 'checked');
            else if (eval(le) == eval(level_bottom)) break;
        } else {
            if (eval(le) > eval(level_bottom)) chk.eq(j).attr("checked", false);
            else if (eval(le) == eval(level_bottom)) break;
        }
    }
}