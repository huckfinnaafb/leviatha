$(document).ready(function() { 
    
    // Fade In Notifications
    $(".js-fadein").show(200);
    
    // Search Results Pagination
    if ($("#searchresults").length) {
        $("#searchresults").tablesorter({
            sortList: [[4,1]],
            widgets: ['zebra']
        }).tablesorterPager({
            container: $("#pager")
        }); 
    }
    
    /* Search Auto-Suggest
    $.getJSON("/jscript/ajax/names.php", function(leviatha) {
        var limit = 5;
        $('#search').keyup(function() {
            var query = this.value;
            var hints = new Array();
            
            // If Empty
            if (this.value == '') {
                $('.js-autocomplete').remove(); return false;
            }
            
            // Push matched names into hints[]
            for (var i in leviatha) {
                if (leviatha[i].toLowerCase().slice(0, query.length) == query.toLowerCase()) {
                    hints.push(leviatha[i]);
                }
                if (hints.length >= limit) { 
                    break; 
                }
            }
            
            // Append Suggestions
            $('.js-autocomplete').remove();
            for (var i in hints) {
                var urlname = hints[i].replace(/[ ]/g, "-").replace(/'/g, '').toLowerCase();
                $('#autocomplete').append("<li class='js-autocomplete'><a class='link-autocomplete' href='/loot/" + urlname + "'>" + hints[i].slice(0, this.value.length) + "<b>" + hints[i].slice(this.value.length, hints[i].length) + "</b>" + "</a></li>");
            }
        });
    });*/
});