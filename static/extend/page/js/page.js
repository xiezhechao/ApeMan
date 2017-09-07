/**
 * Created by xiezhechao on 17/2/14.
 */
$(function(){
    var $td_colspan = $("#td_colspan");
    if ($td_colspan) {
        var col_number = $td_colspan.closest("table").children("thead").children("tr").children("th").length ?
            $td_colspan.closest("table").children("thead").children("tr").children("th").length :
            $td_colspan.closest("table").children("thead").children("tr").children("td").length;
        $td_colspan.attr("colspan",col_number);
    }
});