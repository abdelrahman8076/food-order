@if(session('success'))
    <div class="container mt-3">
        <div class="alert alert-success glass-card border-0 text-white d-flex align-items-center" role="alert" style="background: rgba(40,167,69,0.2);">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="alert"></button>
        </div>
    </div>
@endif
@if(session('error'))
    <div class="container mt-3">
        <div class="alert alert-danger glass-card border-0 text-white d-flex align-items-center" role="alert" style="background: rgba(220,53,69,0.2);">
            <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="alert"></button>
        </div>
    </div>
@endif
