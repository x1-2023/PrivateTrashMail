// OpenTrashMail JavaScript Functions - Enhanced UI

// Lưu trạng thái sidebar trong localStorage
function toggleSidebar() {
    const sidebar = document.getElementById('app-sidebar');
    const isSidebarOpen = sidebar.classList.toggle('sidebar-open');
    localStorage.setItem('sidebarOpen', isSidebarOpen);
    
    // Thêm hiệu ứng ripple khi click
    const toggle = document.getElementById('sidebar-toggle');
    if (toggle) {
        const ripple = document.createElement('span');
        ripple.classList.add('ripple-effect');
        toggle.appendChild(ripple);
        setTimeout(() => ripple.remove(), 600);
    }
}

// Khôi phục trạng thái sidebar khi tải trang
function initSidebar() {
    const sidebar = document.getElementById('app-sidebar');
    const sidebarToggle = document.getElementById('sidebar-toggle');
    
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', toggleSidebar);
    }
    
    // Khôi phục trạng thái từ localStorage
    const isSidebarOpen = localStorage.getItem('sidebarOpen') === 'true';
    if (isSidebarOpen && window.innerWidth < 768) {
        sidebar.classList.add('sidebar-open');
    }
    
    // Đóng sidebar khi click bên ngoài trên mobile
    document.addEventListener('click', function(event) {
        if (window.innerWidth < 768 && 
            !event.target.closest('#app-sidebar') && 
            !event.target.closest('#sidebar-toggle') &&
            sidebar.classList.contains('sidebar-open')) {
            sidebar.classList.remove('sidebar-open');
            localStorage.setItem('sidebarOpen', false);
        }
    });
    
    // Thêm hiệu ứng active cho menu item
    const menuItems = document.querySelectorAll('.sidebar-item');
    menuItems.forEach(item => {
        item.addEventListener('click', function() {
            menuItems.forEach(i => i.classList.remove('active'));
            this.classList.add('active');
        });
    });
}

// Tạo email ngẫu nhiên với hiệu ứng
function generateRandomEmail() {
    const chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
    let randomName = '';
    const length = Math.floor(Math.random() * 8) + 5; // 5-12 ký tự
    
    for (let i = 0; i < length; i++) {
        randomName += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    
    const emailInput = document.getElementById('email_name');
    if (emailInput) {
        // Hiệu ứng typing
        emailInput.value = '';
        let i = 0;
        const typeInterval = setInterval(() => {
            if (i < randomName.length) {
                emailInput.value += randomName.charAt(i);
                i++;
            } else {
                clearInterval(typeInterval);
                // Trigger nút Get mail sau khi hoàn thành typing
                setTimeout(() => document.getElementById('getmail').click(), 300);
            }
        }, 50);
    }
}

// Copy email vào clipboard với hiệu ứng và fallback
function copyEmailToClipboard(email) {
    if (!email) {
        // Thử lấy từ thuộc tính data-email của nút copy
        const btn = document.getElementById('copyemailbtn');
        if (btn && btn.dataset.email) {
            email = btn.dataset.email;
        }
    }
    
    if (email) {
        const btn = document.getElementById('copyemailbtn');
        const originalContent = btn ? btn.innerHTML : '';
        
        // Phương pháp 1: Sử dụng Clipboard API nếu có sẵn
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(email)
                .then(() => {
                    if (btn) {
                        btn.innerHTML = '<span class="material-symbols-outlined">check_circle</span> Đã sao chép!';
                        btn.classList.add('copied');
                        
                        // Khôi phục nội dung ban đầu sau 2 giây
                        setTimeout(() => {
                            btn.innerHTML = originalContent;
                            btn.classList.remove('copied');
                        }, 2000);
                    }
                })
                .catch(err => {
                    // Nếu Clipboard API thất bại, sử dụng phương pháp thay thế
                    fallbackCopyTextToClipboard(email, btn, originalContent);
                });
        } else {
            // Phương pháp 2: Fallback cho các trình duyệt không hỗ trợ Clipboard API
            fallbackCopyTextToClipboard(email, btn, originalContent);
        }
    }
}

// Phương pháp thay thế để sao chép văn bản
function fallbackCopyTextToClipboard(text, btn, originalContent) {
    const textArea = document.createElement("textarea");
    textArea.value = text;
    
    // Tránh cuộn xuống
    textArea.style.top = "0";
    textArea.style.left = "0";
    textArea.style.position = "fixed";
    textArea.style.opacity = "0";
    
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();
    
    try {
        const successful = document.execCommand('copy');
        if (successful && btn) {
            btn.innerHTML = '<span class="material-symbols-outlined">check_circle</span> Đã sao chép!';
            btn.classList.add('copied');
            
            // Khôi phục nội dung ban đầu sau 2 giây
            setTimeout(() => {
                btn.innerHTML = originalContent;
                btn.classList.remove('copied');
            }, 2000);
        }
    } catch (err) {
        console.error('Không thể sao chép: ', err);
        if (btn) {
            btn.innerHTML = '<span class="material-symbols-outlined">error</span> Lỗi sao chép';
            setTimeout(() => {
                btn.innerHTML = originalContent;
            }, 2000);
        }
    }
    
    document.body.removeChild(textArea);
}

// Làm mới danh sách email
function refreshEmails() {
    const currentUrl = window.location.pathname;
    if (currentUrl.includes('/address/')) {
        htmx.ajax('GET', '/api' + currentUrl, {target:'#main-content'});
    }
}

// Debounce function để tránh gọi quá nhiều lần
function debounce(func, wait) {
    let timeout;
    return function(...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), wait);
    };
}

// Tìm kiếm email với debounce
function setupSearch() {
    const searchInput = document.getElementById('search-mail');
    if (!searchInput) return;
    
    const performSearch = debounce(function(query) {
        // Thực hiện tìm kiếm
        console.log('Tìm kiếm:', query);
        // TODO: Implement search functionality
        
        // Highlight kết quả tìm kiếm
        if (query.trim() !== '') {
            const emailRows = document.querySelectorAll('table tbody tr');
            emailRows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (text.includes(query.toLowerCase())) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        } else {
            // Hiển thị lại tất cả khi xóa query
            const emailRows = document.querySelectorAll('table tbody tr');
            emailRows.forEach(row => {
                row.style.display = '';
            });
        }
    }, 300);
    
    searchInput.addEventListener('input', function(e) {
        performSearch(e.target.value);
    });
}

// Tự động làm mới email mỗi 15 giây
function setupAutoRefresh() {
    let autoReloadInterval;
    
    function startAutoReload() {
        autoReloadInterval = setInterval(function() {
            const currentUrl = window.location.pathname;
            if (currentUrl.includes('/address/')) {
                refreshEmails();
                console.log('Tự động làm mới email');
            }
        }, 15000); // 15 giây
    }
    
    function stopAutoReload() {
        clearInterval(autoReloadInterval);
    }
    
    // Bắt đầu auto reload
    startAutoReload();
    
    // Dừng khi tab không active
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            stopAutoReload();
        } else {
            startAutoReload();
        }
    });
    
    return { start: startAutoReload, stop: stopAutoReload };
}

// Cập nhật số email chưa đọc
function updateUnreadCount(count) {
    const unreadBadge = document.querySelector('.unread-count');
    if (unreadBadge) {
        unreadBadge.textContent = count;
        unreadBadge.style.display = count > 0 ? 'inline-flex' : 'none';
    }
}

// Khởi tạo khi trang đã tải
document.addEventListener('DOMContentLoaded', function() {
    // Khởi tạo sidebar
    initSidebar();
    
    // Thiết lập tìm kiếm
    setupSearch();
    
    // Thiết lập tự động làm mới
    const autoRefresh = setupAutoRefresh();
    
    // Xử lý nút Get mail
    const getMailBtn = document.getElementById('getmail');
    if (getMailBtn) {
        getMailBtn.onclick = function(e) {
            e.preventDefault();
            const name = document.getElementById('email_name').value.trim();
            const domain = document.getElementById('email_domain').value.trim();
            if(!name) { alert('Vui lòng nhập tên email'); return; }
            const email = name + '@' + domain;
            htmx.ajax('POST', '/api/address', {target:'#main-content', values:{email:email}});
        };
    }
    
    // Enter trong input cũng trigger nút
    const emailNameInput = document.getElementById('email_name');
    if (emailNameInput) {
        emailNameInput.addEventListener('keydown', function(e){
            if(e.key==='Enter') document.getElementById('getmail').click();
        });
    }
    
    // Nút tạo email ngẫu nhiên
    const composeBtn = document.getElementById('compose-btn');
    if (composeBtn) {
        composeBtn.addEventListener('click', generateRandomEmail);
    }
    
    // Nút làm mới
    const refreshBtn = document.getElementById('refresh-btn');
    if (refreshBtn) {
        refreshBtn.addEventListener('click', refreshEmails);
    }
    
    // Ví dụ: Cập nhật số email chưa đọc
    setTimeout(() => updateUnreadCount(3), 1000);
});