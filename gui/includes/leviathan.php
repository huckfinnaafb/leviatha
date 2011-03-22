<!DOCTYPE html>
<html>
    <head>
        <title>Leviathan</title>
        <link rel="stylesheet" href="/css/reset.css" />
        <link rel="stylesheet" href="/css/style.css" />
        <script>
            document.createElement("article");
            document.createElement("footer");
            document.createElement("header");
            document.createElement("hgroup");
            document.createElement("nav");
            document.createElement("menu");
        </script>
        <script src="jscript/jquery.js"></script>
    </head>
    <body>
        <div class="page">
            <header class="module mod-header">
                <hgroup>
                    <h1 class="heading h-title"><a class="link link-title" href="/">Leviathan</a></h1>
                </hgroup>
                <nav>
                    <a class="link link-nav" href="/">Home</a>
                    <a class="link link-nav" href="/bestiary">Bestiary</a>
                    <a class="link link-nav" href="/loot">Loot</a>
                    <a class="link link-nav" href="/world">World</a>
                    <a class="link link-nav" href="/classes">Classes</a>
                    <a class="link link-nav" href="/forums">Forums</a>
                </nav>
                <form action="search" method="get" class="form form-search">
                    <fieldset class="fieldset field-search">
                        <input type="text" class="input input-text" id="autosuggest" name="q"/>
                        <input type="submit" class="input input-submit" value="Search"/>
                    </fieldset>
                </form>
            </header>
            <div class="content">
<?php include($file); ?>
            
            </div>
            <footer class="module mod-footer">
                <p class="beta">Leviatha is a live project and is subject to completely screwing up for no reason.</p>
                <p class="text text-copyright">Copyright Samuel Ferrell. All Rights Reserved.</p>
            </footer>
        </div>
    </body>
</html>