<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }} Admin</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            :root {
                --sidebar-width: 260px;
                --sidebar-collapsed-width: 86px;
                --header-height: 72px;
                --page-bg: #eef7fa;
                --border: #e5e7eb;
                --text: #111827;
                --muted: #6b7280;
                --accent: #2563eb;
            }

            body {
                background: var(--page-bg);
                color: var(--text);
            }

            .admin-shell {
                min-height: 100vh;
                background: var(--page-bg);
            }

            .sidebar {
                width: var(--sidebar-width);
                height: 100vh;
                position: fixed;
                inset: 0 auto 0 0;
                z-index: 60;
                background: #ffffff;
                border-right: 1px solid var(--border);
                overflow-y: auto;
                transition: width .2s ease, transform .2s ease, box-shadow .2s ease;
            }

            .sidebar-brand {
                display: flex;
                align-items: center;
                gap: 12px;
                padding: 28px 24px 24px;
                min-height: var(--header-height);
            }

            .sidebar-brand-icon {
                width: 42px;
                height: 42px;
                border-radius: 12px;
                border: 2px solid #111827;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
            }

            .sidebar-brand-icon svg {
                width: 23px;
                height: 23px;
            }

            .sidebar-brand-text {
                font-size: 17px;
                font-weight: 800;
                color: #030712;
                letter-spacing: -0.02em;
                white-space: nowrap;
            }

            .content-area {
                margin-left: var(--sidebar-width);
                min-height: 100vh;
                background: var(--page-bg);
                transition: margin-left .2s ease;
            }

            .admin-header {
                min-height: var(--header-height);
                background: rgba(255, 255, 255, .96);
                backdrop-filter: blur(10px);
                border-bottom: 1px solid var(--border);
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 18px;
                padding: 0 32px;
                position: sticky;
                top: 0;
                z-index: 40;
            }

            .admin-header-left {
                display: flex;
                align-items: center;
                gap: 14px;
                min-width: 0;
            }

            .sidebar-toggle {
                width: 44px;
                height: 44px;
                border: 1px solid var(--border);
                border-radius: 12px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                color: #111827;
                background: #ffffff;
                transition: background .15s ease, color .15s ease, border-color .15s ease;
                flex-shrink: 0;
            }

            .sidebar-toggle:hover {
                background: #eff6ff;
                color: #1d4ed8;
                border-color: #bfdbfe;
            }

            .sidebar-toggle svg {
                width: 22px;
                height: 22px;
            }

            .page-title {
                font-size: 22px;
                font-weight: 700;
                color: #111827;
                letter-spacing: -0.02em;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .sidebar-overlay {
                position: fixed;
                inset: 0;
                z-index: 55;
                background: rgba(15, 23, 42, .45);
                opacity: 0;
                pointer-events: none;
                transition: opacity .2s ease;
            }

            body.sidebar-collapsed .sidebar {
                width: var(--sidebar-collapsed-width);
            }

            body.sidebar-collapsed .content-area {
                margin-left: var(--sidebar-collapsed-width);
            }

            body.sidebar-collapsed .sidebar-brand {
                justify-content: center;
                padding-left: 14px;
                padding-right: 14px;
            }

            body.sidebar-collapsed .sidebar-brand-text,
            body.sidebar-collapsed .sidebar-link span,
            body.sidebar-collapsed .category-title span:first-child,
            body.sidebar-collapsed .sidebar-dropdown summary span:last-child {
                display: none;
            }

            body.sidebar-collapsed .sidebar-dropdown summary.category-title {
                display: none;
            }

            .sidebar-link {
                display: flex;
                align-items: center;
                gap: 12px;
                margin: 3px 14px;
                padding: 12px 14px;
                color: #111827;
                border-radius: 12px;
                font-size: 15px;
                font-weight: 600;
                line-height: 1.25;
                position: relative;
                transition: color .18s ease, background .18s ease, box-shadow .18s ease, transform .18s ease;
            }

            .sidebar-link:hover,
            .sidebar-link.active {
                color: #1d4ed8;
                background: linear-gradient(135deg, #eff6ff 0%, #f8fbff 100%);
            }

            .sidebar-link:hover {
                transform: translateX(2px);
            }

            .sidebar-link.active {
                box-shadow: inset 3px 0 0 #2563eb, 0 8px 20px rgba(37, 99, 235, .08);
            }

            .sidebar-link svg {
                width: 20px;
                height: 20px;
                flex-shrink: 0;
            }

            .category-title {
                padding: 18px 24px 9px;
                color: #6b7280;
                font-size: 13px;
                font-weight: 800;
                letter-spacing: .02em;
            }

            .sidebar-dropdown summary.category-title {
                margin: 6px 10px 2px;
                padding: 12px 14px;
                border-radius: 12px;
                color: #4b5563;
                transition: background .18s ease, color .18s ease;
            }

            .sidebar-dropdown summary.category-title:hover {
                background: #f8fafc;
                color: #111827;
            }

            .sidebar-dropdown > .sidebar-dropdown-content {
                display: grid;
                grid-template-rows: 0fr;
                opacity: 0;
                transform: translateY(-4px);
                transition: grid-template-rows .26s ease, opacity .2s ease, transform .24s ease;
            }

            .sidebar-dropdown[open] > .sidebar-dropdown-content,
            body.sidebar-collapsed .sidebar-dropdown > .sidebar-dropdown-content {
                grid-template-rows: 1fr;
                opacity: 1;
                transform: translateY(0);
            }

            .sidebar-dropdown-inner {
                min-height: 0;
                overflow: hidden;
                padding: 2px 0 8px;
            }

            body.sidebar-collapsed .sidebar-dropdown-inner {
                padding: 2px 0;
                overflow: visible;
            }

            body.sidebar-collapsed .sidebar-dropdown {
                margin-bottom: 2px;
            }

            body.sidebar-collapsed .category-title {
                padding-left: 0;
                padding-right: 0;
                text-align: center;
            }

            body.sidebar-collapsed .sidebar-link {
                justify-content: center;
                margin-left: 12px;
                margin-right: 12px;
                padding-left: 0;
                padding-right: 0;
            }

            body.sidebar-collapsed .sidebar-link:hover {
                transform: translateY(-1px);
            }

            .sidebar-dropdown summary::-webkit-details-marker {
                display: none;
            }

            .sidebar-dropdown:not([open]) summary span:last-child {
                transform: rotate(-90deg);
            }

            .sidebar-dropdown summary span:last-child {
                transition: transform .15s ease;
            }

            .dashboard-content {
                position: relative;
                height: calc(100vh - var(--header-height));
                overflow: hidden;
                background: #ffffff;
            }

            .map-panel {
                position: absolute;
                inset: 0;
                background: #ffffff;
                overflow: hidden;
            }

            #map {
                height: 100%;
                width: 100%;
                z-index: 1;
            }

            .map-filter {
                position: absolute;
                top: 12px;
                left: 12px;
                z-index: 500;
                width: 26px;
                height: 26px;
                display: flex;
                align-items: center;
                justify-content: center;
                background: #ffffff;
                border: 1px solid #d1d5db;
                border-radius: 2px;
                box-shadow: 0 1px 2px rgba(0,0,0,.12);
                color: #6b7280;
            }



            .stats-grid {
                position: absolute;
                left: 18px;
                right: 18px;
                bottom: 18px;
                z-index: 600;
                display: grid;
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 8px;
            }

            .card {
                background: #ffffff;
                border: 1px solid var(--border);
                border-radius: 2px;
                padding: 10px 12px 8px;
                min-height: 180px;
                box-shadow: 0 8px 24px rgba(15, 23, 42, .12);
            }

            .card-title {
                color: #111827;
                font-size: 13px;
                font-weight: 700;
                margin: 0 0 6px;
            }

            .chart-box {
                height: 142px;
            }


            .avatar-dot {
                width: 32px;
                height: 32px;
                border-radius: 9999px;
                background: linear-gradient(135deg, #312e81, #c4b5fd);
                border: 2px solid #ffffff;
                box-shadow: 0 1px 4px rgba(0,0,0,.18);
                overflow: hidden;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                color: #ffffff;
                font-size: 13px;
                font-weight: 700;
            }

            .avatar-dot.has-photo {
                background: #ffffff;
            }

            .avatar-dot img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }

            .master-page {
                min-height: calc(100vh - 56px);
                background: #ffffff;
                padding: 24px 32px;
            }

            .master-toolbar {
                display: flex;
                justify-content: space-between;
                align-items: center;
                gap: 16px;
                margin-bottom: 16px;
            }

            .master-search {
                width: 220px;
                border: 1px solid #d1d5db;
                border-radius: 6px;
                padding: 9px 12px;
                font-size: 14px;
            }

            .btn-primary {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                background: #4caf50;
                color: #ffffff;
                border-radius: 6px;
                padding: 9px 16px;
                font-size: 14px;
                font-weight: 600;
            }

            .btn-secondary {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                background: #e5e7eb;
                color: #111827;
                border-radius: 6px;
                padding: 9px 16px;
                font-size: 14px;
                font-weight: 600;
            }

            .btn-edit, .btn-delete {
                width: 30px;
                height: 30px;
                border-radius: 6px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                color: #ffffff;
                font-size: 13px;
                font-weight: 700;
            }

            .btn-edit { background: #f59e0b; }
            .btn-delete { background: #ef4444; }

            .master-table {
                width: 100%;
                border-collapse: collapse;
                font-size: 14px;
            }

            .master-table th {
                text-align: left;
                color: #6b7280;
                font-weight: 600;
                border-bottom: 1px solid #e5e7eb;
                padding: 12px 14px;
            }

            .master-table td {
                border-bottom: 1px solid #e5e7eb;
                padding: 12px 14px;
                color: #111827;
            }

            .master-table tbody tr:nth-child(even) {
                background: #f3f4f6;
            }

            .tab-link {
                display: inline-flex;
                border-radius: 6px;
                padding: 9px 14px;
                font-size: 14px;
                font-weight: 600;
                color: #374151;
            }

            .tab-link.active {
                background: #293b91;
                color: #ffffff;
            }

            .form-card {
                max-width: 720px;
                background: #ffffff;
                border: 1px solid #e5e7eb;
                border-radius: 10px;
                padding: 24px;
            }

            .form-label {
                display: block;
                color: #374151;
                font-size: 14px;
                font-weight: 600;
                margin-bottom: 7px;
            }

            .form-input, .form-select {
                width: 100%;
                border: 1px solid #d1d5db;
                border-radius: 7px;
                padding: 10px 12px;
                font-size: 14px;
            }

            .cctv-page {
                min-height: calc(100vh - 56px);
                background: #ffffff;
                padding: 24px 32px 32px;
            }

            .cctv-topbar {
                display: flex;
                align-items: flex-start;
                justify-content: space-between;
                gap: 18px;
                margin-bottom: 16px;
            }

            .cctv-search {
                display: flex;
                height: 36px;
                overflow: hidden;
                border: 1px solid #d1d5db;
                border-radius: 5px;
                background: #ffffff;
            }

            .cctv-search input {
                width: 210px;
                border: 0;
                padding: 0 12px;
                color: #111827;
                font-size: 13px;
                outline: none;
            }

            .cctv-search button,
            .cctv-action-btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 6px;
                height: 36px;
                border-radius: 5px;
                font-size: 12px;
                font-weight: 700;
            }

            .cctv-search button {
                width: 38px;
                background: #2454c6;
                color: #ffffff;
            }

            .cctv-action-btn {
                border: 1px solid #d1d5db;
                background: #ffffff;
                color: #2454c6;
                padding: 0 12px;
            }

            .cctv-action-btn.secondary {
                color: #374151;
            }

            .cctv-grid {
                display: grid;
                grid-template-columns: repeat(4, minmax(0, 1fr));
                gap: 14px;
            }

            .cctv-card {
                overflow: hidden;
                border-radius: 7px;
                background: #111827;
                box-shadow: 0 8px 18px rgba(15, 23, 42, .14);
            }

            .cctv-stream {
                position: relative;
                display: block;
                aspect-ratio: 16 / 10;
                overflow: hidden;
                background: linear-gradient(135deg, #111827, #1f2937);
            }

            .cctv-stream iframe {
                width: 100%;
                height: 100%;
                border: 0;
                display: block;
            }

            .cctv-empty-preview {
                height: 100%;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                gap: 8px;
                padding: 18px;
                color: #d1d5db;
                text-align: center;
                font-size: 12px;
                font-weight: 600;
            }

            .cctv-stream::after {
                content: "";
                position: absolute;
                inset: 0;
                pointer-events: none;
                background: linear-gradient(180deg, rgba(0,0,0,.24), transparent 34%, rgba(0,0,0,.42));
            }

            .cctv-live-badge,
            .cctv-time {
                position: absolute;
                z-index: 2;
                top: 8px;
                color: #ffffff;
                text-shadow: 0 1px 2px rgba(0,0,0,.45);
            }

            .cctv-live-badge {
                left: 8px;
                display: inline-flex;
                align-items: center;
                gap: 4px;
                border-radius: 3px;
                background: #ef233c;
                padding: 3px 6px;
                font-size: 9px;
                font-weight: 800;
                line-height: 1;
            }

            .cctv-live-badge span {
                width: 5px;
                height: 5px;
                border-radius: 999px;
                background: #ffffff;
            }

            .cctv-time {
                right: 8px;
                font-size: 9px;
                font-weight: 700;
            }

            .cctv-card-footer {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 10px;
                min-height: 46px;
                padding: 8px 10px;
                color: #ffffff;
            }

            .cctv-title-link {
                display: block;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
                font-size: 12px;
                font-weight: 800;
            }

            .cctv-title-link:hover {
                text-decoration: underline;
            }

            .cctv-card-footer p {
                margin-top: 2px;
                color: #86efac;
                font-size: 10px;
                font-weight: 700;
            }

            .cctv-card-actions {
                display: flex;
                align-items: center;
                gap: 5px;
                flex-shrink: 0;
            }

            .cctv-card-actions a,
            .cctv-card-actions button {
                width: 24px;
                height: 24px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                border-radius: 999px;
                background: rgba(255,255,255,.12);
                color: #ffffff;
                font-size: 12px;
                font-weight: 800;
            }

            .cctv-card-actions a:hover,
            .cctv-card-actions button:hover {
                background: rgba(255,255,255,.24);
            }

            .cctv-empty-state {
                grid-column: 1 / -1;
                border: 1px dashed #cbd5e1;
                border-radius: 10px;
                background: #f8fafc;
                padding: 38px;
                text-align: center;
            }

            .cctv-empty-state h2 {
                color: #111827;
                font-size: 18px;
                font-weight: 800;
            }

            .cctv-empty-state p {
                margin: 8px auto 18px;
                max-width: 520px;
                color: #6b7280;
                font-size: 14px;
            }

            .toast-notification {
                position: fixed;
                top: 18px;
                right: 24px;
                z-index: 1200;
                display: grid;
                grid-template-columns: auto 1fr auto;
                align-items: flex-start;
                gap: 12px;
                width: min(380px, calc(100vw - 32px));
                border-radius: 12px;
                background: #ffffff;
                padding: 14px 14px 14px 16px;
                box-shadow: 0 18px 42px rgba(15, 23, 42, .2);
                animation: toast-slide-in .22s ease-out;
                overflow: hidden;
            }

            .toast-notification::after {
                content: "";
                position: absolute;
                left: 0;
                right: 0;
                bottom: 0;
                height: 3px;
                animation: toast-progress 4.5s linear forwards;
            }

            .toast-notification.success {
                border-left: 4px solid #22c55e;
            }

            .toast-notification.success::after {
                background: #22c55e;
            }

            .toast-notification.error {
                border-left: 4px solid #ef4444;
            }

            .toast-notification.error::after {
                background: #ef4444;
            }


            .toast-icon {
                width: 28px;
                height: 28px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                border-radius: 999px;
                color: #ffffff;
                font-size: 15px;
                font-weight: 900;
            }

            .toast-notification.success .toast-icon {
                background: #22c55e;
            }

            .toast-notification.error .toast-icon {
                background: #ef4444;
            }

            .toast-title {
                color: #111827;
                font-size: 14px;
                font-weight: 800;
                line-height: 1.2;
            }

            .toast-message {
                margin-top: 3px;
                color: #6b7280;
                font-size: 13px;
                line-height: 1.4;
            }

            .toast-notification button {
                color: #9ca3af;
                font-size: 22px;
                line-height: 1;
            }

            .toast-notification button:hover {
                color: #111827;
            }

            @keyframes toast-slide-in {
                from {
                    opacity: 0;
                    transform: translateX(18px);
                }
                to {
                    opacity: 1;
                    transform: translateX(0);
                }
            }

            @keyframes toast-progress {
                from { width: 100%; }
                to { width: 0%; }
            }

            .modal-backdrop {
                position: fixed;
                inset: 0;
                z-index: 1000;
                display: flex;
                align-items: center;
                justify-content: center;
                background: rgba(15, 23, 42, .48);
                padding: 24px;
            }

            .modal-backdrop[hidden] {
                display: none;
            }

            .modal-card {
                width: min(760px, 100%);
                max-height: calc(100vh - 48px);
                overflow-y: auto;
                border-radius: 12px;
                background: #ffffff;
                box-shadow: 0 24px 60px rgba(15, 23, 42, .28);
            }

            .modal-card-sm {
                width: min(440px, 100%);
            }

            .modal-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                border-bottom: 1px solid #e5e7eb;
                padding: 18px 22px;
            }

            .modal-header h2 {
                color: #111827;
                font-size: 18px;
                font-weight: 800;
            }

            .modal-header button {
                width: 30px;
                height: 30px;
                border-radius: 999px;
                color: #6b7280;
                font-size: 24px;
                line-height: 1;
            }

            .modal-header button:hover {
                background: #f3f4f6;
                color: #111827;
            }

            .modal-body {
                padding: 22px;
            }

            .modal-footer {
                display: flex;
                justify-content: flex-end;
                gap: 10px;
                padding-top: 10px;
            }

            .btn-delete-text {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                border-radius: 6px;
                background: #ef4444;
                color: #ffffff;
                padding: 9px 16px;
                font-size: 14px;
                font-weight: 700;
            }

            .status-badge,
            .user-role-badge {
                display: inline-flex;
                align-items: center;
                border-radius: 999px;
                padding: 4px 9px;
                font-size: 12px;
                font-weight: 700;
            }

            .status-badge.active {
                background: #dcfce7;
                color: #166534;
            }

            .status-badge.inactive {
                background: #fee2e2;
                color: #991b1b;
            }

            .user-role-badge {
                background: #e0e7ff;
                color: #3730a3;
            }

            .infographic-page {
                min-height: calc(100vh - var(--header-height));
                background: #ffffff;
                padding: 22px 32px 40px;
            }

            .infographic-section {
                margin-bottom: 28px;
            }

            .infographic-filter {
                display: flex;
                align-items: center;
                gap: 10px;
                margin-bottom: 14px;
                color: #374151;
                font-size: 13px;
                font-weight: 600;
            }

            .infographic-filter label {
                margin-right: 4px;
                white-space: nowrap;
            }

            .infographic-filter input {
                height: 34px;
                border: 1px solid #d1d5db;
                border-radius: 5px;
                background: #ffffff;
                padding: 0 10px;
                color: #111827;
                font-size: 12px;
                outline: none;
            }

            .infographic-filter button {
                width: 34px;
                height: 34px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                border-radius: 6px;
                background: #111827;
                color: #ffffff;
            }

            .infographic-filter button svg {
                width: 15px;
                height: 15px;
            }

            .infographic-title {
                margin-bottom: 8px;
                color: #111827;
                font-size: 13px;
                font-weight: 800;
            }

            .infographic-chart {
                position: relative;
                width: 100%;
            }

            .infographic-line-chart {
                height: 305px;
            }

            .infographic-kecamatan-chart {
                height: 390px;
            }

            .infographic-category-chart {
                height: 260px;
            }

            .report-page {
                min-height: calc(100vh - var(--header-height));
                background: #ffffff;
                padding: 24px 32px 36px;
            }

            .report-hero,
            .emergency-hero {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 20px;
                border-radius: 18px;
                padding: 26px 28px;
                margin-bottom: 18px;
                overflow: hidden;
            }

            .report-hero {
                border: 1px solid #e5e7eb;
                background: linear-gradient(135deg, #f9fafb 0%, #ffffff 58%, #f3f4f6 100%);
            }

            .emergency-hero {
                border: 1px solid #e5e7eb;
                background: linear-gradient(135deg, #f9fafb 0%, #ffffff 58%, #f3f4f6 100%);
            }

            .report-eyebrow {
                margin-bottom: 6px;
                color: #111827;
                font-size: 12px;
                font-weight: 900;
                letter-spacing: .12em;
                text-transform: uppercase;
            }

            .emergency-hero .report-eyebrow {
                color: #111827;
            }

            .report-hero h1,
            .emergency-hero h1 {
                color: #111827;
                font-size: 28px;
                font-weight: 900;
                letter-spacing: -0.04em;
            }

            .report-hero p:not(.report-eyebrow),
            .emergency-hero p:not(.report-eyebrow) {
                margin-top: 6px;
                max-width: 680px;
                color: #64748b;
                font-size: 14px;
                line-height: 1.6;
            }

            .report-hero-icon,
            .emergency-pulse {
                width: 74px;
                height: 74px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
                border-radius: 22px;
            }

            .report-hero-icon {
                background: #f3f4f6;
                color: #111827;
            }

            .emergency-pulse {
                position: relative;
                background: #f3f4f6;
                color: #111827;
            }

            .emergency-pulse::after {
                content: "";
                position: absolute;
                inset: -8px;
                border-radius: 28px;
                border: 2px solid rgba(17, 24, 39, .16);
                animation: emergency-pulse 1.4s ease-out infinite;
            }

            .report-hero-icon svg,
            .emergency-pulse svg {
                width: 36px;
                height: 36px;
            }

            @keyframes emergency-pulse {
                from { opacity: .8; transform: scale(.92); }
                to { opacity: 0; transform: scale(1.12); }
            }

            .report-summary-grid {
                display: grid;
                grid-template-columns: repeat(4, minmax(0, 1fr));
                gap: 14px;
                margin-bottom: 18px;
            }

            .report-stat-card {
                position: relative;
                overflow: hidden;
                border: 1px solid #e5e7eb;
                border-radius: 16px;
                background: #ffffff;
                padding: 18px;
                box-shadow: 0 12px 30px rgba(15, 23, 42, .06);
            }

            .report-stat-card::before {
                content: "";
                position: absolute;
                inset: 0 auto 0 0;
                width: 5px;
                background: #111827;
            }

            .report-stat-card span {
                color: #64748b;
                font-size: 13px;
                font-weight: 800;
            }

            .report-stat-card strong {
                display: block;
                margin-top: 8px;
                color: #111827;
                font-size: 30px;
                font-weight: 900;
                letter-spacing: -0.04em;
            }

            .report-stat-card small {
                color: #94a3b8;
                font-size: 12px;
                font-weight: 700;
            }

            .report-stat-card.accent-red::before,
            .report-stat-card.accent-yellow::before,
            .report-stat-card.accent-green::before,
            .report-stat-card.accent-blue::before,
            .report-stat-card.accent-indigo::before { background: #111827; }

            .report-page .btn-primary,
            .infographic-page .btn-primary {
                background: #111827;
                color: #ffffff;
            }

            .report-page .btn-edit,
            .infographic-page .btn-edit {
                background: #111827;
            }

            .report-filter-card {
                display: grid;
                grid-template-columns: 1.5fr repeat(5, minmax(150px, 1fr));
                gap: 12px;
                align-items: end;
                border: 1px solid #e5e7eb;
                border-radius: 16px;
                background: #f8fafc;
                padding: 16px;
                margin-bottom: 18px;
            }

            .report-filter-actions {
                display: flex;
                gap: 8px;
            }

            .report-table-card {
                border: 1px solid #e5e7eb;
                border-radius: 16px;
                background: #ffffff;
                padding: 18px;
                box-shadow: 0 12px 30px rgba(15, 23, 42, .05);
            }

            .report-section-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                margin-bottom: 12px;
            }

            .report-section-header h2 {
                color: #111827;
                font-size: 18px;
                font-weight: 900;
            }

            .report-section-header p {
                margin-top: 3px;
                color: #64748b;
                font-size: 13px;
                font-weight: 600;
            }

            .emergency-map-panel {
                display: grid;
                grid-template-columns: 1fr 320px;
                gap: 16px;
                margin-bottom: 18px;
            }

            .emergency-map-placeholder {
                position: relative;
                min-height: 280px;
                overflow: hidden;
                border: 1px solid #e5e7eb;
                border-radius: 18px;
                background:
                    linear-gradient(90deg, rgba(148, 163, 184, .16) 1px, transparent 1px),
                    linear-gradient(rgba(148, 163, 184, .16) 1px, transparent 1px),
                    radial-gradient(circle at 28% 30%, rgba(17, 24, 39, .07), transparent 18%),
                    radial-gradient(circle at 72% 68%, rgba(17, 24, 39, .06), transparent 20%),
                    #f8fafc;
                background-size: 36px 36px, 36px 36px, auto, auto, auto;
            }

            .sos-map-marker {
                position: absolute;
                width: 34px;
                height: 34px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                border: 3px solid #ffffff;
                border-radius: 999px;
                background: #111827;
                color: #ffffff;
                font-size: 18px;
                font-weight: 1000;
                box-shadow: 0 8px 24px rgba(17, 24, 39, .28);
                transform: translate(-50%, -50%);
            }

            .sos-marker-position-1 { left: 12%; top: 22%; }
            .sos-marker-position-2 { left: 28%; top: 64%; }
            .sos-marker-position-3 { left: 43%; top: 36%; }
            .sos-marker-position-4 { left: 58%; top: 72%; }
            .sos-marker-position-5 { left: 74%; top: 28%; }
            .sos-marker-position-6 { left: 86%; top: 58%; }
            .sos-marker-position-7 { left: 22%; top: 46%; }
            .sos-marker-position-8 { left: 66%; top: 44%; }

            .sos-map-marker.is-blinking {
                animation: sos-marker-blink .8s ease-in-out infinite;
            }

            .sos-map-marker.is-blinking::after {
                content: "";
                position: absolute;
                inset: -12px;
                border-radius: 999px;
                border: 2px solid rgba(17, 24, 39, .28);
                animation: emergency-pulse 1.2s ease-out infinite;
            }

            @keyframes sos-marker-blink {
                0%, 100% { opacity: 1; transform: translate(-50%, -50%) scale(1); }
                50% { opacity: .45; transform: translate(-50%, -50%) scale(1.12); }
            }

            .emergency-map-info {
                border: 1px solid #e5e7eb;
                border-radius: 18px;
                background: #f9fafb;
                padding: 18px;
            }

            .emergency-map-info h2 {
                color: #111827;
                font-size: 16px;
                font-weight: 900;
            }

            .emergency-map-info p {
                margin: 8px 0 14px;
                color: #64748b;
                font-size: 13px;
                line-height: 1.6;
            }

            .sos-row-active {
                background: #f9fafb !important;
                box-shadow: inset 4px 0 0 #111827;
            }

            .sos-screen-alert .admin-header {
                animation: sos-screen-flash 1s ease-in-out 4;
            }

            @keyframes sos-screen-flash {
                0%, 100% { background: rgba(255, 255, 255, .96); }
                50% { background: #f3f4f6; }
            }

            .emergency-list {
                display: grid;
                gap: 14px;
            }

            .emergency-card {
                display: grid;
                grid-template-columns: 1fr auto;
                gap: 18px;
                border: 1px solid #e5e7eb;
                border-left: 5px solid #111827;
                border-radius: 16px;
                background: #ffffff;
                padding: 18px;
                box-shadow: 0 12px 30px rgba(17, 24, 39, .06);
            }

            .emergency-card-top {
                display: flex;
                align-items: center;
                gap: 10px;
                margin-bottom: 8px;
            }

            .emergency-badge {
                display: inline-flex;
                align-items: center;
                border-radius: 999px;
                background: #111827;
                color: #ffffff;
                padding: 5px 10px;
                font-size: 11px;
                font-weight: 900;
                text-transform: uppercase;
                letter-spacing: .04em;
            }

            .emergency-card h2 {
                color: #111827;
                font-size: 18px;
                font-weight: 900;
                letter-spacing: -0.02em;
            }

            .emergency-card-main > p {
                margin-top: 6px;
                color: #64748b;
                font-size: 14px;
                line-height: 1.55;
            }

            .emergency-meta-grid {
                display: grid;
                grid-template-columns: repeat(4, minmax(0, 1fr));
                gap: 10px;
                margin-top: 14px;
            }

            .emergency-meta-grid div {
                border-radius: 12px;
                background: #f8fafc;
                padding: 10px 12px;
            }

            .emergency-meta-grid span,
            .emergency-meta-grid small {
                display: block;
                color: #94a3b8;
                font-size: 11px;
                font-weight: 800;
            }

            .emergency-meta-grid strong {
                display: block;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
                color: #111827;
                font-size: 13px;
                font-weight: 800;
            }

            .emergency-card-actions {
                display: flex;
                flex-direction: column;
                align-items: stretch;
                justify-content: center;
                gap: 8px;
                min-width: 138px;
            }

            .report-empty-state {
                border: 1px dashed #cbd5e1;
                border-radius: 16px;
                background: #f8fafc;
                padding: 42px 24px;
                text-align: center;
            }

            .report-empty-state div {
                width: 52px;
                height: 52px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                border-radius: 999px;
                background: #f3f4f6;
                color: #111827;
                font-size: 26px;
                font-weight: 900;
            }

            .report-empty-state h2 {
                margin-top: 14px;
                color: #111827;
                font-size: 18px;
                font-weight: 900;
            }

            .report-empty-state p {
                margin-top: 5px;
                color: #64748b;
                font-size: 14px;
            }

            @media (max-width: 1280px) {
                .cctv-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }

                .report-summary-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
                .report-filter-card { grid-template-columns: repeat(3, minmax(0, 1fr)); }
                .emergency-meta-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            }

            @media (max-width: 1024px) {
                .content-area,
                body.sidebar-collapsed .content-area {
                    margin-left: 0;
                }

                .sidebar,
                body.sidebar-collapsed .sidebar {
                    width: min(86vw, 300px);
                    transform: translateX(-100%);
                    box-shadow: none;
                }

                body.sidebar-open .sidebar {
                    transform: translateX(0);
                    box-shadow: 0 24px 70px rgba(15, 23, 42, .28);
                }

                body.sidebar-open .sidebar-overlay {
                    opacity: 1;
                    pointer-events: auto;
                }

                body.sidebar-collapsed .sidebar-brand-text,
                body.sidebar-collapsed .sidebar-link span,
                body.sidebar-collapsed .category-title span:first-child,
                body.sidebar-collapsed .sidebar-dropdown summary span:last-child {
                    display: inline;
                }

                body.sidebar-collapsed .sidebar-dropdown summary.category-title {
                    display: flex;
                }

                body.sidebar-collapsed .sidebar-brand {
                    justify-content: flex-start;
                    padding: 28px 24px 24px;
                }

                body.sidebar-collapsed .sidebar-link {
                    justify-content: flex-start;
                    margin: 3px 14px;
                    padding: 12px 14px;
                }

                .admin-header {
                    padding: 0 20px;
                }

                .page-title {
                    font-size: 20px;
                }

                .master-toolbar,
                .cctv-topbar {
                    flex-direction: column;
                    align-items: stretch;
                }

                .report-filter-card,
                .emergency-card,
                .emergency-map-panel {
                    grid-template-columns: 1fr;
                }

                .emergency-card-actions {
                    flex-direction: row;
                    flex-wrap: wrap;
                    justify-content: flex-start;
                }


                .stats-grid { grid-template-columns: 1fr; }
                .chart-box { height: 130px; }
                .cctv-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            }

            @media (max-width: 640px) {
                .admin-header {
                    min-height: 64px;
                    padding: 0 14px;
                }

                .sidebar-toggle {
                    width: 40px;
                    height: 40px;
                    border-radius: 10px;
                }

                .page-title {
                    font-size: 18px;
                }

                .master-page,
                .cctv-page,
                .report-page,
                .infographic-page {
                    padding: 18px;
                }

                .infographic-filter {
                    flex-wrap: wrap;
                }

                .report-hero,
                .emergency-hero {
                    align-items: flex-start;
                    padding: 20px;
                }

                .report-hero-icon,
                .emergency-pulse {
                    display: none;
                }

                .report-hero h1,
                .emergency-hero h1 {
                    font-size: 23px;
                }

                .report-summary-grid,
                .report-filter-card,
                .emergency-meta-grid {
                    grid-template-columns: 1fr;
                }

                .master-search,
                .cctv-search input {
                    width: 100%;
                }

                .cctv-grid { grid-template-columns: 1fr; }
            }
        </style>
    </head>
    @php($globalActiveSosCount = \App\Models\EmergencyReport::where('status', 'aktif')->count())
    <body class="font-sans antialiased" data-active-sos-count="{{ $globalActiveSosCount }}" data-sos-status-url="{{ route('admin.laporan.darurat.status') }}">
        <div class="admin-shell">
            @include('layouts.admin-navigation')
            <div class="sidebar-overlay" data-sidebar-overlay></div>

            <div class="content-area">
                <header class="admin-header">
                    <div class="admin-header-left">
                        <button class="sidebar-toggle" type="button" data-sidebar-toggle aria-label="Buka/tutup sidebar" aria-expanded="true">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                        <div class="page-title">{{ $header ?? 'Dashboard' }}</div>
                    </div>
                    @php($authUser = Auth::user())
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="avatar-dot {{ $authUser?->profile_photo ? 'has-photo' : '' }}" type="button" aria-label="Menu pengguna">
                                @if ($authUser?->profile_photo)
                                    <img src="{{ asset('storage/' . $authUser->profile_photo) }}" alt="Foto profil {{ $authUser->name }}">
                                @else
                                    {{ strtoupper(substr($authUser?->name ?? 'U', 0, 1)) }}
                                @endif
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('Profile') }}
                            </x-dropdown-link>

                            <button type="button" class="block w-full px-4 py-2 text-start text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out" data-modal-target="logout-confirmation-modal">
                                {{ __('Log Out') }}
                            </button>
                        </x-slot>
                    </x-dropdown>
                </header>

                <main>
                    {{ $slot }}
                </main>
            </div>
        </div>

        <div id="logout-confirmation-modal" class="modal-backdrop" hidden>
            <div class="modal-card modal-card-sm">
                <div class="modal-header">
                    <h2>Konfirmasi Logout</h2>
                    <button type="button" data-modal-close="logout-confirmation-modal">×</button>
                </div>
                <div class="modal-body">
                    <p class="text-sm text-gray-700">Apakah Anda yakin ingin keluar dari akun ini?</p>
                    <form class="modal-footer px-0 pb-0" method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="btn-secondary" type="button" data-modal-close="logout-confirmation-modal">Batal</button>
                        <button class="btn-delete-text" type="submit">Logout</button>
                    </form>
                </div>
            </div>
        </div>

        <script>
            (function () {
                const body = document.body;
                const sidebarToggle = document.querySelector('[data-sidebar-toggle]');
                const sidebarOverlay = document.querySelector('[data-sidebar-overlay]');
                const desktopQuery = window.matchMedia('(min-width: 1025px)');

                if (desktopQuery.matches && localStorage.getItem('admin-sidebar-collapsed') === 'true') {
                    body.classList.add('sidebar-collapsed');
                    sidebarToggle?.setAttribute('aria-expanded', 'false');
                }

                function closeMobileSidebar() {
                    body.classList.remove('sidebar-open');
                    sidebarToggle?.setAttribute('aria-expanded', desktopQuery.matches && !body.classList.contains('sidebar-collapsed') ? 'true' : 'false');
                }

                sidebarToggle?.addEventListener('click', function () {
                    if (desktopQuery.matches) {
                        body.classList.toggle('sidebar-collapsed');
                        const collapsed = body.classList.contains('sidebar-collapsed');
                        localStorage.setItem('admin-sidebar-collapsed', collapsed ? 'true' : 'false');
                        sidebarToggle.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
                        return;
                    }

                    body.classList.toggle('sidebar-open');
                    sidebarToggle.setAttribute('aria-expanded', body.classList.contains('sidebar-open') ? 'true' : 'false');
                });

                sidebarOverlay?.addEventListener('click', closeMobileSidebar);

                const sidebarDropdowns = Array.from(document.querySelectorAll('[data-sidebar-dropdown]'));

                function getOpenSidebarDropdowns() {
                    try {
                        return JSON.parse(localStorage.getItem('admin-open-sidebar-dropdowns') || '[]');
                    } catch (error) {
                        return [];
                    }
                }

                function saveOpenSidebarDropdowns() {
                    const openDropdowns = sidebarDropdowns
                        .filter((dropdown) => dropdown.open)
                        .map((dropdown) => dropdown.dataset.sidebarDropdown);
                    localStorage.setItem('admin-open-sidebar-dropdowns', JSON.stringify(openDropdowns));
                }

                getOpenSidebarDropdowns().forEach((dropdownName) => {
                    document.querySelector(`[data-sidebar-dropdown="${dropdownName}"]`)?.setAttribute('open', true);
                });

                document.querySelectorAll('.sidebar-link').forEach((link) => {
                    link.addEventListener('click', function () {
                        saveOpenSidebarDropdowns();
                        if (!desktopQuery.matches) closeMobileSidebar();
                    });
                });

                sidebarDropdowns.forEach((dropdown) => {
                    dropdown.addEventListener('toggle', saveOpenSidebarDropdowns);
                });

                document.addEventListener('click', function (event) {
                    const targetId = event.target.closest('[data-modal-target]')?.dataset.modalTarget;
                    if (targetId) document.getElementById(targetId)?.removeAttribute('hidden');

                    const closeId = event.target.closest('[data-modal-close]')?.dataset.modalClose;
                    if (closeId) document.getElementById(closeId)?.setAttribute('hidden', true);

                    if (event.target.classList.contains('modal-backdrop')) event.target.setAttribute('hidden', true);
                });

                document.addEventListener('keydown', function (event) {
                    if (event.key === 'Escape') {
                        closeMobileSidebar();
                        document.querySelectorAll('.modal-backdrop:not([hidden])').forEach((modal) => modal.setAttribute('hidden', true));
                    }
                });

                desktopQuery.addEventListener('change', function () {
                    body.classList.remove('sidebar-open');

                    if (desktopQuery.matches && localStorage.getItem('admin-sidebar-collapsed') === 'true') {
                        body.classList.add('sidebar-collapsed');
                        sidebarToggle?.setAttribute('aria-expanded', 'false');
                    } else if (!desktopQuery.matches) {
                        body.classList.remove('sidebar-collapsed');
                        sidebarToggle?.setAttribute('aria-expanded', 'false');
                    }
                });
            })();

            (function () {
                let activeSosCount = Number(document.body.dataset.activeSosCount || 0);
                const statusUrl = document.body.dataset.sosStatusUrl;
                let alarmContext;
                let alarmOscillator;
                let alarmGain;
                let alarmInterval;

                async function getAlarmContext() {
                    const AudioContext = window.AudioContext || window.webkitAudioContext;
                    if (!AudioContext) return null;

                    alarmContext ??= new AudioContext();

                    if (alarmContext.state === 'suspended') {
                        try {
                            await alarmContext.resume();
                        } catch (error) {
                            return null;
                        }
                    }

                    return alarmContext.state === 'running' ? alarmContext : null;
                }

                async function playSosAlarm() {
                    const ctx = await getAlarmContext();
                    if (!ctx) return false;

                    const oscillator = ctx.createOscillator();
                    const gain = ctx.createGain();
                    oscillator.type = 'sawtooth';
                    oscillator.frequency.setValueAtTime(780, ctx.currentTime);
                    oscillator.frequency.setValueAtTime(520, ctx.currentTime + 0.22);
                    oscillator.connect(gain);
                    gain.connect(ctx.destination);
                    gain.gain.setValueAtTime(0.001, ctx.currentTime);
                    gain.gain.exponentialRampToValueAtTime(0.18, ctx.currentTime + 0.03);
                    gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.6);
                    oscillator.start();
                    oscillator.stop(ctx.currentTime + 0.65);
                    return true;
                }

                async function startContinuousSosAlarm() {
                    if (activeSosCount <= 0 || alarmOscillator) return false;

                    const ctx = await getAlarmContext();
                    if (!ctx) return false;

                    localStorage.setItem('sos-alarm-unlocked', 'true');
                    alarmOscillator = ctx.createOscillator();
                    alarmGain = ctx.createGain();
                    alarmOscillator.type = 'sawtooth';
                    alarmOscillator.frequency.setValueAtTime(760, ctx.currentTime);
                    alarmGain.gain.setValueAtTime(0.001, ctx.currentTime);
                    alarmOscillator.connect(alarmGain);
                    alarmGain.connect(ctx.destination);
                    alarmOscillator.start();

                    let highTone = false;
                    alarmInterval = window.setInterval(() => {
                        if (!alarmOscillator || !alarmGain) return;

                        const now = ctx.currentTime;
                        highTone = !highTone;
                        alarmOscillator.frequency.cancelScheduledValues(now);
                        alarmGain.gain.cancelScheduledValues(now);
                        alarmOscillator.frequency.setTargetAtTime(highTone ? 920 : 520, now, 0.05);
                        alarmGain.gain.setTargetAtTime(highTone ? 0.2 : 0.08, now, 0.03);
                    }, 360);

                    return true;
                }

                function stopContinuousSosAlarm() {
                    window.clearInterval(alarmInterval);
                    alarmInterval = null;

                    if (alarmGain && alarmContext) {
                        alarmGain.gain.setTargetAtTime(0.001, alarmContext.currentTime, 0.03);
                    }

                    const oscillator = alarmOscillator;
                    if (oscillator) {
                        window.setTimeout(() => {
                            try {
                                oscillator.stop();
                            } catch (error) {
                                // Oscillator may already be stopped by the browser lifecycle.
                            }
                        }, 120);
                    }

                    alarmOscillator = null;
                    alarmGain = null;
                }



                async function syncAlarmWithActiveSos() {
                    if (activeSosCount > 0) {
                        document.body.classList.add('sos-screen-alert');
                        await startContinuousSosAlarm();
                        return;
                    }

                    stopContinuousSosAlarm();
                }

                async function refreshActiveSosCount() {
                    if (!statusUrl) return;

                    try {
                        const response = await fetch(statusUrl, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                        });
                        if (!response.ok) return;

                        const data = await response.json();
                        const latestCount = Number(data.active || 0);
                        const previousCount = activeSosCount;
                        activeSosCount = latestCount;
                        document.body.dataset.activeSosCount = String(activeSosCount);

                        if (activeSosCount > previousCount) {
                            document.body.classList.add('sos-screen-alert');
                        }

                        await syncAlarmWithActiveSos();
                    } catch (error) {
                        // Keep the current alarm state if polling temporarily fails.
                    }
                }

                document.addEventListener('click', function (event) {
                    if (event.target.closest('[data-test-alarm]')) playSosAlarm();
                });

                ['pointerdown', 'mousedown', 'click', 'keydown', 'touchstart'].forEach((eventName) => {
                    document.addEventListener(eventName, async () => {
                        if (activeSosCount > 0 && !alarmOscillator) {
                            await startContinuousSosAlarm();
                        }
                    });
                });

                document.addEventListener('visibilitychange', function () {
                    if (!document.hidden) syncAlarmWithActiveSos();
                });

                window.addEventListener('focus', syncAlarmWithActiveSos);

                syncAlarmWithActiveSos();
                window.setTimeout(refreshActiveSosCount, 1000);
                window.setInterval(refreshActiveSosCount, 5000);
                window.setInterval(() => {
                    if (!document.hidden && activeSosCount > 0 && !alarmOscillator) {
                        syncAlarmWithActiveSos();
                    }
                }, 2000);
            })();
        </script>
    </body>
</html>
