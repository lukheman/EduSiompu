# AI Agent Instructions: Project Standards & Best Practices

This project uses **Laravel 12**, **Livewire 4**, **Livewire Volt**, and **Bootstrap 5**. When asked to build a new feature or modify an existing one, you **MUST** adhere strictly to the following rules to ensure optimal performance, maintainability, and DRY (Don't Repeat Yourself) principles.

### 1. Framework & Styling (Bootstrap)
- The project uses **Bootstrap** as its primary CSS framework, as configured in the layout templates.
- **DO NOT** use Tailwind CSS or other utility-first frameworks.
- Use standard Bootstrap classes for layout, typography, spacing, and styling.
- Avoid using inline styles (`style="..."`) unless absolutely necessary.

### 2. Layout Usage
- Always extend the appropriate layout file when creating new views:
  - Use `resources/views/layouts/app.blade.php` for authenticated/dashboard views.
  - Use `resources/views/layouts/guest.blade.php` for public/unauthenticated views (like login, register, etc.).

### 3. Folder Structure & DRY Principle
You MUST follow this strictly organized folder structure when creating new files:

- **Blade UI Components (Visual/Dumb Components)**:
  Place all reusable, purely UI components (HTML/Bootstrap only) here. Never put business logic here.
  - `resources/views/components/ui/` (e.g., buttons, inputs, badges)
  - `resources/views/components/layout/` (e.g., cards, wrappers)
  - `resources/views/components/form/` (e.g., form groups)

- **Livewire Components (Stateful/Smart Components)**:
  - **Pages**: Full page components tied to a route go in `app/Livewire/Pages/` (and views in `resources/views/livewire/pages/`).
  - **Shared/Reusable Widgets**: Reusable components that contain logic (like a search bar, notification bell, etc.) go in `app/Livewire/Shared/` (and views in `resources/views/livewire/shared/`).

- **Business Logic Extraction**:
  - Complex logic should be extracted from Livewire components into `app/Actions/` or `app/Services/` to keep components clean.

### 4. UI Component Usage (Blade)
- **Analyze Existing Components**: Before writing any new UI code or HTML, always check the `resources/views/components` subdirectories (`ui`, `layout`, `form`) to see if a suitable Blade component already exists (e.g., `<x-ui.button>`, `<x-form.input>`).
- **Reuse First**: If a component exists that fits the requirement, use it instead of writing raw HTML/CSS from scratch.
- **Modify with Care**: If an existing component is close to what you need, prefer modifying it by adding optional props rather than duplicating it.
- **Consistency**: New components must be built using Bootstrap classes and match the styling/structure of the other components.

### 5. Livewire 4 & Volt Best Practices
- **Livewire 4 Features**: Utilize Livewire 4 optimizations and PHP Attributes (e.g., `#[Validate]`, `#[Url]`, `#[On]`, `#[Computed]`, `#[Locked]`).
- **Livewire Volt**: If you create single-file functional components using Livewire Volt, place them correctly in `resources/views/livewire/pages/` or `resources/views/livewire/shared/` and follow the Volt API.
- **SPA-like Navigation**: Use `wire:navigate` on links (`<a>` tags) for faster page transitions within the application instead of full page reloads.
- **State Management**: Keep component state minimal. Use `#[Computed]` (or `computed()` in Volt) for properties derived from database queries to avoid serializing large models or collections unnecessarily.
- **Forms**: Use `wire:model` (which defers updates to the next network request by default) unless real-time reactivity is explicitly needed, in which case use `wire:model.live`.
- **Events**: Dispatch and listen to events using the modern Livewire 3/4 approach (e.g., `$this->dispatch('event-name')` and `#[On('event-name')]`).

### 6. Laravel 12 Best Practices
- **Strict Types**: Leverage PHP 8.2+ features, strict typing, typed properties, and modern PHP syntax.
- **Business Logic**: Keep Livewire components clean by extracting complex business logic into Action classes (`app/Actions`) or Services (`app/Services`).
- **Eloquent Models**: Use Eloquent scopes for complex queries.
