<div class="docs-section-header">
    <h2 class="docs-section-title">
        <i class="fas fa-comment"></i>
        Feedback
    </h2>
    <p class="docs-section-desc">Components for displaying progress, loading states, and user feedback.</p>
</div>

{{-- Progress Bar --}}
<div class="docs-card">
    <div class="docs-card-header">
        <h3 class="docs-card-title">
            <i class="fas fa-tasks"></i>
            Progress Bar
        </h3>
        <div class="docs-card-actions">
            <button class="docs-copy-btn" onclick="copyCode(this)">
                <i class="fas fa-copy"></i> Copy
            </button>
        </div>
    </div>
    <div class="docs-code">
        <pre><code>&lt;x-progress-bar :value="75" label="Progress" variant="primary" /&gt;
&lt;x-progress-bar :value="45" label="Storage" variant="warning" /&gt;</code></pre>
    </div>
    <div class="docs-preview">
        <div class="docs-preview-label">Live Preview</div>
        <x-ui.progress-bar :value="75" label="Progress" variant="primary" class="mb-3" />
        <x-ui.progress-bar :value="45" label="Storage" variant="warning" />
    </div>
</div>

{{-- Skeleton --}}
<div class="docs-card">
    <div class="docs-card-header">
        <h3 class="docs-card-title">
            <i class="fas fa-spinner"></i>
            Skeleton Loading
        </h3>
        <div class="docs-card-actions">
            <button class="docs-copy-btn" onclick="copyCode(this)">
                <i class="fas fa-copy"></i> Copy
            </button>
        </div>
    </div>
    <div class="docs-code">
        <pre><code>&lt;x-skeleton type="text" :count="3" /&gt;
&lt;x-skeleton type="circle" /&gt;
&lt;x-skeleton type="table" :count="2" /&gt;</code></pre>
    </div>
    <div class="docs-preview">
        <div class="docs-preview-label">Live Preview</div>
        <div class="row g-4">
            <div class="col-md-4">
                <small class="text-muted d-block mb-2">Text:</small>
                <x-ui.skeleton type="text" :count="3" />
            </div>
            <div class="col-md-4">
                <small class="text-muted d-block mb-2">Circle:</small>
                <x-ui.skeleton type="circle" />
            </div>
            <div class="col-md-4">
                <small class="text-muted d-block mb-2">Table:</small>
                <x-ui.skeleton type="table" :count="2" />
            </div>
        </div>
    </div>
</div>

{{-- Empty State --}}
<div class="docs-card">
    <div class="docs-card-header">
        <h3 class="docs-card-title">
            <i class="fas fa-inbox"></i>
            Empty State
        </h3>
        <div class="docs-card-actions">
            <button class="docs-copy-btn" onclick="copyCode(this)">
                <i class="fas fa-copy"></i> Copy
            </button>
        </div>
    </div>
    <div class="docs-code">
        <pre><code>&lt;x-empty-state
    icon="fas fa-inbox"
    title="No data found"
    description="Start by adding your first item."
    actionLabel="Add Item"
    actionHref="/items/create"
/&gt;</code></pre>
    </div>
    <div class="docs-preview">
        <div class="docs-preview-label">Live Preview</div>
        <x-ui.empty-state icon="fas fa-inbox" title="No data found" description="Start by adding your first item."
            actionLabel="Add Item" actionHref="#" size="sm" />
    </div>
</div>

{{-- Tooltip --}}
<div class="docs-card">
    <div class="docs-card-header">
        <h3 class="docs-card-title">
            <i class="fas fa-info-circle"></i>
            Tooltip
        </h3>
        <div class="docs-card-actions">
            <button class="docs-copy-btn" onclick="copyCode(this)">
                <i class="fas fa-copy"></i> Copy
            </button>
        </div>
    </div>
    <div class="docs-code">
        <pre><code>&lt;x-tooltip content="This is a tooltip" position="top"&gt;
    &lt;x-button variant="primary"&gt;Hover me&lt;/x-button&gt;
&lt;/x-tooltip&gt;</code></pre>
    </div>
    <div class="docs-preview">
        <div class="docs-preview-label">Live Preview</div>
        <div class="d-flex gap-2 flex-wrap">
            <x-ui.tooltip content="Top tooltip" position="top">
                <x-ui.button variant="outline">Top</x-ui.button>
            </x-ui.tooltip>
            <x-ui.tooltip content="Right tooltip" position="right">
                <x-ui.button variant="outline">Right</x-ui.button>
            </x-ui.tooltip>
            <x-ui.tooltip content="Bottom tooltip" position="bottom">
                <x-ui.button variant="outline">Bottom</x-ui.button>
            </x-ui.tooltip>
            <x-ui.tooltip content="Left tooltip" position="left">
                <x-ui.button variant="outline">Left</x-ui.button>
            </x-ui.tooltip>
        </div>
    </div>
</div>