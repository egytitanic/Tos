document.addEventListener('DOMContentLoaded', () => {
    // Existing selectors...
    const desktopIconsContainer = document.getElementById('desktop-icons');
    const mostUsedList = document.getElementById('most-used-apps');

    // --- Function to dynamically load installed apps ---
    const loadInstalledApps = () => {
        fetch('api/get_installed_apps.php')
            .then(response => response.json())
            .then(data => {
                if (data.success && data.apps) {
                    data.apps.forEach(app => {
                        const iconElement = document.createElement('div');
                        iconElement.className = 'desktop-icon';
                        // Use run_app.php as the URL source
                        iconElement.dataset.appUrl = `run_app.php?id=${app.id}`;

                        iconElement.innerHTML = `
                            <div class="icon-placeholder">${app.icon || '📦'}</div>
                            <span>${app.name}</span>
                        `;
                        desktopIconsContainer.appendChild(iconElement);
                    });
                }
            })
            .catch(error => {
                console.error("Failed to load installed apps:", error);
            });
    };

    // --- Function to load most used apps ---
    const loadMostUsedApps = () => {
        if (!mostUsedList) return;

        fetch('/api/most_used_apps.php')
            .then(response => response.json())
            .then(apps => {
                mostUsedList.innerHTML = ''; // Clear existing items
                if (apps.error) {
                    console.error(apps.error);
                    return;
                }
                if (apps.length === 0) {
                    mostUsedList.innerHTML = '<li>لا توجد بيانات استخدام بعد.</li>';
                    return;
                }
                apps.forEach(app => {
                    const li = document.createElement('li');
                    const a = document.createElement('a');
                    a.href = '#';
                    a.textContent = app.name;
                    a.setAttribute('data-app-url', `run_app.php?id=${app.id}`);
                    li.appendChild(a);
                    mostUsedList.appendChild(li);
                });
            })
            .catch(error => {
                console.error('Error fetching most used apps:', error);
                mostUsedList.innerHTML = '<li>فشل تحميل التطبيقات.</li>';
            });
    };

    // --- Function to update notification badge ---
    const updateNotificationBadge = () => {
        const notificationsIcon = document.getElementById('notifications-icon');
        if (!notificationsIcon) return;

        fetch('/api/notifications_count.php')
            .then(response => response.json())
            .then(data => {
                if (data.unread_count > 0) {
                    notificationsIcon.setAttribute('data-count', data.unread_count);
                    notificationsIcon.classList.add('has-notifications');
                } else {
                    notificationsIcon.removeAttribute('data-count');
                    notificationsIcon.classList.remove('has-notifications');
                }
            })
            .catch(error => console.error('Error fetching notification count:', error));
    };

    // --- Initial Load & Periodic Updates ---
    loadInstalledApps();
    loadMostUsedApps();
    updateNotificationBadge();
    setInterval(updateNotificationBadge, 30000); // Update every 30 seconds

    // --- All other existing JS code for window management, etc. ---
    // ... (rest of the code is the same)
    const startButton = document.getElementById('start-button');
    const startMenu = document.getElementById('start-menu');
    const windowsContainer = document.getElementById('windows-container');
    const taskbarWindowList = document.getElementById('open-windows-list');
    const hudTime = document.getElementById('hud-time');
    const hudDate = document.getElementById('hud-date');

    startButton.addEventListener('click', (e) => {
        e.stopPropagation();
        startMenu.classList.toggle('open');
    });
    document.addEventListener('click', () => startMenu.classList.remove('open'));
    startMenu.addEventListener('click', (e) => e.stopPropagation());

    let zIndexCounter = 100;
    const openWindows = {};

    window.createWindow = (url, title) => {
        const windowId = `window-${Date.now()}`;
        zIndexCounter++;

        const windowElement = document.createElement('div');
        windowElement.className = 'app-window';
        windowElement.style.zIndex = zIndexCounter;
        windowElement.dataset.windowId = windowId;

        windowElement.innerHTML = `
            <div class="window-title-bar">
                <span class="title">${title}</span>
                <div class="window-controls">
                    <button class="minimize">-</button>
                    <button class="maximize">[]</button>
                    <button class="close">X</button>
                </div>
            </div>
            <div class="window-content"><iframe src="${url}"></iframe></div>`;

        windowsContainer.appendChild(windowElement);
        openWindows[windowId] = windowElement;

        const taskbarItem = document.createElement('div');
        taskbarItem.className = 'taskbar-item';
        taskbarItem.textContent = title;
        taskbarItem.dataset.windowId = windowId;
        taskbarWindowList.appendChild(taskbarItem);

        setupWindowEvents(windowElement, taskbarItem);
    };

    const setupWindowEvents = (win, taskbarItem) => {
        const titleBar = win.querySelector('.window-title-bar');

        win.addEventListener('click', () => {
            win.style.zIndex = ++zIndexCounter;
        });

        let isDragging = false;
        let offsetX, offsetY;
        titleBar.addEventListener('mousedown', (e) => {
            isDragging = true;
            offsetX = e.clientX - win.offsetLeft;
            offsetY = e.clientY - win.offsetTop;
            win.style.zIndex = ++zIndexCounter;
        });
        document.addEventListener('mousemove', (e) => {
            if (isDragging) {
                win.style.left = `${e.clientX - offsetX}px`;
                win.style.top = `${e.clientY - offsetY}px`;
            }
        });
        document.addEventListener('mouseup', () => {
            isDragging = false;
        });

        win.querySelector('.close').addEventListener('click', () => {
            win.remove();
            taskbarItem.remove();
            delete openWindows[win.dataset.windowId];
        });
        win.querySelector('.minimize').addEventListener('click', () => {
            win.style.display = 'none';
        });
        win.querySelector('.maximize').addEventListener('click', () => {
            if (win.classList.contains('maximized')) {
                win.classList.remove('maximized');
                win.style.top = win.dataset.originalTop;
                win.style.left = win.dataset.originalLeft;
                win.style.width = win.dataset.originalWidth;
                win.style.height = win.dataset.originalHeight;
            } else {
                win.classList.add('maximized');
                win.dataset.originalTop = win.style.top;
                win.dataset.originalLeft = win.style.left;
                win.dataset.originalWidth = win.style.width;
                win.dataset.originalHeight = win.style.height;
                win.style.top = '0';
                win.style.left = '0';
                win.style.width = '100%';
                win.style.height = 'calc(100vh - 40px)';
            }
        });

        taskbarItem.addEventListener('click', () => {
            win.style.display = 'block';
            win.style.zIndex = ++zIndexCounter;
        });
    };

    const appLaunchHandler = (e) => {
        const target = e.target.closest('[data-app-url]');
        if (target) {
            e.preventDefault();
            const url = target.dataset.appUrl;
            const title = target.querySelector('span')?.innerText || target.innerText;
            createWindow(url, title);
        }
    };

    desktopIconsContainer.addEventListener('click', appLaunchHandler);
    startMenu.addEventListener('click', appLaunchHandler);
    document.getElementById('hud-widget').addEventListener('click', appLaunchHandler);

    const updateTime = () => {
        const now = new Date();
        hudTime.textContent = now.toLocaleTimeString('ar-EG', { hour: '2-digit', minute: '2-digit' });
        hudDate.textContent = now.toLocaleDateString('ar-EG', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
    };
    setInterval(updateTime, 1000);
    updateTime();
});
