<div>
    {{-- Page Header --}}
    <x-layout.page-header title="Dashboard Overview" subtitle="Welcome back, John! Here's what's happening today.">
        <x-slot:actions>
            <x-ui.button variant="primary" icon="fas fa-plus">New Report</x-ui.button>
        </x-slot:actions>
    </x-layout.page-header>

    {{-- Stats Cards --}}
    <div class="row g-4 mb-4">
        <div class="col-md-6 col-lg-3">
            <x-layout.stat-card icon="fas fa-dollar-sign" label="Total Revenue" value="$48,574"
                trend-value="12.5% from last month" trend-direction="up" variant="primary" />
        </div>
        <div class="col-md-6 col-lg-3">
            <x-layout.stat-card icon="fas fa-shopping-bag" label="New Orders" value="1,245"
                trend-value="8.2% from last month" trend-direction="up" variant="secondary" />
        </div>
        <div class="col-md-6 col-lg-3">
            <x-layout.stat-card icon="fas fa-users" label="Total Users" value="8,456" trend-value="15.3% from last month"
                trend-direction="up" variant="success" />
        </div>
        <div class="col-md-6 col-lg-3">
            <x-layout.stat-card icon="fas fa-chart-pie" label="Conversion Rate" value="3.24%"
                trend-value="2.1% from last month" trend-direction="down" variant="warning" />
        </div>
    </div>

    {{-- Component Preview Section --}}
    <div class="component-preview">
        <h2 class="mb-4" style="color: #1e293b; font-weight: 700;">UI Components Preview</h2>

        {{-- Buttons Preview --}}
        <div class="preview-section">
            <div class="preview-title">Buttons</div>
            <div class="d-flex flex-wrap gap-2">
                <x-ui.button variant="primary">Primary Button</x-ui.button>
                <x-ui.button variant="secondary">Secondary Button</x-ui.button>
                <x-ui.button variant="success">Success Button</x-ui.button>
                <x-ui.button variant="warning">Warning Button</x-ui.button>
                <x-ui.button variant="danger">Danger Button</x-ui.button>
                <x-ui.button variant="outline">Outline Button</x-ui.button>
            </div>
        </div>

        {{-- Badges Preview --}}
        <div class="preview-section">
            <div class="preview-title">Badges</div>
            <div class="d-flex flex-wrap gap-2">
                <x-ui.badge variant="primary" icon="fas fa-circle">Primary</x-ui.badge>
                <x-ui.badge variant="success" icon="fas fa-check-circle">Success</x-ui.badge>
                <x-ui.badge variant="warning" icon="fas fa-exclamation-circle">Warning</x-ui.badge>
                <x-ui.badge variant="danger" icon="fas fa-times-circle">Danger</x-ui.badge>
                <x-ui.badge variant="info" icon="fas fa-info-circle">Info</x-ui.badge>
            </div>
        </div>

        {{-- Alerts Preview --}}
        <div class="preview-section">
            <div class="preview-title">Alerts</div>
            <x-ui.alert variant="success" title="Success!" class="mb-3">
                Your changes have been saved successfully.
            </x-ui.alert>
            <x-ui.alert variant="danger" title="Error!" class="mb-3">
                There was a problem processing your request.
            </x-ui.alert>
            <x-ui.alert variant="info" title="Info!">
                You have 3 new notifications waiting for you.
            </x-ui.alert>
        </div>

        {{-- Progress Bars Preview --}}
        <div class="preview-section">
            <div class="preview-title">Progress Bars</div>
            <x-ui.progress-bar :value="75" label="Project Progress" variant="primary" class="mb-3" />
            <x-ui.progress-bar :value="45" label="Storage Used" variant="secondary" class="mb-3" />
            <x-ui.progress-bar :value="32" label="CPU Usage" variant="success" />
        </div>

        {{-- Feature Cards Preview --}}
        <div class="preview-section">
            <div class="preview-title">Feature Cards</div>
            <div class="row g-3">
                <div class="col-md-4">
                    <x-layout.feature-card icon="fas fa-rocket" title="Fast Performance"
                        description="Lightning fast load times and smooth interactions" variant="primary" />
                </div>
                <div class="col-md-4">
                    <x-layout.feature-card icon="fas fa-shield-alt" title="Secure by Default"
                        description="Enterprise-grade security for your data" variant="secondary" />
                </div>
                <div class="col-md-4">
                    <x-layout.feature-card icon="fas fa-mobile-alt" title="Mobile Responsive"
                        description="Perfect experience on any device" variant="success" />
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Orders Table --}}
    <x-layout.table-card title="Recent Orders" view-all-href="#orders" :headers="['Order ID', 'Customer', 'Product', 'Amount', 'Status', 'Date']">
        <tr>
            <td><strong style="color: #1e293b;">#ORD-2024</strong></td>
            <td>Alice Johnson</td>
            <td>Wireless Headphones</td>
            <td><strong style="color: #1e293b;">$129.99</strong></td>
            <td><x-ui.badge variant="success" icon="fas fa-check-circle">Delivered</x-ui.badge></td>
            <td class="text-muted">Jan 10, 2026</td>
        </tr>
        <tr>
            <td><strong style="color: #1e293b;">#ORD-2023</strong></td>
            <td>Bob Smith</td>
            <td>Smart Watch</td>
            <td><strong style="color: #1e293b;">$299.99</strong></td>
            <td><x-ui.badge variant="warning" icon="fas fa-clock">Pending</x-ui.badge></td>
            <td class="text-muted">Jan 10, 2026</td>
        </tr>
        <tr>
            <td><strong style="color: #1e293b;">#ORD-2022</strong></td>
            <td>Carol White</td>
            <td>Laptop Stand</td>
            <td><strong style="color: #1e293b;">$49.99</strong></td>
            <td><x-ui.badge variant="secondary" icon="fas fa-shipping-fast">Shipped</x-ui.badge></td>
            <td class="text-muted">Jan 9, 2026</td>
        </tr>
        <tr>
            <td><strong style="color: #1e293b;">#ORD-2021</strong></td>
            <td>David Lee</td>
            <td>USB-C Hub</td>
            <td><strong style="color: #1e293b;">$79.99</strong></td>
            <td><x-ui.badge variant="success" icon="fas fa-check-circle">Delivered</x-ui.badge></td>
            <td class="text-muted">Jan 9, 2026</td>
        </tr>
    </x-layout.table-card>
</div>