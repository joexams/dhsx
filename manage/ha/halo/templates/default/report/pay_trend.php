<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
function showChart(type, cid)
{
    type = type || 0;
    cid = cid || 0;
    var url = "<?php echo INDEX; ?>?m=report&c=pay&v=ajax_trend_list";
    Ha.common.ajax(url, 'json', "&daytype="+type+"&cid="+cid, 'get', 'container', function(data){
        $("#flashChart").multiChart(data.list);
    }, 1);
}
$(function() {
    /**
    * 运营平台
    */
    if (typeof global_companylist != 'undefined') {
        $('#global_companylisttpl').tmpl(global_companylist).appendTo('.cid');
    }

    $('#toolbar').on('click', 'a.daytype', function(){
        $('.active', $('#toolbar')).removeClass('active');
        var daytype = $(this).attr('data');
        var cid = $('#cid1').val();
        $(this).parent().addClass('active');
        showChart(daytype, cid);
    });
    $('#toolbar .daytype').eq(0).click();

    $('.first_level_tab').on('click', 'a.trendtype', function(){
        $('.first_level_tab .current').removeClass('current');
        $(this).parent().addClass('current');
        var obj = $(this), type = $(this).attr('data-type'), cid = 0; 
    });

    $('#cid1').on('change', function(){
        $('#toolbar .daytype').eq($('.active', $('#toolbar')).index()).click();
    });

    $('#get_search_submit').on('submit', function(e){
        e.preventDefault();
        $('#graph_wrapper3').html('');
        $('.first_level_tab .trendtype').eq(1).click();
    });
});
</script>


<h2><span id="tt"><?php echo Lang('pay_trend') ?></span></h2>
<div class="container" id="container">
    <div class="tool_date cf">
        <div class="title cf">
            <div class="tool_group">
                <select name="cid" id="cid1" class="cid ipt_select">
                    <option value="0"><?php echo Lang('company_platform'); ?></option>
                </select>

                <!-- <label>开服时间： 
                <input name="opendate" class="ipt_txt" type="text" id="opendate" value="<?php echo date('Y-m-d', time()-24*3600); ?>" onclick="WdatePicker()" readonly> </label> 
                
                <input name="dogetSubmit" type="hidden" value="1">
                <input type="submit" class="btn_sbm" value="查询" id="query"> -->
            </div>
            <div class="more">
                <ul class="select" id="toolbar">
                    <li class="active"><a class="daytype" href="javascript:void(0);" data="0">今天</a></li>
                    <li><a class="daytype" href="javascript:void(0);" data="1">昨天</a></li>
                    <li><a class="daytype" href="javascript:void(0);" data="2">最近7天</a></li>
                    <li><a class="daytype" href="javascript:void(0);" data="3">最近30天</a></li>
                    <li><a class="daytype" href="javascript:void(0);" data="4">全部</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="speed_result">
        <div class="mod_tab_title first_level_tab">
            <ul>
                <li class="current"><a href="javascript:void(0);" data-type="0" class="trendtype">收益趋势图</a></li>
                <!-- <li><a href="javascript:void(0);" data-type="1" class="trendtype">详细数据</a></li> -->
            </ul>
        </div>
    </div>
    <div class="column cf" id="chart_column">
    <div id="flashChart"></div>
    </div>
</div>