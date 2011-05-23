<!DOCTYPE html>
<html lang="<?= $this->language ?>" dir="<?= $this->direction ?>">
    <head>
        <meta charset="utf-8">
        <title><?= $this->title ?></title>
        <meta name="description" content="<?= $this->description; ?>">
        <meta name="keywords" content="<?= $this->keywords; ?>">
        <meta name="robots" content="all">
        <meta name="language" content="<?= $this->language; ?>">
        <meta name="author" content="<?= $this->author; ?>">
        <meta name="copyright" content="Copyright 2011 - TodayICooked.com">
        <script>
            document.createElement("header");
            document.createElement("footer");
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
        
            <?php // if ($this->flag["exceptions"]) { include (F3::get('GUI') . "notification.php"); } ?>
            
            <?php include (F3::get('GUI') . $file); ?>
            
            <?php include (F3::get('GUI') . "footer.php"); ?>
        
        </div>
        
        <?php
        
            // Dynamic Script Loading
            foreach($this->scripts as $script) {
                echo "<script src=\"/jscript/{$script}.js\"></script>\n\t";
            }
        ?>
        
        <!-- Google Analytics -->
        <script type="text/javascript">

        </script>
    </body>
</html>