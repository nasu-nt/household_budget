{{-- resources/views/dashboard/partials/_sidebar-handle.blade.php --}}
<button
    type="button"
    class="dashboard__sidebar-handle"
    data-dashboard-sidebar-toggle
    aria-controls="dashboard-sidebar-content"
    aria-expanded="true"
    aria-label="{{ __('Close expense sidebar') }}"
>
    <span
        class="sidebar-handle__icon sidebar-handle__icon--close"
        aria-hidden="true"
    ></span>

    <span
        class="sidebar-handle__icon sidebar-handle__icon--open"
        aria-hidden="true"
    ></span>
</button>
