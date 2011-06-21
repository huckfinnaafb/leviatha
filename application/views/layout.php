<!DOCTYPE html>
<html lang="<?= $this->language ?>" dir="<?php echo $this->direction ?>">
    <head>
        <meta charset="utf-8">
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
            foreach($this->styles as $style) {
                echo "<link rel=\"stylesheet\" href=\"/css/{$style}.css\">\n\t";
            }
        ?>

    </head>
    <body>
        <div class="page">
            
            <?php include (F3::get('GUI') . "header.php"); ?>
            
            <?php if (isset($this->heading)) { ?>
                <div class="mod">
                    <h1 class="h-page"><?php echo $this->heading; ?></h1>
                </div>
            <?php } ?>
            
            <?php
            
                // Notifications
                if ($this->flag['exceptions']) {
                    if (F3::get('EXCEPTION.error')) {
                        echo "<p class=\"mod mod-notify mod-error\">" . F3::get('EXCEPTION.error') . "</p>";
                    } else if (F3::get('EXCEPTION.warning')) {
                        echo "<p class=\"mod mod-notify mod-warning\">" . F3::get('EXCEPTION.warning') . "</p>";
                    } else if (F3::get('EXCEPTION.tip')) {
                        echo "<p class=\"mod mod-notify mod-tip\">" . F3::get('EXCEPTION.tip') . "</p>";
                    } else if (F3::get('EXCEPTION.success')) {
                        echo "<p class=\"mod mod-notify mod-success\">" . F3::get('EXCEPTION.success') . "</p>";
                    }
                }
            ?>
            
            <?php include (F3::get('GUI') . $file); ?>
            
            <?php include (F3::get('GUI') . "footer.php"); ?>
        
        </div>
        
        <?php
        
            // Dynamic Script Loading
            foreach($this->scripts as $script) {
                echo "<script src=\"/jscript/{$script}.js\"></script>\n\t";
            }
        ?>
        
        <?php if (F3::get('RELEASE')) { ?>
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