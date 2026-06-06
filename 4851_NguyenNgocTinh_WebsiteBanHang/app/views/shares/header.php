<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NTECH STORE - Quản Lý Bán Hàng</title>
    <meta name="description" content="NTECH STORE - Cửa hàng công nghệ hàng đầu. Sản phẩm chất lượng, giá tốt nhất.">
    <!-- Google Font Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        // Immediately load preferred theme before rendering to avoid theme flash
        (function() {
            const savedTheme = localStorage.getItem('theme') || 'dark';
            document.documentElement.setAttribute('data-theme', savedTheme);
        })();

        // Global Socket Manager for managing WebSocket connections across logins/logouts
        const SocketManager = {
            socket: null,
            
            // Connect to WebSocket server using the provided token or session ID
            connect(token) {
                // If there's an existing socket connection, disconnect it first
                this.disconnect();
                
                if (!token) {
                    token = localStorage.getItem('jwtToken');
                }
                
                // Connect to WebSocket server
                const wsUrl = `ws://localhost:8080/ws?token=${encodeURIComponent(token || '')}`;
                
                try {
                    console.log("[SocketManager] Initiating connection for token:", token ? "TokenPresent" : "NoToken");
                    this.socket = new WebSocket(wsUrl);
                    
                    this.socket.onopen = () => {
                        console.log("[SocketManager] WebSocket connection established successfully.");
                        sessionStorage.setItem('activeSocketSession', 'true');
                    };
                    
                    this.socket.onmessage = (event) => {
                        console.log("[SocketManager] Received message:", event.data);
                    };
                    
                    this.socket.onerror = (error) => {
                        console.error("[SocketManager] WebSocket error:", error);
                    };
                    
                    this.socket.onclose = (event) => {
                        console.log("[SocketManager] WebSocket connection closed:", event.reason);
                        sessionStorage.removeItem('activeSocketSession');
                    };
                    
                } catch (e) {
                    console.error("[SocketManager] Failed to create WebSocket connection:", e);
                }
            },
            
            // Disconnect and clean up the current socket connection
            disconnect() {
                if (this.socket) {
                    console.log("[SocketManager] Disconnecting active WebSocket connection.");
                    this.socket.onopen = null;
                    this.socket.onmessage = null;
                    this.socket.onerror = null;
                    this.socket.onclose = null;
                    
                    if (this.socket.readyState === WebSocket.OPEN || this.socket.readyState === WebSocket.CONNECTING) {
                        this.socket.close();
                    }
                    this.socket = null;
                }
                sessionStorage.removeItem('activeSocketSession');
                localStorage.removeItem('activeSocketId');
            },

            // Completely clear all socket session traces
            clearSocketSession() {
                console.log("[SocketManager] Clearing current socket session resources.");
                this.disconnect();
                localStorage.removeItem('activeSocketId');
            }
        };

        // Add to Cart AJAX Helper
        function addToCartAjax(event, productId) {
            if (event) event.preventDefault();
            
            const currentTheme = localStorage.getItem('theme') || 'dark';
            
            // Find product image to animate
            let imgToAnimate = null;
            if (event && event.target) {
                const btn = event.target.closest('button, a');
                if (btn) {
                    const card = btn.closest('.product-card, .product-grid-card, .glass-card');
                    if (card) {
                        imgToAnimate = card.querySelector('img.product-card-img, img.product-grid-img, img.detail-img');
                    }
                }
            }
            
            fetch('<?php echo BASE_URL; ?>/Product/addToCart/' + productId + '?ajax=1', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (imgToAnimate) {
                        animateFlyToCart(imgToAnimate);
                    }
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Đã thêm vào giỏ!',
                        text: data.message,
                        background: currentTheme === 'light' ? '#ffffff' : '#1d1d1f',
                        color: currentTheme === 'light' ? '#1d1d1f' : '#f5f5f7',
                        confirmButtonColor: '#0071e3',
                        timer: 2000,
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false
                    });
                    
                    updateCartBadge(data.cart_count);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi!',
                        text: data.message,
                        background: currentTheme === 'light' ? '#ffffff' : '#1d1d1f',
                        color: currentTheme === 'light' ? '#1d1d1f' : '#f5f5f7',
                        confirmButtonColor: '#ff453a',
                        timer: 3000
                    });
                }
            })
            .catch(error => {
                console.error('Error adding to cart:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi!',
                    text: 'Có lỗi xảy ra khi thêm vào giỏ hàng.',
                    background: currentTheme === 'light' ? '#ffffff' : '#1d1d1f',
                    color: currentTheme === 'light' ? '#1d1d1f' : '#f5f5f7',
                    confirmButtonColor: '#ff453a',
                    timer: 3000
                });
            });
        }
        
        function animateFlyToCart(img) {
            const cartIcon = document.querySelector('.fa-cart-shopping');
            if (!cartIcon || !img) return;

            // Get coordinates
            const imgRect = img.getBoundingClientRect();
            const cartRect = cartIcon.getBoundingClientRect();

            // Create a clone
            const clone = img.cloneNode();
            clone.style.position = 'fixed';
            clone.style.left = `${imgRect.left}px`;
            clone.style.top = `${imgRect.top}px`;
            clone.style.width = `${imgRect.width}px`;
            clone.style.height = `${imgRect.height}px`;
            clone.style.zIndex = '9999';
            clone.style.transition = 'all 0.8s cubic-bezier(0.25, 1, 0.5, 1)';
            clone.style.pointerEvents = 'none';
            clone.style.borderRadius = '50%';
            clone.style.opacity = '0.8';

            document.body.appendChild(clone);

            // Force reflow
            clone.offsetWidth;

            // Animate
            clone.style.left = `${cartRect.left}px`;
            clone.style.top = `${cartRect.top}px`;
            clone.style.width = '24px';
            clone.style.height = '24px';
            clone.style.opacity = '0';
            clone.style.transform = 'rotate(360deg)';

            // Clean up
            setTimeout(() => {
                clone.remove();
                // Add a small shake/bounce to cart icon
                cartIcon.classList.add('fa-bounce');
                setTimeout(() => {
                    cartIcon.classList.remove('fa-bounce');
                }, 1000);
            }, 800);
        }
        
        function updateCartBadge(count) {
            const cartLink = document.querySelector('a[href$="/Product/cart"]');
            if (!cartLink) return;
            
            let badge = cartLink.querySelector('.cart-badge');
            if (count > 0) {
                if (!badge) {
                    badge = document.createElement('span');
                    badge.className = 'cart-badge';
                    badge.style.top = '-2px';
                    badge.style.right = '-2px';
                    cartLink.appendChild(badge);
                }
                badge.textContent = count;
            } else {
                if (badge) {
                    badge.remove();
                }
            }
        }
    </script>
    
    <style>
        :root {
            /* Apple Design System Variables */
            --primary: #0066cc;
            --primary-focus: #0071e3;
            --primary-on-dark: #2997ff;
            --ink: #1d1d1f;
            --body: #1d1d1f;
            --body-on-dark: #ffffff;
            --body-muted: #cccccc;
            --ink-muted-80: #333333;
            --ink-muted-48: #7a7a7a;
            --divider-soft: rgba(255, 255, 255, 0.08);
            --hairline: rgba(255, 255, 255, 0.08);
            --canvas: #161617;
            --canvas-parchment: #000000;
            --surface-pearl: #1d1d1f;
            --surface-tile-1: #272729;
            --surface-tile-2: #2a2a2c;
            --surface-tile-3: #252527;
            --surface-black: #000000;
            --surface-chip-translucent: rgba(210, 210, 215, 0.64);
            
            /* Compatibility mappings */
            --bg-gradient: var(--canvas-parchment);
            --glass-bg: var(--surface-tile-1);
            --glass-border: var(--hairline);
            --glass-shadow: rgba(0, 0, 0, 0.22) 3px 5px 30px 0px;
            --accent-color: var(--primary);
            --accent-hover: var(--primary-focus);
            --text-main: #ffffff; /* White text for dark mode */
            --text-muted: var(--body-muted);
            --card-hover-bg: var(--surface-tile-2);
            
            --font-display: "SF Pro Display", -apple-system, BlinkMacSystemFont, "Inter", "Segoe UI", sans-serif;
            --font-text: "SF Pro Text", -apple-system, BlinkMacSystemFont, "Inter", "Segoe UI", sans-serif;
            --text-gradient: linear-gradient(180deg, #ffffff 0%, #a1a1a6 100%);
            
            /* Navbar variables (Always black global-nav) */
            --nav-bg: var(--surface-black);
            --nav-border: rgba(255, 255, 255, 0.08);
            --nav-link: rgba(255, 255, 255, 0.8);
            --nav-link-hover: #ffffff;
            --nav-brand: #ffffff;
            
            /* Footer variables */
            --footer-bg: var(--surface-tile-3);
            --footer-border: var(--hairline);
            --footer-text: var(--ink-muted-48);
        }

        /* Light Theme variables override */
        [data-theme="light"] {
            --primary: #0066cc;
            --primary-focus: #0071e3;
            --primary-on-dark: #2997ff;
            --ink: #1d1d1f;
            --body: #1d1d1f;
            --body-on-dark: #ffffff;
            --body-muted: #7a7a7a;
            --ink-muted-80: #333333;
            --ink-muted-48: #7a7a7a;
            --divider-soft: #f0f0f0;
            --hairline: #e0e0e0;
            --canvas: #ffffff;
            --canvas-parchment: #f5f5f7;
            --surface-pearl: #fafafc;
            --surface-tile-1: #272729;
            --surface-tile-2: #2a2a2c;
            --surface-tile-3: #252527;
            --surface-black: #000000;
            --surface-chip-translucent: rgba(210, 210, 215, 0.64);
            
            /* Compatibility mappings */
            --bg-gradient: var(--canvas-parchment);
            --glass-bg: var(--canvas);
            --glass-border: var(--hairline);
            --glass-shadow: rgba(0, 0, 0, 0.04) 0px 8px 30px;
            --accent-color: var(--primary);
            --accent-hover: var(--primary-focus);
            --text-main: #000000; /* Black text in light mode */
            --text-muted: var(--ink-muted-48);
            --card-hover-bg: var(--surface-pearl);
            --text-gradient: linear-gradient(180deg, #1d1d1f 0%, #6e6e73 100%);
            
            /* Navbar variables in Light mode (Always black global-nav) */
            --nav-bg: var(--surface-black);
            --nav-border: rgba(255, 255, 255, 0.08);
            --nav-link: rgba(255, 255, 255, 0.8);
            --nav-link-hover: #ffffff;
            --nav-brand: #ffffff;
            
            /* Footer variables in Light mode */
            --footer-bg: var(--canvas-parchment);
            --footer-border: var(--hairline);
            --footer-text: var(--ink-muted-48);
        }

        /* Smooth visual transitions */
        body, .glass-card, .navbar, .footer, tr, td, .form-control-glass, a, button, span, h1, h2, h3, h4, h5, h6, .input-group-text, .preview-container {
            transition: background-color 0.3s cubic-bezier(0.25, 0.8, 0.25, 1), 
                        color 0.3s cubic-bezier(0.25, 0.8, 0.25, 1), 
                        border-color 0.3s cubic-bezier(0.25, 0.8, 0.25, 1), 
                        box-shadow 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        body {
            font-family: var(--font-text);
            font-size: 17px;
            line-height: 1.47;
            background: var(--bg-gradient);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            letter-spacing: -0.374px;
            -webkit-font-smoothing: antialiased;
        }

        h1, h2, h3, h4, h5, h6, .text-display-lg {
            font-family: var(--font-display);
            font-weight: 600;
            letter-spacing: -0.01em; /* Apple tight negative tracking */
            color: var(--text-main);
        }

        /* Force text colors to follow theme in Light Mode, except inside elements that must stay light */
        [data-theme="light"] .text-white:not(.navbar):not(.navbar *):not(.btn):not(.btn *):not(.badge):not(.badge *):not(.btn-premium):not(.btn-premium-danger):not(.btn-premium-warning):not(.btn-premium-success):not(.alert):not(.alert *) {
            color: var(--text-main) !important;
        }

        .navbar {
            background: var(--nav-bg) !important;
            backdrop-filter: saturate(180%) blur(20px);
            -webkit-backdrop-filter: saturate(180%) blur(20px);
            border-bottom: 1px solid var(--nav-border);
            height: 48px;
            padding: 0 !important;
        }

        .navbar-brand {
            font-family: var(--font-display);
            font-weight: 600;
            font-size: 1.15rem;
            letter-spacing: -0.28px;
            color: var(--nav-brand) !important;
            display: flex;
            align-items: center;
        }

        .nav-link {
            font-size: 12px;
            font-weight: 400;
            color: var(--nav-link) !important;
            transition: color 0.15s ease;
            padding: 0.5rem 1rem !important;
        }

        .nav-link:hover, .nav-link.active {
            color: var(--nav-link-hover) !important;
        }

        .nav-link.active::after {
            display: none;
        }

        .main-container {
            flex: 1;
            padding-top: 3.5rem;
            padding-bottom: 5rem;
        }

        .glass-card {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: 18px; /* rounded.lg */
            box-shadow: var(--glass-shadow);
            padding: 2rem;
            transition: transform 0.3s cubic-bezier(0.25, 1, 0.5, 1), background-color 0.2s ease, box-shadow 0.3s ease;
        }

        .btn-premium {
            background: var(--accent-color);
            border: none;
            color: white !important;
            font-weight: 400;
            padding: 11px 22px; /* updated padding to match primary-button */
            border-radius: 980px; /* Signature Apple Pill */
            transition: opacity 0.15s ease, transform 0.1s ease;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
        }

        .btn-premium:hover {
            background: var(--accent-color) !important;
            opacity: 0.88;
            color: white !important;
        }

        .btn-premium:active {
            transform: scale(0.95);
        }

        .btn-secondary-pill {
            background: transparent;
            border: 1px solid var(--primary);
            color: var(--primary) !important;
            font-weight: 400;
            padding: 11px 22px;
            border-radius: 980px;
            transition: opacity 0.15s ease, transform 0.1s ease;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
        }

        .btn-secondary-pill:hover {
            background-color: rgba(0, 102, 204, 0.05) !important;
            color: var(--primary) !important;
        }

        .btn-secondary-pill:active {
            transform: scale(0.95);
        }

        .btn-glass-secondary {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.05);
            color: var(--text-main);
            font-weight: 400;
            padding: 8px 22px;
            border-radius: 980px;
            transition: background 0.15s ease, transform 0.1s ease;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
        }

        [data-theme="light"] .btn-glass-secondary {
            background: rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(0, 0, 0, 0.03);
        }

        .btn-glass-secondary:hover {
            background: rgba(255, 255, 255, 0.18) !important;
            color: var(--text-main) !important;
        }

        [data-theme="light"] .btn-glass-secondary:hover {
            background: rgba(0, 0, 0, 0.1) !important;
            color: var(--text-main) !important;
        }

        .btn-glass-secondary:active {
            transform: scale(0.95);
        }

        .btn-dark-utility {
            background: var(--ink);
            color: var(--body-on-dark) !important;
            font-size: 14px;
            font-weight: 400;
            padding: 8px 15px; /* padding 8px x 15px */
            border-radius: 8px; /* rounded.sm */
            transition: opacity 0.15s ease, transform 0.1s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            border: none;
        }

        [data-theme="light"] .btn-dark-utility {
            background: var(--ink);
            color: #ffffff !important;
        }

        .btn-dark-utility:hover {
            background: var(--ink) !important;
            color: var(--body-on-dark) !important;
            opacity: 0.9;
        }

        [data-theme="light"] .btn-dark-utility:hover {
            background: var(--ink) !important;
            color: #ffffff !important;
            opacity: 0.9;
        }

        .btn-dark-utility:active {
            transform: scale(0.95);
        }

        .btn-premium-danger {
            background: #ff453a; /* Apple system red */
            border: none;
            color: white;
            font-weight: 400;
            padding: 8px 22px;
            border-radius: 980px;
            transition: opacity 0.15s ease, transform 0.1s ease;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
        }

        .btn-premium-danger:hover {
            background: #ff453a !important;
            opacity: 0.88;
            color: white !important;
        }

        .btn-premium-danger:active {
            transform: scale(0.95);
        }

        .btn-premium-warning {
            background: #ff9f0a; /* Apple system orange */
            border: none;
            color: white;
            font-weight: 400;
            padding: 8px 22px;
            border-radius: 980px;
            transition: opacity 0.15s ease, transform 0.1s ease;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
        }

        .btn-premium-warning:hover {
            background: #ff9f0a !important;
            opacity: 0.88;
            color: white !important;
        }

        .btn-premium-warning:active {
            transform: scale(0.95);
        }

        .text-gradient {
            font-family: var(--font-display);
            background: var(--text-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: -0.28px;
        }

        .table-premium {
            --bs-table-bg: transparent !important;
            --bs-table-color: var(--text-main) !important;
            --bs-table-border-color: var(--glass-border) !important;
            --bs-table-striped-bg: rgba(255, 255, 255, 0.02) !important;
            --bs-table-hover-bg: rgba(255, 255, 255, 0.04) !important;
            color: var(--text-main) !important;
        }

        [data-theme="light"] .table-premium {
            --bs-table-striped-bg: rgba(0, 0, 0, 0.01) !important;
            --bs-table-hover-bg: rgba(0, 0, 0, 0.02) !important;
        }

        .table-premium th {
            font-weight: 500;
            color: var(--text-muted);
            border-bottom: 1px solid var(--glass-border) !important;
            padding: 1rem;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .table-premium td {
            vertical-align: middle;
            border-bottom: 1px solid var(--glass-border);
            padding: 1rem;
            color: var(--text-main);
            font-size: 15px;
            opacity: 0.9;
        }

        .table-premium tr:hover {
            background: rgba(255, 255, 255, 0.02);
        }

        [data-theme="light"] .table-premium tr:hover {
            background: rgba(0, 0, 0, 0.01);
        }

        .text-muted {
            color: var(--text-muted) !important;
        }

        .footer {
            background: var(--footer-bg);
            border-top: 1px solid var(--footer-border);
            padding: 2rem 0;
            font-size: 12px;
            color: var(--footer-text);
            margin-top: auto;
            letter-spacing: -0.12px;
        }
        
        .footer a {
            color: var(--link-blue-dark);
            text-decoration: none;
        }

        [data-theme="light"] .footer a {
            color: var(--link-blue);
        }

        .footer a:hover {
            text-decoration: underline;
        }

        .badge-premium {
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: var(--link-blue-dark);
            padding: 0.3em 0.8em;
            border-radius: 980px;
            font-weight: 500;
            font-size: 0.8rem;
            letter-spacing: -0.2px;
        }

        [data-theme="light"] .badge-premium {
            background: rgba(0, 0, 0, 0.03);
            border: 1px solid rgba(0, 0, 0, 0.06);
            color: var(--link-blue);
        }

        /* Cart counter badge */
        .cart-badge {
            position: absolute;
            top: -4px;
            right: -6px;
            padding: 2px 6px;
            border-radius: 50%;
            background: #ff453a;
            color: white;
            font-size: 0.65rem;
            font-weight: bold;
        }

        /* Light Mode Specific Form Control Overrides */
        [data-theme="light"] .form-control-glass {
            background: rgba(0, 0, 0, 0.03) !important;
            color: #1d1d1f !important;
        }

        [data-theme="light"] .form-control-glass:focus {
            background: rgba(0, 0, 0, 0.05) !important;
            color: #1d1d1f !important;
        }

        [data-theme="light"] .input-group-text {
            background: rgba(0, 0, 0, 0.02) !important;
            border-color: var(--glass-border) !important;
            color: rgba(29, 29, 31, 0.5) !important;
        }

        [data-theme="light"] .preview-placeholder {
            color: rgba(29, 29, 31, 0.5) !important;
        }

        [data-theme="light"] .preview-container {
            background: rgba(0, 0, 0, 0.01) !important;
        }
        
        [data-theme="light"] .no-image-placeholder {
            background: rgba(0, 0, 0, 0.03) !important;
            color: rgba(29, 29, 31, 0.4) !important;
        }
        
        [data-theme="light"] .product-card {
            background: #ffffff !important;
            box-shadow: rgba(0, 0, 0, 0.03) 0px 4px 20px !important;
        }
        
        [data-theme="light"] .product-card:hover {
            box-shadow: rgba(0, 0, 0, 0.08) 0px 8px 30px !important;
            background: #ffffff !important;
        }

        [data-theme="light"] .cart-total-box {
            background: rgba(0, 0, 0, 0.02) !important;
        }

        [data-theme="light"] .order-summary-card {
            background: rgba(0, 0, 0, 0.02) !important;
        }

        /* User badge in nav */
        .user-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 980px;
            padding: 4px 12px 4px 8px;
            font-size: 12px;
            color: var(--text-main);
        }

        .user-badge .user-avatar {
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: var(--accent-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            font-weight: 600;
            color: white;
        }
    </style>
</head>
<body>

    <!-- Dynamic base path detection -->
    <?php
    $current_uri = $_SERVER['REQUEST_URI'] ?? '';
    $request_path = parse_url($current_uri, PHP_URL_PATH);
    $request_path = is_string($request_path) ? $request_path : '';
    $relative_path = str_replace(BASE_URL, '', $request_path);
    $relative_path = is_string($relative_path) ? trim($relative_path, '/') : '';
    
    function is_active($route) {
        global $relative_path;
        $rel = is_string($relative_path) ? $relative_path : '';
        $r = is_string($route) ? trim($route, '/') : '';
        if ($r === $rel) {
            return 'active';
        }
        if (!empty($r) && $r !== '/' && strpos($rel, $r) === 0) {
            return 'active';
        }
        return '';
    }
    $is_home = (empty($relative_path) || $relative_path === 'index.php');
    
    // Count items in cart
    $cart_count = 0;
    if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
            $cart_count += $item['quantity'];
        }
    }

    // Check login state from session
    $isLoggedIn = SessionHelper::isLoggedIn();
    $currentUsername = $_SESSION['username'] ?? '';
    $currentFullname = $_SESSION['user_fullname'] ?? $currentUsername;
    $currentRole = $_SESSION['user_role'] ?? 'user';
    ?>

    <script>
        // Clear JWT token and socket session if user is not logged in in PHP Session
        if (!<?php echo $isLoggedIn ? 'true' : 'false'; ?>) {
            localStorage.removeItem('jwtToken');
            if (typeof SocketManager !== 'undefined') {
                SocketManager.clearSocketSession();
            }
        } else {
            // Automatically initialize/restore socket connection when logged in
            document.addEventListener('DOMContentLoaded', () => {
                if (typeof SocketManager !== 'undefined') {
                    SocketManager.connect();
                }
            });
        }
    </script>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
        <div class="container">
            <a class="navbar-brand" href="<?php echo BASE_URL; ?>/">
                <i class="fa-solid fa-laptop-code me-2 text-primary"></i>NTECH STORE
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link <?php echo $is_home ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>/">
                            <i class="fa-solid fa-house me-1"></i>Trang chủ
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo (is_active('/Product') && !is_active('/Product/add') && !is_active('/Product/cart') && !is_active('/Product/checkout')) ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>/Product">
                            <i class="fa-solid fa-boxes-stacked me-1"></i>Sản phẩm
                        </a>
                    </li>
                    <?php if ($isLoggedIn && $currentRole === 'admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo is_active('/Product/add') ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>/Product/add">
                                <i class="fa-solid fa-plus me-1"></i>Thêm sản phẩm
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo is_active('/Category') ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>/Category/list">
                                <i class="fa-solid fa-tags me-1"></i>Danh mục
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo is_active('/Order') && !is_active('/Order/revenue') ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>/Order">
                                <i class="fa-solid fa-receipt me-1"></i>Quản lý đơn hàng
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo is_active('/Order/revenue') ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>/Order/revenue">
                                <i class="fa-solid fa-chart-line me-1"></i>Doanh thu
                            </a>
                        </li>
                    <?php elseif ($isLoggedIn): ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo is_active('/Order') ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>/Order">
                                <i class="fa-solid fa-receipt me-1"></i>Đơn hàng của tôi
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
                <div class="d-flex align-items-center gap-2">
                    <?php if ($isLoggedIn): ?>
                        <div class="user-badge">
                            <div class="user-avatar">
                                <?php echo strtoupper(substr($currentUsername, 0, 1)); ?>
                            </div>
                            <span><?php echo htmlspecialchars($currentFullname, ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php if ($currentRole === 'admin'): ?>
                                <span class="badge bg-warning text-dark" style="font-size: 9px; padding: 2px 6px; border-radius: 4px;">Admin</span>
                            <?php endif; ?>
                        </div>
                        <a href="<?php echo BASE_URL; ?>/account/logout" onclick="if (typeof SocketManager !== 'undefined') SocketManager.clearSocketSession();" class="btn btn-outline-light border-0 nav-link p-2" title="Đăng xuất">
                            <i class="fa-solid fa-right-from-bracket"></i>
                        </a>
                    <?php else: ?>
                        <a href="<?php echo BASE_URL; ?>/account/login" class="btn btn-premium btn-sm" style="font-size: 12px;">
                            <i class="fa-solid fa-user me-1"></i>Đăng nhập
                        </a>
                    <?php endif; ?>

                    <button id="theme-toggle" class="btn btn-outline-light border-0 text-muted nav-link p-0" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;" aria-label="Toggle theme" title="Chuyển chế độ sáng/tối">
                        <i class="fa-solid fa-moon fs-5" id="theme-icon"></i>
                    </button>
                    <a href="<?php echo BASE_URL; ?>/Product/cart" class="btn btn-outline-light border-0 position-relative text-muted nav-link <?php echo is_active('/Product/cart') ? 'active' : ''; ?> p-0" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                        <i class="fa-solid fa-cart-shopping fs-5"></i>
                        <?php if ($cart_count > 0): ?>
                            <span class="cart-badge" style="top: -2px; right: -2px;"><?php echo $cart_count; ?></span>
                        <?php endif; ?>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Container -->
    <div class="container main-container">
        
        <!-- SweetAlert2 flash notifications -->
        <?php if (isset($_SESSION['success_msg'])): ?>
            <script>
                (function() {
                    const currentTheme = localStorage.getItem('theme') || 'dark';
                    Swal.fire({
                        icon: 'success',
                        title: 'Thành công!',
                        text: '<?php echo htmlspecialchars($_SESSION['success_msg'], ENT_QUOTES, 'UTF-8'); ?>',
                        background: currentTheme === 'light' ? '#ffffff' : '#1d1d1f',
                        color: currentTheme === 'light' ? '#1d1d1f' : '#f5f5f7',
                        confirmButtonColor: '#0071e3',
                        timer: 3000
                    });
                })();
            </script>
            <?php unset($_SESSION['success_msg']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_msg'])): ?>
            <script>
                (function() {
                    const currentTheme = localStorage.getItem('theme') || 'dark';
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi!',
                        text: '<?php echo htmlspecialchars($_SESSION['error_msg'], ENT_QUOTES, 'UTF-8'); ?>',
                        background: currentTheme === 'light' ? '#ffffff' : '#1d1d1f',
                        color: currentTheme === 'light' ? '#1d1d1f' : '#f5f5f7',
                        confirmButtonColor: '#ff453a',
                        timer: 4000
                    });
                })();
            </script>
            <?php unset($_SESSION['error_msg']); ?>
        <?php endif; ?>
