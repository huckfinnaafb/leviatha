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
        
            <?php if ($this->flag["exceptions"]) { include (F3::get('GUI') . "notification.php"); } ?>
            
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