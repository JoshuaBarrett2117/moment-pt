-- 创建轮播图表
CREATE TABLE IF NOT EXISTS carousels (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL COMMENT '轮播图标题',
    description VARCHAR(255) NULL COMMENT '轮播图描述',
    link VARCHAR(255) NULL COMMENT '轮播图链接',
    background VARCHAR(255) DEFAULT 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)' COMMENT '轮播图背景（颜色、渐变或图片URL）',
    sort_order INT DEFAULT 0 COMMENT '排序顺序',
    active BOOLEAN DEFAULT TRUE COMMENT '是否激活',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_sort_order (sort_order),
    INDEX idx_active (active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 插入默认轮播图数据
INSERT INTO carousels (title, description, link, background, sort_order, active) VALUES
('欢迎来到PT站', '享受高速下载体验', '#', 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)', 1, TRUE),
('最新热门种子', '每周更新精选内容', '#', 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)', 2, TRUE),
('邀请好友', '', '#', 'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)', 3, TRUE);
