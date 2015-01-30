<?php defined('IN_G') or exit('No permission resources.'); ?>
<script type="text/javascript">
$(function() {
    <?php if ($max > 0) { ?>
    $("#flashChart").multiChart(<?php echo $chartData ?>); 
    <?php } ?>
});
</script>

<h2><span id="tt"><?php echo Lang('data_active') ?></span></h2>
<div class="container" id="container">
    <div class="toolbar">
        <div class="tool_date">
            <div class="title cf">
            <div class="tool_group">
                 <select name="date" id="datelist" class="ipt_select">
                    <option value="0"><?php echo Lang('date'); ?></option>
                 </select>
            </div>
            </div>
        </div>
    </div>
    <div class="toolbar_opt cf">
        <h3 id="toolbar_title">趋势图</h3>
    </div>
    <div class="column cf" id="chart_column">
    <div id="flashChart"></div>
    </div>
    <div id="table_column" class="column cf">
    <div class="title">
        详细数据
    </div>
    <div id="dataTable">
        <table>
        <thead>
        <tr id="dataTheadTr">
            <th>日期</th>
            <th>注册数</th>
            <th>创建数</th>
            <th>创建率</th>
        </tr>
        </thead>
        <tbody>
        <?php if (!empty($list)) { ?>
            <?php foreach ($list as $key => $value): ?>
            <tr>
                <td><span class="<?php echo isset($value['fill']) ? 'redtitle':'' ?>"><?php echo $value['gdate'] ?></span></td>
                <td><?php echo intval($value['register_count']) ?></td>
                <td><?php echo intval($value['create_count']); ?></td>
                <td><?php echo intval($value['register_count']) > 0 ? round(intval($value['create_count'])*100/intval($value['register_count']), 2) : 0; ?>%</td>

            </tr>
            <?php endforeach ?>
        <?php }else { ?>
            <tr>
                <td colspan="4" style="text-align:left">没有找到数据。</td>
            </tr>
        <?php } ?>
        
        </tbody>
        </table>
    </div>
    </div>
</div>