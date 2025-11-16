<?php
ob_start();
require_once("../include/bittorrent.php");
dbconn();
loggedinorreturn();

// 检查用户权限
if (get_user_class() < UC_SYSOP) {
    stdmsg("错误", "访问被拒绝！");
    stdfoot();
    exit;
}

// 处理表单提交
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add'])) {
        // 添加新轮播图
        $title = $_POST['title'] ?? '';
        $description = $_POST['description'] ?? '';
        $link = $_POST['link'] ?? '';
        $image = $_POST['image'] ?? '';
        $background = $_POST['background'] ?? 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)';
        $sort_order = (int)($_POST['sort_order'] ?? 0);
        $active = isset($_POST['active']) ? 1 : 0;

        $title = htmlspecialchars(trim($title));
        $description = htmlspecialchars(trim($description));
        $link = htmlspecialchars(trim($link));
        $image = htmlspecialchars(trim($image));
        $background = htmlspecialchars(trim($background));

        if (empty($link) || empty($image)) {
            stdmsg("错误", "标题、链接和图片是必填项！");
        } else {
            sql_query("INSERT INTO carousels (title, description, link, image, background, sort_order, active, created_at, updated_at) VALUES
                      ('$title', '$description', '$link', '$image', '$background', $sort_order, $active, NOW(), NOW())");
            stdmsg("成功", "轮播图已添加！");
        }
    } elseif (isset($_POST['edit'])) {
        // 编辑轮播图
        $id = (int)($_POST['id'] ?? 0);
        $title = $_POST['title'] ?? '';
        $description = $_POST['description'] ?? '';
        $link = $_POST['link'] ?? '';
        $image = $_POST['image'] ?? '';
        $background = $_POST['background'] ?? 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)';
        $sort_order = (int)($_POST['sort_order'] ?? 0);
        $active = isset($_POST['active']) ? 1 : 0;

        $title = htmlspecialchars(trim($title));
        $description = htmlspecialchars(trim($description));
        $link = htmlspecialchars(trim($link));
        $image = htmlspecialchars(trim($image));
        $background = htmlspecialchars(trim($background));

        if (empty($link) || empty($image)) {
            stdmsg("错误", "url和图片地址是必填项！");
        } else {
            sql_query("UPDATE carousels SET title = '$title', description = '$description', link = '$link',
                      image = '$image', background = '$background', sort_order = $sort_order, active = $active,
                      updated_at = NOW() WHERE id = $id");
            stdmsg("成功", "轮播图已更新！");
        }
    } elseif (isset($_POST['delete'])) {
        // 删除轮播图
        $id = (int)($_POST['id'] ?? 0);
        if (!empty($id)) {
            sql_query("DELETE FROM carousels WHERE id = $id");
            stdmsg("成功", "轮播图已删除！");
        }
    } elseif (isset($_POST['toggle_status'])) {
        // 切换状态
        $id = (int)($_POST['id'] ?? 0);
        if (!empty($id)) {
            sql_query("UPDATE carousels SET active = NOT active, updated_at = NOW() WHERE id = $id");
        }
    }
}

// 编辑模式
$edit_mode = false;
$edit_carousel = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $res = sql_query("SELECT * FROM carousels WHERE id = $id");
    if (mysql_num_rows($res) > 0) {
        $edit_carousel = mysql_fetch_assoc($res);
        $edit_mode = true;
    }
}

// 获取所有轮播图
$res = sql_query("SELECT * FROM carousels ORDER BY sort_order ASC, id DESC");
$carousels = array();
while ($row = mysql_fetch_assoc($res)) {
    $carousels[] = $row;
}

stdhead("轮播图配置");
print("<h1 align=center>轮播图配置</h1>");

begin_main_frame();

// 添加/编辑表单
print("<table width=80% border=1 cellspacing=0 cellpadding=5 align=center>");
print("<tr><td class=colhead colspan=2 align=center>" . ($edit_mode ? "编辑轮播图" : "添加轮播图") . "</td></tr>");
print("<form method=post action=carousel_config.php>");

if ($edit_mode) {
    print("<input type=hidden name=id value=\"{$edit_carousel['id']}\">");
}

print("<tr>");
print("<td class=rowhead>标题</td>");
print("<td><input type=text name=title size=60 value=\"" . ($edit_mode ? htmlspecialchars($edit_carousel['title']) : "") . "\"></td>");
print("</tr>");

print("<tr>");
print("<td class=rowhead>描述</td>");
print("<td><textarea name=description rows=3 cols=60>" . ($edit_mode ? htmlspecialchars($edit_carousel['description']) : "") . "</textarea></td>");
print("</tr>");

print("<tr>");
print("<td class=rowhead>链接</td>");
print("<td><input type=text name=link size=60 value=\"" . ($edit_mode ? htmlspecialchars($edit_carousel['link']) : "") . "\"></td>");
print("</tr>");

print("<tr>");
print("<td class=rowhead>图片URL</td>");
print("<td><input type=text name=image size=60 value=\"" . ($edit_mode ? htmlspecialchars($edit_carousel['image']) : "") . "\"></td>");
print("</tr>");

print("<tr>");
print("<td class=rowhead>背景渐变</td>");
print("<td><input type=text name=background size=60 value=\"" . ($edit_mode ? htmlspecialchars($edit_carousel['background']) : "linear-gradient(135deg, #667eea 0%, #764ba2 100%)") . "\"></td>");
print("</tr>");

print("<tr>");
print("<td class=rowhead>排序顺序</td>");
print("<td><input type=number name=sort_order size=10 value=\"" . ($edit_mode ? $edit_carousel['sort_order'] : "0") . "\"></td>");
print("</tr>");

print("<tr>");
print("<td class=rowhead>启用</td>");
print("<td><input type=checkbox name=active" . ($edit_mode && $edit_carousel['active'] ? " checked" : "") . "></td>");
print("</tr>");

print("<tr>");
print("<td colspan=2 align=center>");
print("<input type=submit name=\"" . ($edit_mode ? "edit" : "add") . "\" value=\"" . ($edit_mode ? "更新" : "添加") . "\" class=btn>");
if ($edit_mode) {
    print(" <a href=carousel_config.php class=btn>取消</a>");
}
print("</td>");
print("</tr>");

print("</form>");
print("</table>");

print("<br /><br />");

// 轮播图列表
print("<table width=80% border=1 cellspacing=0 cellpadding=5 align=center>");
print("<tr>");
print("<td class=colhead>ID</td>");
print("<td class=colhead>标题</td>");
print("<td class=colhead>链接</td>");
print("<td class=colhead>状态</td>");
print("<td class=colhead>排序</td>");
print("<td class=colhead>操作</td>");
print("</tr>");

foreach ($carousels as $carousel) {
    print("<tr>");
    print("<td class=rowfollow>" . $carousel['id'] . "</td>");
    print("<td class=rowfollow>" . htmlspecialchars($carousel['title']) . "</td>");
    print("<td class=rowfollow><a href=\"" . htmlspecialchars($carousel['link']) . "\" target=_blank>" . htmlspecialchars($carousel['link']) . "</a></td>");
    print("<td class=rowfollow>" . ($carousel['active'] ? "<font color=green>启用</font>" : "<font color=red>禁用</font>") . "</td>");
    print("<td class=rowfollow>" . $carousel['sort_order'] . "</td>");
    print("<td class=rowfollow>");

    // 编辑按钮
    print("<a href=carousel_config.php?edit=" . $carousel['id'] . " class=btn>编辑</a> ");

    // 状态切换按钮
    print("<form method=post action=carousel_config.php style=display:inline>");
    print("<input type=hidden name=id value=\"" . $carousel['id'] . "\">");
    print("<input type=submit name=toggle_status value=\"" . ($carousel['active'] ? "禁用" : "启用") . "\" class=btn>");
    print("</form> ");

    // 删除按钮
    print("<form method=post action=carousel_config.php style=display:inline onsubmit=\"return confirm('确定要删除这个轮播图吗？');\">");
    print("<input type=hidden name=id value=\"" . $carousel['id'] . "\">");
    print("<input type=submit name=delete value=删除 class=btn>");
    print("</form>");

    print("</td>");
    print("</tr>");
}

print("</table>");

end_main_frame();
stdfoot();
?>
