<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title><?php
                if (isset($this->title)) {
                    echo $this->title . " - Diablo Database";
                } else {
                    echo "Leviatha - Diablo Database | Loot, Builds, Monsters, and More!";
                }
            ?></title>
        <meta name="keywords" content="diablo database, diablo 2 database">
        <meta name="description" content="Diablo II Database - Search for items, builds, stats, and more on the most late website to the game.">
        <meta name="robots" content="all">
        <meta name="language" content="English">
        <meta name="author" content="Samuel Ferrell">
        <meta name="copyright" content="Copyright 2011 - Samuel Ferrell">
        <link rel="stylesheet" href="/css/styleuncompressed.css" />
    </head>
    <body>
        <div class="page">
            <div class="module mod-header">
                <div class="line">
                    <div class="unit size1of2">
                        <div class="hgroup">
                            <h1 class="h-title"><a class="link-title" title="Return Home" href="/">Leviatha</a></h1>
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
