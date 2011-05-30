$(document).ready(function() { 
    $("#searchresults")
        .tablesorter({
            sortList: [[4,1]],
            widgets: ['zebra']
        })
        .tablesorterPager({container: $("#pager")}); 
});