<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>خدماتنا - TEKNATON OS</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f2f5;
            padding: 20px;
            line-height: 1.6;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .service-card {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .service-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .service-card h3 {
            margin-top: 0;
            color: #1a73e8;
        }
        #service-details {
            margin-top: 30px;
            padding: 20px;
            background-color: #fff;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-family: 'Courier New', Courier, monospace;
            display: none; /* Hidden by default */
        }
    </style>
</head>
<body>
    <h1>خدماتنا</h1>

    <div class="services-grid">
        <div class="service-card" data-title="تطوير أنظمة ERP" data-description="نقوم ببناء أنظمة تخطيط موارد مؤسسات مخصصة لإدارة عمليات شركتك بكفاءة، من المحاسبة والمخزون إلى الموارد البشرية.">
            <h3>تطوير أنظمة ERP</h3>
            <p>حلول متكاملة لإدارة أعمالك.</p>
        </div>
        <div class="service-card" data-title="تطبيقات الويب السحابية" data-description="نطور تطبيقات ويب حديثة وقابلة للتطوير تعمل على السحابة، مما يتيح الوصول إليها من أي مكان وفي أي وقت.">
            <h3>تطبيقات الويب السحابية</h3>
            <p>تطبيقات قوية ومتاحة دائمًا.</p>
        </div>
        <div class="service-card" data-title="متاجر التجارة الإلكترونية" data-description="نصمم ونطور متاجر إلكترونية احترافية تساعدك على بيع منتجاتك عبر الإنترنت بسهولة وأمان.">
            <h3>متاجر التجارة الإلكترونية</h3>
            <p>زد مبيعاتك وتوسع في السوق.</p>
        </div>
        <div class="service-card" data-title="استشارات تقنية" data-description="نقدم استشارات لمساعدتك في اختيار أفضل التقنيات والاستراتيجيات لتحقيق أهداف عملك الرقمية.">
            <h3>استشارات تقنية</h3>
            <p>خبرتنا في خدمتك لاتخاذ القرارات الصحيحة.</p>
        </div>
    </div>

    <div id="service-details">
        <h2 id="details-title"></h2>
        <p id="details-description"></p>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const serviceCards = document.querySelectorAll('.service-card');
            const detailsContainer = document.getElementById('service-details');
            const detailsTitle = document.getElementById('details-title');
            const detailsDescription = document.getElementById('details-description');

            serviceCards.forEach(card => {
                card.addEventListener('click', () => {
                    const title = card.getAttribute('data-title');
                    const description = card.getAttribute('data-description');

                    detailsTitle.textContent = title;
                    detailsDescription.textContent = description;
                    detailsContainer.style.display = 'block';
                });
            });
        });
    </script>
</body>
</html>
