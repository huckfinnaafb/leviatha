<?php
if (F3::get('EXCEPTION.error')) {
    echo "<p class=\"mod mod-notify mod-error\">" . F3::get('EXCEPTION.error') . "</p>";
} else if (F3::get('EXCEPTION.warning')) {
    echo "<p class=\"mod mod-notify mod-warning\">" . F3::get('EXCEPTION.warning') . "</p>";
} else if (F3::get('EXCEPTION.tip')) {
    echo "<p class=\"mod mod-notify mod-tip\">" . F3::get('EXCEPTION.tip') . "</p>";
} else if (F3::get('EXCEPTION.success')) {
    echo "<p class=\"mod mod-notify mod-success\">" . F3::get('EXCEPTION.success') . "</p>";
}