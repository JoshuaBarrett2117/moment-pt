<?php
// 添加轮播图配置菜单项的PHP脚本
require_once("include/bittorrent.php");
dbconn();

// 检查是否已存在该菜单项
$res = sql_query("SELECT * FROM sysoppanel WHERE url = 'carousel_config.php'");
if (mysql_num_rows($res) > 0) {
    echo "轮播图配置菜单项已存在！\n";
    exit;
}

// 查找最大ID并加1
$res = sql_query("SELECT MAX(id) as max_id FROM sysoppanel");
$row = mysql_fetch_assoc($res);
$new_id = ($row['max_id'] ?? 0) + 1;

// 插入菜单项
$result = sql_query("INSERT INTO sysoppanel (id, name, url, info) VALUES ($new_id, '轮播图配置', 'carousel_config.php', '管理网站首页轮播图内容')");

if ($result) {
    echo "轮播图配置菜单项添加成功！\n";
    echo "菜单项ID: $new_id\n";
} else {
    echo "添加失败: " . mysql_error() . "\n";
}

// 提示更新语言文件
echo "\n请记得在相应的语言文件中添加翻译条目！\n";
echo "例如在 lang/chs/lang_staffpanel.php 中添加：\n";
echo "'轮播图配置' => '轮播图配置',\n";
echo "'管理网站首页轮播图内容' => '管理网站首页轮播图内容',\n";

?>