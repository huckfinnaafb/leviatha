<!DOCTYPE html>
<html lang="<?= $this->language ?>" dir="<?php echo $this->direction ?>">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title><?php echo $this->title ?></title>
        <meta name="description" content="<?php echo $this->description; ?>">
        <meta name="keywords" content="<?php echo $this->keywords; ?>">
        <meta name="robots" content="all">
        <meta name="language" content="<?php echo $this->language; ?>">
        <meta name="author" content="<?php echo $this->author; ?>">
        <meta name="copyright" content="<?php echo $this->copyright; ?>">
        <script>
            document.createElement("header");
            document.createElement("footer");
            document.createElement("nav");
        </script>
        <?php
        
            // Dynamic Style Loading
            foreach($this->styles as $style) { ?>
                <link rel="stylesheet" href="/css/<?php echo $style; ?>.css">
            <?php }
        ?>
    </head>
    <body>
    
        <!-- Page -->
        <div class="page">
            
            <!-- Header -->
            <header>
                
                <!-- Logo and Search -->
                <div class="mod mod-padding mod-header">
                    <div style="float:left;margin-right:24px">
                        <a href="/"><img width=250 src="/img/logosmall.png"></a>
                    </div>
                    <div style="float:left">
                        <form action="/search" method="get" class="form-search">
                            <fieldset>
                                <div style="float:left">
                                    <input id="search" type="text" class="input-search" name="q" x-webkit-speech/>
                                    <ul id="autocomplete"></ul>
                                </div>
                                <input type="submit" class="submit-search" value="" title="Search"/>
                            </fieldset>
                        </form>
                    </div>
                </div>
                
                <!-- Navigation -->
                <nav class="mod" style="margin:0">
                    <?php foreach($this->navigation as $uri => $info) { ?>
                        <a href="<?php echo $uri; ?>" title="<?php echo $info['title']; ?>" class="link-nav <?php if($info['selected']) echo "link-nav-selected"; if (!$info['enabled']) echo "link-nav-disabled"; ?>"><?php echo $info['text']; ?></a>
                    <?php } ?>
                </nav>
                
            </header>
            
            <!-- Page Heading -->
            <?php 
                if (isset($this->heading) && ($this->flag['headings'])) { ?>
                    <div class="mod">
                        <h1 class="h-page"><?php echo $this->heading; ?></h1>
                    </div>
                <?php }
            ?>
            
            <!-- Notifications -->
            <?php 
                if ($this->flag['notifications']) {
                    if (F3::get('NOTIFY')) {
                        foreach(F3::get('NOTIFY') as $type => $message) { ?>
                            <p class="mod mod-button pattern-<?php echo $type; ?>"><?php echo $message; ?></p>
                        <?php }
                    }
                }
            ?>
            
            <!-- Content -->
            <?php include (F3::get('GUI') . $file); ?>
            
            <!-- Footer -->
            <footer class="mod mod-padding" style="margin:0">
                <p class="text-subtle" style="margin-bottom:10px">Leviatha.org | Diablo II Database | <a href="mailto:huckfinnaafb@gmail.com">Samuel Ferrell</a> | 2011 | <a href="https://github.com/huckfinnaafb/Leviatha">GitHub</a> | <a href="/about" title="About Leviatha">About</a></p>
            </footer>
            
        </div>
        
        <!-- Scripts -->
        <?php foreach($this->scripts as $script) { ?>
            <script src="/jscript/<?php echo $script; ?>.js"></script>
        <?php } ?>
        
        <?php if (!F3::get('DEBUG')) { ?>
            <!-- Google Analytics -->
            <script type="text/javascript">
                var _gaq = _gaq || [];
                _gaq.push(['_setAccount', 'UA-21869873-1']);
                _gaq.push(['_trackPageview']);

                (function() {
                    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
                    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
                    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
                })();
            
            </script>
        <?php } ?>
    </body>
</html>