<?php
// 简单的添加菜单项脚本，不依赖项目其他组件

// 数据库连接参数
$host = 'localhost';
$username = 'root';
$password = ''; // 根据实际情况修改
$dbname = 'moment_pt'; // 数据库名称

// 创建数据库连接
$conn = mysqli_connect($host, $username, $password, $dbname);

// 检查连接
if (!$conn) {
    die("连接失败: " . mysqli_connect_error());
}

// 检查是否已存在该菜单项
$result = mysqli_query($conn, "SELECT * FROM sysoppanel WHERE url = 'carousel_config.php'");
if (mysqli_num_rows($result) > 0) {
    echo "轮播图配置菜单项已存在！\n";
    mysqli_close($conn);
    exit;
}

// 查找最大ID并加1
$result = mysqli_query($conn, "SELECT MAX(id) as max_id FROM sysoppanel");
$row = mysqli_fetch_assoc($result);
$new_id = ($row['max_id'] ?? 0) + 1;

// 插入菜单项
$sql = "INSERT INTO sysoppanel (id, name, url, info) VALUES ($new_id, '轮播图配置', 'carousel_config.php', '管理网站首页轮播图内容')";

if (mysqli_query($conn, $sql)) {
    echo "轮播图配置菜单项添加成功！\n";
    echo "菜单项ID: $new_id\n";
} else {
    echo "添加失败: " . mysqli_error($conn) . "\n";
}

// 关闭连接
mysqli_close($conn);

// 提示更新语言文件
echo "\n请记得在相应的语言文件中添加翻译条目！\n";
echo "例如在 lang/chs/lang_staffpanel.php 中添加：\n";
echo "'轮播图配置' => '轮播图配置',\n";
echo "'管理网站首页轮播图内容' => '管理网站首页轮播图内容',\n";

?>