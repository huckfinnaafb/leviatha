<!DOCTYPE html>
<html>
    <head>
        <title><?php
                if (isset($this->title)) {
                    echo $this->title . " - Diablo Database";
                } else {
                    echo "Leviatha - Diablo Database | Loot, Monsters, and More!";
                }
            ?></title>
        <meta name="keywords" content="diablo database, diablo 2 database">
        <meta name="description" content="Diablo II Database - Search for items, builds, stats, and more on the most late website to the game.">
        <meta name="robots" content="all">
        <meta name="language" content="English">
        <meta name="author" content="Samuel Ferrell">
        <meta name="copyright" content="Copyright 2011 - Samuel Ferrell">
        <link rel="stylesheet" href="/css/style.css" />
        <script>
            var _gaq = _gaq || [];
            _gaq.push(['_setAccount', 'UA-21869873-1']);
            _gaq.push(['_trackPageview']);

            (function() {
            var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
            ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
            var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
            })();
        </script>
    </head>
    <body>
        <div class="page">
            <div class="module mod-header">
                <div class="line">
                    <div class="unit size1of2">
                        <div class="hgroup">
                            <h1 class="h-title"><a class="link-title" href="/">Leviatha</a></h1>
                        </div>
                        <div class="nav">
                            <a class="link-nav" href="/">Home</a>
                            <a class="link-nav" href="/loot">Loot</a>
                            <a class="link-nav" style="text-decoration: line-through" href="/">Monsters</a>
                            <a class="link-nav" style="text-decoration: line-through" href="/">World</a>
                            <a class="link-nav" style="text-decoration: line-through" href="/">Classes</a>
                            <a class="link-nav" style="text-decoration: line-through" href="/">Skills</a>
                        </div>
                    </div>
                    <div class="unit size1of2 lastUnit">
                        <form action="/search" method="get" class="form-search">
                            <fieldset class="fieldset field-search">
                                <input type="text" class="input input-text" id="autosuggest" name="q"/>
                                <input type="submit" class="input input-submit" value="Search"/>
                            </fieldset>
                        </form>
                    </div>
                </div>
            </div>
            <div class="content">
