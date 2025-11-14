<?php
// 轮播图显示组件
require_once("../include/bittorrent.php");

function display_carousel() {
    global $BASEURL, $CURUSER;
    
    // 从数据库获取启用的轮播图
    $query = "SELECT * FROM carousels WHERE active = '1' ORDER BY sort_order ASC";
    $result = sql_query($query);
    $carousels = [];
    
    while ($row = mysql_fetch_assoc($result)) {
        $carousels[] = $row;
    }
    
    // 如果没有轮播图，则不显示
    if (empty($carousels)) {
        return '';
    }
    
    // 生成轮播图HTML（使用双引号，使\n被正确解析为换行符）
    $carousel_html = "<div class=\"carousel-container\" style=\"width: 100%; margin: 20px 0; overflow: hidden; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);\">
";
    $carousel_html .= "  <div id=\"carousel\" style=\"position: relative; height: 300px; background-color: #f5f5f5;\">
";
    
    foreach ($carousels as $index => $carousel) {
        $active = $index == 0 ? 'style=\"display: block;' : 'style=\"display: none;';
        if (!empty($carousel['background'])) {
            $active .= ' background: ' . htmlspecialchars($carousel['background']) . ';';
        }
        $active .= '\"';
        $carousel_html .= "    <div class=\"carousel-item\" $active>
";
        
        // 如果有链接，用a标签包裹
        if (!empty($carousel['link'])) {
            $carousel_html .= "      <a href=\"" . htmlspecialchars($carousel['link']) . "\" target=\"_blank\" style=\"display: block; height: 100%;\">
";
        }
        
        // 显示图片
        if (!empty($carousel['image'])) {
            $img_url = $BASEURL . '/public/carousel/' . htmlspecialchars($carousel['image']);
            $carousel_html .= "        <img src=\"$img_url\" alt=\"" . htmlspecialchars($carousel['title']) . "\" style=\"width: 100%; height: 100%; object-fit: cover;\" />
";
        }
        
        // 如果有链接，关闭a标签
        if (!empty($carousel['link'])) {
            $carousel_html .= "      </a>
";
        }
        
        // 如果有标题和描述，显示在图片下方
        if (!empty($carousel['title']) || !empty($carousel['description'])) {
            $carousel_html .= "      <div class=\"carousel-caption\" style=\"position: absolute; bottom: 0; left: 0; right: 0; background-color: rgba(0,0,0,0.7); color: white; padding: 15px; text-align: center;\">
";
            if (!empty($carousel['title'])) {
                $carousel_html .= "        <h3 style=\"margin: 0 0 10px 0; font-size: 18px;\">" . htmlspecialchars($carousel['title']) . "</h3>
";
            }
            if (!empty($carousel['description'])) {
                $carousel_html .= "        <p style=\"margin: 0; font-size: 14px;\">" . htmlspecialchars($carousel['description']) . "</p>
";
            }
            $carousel_html .= "      </div>
";
        }
        
        $carousel_html .= "    </div>
";
    }
    
    // 添加轮播控制按钮
    $carousel_html .= "    <button id=\"carousel-prev\" style=\"position: absolute; left: 10px; top: 50%; transform: translateY(-50%); background-color: rgba(0,0,0,0.5); color: white; border: none; border-radius: 50%; width: 40px; height: 40px; cursor: pointer; font-size: 18px; z-index: 10; transition: background-color 0.3s;\">&lt;</button>
";
    $carousel_html .= "    <button id=\"carousel-next\" style=\"position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background-color: rgba(0,0,0,0.5); color: white; border: none; border-radius: 50%; width: 40px; height: 40px; cursor: pointer; font-size: 18px; z-index: 10; transition: background-color 0.3s;\">&gt;</button>
";
    
    // 添加轮播指示器
    if (count($carousels) > 1) {
        $carousel_html .= "    <div class=\"carousel-indicators\" style=\"position: absolute; bottom: 10px; left: 50%; transform: translateX(-50%); display: flex; gap: 8px;\">
";
        foreach ($carousels as $index => $carousel) {
            $active_class = $index == 0 ? 'background-color: white;' : 'background-color: rgba(255,255,255,0.5);';
            $carousel_html .= "      <button data-index=\"$index\" style=\"width: 12px; height: 12px; border-radius: 50%; border: none; cursor: pointer; transition: background-color 0.3s;$active_class\"></button>
";
        }
        $carousel_html .= "    </div>
";
    }
    
    $carousel_html .= "  </div>
";
    $carousel_html .= "</div>
";
    
    // 添加轮播图JavaScript
    $carousel_html .= "<script type=\"text/javascript\">
";
    $carousel_html .= "  document.addEventListener(\"DOMContentLoaded\", function() {
";
    $carousel_html .= "    const items = document.querySelectorAll('.carousel-item');
";
    $carousel_html .= "    const prevBtn = document.getElementById('carousel-prev');
";
    $carousel_html .= "    const nextBtn = document.getElementById('carousel-next');
";
    $carousel_html .= "    const indicators = document.querySelectorAll('.carousel-indicators button');
";
    $carousel_html .= "    const carousel = document.getElementById('carousel');
";
    $carousel_html .= "    let currentIndex = 0;
";
    $carousel_html .= "    let interval;
";
    
    // 添加空值检查
    $carousel_html .= "    if (!items.length) {
";
    $carousel_html .= "      console.log('没有轮播项');
";
    $carousel_html .= "      return;
";
    $carousel_html .= "    }
";
    
    $carousel_html .= "    function showSlide(index) {
";
    $carousel_html .= "      // 确保索引在有效范围内
";
    $carousel_html .= "      if (index < 0) index = items.length - 1;
";
    $carousel_html .= "      if (index >= items.length) index = 0;
";
    $carousel_html .= "      
";
    $carousel_html .= "      // 隐藏所有轮播项
";
    $carousel_html .= "      items.forEach(item => item.style.display = 'none');
";
    $carousel_html .= "      
";
    $carousel_html .= "      // 重置所有指示器
";
    $carousel_html .= "      if (indicators && indicators.length) {
";
    $carousel_html .= "        indicators.forEach(indicator => {
";
    $carousel_html .= "          if (indicator) indicator.style.backgroundColor = 'rgba(255,255,255,0.5)';
";
    $carousel_html .= "        });
";
    $carousel_html .= "      }
";
    $carousel_html .= "      
";
    $carousel_html .= "      // 显示当前轮播项
";
    $carousel_html .= "      if (items[index]) items[index].style.display = 'block';
";
    $carousel_html .= "      
";
    $carousel_html .= "      // 设置当前指示器为激活状态
";
    $carousel_html .= "      if (indicators && indicators.length > index && indicators[index]) {
";
    $carousel_html .= "        indicators[index].style.backgroundColor = 'white';
";
    $carousel_html .= "      }
";
    $carousel_html .= "      
";
    $carousel_html .= "      currentIndex = index;
";
    $carousel_html .= "    }
";
    
    $carousel_html .= "    function nextSlide() {
";
    $carousel_html .= "      let nextIndex = currentIndex + 1;
";
    $carousel_html .= "      if (nextIndex >= items.length) {
";
    $carousel_html .= "        nextIndex = 0;
";
    $carousel_html .= "      }
";
    $carousel_html .= "      showSlide(nextIndex);
";
    $carousel_html .= "    }
";
    
    $carousel_html .= "    function prevSlide() {
";
    $carousel_html .= "      let prevIndex = currentIndex - 1;
";
    $carousel_html .= "      if (prevIndex < 0) {
";
    $carousel_html .= "        prevIndex = items.length - 1;
";
    $carousel_html .= "      }
";
    $carousel_html .= "      showSlide(prevIndex);
";
    $carousel_html .= "    }
";
    
    $carousel_html .= "    // 开始自动轮播
";
    $carousel_html .= "    function startCarousel() {
";
    $carousel_html .= "      // 清除可能存在的定时器
";
    $carousel_html .= "      if (interval) clearInterval(interval);
";
    $carousel_html .= "      interval = setInterval(nextSlide, 5000); // 每5秒自动切换
";
    $carousel_html .= "    }
";
    
    $carousel_html .= "    // 停止自动轮播
";
    $carousel_html .= "    function stopCarousel() {
";
    $carousel_html .= "      clearInterval(interval);
";
    $carousel_html .= "    }
";
    
    // 添加事件监听器前进行空值检查
    $carousel_html .= "    // 添加事件监听器
";
    $carousel_html .= "    if (prevBtn) {
";
    $carousel_html .= "      prevBtn.addEventListener('click', function() {
";
    $carousel_html .= "        stopCarousel();
";
    $carousel_html .= "        prevSlide();
";
    $carousel_html .= "        startCarousel();
";
    $carousel_html .= "      });
";
    $carousel_html .= "    }
";
    
    $carousel_html .= "    if (nextBtn) {
";
    $carousel_html .= "      nextBtn.addEventListener('click', function() {
";
    $carousel_html .= "        stopCarousel();
";
    $carousel_html .= "        nextSlide();
";
    $carousel_html .= "        startCarousel();
";
    $carousel_html .= "      });
";
    $carousel_html .= "    }
";
    
    $carousel_html .= "    // 为指示器添加点击事件
";
    $carousel_html .= "    if (indicators && indicators.length) {
";
    $carousel_html .= "      indicators.forEach((indicator, index) => {
";
    $carousel_html .= "        if (indicator) {
";
    $carousel_html .= "          indicator.addEventListener('click', function() {
";
    $carousel_html .= "            stopCarousel();
";
    $carousel_html .= "            showSlide(index);
";
    $carousel_html .= "            startCarousel();
";
    $carousel_html .= "          });
";
    $carousel_html .= "        }
";
    $carousel_html .= "      });
";
    $carousel_html .= "    }
";
    
    $carousel_html .= "    // 鼠标悬停时停止轮播，离开时继续
";
    $carousel_html .= "    if (carousel) {
";
    $carousel_html .= "      carousel.addEventListener('mouseenter', stopCarousel);
";
    $carousel_html .= "      carousel.addEventListener('mouseleave', startCarousel);
";
    $carousel_html .= "    }
";
    
    $carousel_html .= "    // 开始轮播
";
    $carousel_html .= "    showSlide(0); // 确保第一个轮播项正确显示
";
    $carousel_html .= "    startCarousel();
";
    $carousel_html .= "  });
";
    $carousel_html .= "</script>
";
    
    return $carousel_html;
}

// 如果直接访问这个文件，显示错误
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    httperr();
}

?>