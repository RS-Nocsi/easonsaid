// API基础URL（api.easonsaid.cn 指向 api 目录）
const API_BASE = 'https://api.easonsaid.cn';
const API_URL = `${API_BASE}/output.php`;

// DOM元素
const contentElement = document.getElementById('content');
const contentTextElement = document.getElementById('content-text');
const contentSourceElement = document.getElementById('content-source');
const loadingElement = document.getElementById('loading');
const errorElement = document.getElementById('error');
const refreshButton = document.getElementById('refresh-btn');

// 导航相关DOM元素
const hamburger = document.getElementById('hamburger');
const navMenu = document.getElementById('nav-menu');
const modal = document.getElementById('modal');
const modalBody = document.getElementById('modal-body');

// 背景图片URL
const PORTRAIT_BG_URL = `${API_BASE}/portrait_images.php`;
const LANDSCAPE_BG_URL = `${API_BASE}/landscape_images.php`;

// 导航菜单功能
function initNavigation() {
    // 汉堡菜单切换
    hamburger.addEventListener('click', () => {
        hamburger.classList.toggle('active');
        navMenu.classList.toggle('active');
    });

    // 点击菜单项关闭移动端菜单
    document.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', () => {
            hamburger.classList.remove('active');
            navMenu.classList.remove('active');
        });
    });

    // 移动端点击空白处关闭菜单
    document.addEventListener('click', (e) => {
        const isMobile = window.innerWidth <= 768;
        if (isMobile && navMenu.classList.contains('active')) {
            // 检查点击的是否在菜单外部
            const isClickInsideMenu = navMenu.contains(e.target);
            const isClickOnHamburger = hamburger.contains(e.target);
            
            if (!isClickInsideMenu && !isClickOnHamburger) {
                hamburger.classList.remove('active');
                navMenu.classList.remove('active');
            }
        }
    });

    // 阻止菜单内部的点击事件冒泡
    navMenu.addEventListener('click', (e) => {
        e.stopPropagation();
    });
}

// 显示模态框
function showModal(type) {
    const template = document.getElementById(`tpl-${type}`);
    if (template) {
        modalBody.innerHTML = template.innerHTML;
    }
    modal.classList.remove('hidden');
}

// 关闭模态框
function closeModal() {
    modal.classList.add('hidden');
}

// 初始化模态框事件
function initModal() {
    // 点击模态框外部关闭
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            closeModal();
        }
    });

    // ESC键关闭模态框
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeModal();
        }
    });
}

// 显示加载状态
function showLoading() {
    loadingElement.classList.remove('hidden');
    contentElement.classList.add('hidden');
    errorElement.classList.add('hidden');
}

// 显示内容
function showContent(content, source) {
    contentTextElement.textContent = content;
    contentSourceElement.textContent = source;
    
    loadingElement.classList.add('hidden');
    errorElement.classList.add('hidden');
    contentElement.classList.remove('hidden');
}

// 显示错误
function showError(message = '抱歉，暂时无法获取分享内容，请稍后再试。') {
    loadingElement.classList.add('hidden');
    contentElement.classList.add('hidden');
    errorElement.classList.remove('hidden');
    errorElement.textContent = message;
}

// 从API获取数据
async function fetchContent() {
    showLoading();
    
    try {
        const response = await fetch(API_URL);
        
        if (!response.ok) {
            throw new Error(`HTTP错误! 状态码: ${response.status}`);
        }
        
        const data = await response.text();
        
        // 解析返回的文本内容 - 使用中文引号
        const match = data.match(/“(.*?)” 向你分享：“(.*?)”。它出自：《(.*?)》。/);
        
        if (match) {
            const [, contributor, content, source] = match;
            showContent(content, `—— ${contributor} | 《${source}》`);
        } else {
            // 如果正则匹配失败，直接显示整个文本
            showContent(data, '');
        }
        
    } catch (error) {
        console.error('获取内容失败:', error);
        showError('网络错误，请稍后再试');
    }
}

// 设置背景图片 - 添加时间戳避免缓存
function setBackgroundImage() {
    const isPortrait = window.innerHeight > window.innerWidth;
    const baseUrl = isPortrait ? PORTRAIT_BG_URL : LANDSCAPE_BG_URL;
    
    // 生成唯一的时间戳，确保每次刷新都不同
    const timestamp = new Date().getTime();
    const bgUrl = `${baseUrl}?t=${timestamp}`;
    
    console.log('加载背景图片:', bgUrl);
    
    // 显示加载状态
    document.body.classList.add('bg-loading');
    
    // 预加载图片
    const img = new Image();
    
    const loadTimeout = setTimeout(() => {
        console.warn('背景图片加载超时');
        document.body.classList.remove('bg-loading');
        document.body.classList.add('bg-error');
    }, 8000);
    
    img.onload = function() {
        clearTimeout(loadTimeout);
        document.body.classList.remove('bg-loading');
        document.body.classList.add('bg-loaded');
        
        // 使用CSS变量设置背景图片
        document.documentElement.style.setProperty('--bg-image', `url('${bgUrl}')`);
    };
    
    img.onerror = function() {
        clearTimeout(loadTimeout);
        console.error('背景图片加载失败');
        document.body.classList.remove('bg-loading');
        document.body.classList.add('bg-error');
    };
    
    img.src = bgUrl;
}

// 刷新按钮点击事件 - 只刷新文字内容
refreshButton.addEventListener('click', function() {
    console.log('刷新文字内容');
    
    // 添加按钮点击动画
    this.classList.add('refreshing');
    
    // 只刷新文本内容
    fetchContent();
    
    // 移除动画类
    setTimeout(() => {
        this.classList.remove('refreshing');
    }, 1000);
});

// 页面加载时初始化
document.addEventListener('DOMContentLoaded', function() {
    // 初始化导航功能
    initNavigation();
    initModal();
    
    // 自动填充版权年份
    document.querySelectorAll('.year').forEach(el => el.textContent = new Date().getFullYear());
    
    // 初始加载内容和背景
    fetchContent();
    setBackgroundImage();
});

// 监听窗口大小变化（设备旋转）- 重新加载对应方向的背景图片
let resizeTimeout;
window.addEventListener('resize', function() {
    clearTimeout(resizeTimeout);
    resizeTimeout = setTimeout(setBackgroundImage, 250);
});

// 监听设备方向变化（移动设备旋转时切换竖屏/横屏背景）
window.addEventListener('orientationchange', function() {
    setTimeout(setBackgroundImage, 100);
});

// 监听页面显示事件（当用户切换回页面时）
document.addEventListener('visibilitychange', function() {
    if (!document.hidden) {
        // 页面重新可见时，可以在这里决定是否刷新背景
        // 为了性能考虑，这里不自动刷新
    }
});

// 全局函数，供HTML调用
window.showModal = showModal;
window.closeModal = closeModal;