// 在网站根目录创建时区检查脚本
<?php
echo "PHP时区: " . date_default_timezone_get() . "<br>";
echo "当前时间: " . date('Y-m-d H:i:s') . "<br>";
echo "PHP版本: " . phpversion() . "<br>";

// 检查所有时区相关配置
phpinfo();
?>