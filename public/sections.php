<?php
require_once 'include/bittorrent.php';
require_once 'include/user_functions.php';
require_once 'include/bbcode_functions.php';

loggedinorreturn();

stdhead('Sections');

// 获取CSRF令牌
$csrfToken = get_form_token();

// 判断用户是否为管理员
$isAdmin = check_user_class(UC_ADMINISTRATOR);
?>
<meta name="csrf-token" content="<?php echo htmlspecialchars($csrfToken); ?>">
<div class="container" style="max-width: 1200px; margin: 0 auto;">
    <h1>Sections</h1>
    
    <!-- 轮播图组件 -->
    <div class="carousel-container" style="width: 100%; margin: 20px 0; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);">
        <div class="carousel" id="sections-carousel" style="position: relative; width: 100%; height: 250px;">
            <!-- 轮播项容器 -->
            <div class="carousel-items" style="display: flex; transition: transform 0.5s ease; height: 100%;">
                <!-- 轮播项将通过JavaScript动态加载 -->
                <div class="carousel-item-loading" style="min-width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: #f5f5f5; color: #666;">
                    <div>加载中...</div>
                </div>
            </div>
            
            <!-- 轮播指示器 -->
            <div class="carousel-indicators" style="position: absolute; bottom: 15px; left: 50%; transform: translateX(-50%); display: flex; gap: 10px;">
                <!-- 指示器将通过JavaScript动态加载 -->
            </div>
            
            <!-- 轮播控制按钮 -->
            <button class="carousel-control prev" style="position: absolute; top: 50%; left: 15px; transform: translateY(-50%); width: 45px; height: 45px; border-radius: 50%; border: none; background: rgba(0, 0, 0, 0.6); color: white; cursor: pointer; font-size: 24px; display: flex; align-items: center; justify-content: center; transition: all 0.3s ease;">‹</button>
            <button class="carousel-control next" style="position: absolute; top: 50%; right: 15px; transform: translateY(-50%); width: 45px; height: 45px; border-radius: 50%; border: none; background: rgba(0, 0, 0, 0.6); color: white; cursor: pointer; font-size: 24px; display: flex; align-items: center; justify-content: center; transition: all 0.3s ease;">›</button>
        </div>
    </div>
    
    <!-- 管理员操作按钮 -->
    <?php if ($isAdmin) { ?>
    <div class="admin-actions" style="margin: 15px 0; text-align: right;">
        <button id="add-carousel-btn" class="btn btn-primary" style="padding: 8px 16px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer;">
            添加轮播图
        </button>
    </div>
    <?php } ?>
    
    <!-- Sections 内容容器 -->
    <div class="sections-content" style="margin-top: 30px;">
        <h2>可用分类</h2>
        <div id="sections-list" style="margin-top: 20px;">
            <!-- Sections 将通过JavaScript动态加载 -->
            <div class="loading" style="text-align: center; padding: 40px; color: #666;">
                加载分类中...
            </div>
        </div>
    </div>
</div>

<!-- 轮播图编辑模态框 -->
<div id="carousel-modal" class="modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 1000; justify-content: center; align-items: center;">
    <div class="modal-content" style="background: white; padding: 30px; border-radius: 8px; width: 90%; max-width: 500px; max-height: 80vh; overflow-y: auto;">
        <div class="modal-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 15px;">
            <h3 id="modal-title">编辑轮播图</h3>
            <button id="close-modal" style="background: none; border: none; font-size: 24px; cursor: pointer; color: #666;">×</button>
        </div>
        <form id="carousel-form">
            <input type="hidden" id="carousel-id" value="">
            <div class="form-group" style="margin-bottom: 20px;">
                <label for="carousel-title" style="display: block; margin-bottom: 8px; font-weight: 500;">标题 *</label>
                <input type="text" id="carousel-title" name="title" required maxlength="100" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
            </div>
            <div class="form-group" style="margin-bottom: 20px;">
                <label for="carousel-description" style="display: block; margin-bottom: 8px; font-weight: 500;">描述</label>
                <input type="text" id="carousel-description" name="description" maxlength="255" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
            </div>
            <div class="form-group" style="margin-bottom: 20px;">
                <label for="carousel-link" style="display: block; margin-bottom: 8px; font-weight: 500;">链接</label>
                <input type="url" id="carousel-link" name="link" maxlength="255" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
            </div>
            <div class="form-group" style="margin-bottom: 20px;">
                <label for="carousel-background" style="display: block; margin-bottom: 8px; font-weight: 500;">背景</label>
                <input type="text" id="carousel-background" name="background" value="linear-gradient(135deg, #667eea 0%, #764ba2 100%)" maxlength="255" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                <small style="display: block; margin-top: 5px; color: #666;">可以是CSS颜色、渐变或图片URL</small>
            </div>
            <div class="form-group" style="margin-bottom: 20px;">
                <label for="carousel-sort-order" style="display: block; margin-bottom: 8px; font-weight: 500;">排序顺序 *</label>
                <input type="number" id="carousel-sort-order" name="sort_order" required min="0" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
            </div>
            <div class="form-group" style="margin-bottom: 20px;">
                <label style="display: flex; align-items: center; font-weight: 500;">
                    <input type="checkbox" id="carousel-active" name="active" checked style="margin-right: 10px;">
                    启用此轮播图
                </label>
            </div>
            <div class="form-actions" style="margin-top: 30px; text-align: right;">
                <button type="button" id="delete-carousel-btn" class="btn btn-danger" style="padding: 10px 20px; background: #dc3545; color: white; border: none; border-radius: 4px; cursor: pointer; margin-right: 10px; display: none;">
                    删除
                </button>
                <button type="submit" class="btn btn-success" style="padding: 10px 30px; background: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer;">
                    保存
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    /* 轮播图样式 */
    .carousel-control:hover {
        background: rgba(0, 0, 0, 0.8);
        transform: translateY(-50%) scale(1.1);
    }
    
    .carousel-indicator {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        border: none;
        background: rgba(255, 255, 255, 0.6);
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .carousel-indicator.active {
        background: white;
        transform: scale(1.3);
    }
    
    .carousel-indicator:hover {
        background: rgba(255, 255, 255, 0.9);
        transform: scale(1.2);
    }
    
    .carousel-item {
        min-width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        text-align: center;
        padding: 20px;
    }
    
    .carousel-item h3 {
        margin: 0 0 15px 0;
        font-size: 28px;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
    }
    
    .carousel-item p {
        margin: 0;
        font-size: 18px;
        text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.5);
    }
    
    .carousel-item a {
        text-decoration: none;
        color: white;
        display: block;
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    /* 响应式设计 */
    @media (max-width: 768px) {
        .carousel {
            height: 200px !important;
        }
        
        .carousel-item h3 {
            font-size: 24px;
        }
        
        .carousel-item p {
            font-size: 16px;
        }
        
        .carousel-control {
            width: 35px !important;
            height: 35px !important;
            font-size: 20px;
        }
    }
    
    @media (max-width: 480px) {
        .carousel {
            height: 150px !important;
        }
        
        .carousel-item h3 {
            font-size: 20px;
        }
        
        .carousel-item p {
            font-size: 14px;
        }
        
        .carousel-indicator {
            width: 10px;
            height: 10px;
        }
    }
    
    /* Section 卡片样式 */
    .section-card {
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
        background: #fff;
        transition: all 0.3s ease;
    }
    
    .section-card:hover {
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
    }
    
    .section-card h3 {
        margin: 0 0 15px 0;
        color: #333;
        font-size: 22px;
    }
    
    .section-content {
        color: #666;
    }
    
    /* 加载动画 */
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    .loading::after {
        content: '';
        display: inline-block;
        width: 20px;
        height: 20px;
        border: 2px solid #f3f3f3;
        border-top: 2px solid #007bff;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin-left: 10px;
    }
</style>

<script>
    // 全局变量
    let carouselIndex = 0;
    let carouselItems = null;
    let carouselIndicators = null;
    let carouselTimer = null;
    let carouselsData = [];
    let isAdmin = <?php echo $isAdmin ? 'true' : 'false'; ?>;
    
    // 页面加载完成后初始化
    document.addEventListener('DOMContentLoaded', function() {
        loadSections();
        loadCarousels();
        initModal();
    });
    
    // 加载轮播图数据
    function loadCarousels() {
        fetch('/api/carousels/active')
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success' && data.data && data.data.length > 0) {
                    carouselsData = data.data;
                    renderCarousel();
                    carouselInit();
                } else {
                    // 显示默认轮播图
                    renderDefaultCarousel();
                    carouselInit();
                }
            })
            .catch(error => {
                console.error('加载轮播图失败:', error);
                renderDefaultCarousel();
                carouselInit();
            });
    }
    
    // 加载Sections数据
    function loadSections() {
        fetch('/api/sections')
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success' && data.data && data.data.sections) {
                    renderSections(data.data.sections);
                } else {
                    document.getElementById('sections-list').innerHTML = '<div class="error" style="color: #dc3545; text-align: center; padding: 40px;">加载分类失败</div>';
                }
            })
            .catch(error => {
                console.error('加载分类失败:', error);
                document.getElementById('sections-list').innerHTML = '<div class="error" style="color: #dc3545; text-align: center; padding: 40px;">加载分类失败</div>';
            });
    }
    
    // 渲染轮播图
    function renderCarousel() {
        const carouselItemsContainer = document.querySelector('.carousel-items');
        const indicatorsContainer = document.querySelector('.carousel-indicators');
        
        // 清空现有内容
        carouselItemsContainer.innerHTML = '';
        indicatorsContainer.innerHTML = '';
        
        // 添加轮播项
        carouselsData.forEach((carousel, index) => {
            const item = document.createElement('div');
            item.className = 'carousel-item';
            item.style.minWidth = '100%';
            item.style.height = '100%';
            item.style.background = carousel.background || 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)';
            item.style.display = 'flex';
            item.style.alignItems = 'center';
            item.style.justifyContent = 'center';
            item.style.color = 'white';
            item.style.textAlign = 'center';
            
            let content = '';
            if (carousel.link) {
                content = `<a href="${carousel.link}" style="text-decoration: none; color: white; display: block; width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; flex-direction: column;">`;
            }
            content += `<h3>${carousel.title}</h3>`;
            if (carousel.description) {
                content += `<p>${carousel.description}</p>`;
            }
            if (carousel.link) {
                content += `</a>`;
            }
            
            item.innerHTML = content;
            carouselItemsContainer.appendChild(item);
            
            // 添加指示器
            const indicator = document.createElement('button');
            indicator.className = `carousel-indicator ${index === 0 ? 'active' : ''}`;
            indicator.setAttribute('data-index', index);
            indicator.style.width = '12px';
            indicator.style.height = '12px';
            indicator.style.borderRadius = '50%';
            indicator.style.border = 'none';
            indicator.style.background = index === 0 ? 'white' : 'rgba(255, 255, 255, 0.6)';
            indicator.style.cursor = 'pointer';
            indicator.style.transition = 'all 0.3s ease';
            indicator.onclick = function() {
                carouselGoTo(index);
            };
            indicatorsContainer.appendChild(indicator);
        });
    }
    
    // 渲染默认轮播图
    function renderDefaultCarousel() {
        const defaultCarousels = [
            {
                title: '欢迎来到PT站',
                description: '享受高速下载体验',
                link: '#',
                background: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)'
            },
            {
                title: '最新热门种子',
                description: '每周更新精选内容',
                link: '#',
                background: 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)'
            },
            {
                title: '邀请好友',
                description: '',
                link: '#',
                background: 'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)'
            }
        ];
        
        carouselsData = defaultCarousels;
        renderCarousel();
    }
    
    // 渲染Sections
    function renderSections(sections) {
        const sectionsList = document.getElementById('sections-list');
        sectionsList.innerHTML = '';
        
        if (!sections.length) {
            sectionsList.innerHTML = '<div class="no-data" style="text-align: center; padding: 40px; color: #666;">暂无可用分类</div>';
            return;
        }
        
        sections.forEach(section => {
            const card = document.createElement('div');
            card.className = 'section-card';
            
            const content = `
                <h3>${section.name || '未命名分类'}</h3>
                <div class="section-content">
                    <p>包含 ${section.categories?.length || 0} 个子分类</p>
                </div>
            `;
            
            card.innerHTML = content;
            sectionsList.appendChild(card);
        });
    }
    
    // 轮播图初始化
    function carouselInit() {
        const carousel = document.getElementById('sections-carousel');
        if (!carousel) return;
        
        carouselItems = carousel.querySelector('.carousel-items');
        carouselIndicators = carousel.querySelectorAll('.carousel-indicator');
        const prevBtn = carousel.querySelector('.carousel-control.prev');
        const nextBtn = carousel.querySelector('.carousel-control.next');
        
        // 设置按钮点击事件
        if (prevBtn) {
            prevBtn.onclick = function() {
                carouselGoToPrev();
            };
        }
        
        if (nextBtn) {
            nextBtn.onclick = function() {
                carouselGoToNext();
            };
        }
        
        // 设置鼠标悬停事件
        carousel.onmouseenter = function() {
            carouselStop();
        };
        
        carousel.onmouseleave = function() {
            carouselStart();
        };
        
        // 初始化显示
        carouselUpdate();
        // 开始自动轮播
        carouselStart();
    }
    
    // 轮播图方法
    function carouselGoToNext() {
        carouselStop();
        carouselIndex = (carouselIndex + 1) % carouselsData.length;
        carouselUpdate();
        carouselStart();
    }
    
    function carouselGoToPrev() {
        carouselStop();
        carouselIndex = (carouselIndex - 1 + carouselsData.length) % carouselsData.length;
        carouselUpdate();
        carouselStart();
    }
    
    function carouselGoTo(index) {
        carouselStop();
        carouselIndex = index;
        carouselUpdate();
        carouselStart();
    }
    
    function carouselUpdate() {
        if (carouselItems) {
            carouselItems.style.transform = 'translateX(-' + (carouselIndex * 100) + '%)';
        }
        
        if (carouselIndicators) {
            for (let i = 0; i < carouselIndicators.length; i++) {
                if (i === carouselIndex) {
                    carouselIndicators[i].classList.add('active');
                    carouselIndicators[i].style.background = 'white';
                } else {
                    carouselIndicators[i].classList.remove('active');
                    carouselIndicators[i].style.background = 'rgba(255, 255, 255, 0.6)';
                }
            }
        }
    }
    
    function carouselStart() {
        carouselStop();
        carouselTimer = setInterval(function() {
            carouselGoToNext();
        }, 5000); // 5秒切换一次
    }
    
    function carouselStop() {
        if (carouselTimer) {
            clearInterval(carouselTimer);
            carouselTimer = null;
        }
    }
    
    // 初始化模态框
    function initModal() {
        if (!isAdmin) return;
        
        const modal = document.getElementById('carousel-modal');
        const closeBtn = document.getElementById('close-modal');
        const addBtn = document.getElementById('add-carousel-btn');
        const form = document.getElementById('carousel-form');
        const deleteBtn = document.getElementById('delete-carousel-btn');
        
        // 打开添加模态框
        addBtn.onclick = function() {
            document.getElementById('modal-title').textContent = '添加轮播图';
            document.getElementById('carousel-id').value = '';
            form.reset();
            document.getElementById('carousel-sort-order').value = carouselsData.length + 1;
            deleteBtn.style.display = 'none';
            modal.style.display = 'flex';
        };
        
        // 关闭模态框
        closeBtn.onclick = function() {
            modal.style.display = 'none';
        };
        
        // 点击模态框外部关闭
        window.onclick = function(event) {
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        };
        
        // 提交表单
        form.onsubmit = function(e) {
            e.preventDefault();
            saveCarousel();
        };
        
        // 删除轮播图
        deleteBtn.onclick = function() {
            if (confirm('确定要删除这个轮播图吗？')) {
                deleteCarousel();
            }
        };
    }
    
    // 保存轮播图
    function saveCarousel() {
        const id = document.getElementById('carousel-id').value;
        const data = {
            title: document.getElementById('carousel-title').value,
            description: document.getElementById('carousel-description').value,
            link: document.getElementById('carousel-link').value,
            background: document.getElementById('carousel-background').value,
            sort_order: parseInt(document.getElementById('carousel-sort-order').value),
            active: document.getElementById('carousel-active').checked
        };
        
        let url = '/api/carousels';
        let method = 'POST';
        
        if (id) {
            url += '/' + id;
            method = 'PUT';
        }
        
        fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(result => {
            if (result.status === 'success') {
                alert('保存成功！');
                document.getElementById('carousel-modal').style.display = 'none';
                loadCarousels(); // 重新加载轮播图
            } else {
                alert('保存失败：' + (result.message || '未知错误'));
            }
        })
        .catch(error => {
            console.error('保存失败:', error);
            alert('保存失败，请检查网络连接');
        });
    }
    
    // 删除轮播图
    function deleteCarousel() {
        const id = document.getElementById('carousel-id').value;
        
        fetch('/api/carousels/' + id, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(result => {
            if (result.status === 'success') {
                alert('删除成功！');
                document.getElementById('carousel-modal').style.display = 'none';
                loadCarousels(); // 重新加载轮播图
            } else {
                alert('删除失败：' + (result.message || '未知错误'));
            }
        })
        .catch(error => {
            console.error('删除失败:', error);
            alert('删除失败，请检查网络连接');
        });
    }
    
    // 为轮播图添加编辑功能
    function addEditFunctionality() {
        if (!isAdmin || !carouselsData.length) return;
        
        const items = document.querySelectorAll('.carousel-item');
        items.forEach((item, index) => {
            const editBtn = document.createElement('button');
            editBtn.textContent = '编辑';
            editBtn.style.position = 'absolute';
            editBtn.style.top = '10px';
            editBtn.style.right = '10px';
            editBtn.style.padding = '5px 10px';
            editBtn.style.background = 'rgba(0, 0, 0, 0.7)';
            editBtn.style.color = 'white';
            editBtn.style.border = 'none';
            editBtn.style.borderRadius = '4px';
            editBtn.style.cursor = 'pointer';
            editBtn.style.fontSize = '12px';
            editBtn.onclick = function(e) {
                e.stopPropagation();
                openEditModal(index);
            };
            
            // 添加到父元素，而不是item内部，避免点击编辑按钮触发链接跳转
            const carousel = document.getElementById('sections-carousel');
            carousel.appendChild(editBtn);
        });
    }
    
    // 打开编辑模态框
    function openEditModal(index) {
        const carousel = carouselsData[index];
        if (!carousel) return;
        
        document.getElementById('modal-title').textContent = '编辑轮播图';
        document.getElementById('carousel-id').value = carousel.id || '';
        document.getElementById('carousel-title').value = carousel.title || '';
        document.getElementById('carousel-description').value = carousel.description || '';
        document.getElementById('carousel-link').value = carousel.link || '';
        document.getElementById('carousel-background').value = carousel.background || '';
        document.getElementById('carousel-sort-order').value = carousel.sort_order || (index + 1);
        document.getElementById('carousel-active').checked = carousel.active !== false;
        document.getElementById('delete-carousel-btn').style.display = carousel.id ? 'inline-block' : 'none';
        
        document.getElementById('carousel-modal').style.display = 'flex';
    }
    
    // 确保在渲染轮播图后添加编辑功能
    function renderCarouselWithEdit() {
        renderCarousel();
        if (isAdmin) {
            // 延迟添加编辑按钮，确保DOM已更新
            setTimeout(addEditFunctionality, 100);
        }
    }
    
    // 重写renderCarousel函数
    const originalRenderCarousel = renderCarousel;
    renderCarousel = renderCarouselWithEdit;
</script>

<?php
stdfoot();
