
<?php
$str = 'test<>`dfef`';
var_dump(str_replace('`', '\`', addslashes($str)));