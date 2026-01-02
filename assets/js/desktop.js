document.addEventListener('DOMContentLoaded', () => {
    const startButton = document.getElementById('start-button');
    const startMenu = document.getElementById('start-menu');
    const windowsContainer = document.getElementById('windows-container');
    const desktopIcons = document.getElementById('desktop-icons');
    const taskbarWindowList = document.getElementById('open-windows-list');
    const hudTime = document.getElementById('hud-time');
    const hudDate = document.getElementById('hud-date');

    // --- Start Menu Logic ---
    startButton.addEventListener('click', (e) => {
        e.stopPropagation();
        startMenu.classList.toggle('open');
    });
    document.addEventListener('click', () => startMenu.classList.remove('open'));
    startMenu.addEventListener('click', (e) => e.stopPropagation());

    // --- Window Management Logic ---
    let zIndexCounter = 100;
    const openWindows = {};

    // Make createWindow a global function to be accessible by guest mode override
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

        // Add to taskbar
        const taskbarItem = document.createElement('div');
        taskbarItem.className = 'taskbar-item';
        taskbarItem.textContent = title;
        taskbarItem.dataset.windowId = windowId;
        taskbarWindowList.appendChild(taskbarItem);

        // --- Event Listeners for Window ---
        setupWindowEvents(windowElement, taskbarItem);
    };

    const setupWindowEvents = (win, taskbarItem) => {
        const titleBar = win.querySelector('.window-title-bar');

        // Focus window on click
        win.addEventListener('click', () => {
            win.style.zIndex = ++zIndexCounter;
        });

        // Dragging
        let isDragging = false;
        let offsetX, offsetY;
        titleBar.addEventListener('mousedown', (e) => {
            isDragging = true;
            offsetX = e.clientX - win.offsetLeft;
            offsetY = e.clientY - win.offsetTop;
            win.style.zIndex = ++zIndexCounter; // Bring to front
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

        // Controls
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
                // Restore
                win.classList.remove('maximized');
                win.style.top = win.dataset.originalTop;
                win.style.left = win.dataset.originalLeft;
                win.style.width = win.dataset.originalWidth;
                win.style.height = win.dataset.originalHeight;
            } else {
                // Maximize
                win.classList.add('maximized');
                // Save original dimensions and position
                win.dataset.originalTop = win.style.top;
                win.dataset.originalLeft = win.style.left;
                win.dataset.originalWidth = win.style.width;
                win.dataset.originalHeight = win.style.height;
                // Apply maximized styles
                win.style.top = '0';
                win.style.left = '0';
                win.style.width = '100%';
                win.style.height = 'calc(100vh - 40px)'; // Full viewport height minus taskbar
            }
        });

        // Taskbar interaction
        taskbarItem.addEventListener('click', () => {
            win.style.display = 'block'; // Show if minimized
            win.style.zIndex = ++zIndexCounter; // Bring to front
        });
    };

    // --- Universal Click Handler for Apps ---
    const appLaunchHandler = (e) => {
        const target = e.target.closest('[data-app-url]');
        if (target) {
            e.preventDefault();
            const url = target.dataset.appUrl;
            // Try to find a title from a span, or fallback to the element's text content
            const title = target.querySelector('span')?.innerText || target.innerText;
            createWindow(url, title);
        }
    };

    desktopIcons.addEventListener('click', appLaunchHandler);
    startMenu.addEventListener('click', appLaunchHandler);
    document.getElementById('hud-widget').addEventListener('click', appLaunchHandler);

    // --- HUD Widget Logic ---
    const updateTime = () => {
        const now = new Date();
        hudTime.textContent = now.toLocaleTimeString('ar-EG', { hour: '2-digit', minute: '2-digit' });
        hudDate.textContent = now.toLocaleDateString('ar-EG', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
    };
    setInterval(updateTime, 1000);
    updateTime();
});
