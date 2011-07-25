$(document).ready(function() {
    
    // Fade In Stuff
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
    
    // Search Auto-Suggest
    $('#search').focus(function() {
        
        $.getJSON("/ajax/loot", function(loot) {

            var limit = 7;
            $('#search').keyup(function() {
                var query = this.value;
                var hints = new Array();
                
                // If Empty
                if (this.value == '') {
                    $('.link-autocomplete').remove(); return false;
                }
                
                // Push matched names into hints[]
                for (var i in loot) {
                    if (loot[i].name.toLowerCase().slice(0, query.length) == query.toLowerCase()) {
                        hints.push(loot[i]);
                        console.log(loot[i].name);
                    }
                    if (hints.length >= limit) { 
                        break; 
                    }
                }
                
                // Append Suggestions
                $('.link-autocomplete').remove();
                for (var i in hints) {
                    var urlname = hints[i].name.replace(/[ ]/g, "-").replace(/'/g, '').toLowerCase();
                    $('#autocomplete').append(
                        "<a class='link-autocomplete' href='/loot/" + urlname + "'><li class='js-autocomplete'>" + 
                        hints[i].name.slice(0, this.value.length) + 
                        "<b>" + hints[i].name.slice(this.value.length, hints[i].name.length) + "</b></li></a>"
                    );
                }
                
            });
        });
    });
    
    // Blur off search
    $('#search').blur(function() {
        $('.link-autocomplete').remove();
    });
});