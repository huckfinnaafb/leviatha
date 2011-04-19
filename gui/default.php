<!DOCTYPE html>
<html lang="en" style="background-color:#eee">
    <head>
        <meta charset="utf-8">
        <title>Leviatha - Diablo Database | Loot, Builds, Monsters, and More!</title>
        <meta name="keywords" content="diablo database, diablo 2 database">
        <meta name="description" content="Diablo II Database - Search for items, builds, stats, and more on the most late website to the game."><meta name="robots" content="all">
        <meta name="language" content="English">
        <meta name="author" content="Samuel FerrelL">
        <meta name="copyright" content="Copyright 2011 - Samuel Ferrell">
        <link rel="stylesheet" href="/css/style.css" />
    </head>
    <body style="text-align:center;padding-top:100px">
        <img width=668 height=96 src="/img/logo8.png">
        <div style="margin: 20px 0">
            <a class="link-homepage" href="/">Home</a>
            <a class="link-homepage" href="/loot/">Loot</a>
            <a class="link-homepage" href="/blog/">Blog</a>
            <a class="link-homepage" style="text-decoration: line-through" href="/">Monsters</a>
            <a class="link-homepage" style="text-decoration: line-through" href="/">World</a>
            <a class="link-homepage" style="text-decoration: line-through" href="/">Classes</a>
        </div>
        <form id="searchform" action="search" method="get">
            <input id="search" type="text" class="input-text" name="q" style="width:390px"/>
            <input type="submit" class="input-submit" value="Search"/>
            <ul class="list-autocomplete" id="autocomplete"></ul>
        </form>
        <div style="margin-top:50px">
            <p class="text-subtle" style="margin:9px">Leviatha.org | Diablo II Database | <a href="mailto:huckfinnaafb@gmail.com">Samuel Ferrell</a> | 2011 | <a href="https://github.com/huckfinnaafb/Leviatha">GitHub</a></p>
            <img width=300 height=34 src="/img/software8.png">
        </div>
        <script src="/jscript/jquery.js"></script>
        <script src="/jscript/leviatha.js"></script>
        <script>
            $('#search').focus();
            
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
            });
        </script>
    </body>
</html>