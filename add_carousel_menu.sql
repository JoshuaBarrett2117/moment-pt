-- 在sysoppanel表中添加轮播图配置菜单项
INSERT INTO `sysoppanel` (`id`, `name`, `url`, `info`) VALUES
(100, '轮播图配置', 'carousel_config.php', '管理网站首页轮播图内容');

-- 可选：更新语言文件
-- 注意：这部分需要手动添加到相应的语言文件中
-- 在lang/chs/lang_staffpanel.php中添加：
-- '轮播图配置' => '轮播图配置',
-- '管理网站首页轮播图内容' => '管理网站首页轮播图内容',
