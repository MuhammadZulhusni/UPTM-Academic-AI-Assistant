@extends('superadmin.dashboard')

@section('superadmin')
<div class="nk-content-inner">
    <div class="nk-content-body">
        <!-- Page Header -->
        <div class="nk-block-head nk-page-head">
            <div class="nk-block-head-between flex-wrap g-2">
                <div class="nk-block-head-content">
                    <h2 class="display-6">
                        Admin Activity Tracking
                    </h2>
                    <p class="text-muted mb-3">Monitor all admin actions and activities</p>
                </div>
                <div class="nk-block-head-content">
                    <a href="{{ route('superadmin.admin.activities.export') }}" class="btn btn-primary">
                        <i class="bi bi-download me-2"></i>Export to CSV
                    </a>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row g-4 mb-4">
            <div class="col-lg-3 col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="icon-circle bg-primary-subtle">
                                    <i class="bi bi-calendar-check text-primary fs-4"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="text-muted mb-1">Total Activities</h6>
                                <h3 class="mb-0 fw-bold">{{ number_format($stats['total_activities']) }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="icon-circle bg-success-subtle">
                                    <i class="bi bi-clock-history text-success fs-4"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="text-muted mb-1">Today's Activities</h6>
                                <h3 class="mb-0 fw-bold">{{ number_format($stats['today_activities']) }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="icon-circle bg-info-subtle">
                                    <i class="bi bi-file-earmark-text text-info fs-4"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="text-muted mb-1">Templates Created</h6>
                                <h3 class="mb-0 fw-bold">{{ number_format($stats['templates_created']) }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="icon-circle bg-warning-subtle">
                                    <i class="bi bi-people text-warning fs-4"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="text-muted mb-1">Active Admins</h6>
                                <h3 class="mb-0 fw-bold">{{ number_format($stats['total_admins']) }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('superadmin.admin.activities') }}" class="row g-3">
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label fw-semibold">Admin</label>
                        <select name="admin" class="form-select" onchange="this.form.submit()">
                            <option value="all" {{ $adminFilter == 'all' ? 'selected' : '' }}>All Admins</option>
                            @foreach($admins as $admin)
                                <option value="{{ $admin->id }}" {{ $adminFilter == $admin->id ? 'selected' : '' }}>
                                    {{ $admin->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <label class="form-label fw-semibold">Activity Type</label>
                        <select name="activity" class="form-select" onchange="this.form.submit()">
                            <option value="all" {{ $activityFilter == 'all' ? 'selected' : '' }}>All Activities</option>
                            <option value="template_created" {{ $activityFilter == 'template_created' ? 'selected' : '' }}>Template Created</option>
                            <option value="template_updated" {{ $activityFilter == 'template_updated' ? 'selected' : '' }}>Template Updated</option>
                            <option value="template_deleted" {{ $activityFilter == 'template_deleted' ? 'selected' : '' }}>Template Deleted</option>
                            <!-- <option value="user_created" {{ $activityFilter == 'user_created' ? 'selected' : '' }}>User Created</option>
                            <option value="user_updated" {{ $activityFilter == 'user_updated' ? 'selected' : '' }}>User Updated</option>
                            <option value="user_deleted" {{ $activityFilter == 'user_deleted' ? 'selected' : '' }}>User Deleted</option> -->
                            <option value="login" {{ $activityFilter == 'login' ? 'selected' : '' }}>Login</option>
                            <option value="logout" {{ $activityFilter == 'logout' ? 'selected' : '' }}>Logout</option>
                        </select>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <label class="form-label fw-semibold">Date Range</label>
                        <select name="date" class="form-select" onchange="this.form.submit()">
                            <option value="all" {{ $dateFilter == 'all' ? 'selected' : '' }}>All Time</option>
                            <option value="today" {{ $dateFilter == 'today' ? 'selected' : '' }}>Today</option>
                            <option value="yesterday" {{ $dateFilter == 'yesterday' ? 'selected' : '' }}>Yesterday</option>
                            <option value="week" {{ $dateFilter == 'week' ? 'selected' : '' }}>Last 7 Days</option>
                            <option value="month" {{ $dateFilter == 'month' ? 'selected' : '' }}>Last 30 Days</option>
                        </select>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <label class="form-label fw-semibold">Search</label>
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Search..." value="{{ $search }}">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Activities Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="px-4">Date & Time</th>
                                <th>Admin</th>
                                <th>Activity</th>
                                <th>Description</th>
                                <!-- <th>IP Address</th> -->
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($activities as $activity)
                            <tr>
                                <td class="px-4">
                                    <div class="fw-semibold">{{ $activity->formatted_date }}</div>
                                    <small class="text-muted">
                                        {{ $activity->formatted_time }} • {{ $activity->day_name }}
                                    </small>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="me-2">
                                            @if(!empty($activity->admin->photo))
                                                <img
                                                    src="{{ asset('upload/admin_images/' . $activity->admin->photo) }}"
                                                    alt="{{ $activity->admin->name }}"
                                                    class="rounded-circle"
                                                    width="40"
                                                    height="40"
                                                    style="object-fit: cover;"
                                                >
                                            @else
                                                <div class="avatar-circle">
                                                    {{ strtoupper(substr($activity->admin->name, 0, 2)) }}
                                                </div>
                                            @endif
                                        </div>

                                        <div>
                                            <div class="fw-semibold">{{ $activity->admin->name }}</div>
                                            <small class="text-muted">{{ $activity->admin->email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $badgeClass = match($activity->activity_type) {
                                            'template_created' => 'bg-success',
                                            'template_updated' => 'bg-info',
                                            'template_deleted' => 'bg-danger',
                                            'user_created' => 'bg-primary',
                                            'user_updated' => 'bg-warning',
                                            'user_deleted' => 'bg-danger',
                                            'login' => 'bg-success',
                                            'logout' => 'bg-secondary',
                                            default => 'bg-secondary'
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">
                                        {{ str_replace('_', ' ', ucwords($activity->activity_type)) }}
                                    </span>
                                </td>
                                <td>{{ $activity->activity_description }}</td>
                                <!-- <td>
                                    <code class="text-muted">{{ $activity->ip_address ?? 'N/A' }}</code>
                                </td> -->
                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-primary" onclick="viewDetails({{ $activity->id }})">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <i class="bi bi-inbox fs-1 text-muted d-block mb-2"></i>
                                    <p class="text-muted">No activities found</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            @if($activities->hasPages())
            <div class="card-footer bg-white">
                {{ $activities->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Activity Details Modal -->
<div class="modal fade" id="activityDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Activity Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="activityDetailsContent">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.icon-circle {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.avatar-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    text-transform: uppercase;
}

.table th {
    font-weight: 600;
    color: #526484;
    text-transform: uppercase;
    font-size: 12px;
    letter-spacing: 0.5px;
}

.badge {
    padding: 6px 12px;
    font-weight: 500;
    font-size: 11px;
}
</style>

<script>
function viewDetails(activityId) {
    const modal = new bootstrap.Modal(document.getElementById('activityDetailsModal'));
    const content = document.getElementById('activityDetailsContent');
    
    modal.show();
    
    fetch(`/superadmin/admin-activity/${activityId}`)
        .then(response => response.json())
        .then(data => {
            content.innerHTML = `
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="fw-semibold text-muted small">Admin Name</label>
                        <p class="mb-0">${data.admin.name}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="fw-semibold text-muted small">Admin Email</label>
                        <p class="mb-0">${data.admin.email}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="fw-semibold text-muted small">Activity Type</label>
                        <p class="mb-0">${data.activity_type.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="fw-semibold text-muted small">Date & Time</label>
                        <p class="mb-0">${new Date(data.created_at).toLocaleString()}</p>
                    </div>
                    <div class="col-12">
                        <label class="fw-semibold text-muted small">Description</label>
                        <p class="mb-0">${data.activity_description}</p>
                    </div>
                    ${data.metadata ? `
                    <div class="col-12">
                        <label class="fw-semibold text-muted small">Additional Details</label>
                        <pre class="bg-light p-3 rounded"><code>${JSON.stringify(data.metadata, null, 2)}</code></pre>
                    </div>
                    ` : ''}
                </div>
            `;
        })
        .catch(error => {
            content.innerHTML = '<div class="alert alert-danger">Error loading details</div>';
        });
}
</script>
@endsection