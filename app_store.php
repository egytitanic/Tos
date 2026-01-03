<?php session_start(); ?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>متجر التطبيقات - TEKNATON OS</title>
</head>
<body>

    <div class="store-header">
        <h1>متجر التطبيقات</h1>
        <p id="feedback-message" style="display:none;"></p>
    </div>

    <div class="filters">
        <input type="search" id="search-box" placeholder="ابحث عن تطبيقات...">
        <div>
            <select id="category-filter">
                <option value="all">كل الفئات</option>
                <option value="business">أعمال</option>
                <option value="tools">أدوات</option>
            </select>
            <select id="sort-by">
                <option value="newest">الأحدث</option>
                <option value="top_rated">الأعلى تقييمًا</option>
                <option value="price_asc">السعر: من الأقل</option>
                <option value="price_desc">السعر: من الأعلى</option>
            </select>
        </div>
    </div>

    <div class="apps-grid" id="apps-grid-container">
        <!-- Apps will be loaded dynamically -->
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const appsGrid = document.getElementById('apps-grid-container');
        const feedbackMessage = document.getElementById('feedback-message');
        const searchBox = document.getElementById('search-box');
        const categoryFilter = document.getElementById('category-filter');
        const sortBy = document.getElementById('sort-by');

        const renderApps = (apps) => {
            appsGrid.innerHTML = ''; // Clear current apps
            if (apps.length === 0) {
                appsGrid.innerHTML = '<p>لم يتم العثور على تطبيقات تطابق بحثك.</p>';
                return;
            }
            apps.forEach(app => {
                const priceText = parseFloat(app.price_monthly) > 0 ? `$${app.price_monthly}/شهريًا` : 'مجانًا';
                const buttonText = parseFloat(app.price_monthly) > 0 ? 'اشتراك' : 'تثبيت';
                const appCard = `
                    <div class="app-card">
                        <div class="icon">${app.icon || '📦'}</div>
                        <h3>${app.name}</h3>
                        <p class="price">${priceText}</p>
                        <button class="install-btn" data-app-id="${app.id}" data-price="${app.price_monthly}">
                            ${buttonText}
                        </button>
                    </div>`;
                appsGrid.innerHTML += appCard;
            });
        };

        const fetchAndRenderApps = () => {
            const searchTerm = searchBox.value;
            const category = categoryFilter.value;
            const sort = sortBy.value;

            const apiUrl = `api/filter_apps.php?search=${encodeURIComponent(searchTerm)}&category=${category}&sort=${sort}`;

            fetch(apiUrl)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        renderApps(data.apps);
                    } else {
                        feedbackMessage.textContent = data.message || 'فشل تحميل التطبيقات.';
                        feedbackMessage.style.color = 'red';
                        feedbackMessage.style.display = 'block';
                    }
                })
                .catch(() => {
                    feedbackMessage.textContent = 'خطأ في الشبكة.';
                    feedbackMessage.style.color = 'red';
                    feedbackMessage.style.display = 'block';
                });
        };

        // Initial load
        fetchAndRenderApps();

        // Event listeners for filters
        searchBox.addEventListener('input', fetchAndRenderApps);
        categoryFilter.addEventListener('change', fetchAndRenderApps);
        sortBy.addEventListener('change', fetchAndRenderApps);

        // Event listener for install/subscribe buttons
        appsGrid.addEventListener('click', (e) => {
            if (e.target.classList.contains('install-btn')) {
                const button = e.target;
                const appId = button.dataset.appId;
                const price = parseFloat(button.dataset.price);

                if (price > 0) {
                    // Handle subscription
                    alert('سيتم توجيهك إلى صفحة الاشتراك. (هذه الميزة قيد التطوير)');
                    // window.location.href = `subscribe.php?app_id=${appId}`;
                } else {
                    // Handle free installation
                    button.disabled = true;
                    button.textContent = '...جارٍ التثبيت';

                    const formData = new FormData();
                    formData.append('app_id', appId);

                    fetch('api/install_app.php', { method: 'POST', body: formData })
                        .then(response => response.json())
                        .then(data => {
                            feedbackMessage.textContent = data.message;
                            feedbackMessage.style.color = data.success ? 'green' : 'red';
                            feedbackMessage.style.display = 'block';
                            button.textContent = data.success ? 'تم التثبيت' : 'فشل';
                        })
                        .catch(() => {
                           feedbackMessage.textContent = 'خطأ في الشبكة.';
                           feedbackMessage.style.color = 'red';
                           feedbackMessage.style.display = 'block';
                           button.disabled = false;
                        });
                }
            }
        });
    });
    </script>
</body>
</html>
