@extends('layouts.app')

@section('title', 'Users — Anesi Staff System')
@section('page-title', 'User Management')

@section('content')
<div class="table-card">
    <div class="table-card-header">
        <h6><i class="bi bi-people-fill me-2"></i>All Users</h6>
        <button class="btn btn-sm btn-forest" data-bs-toggle="modal" data-bs-target="#addUserModal">
            <i class="bi bi-plus-lg me-1"></i> Add User
        </button>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead style="background:#f8f4ee;">
                <tr>
                    <th class="ps-3">#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Emp. ID</th>
                    <th>Position</th>
                    <th>Created</th>
                    <th class="text-end pe-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                @php $sr = $user->staffRecord; @endphp
                <tr>
                    <td class="ps-3 text-muted" style="font-size:13px;">{{ $user->id }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div style="width:32px;height:32px;border-radius:50%;background:#1B3A2D;color:#fff;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;flex-shrink:0;overflow:hidden;">
                                @if($user->avatar)
                                    <img src="{{ asset('storage/'.$user->avatar) }}" style="width:100%;height:100%;object-fit:cover;">
                                @else
                                    {{ strtoupper(substr($user->name,0,1)) }}
                                @endif
                            </div>
                            <span style="font-weight:500;">{{ $user->name }}</span>
                        </div>
                    </td>
                    <td style="font-size:13px;">{{ $user->email }}</td>
                    <td>
                        @php
                            $rc = ['admin'=>'#C8922A','staff'=>'#8A7A65'][$user->role] ?? '#8A7A65';
                        @endphp
                        <span class="badge" style="background:{{ $rc }}22;color:{{ $rc }};border:1px solid {{ $rc }}44;">
                            {{ ucfirst($user->role ?? 'staff') }}
                        </span>
                    </td>
                    <td style="font-size:13px;"><code>{{ $sr->employee_id ?? '—' }}</code></td>
                    <td style="font-size:13px;color:#8A7A65;">{{ $sr->position ?? '—' }}</td>
                    <td style="font-size:13px;color:#8A7A65;">{{ $user->created_at->format('M d, Y') }}</td>
                    <td class="text-end pe-3">
                        <button class="btn btn-sm btn-outline-secondary me-1"
                            data-bs-toggle="modal" data-bs-target="#editUserModal"
                            data-id="{{ $user->id }}"
                            data-name="{{ $user->name }}"
                            data-email="{{ $user->email }}"
                            data-role="{{ $user->role }}">
                            <i class="bi bi-pencil-fill"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger"
                            data-bs-toggle="modal" data-bs-target="#deleteUserModal"
                            data-id="{{ $user->id }}"
                            data-name="{{ $user->name }}">
                            <i class="bi bi-trash-fill"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center py-4 text-muted">No users found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
    <div class="p-3">{{ $users->links() }}</div>
    @endif
</div>

{{-- ADD USER MODAL --}}
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form method="POST" action="{{ route('users.store') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header" style="background:#1B3A2D;">
                    <h5 class="modal-title text-white" style="font-family:'Lora',serif;">
                        <i class="bi bi-person-plus-fill me-2"></i>Add User
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">

                    {{-- Account Info --}}
                    <p class="fw-semibold mb-2" style="font-size:13px;color:#1B3A2D;border-bottom:1px solid #DDD3C0;padding-bottom:6px;">
                        <i class="bi bi-person-circle me-1"></i> Account Information
                    </p>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Full Name</label>
                            <input type="text" name="name" class="form-control" placeholder="Juan dela Cruz" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email Address</label>
                            <input type="email" name="email" class="form-control" placeholder="juan@anesi.com" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Password</label>
                            <input type="password" name="password" class="form-control" placeholder="Min. 8 characters" required minlength="8">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Role</label>
                            <select name="role" id="addRole" class="form-select" required>
                                <option value="" disabled selected>— Select role —</option>
                                <option value="admin">Admin</option>
                                <option value="staff">Staff</option>
                            </select>
                        </div>
                    </div>

                    {{-- Employment Details — shown only for staff --}}
                    <div id="employmentSection" style="display:none;">
                        <p class="fw-semibold mb-2" style="font-size:13px;color:#1B3A2D;border-bottom:1px solid #DDD3C0;padding-bottom:6px;">
                            <i class="bi bi-person-badge-fill me-1"></i> Employment Details
                            <small class="text-muted fw-normal ms-1">(auto-creates staff record)</small>
                        </p>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Employee ID</label>
                                <input type="text" name="employee_id" id="addEmpId" class="form-control" placeholder="EMP-001">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Position</label>
                                <input type="text" name="position" id="addPosition" class="form-control" placeholder="Barista">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Department</label>
                                <input type="text" name="department" class="form-control" placeholder="Operations">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Branch</label>
                                <input type="text" name="branch" class="form-control" placeholder="Main Branch">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Hire Date</label>
                                <input type="date" name="hire_date" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Salary</label>
                                <input type="number" name="salary" class="form-control" placeholder="0.00" step="0.01" min="0">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Status</label>
                                <select name="status" class="form-select">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                    <option value="on_leave">On Leave</option>
                                </select>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-forest">
                        <i class="bi bi-person-check-fill me-1"></i> Create Account
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- EDIT USER MODAL --}}
<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" id="editUserForm">
            @csrf @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" style="font-family:'Lora',serif;">Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Full Name</label>
                        <input type="text" name="name" id="editName" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email</label>
                        <input type="email" name="email" id="editEmail" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">New Password <small class="text-muted">(leave blank to keep)</small></label>
                        <input type="password" name="password" class="form-control" minlength="8">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Role</label>
                        <select name="role" id="editRole" class="form-select" required>
                            <option value="staff">Staff</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-forest">Save Changes</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- DELETE USER MODAL --}}
<div class="modal fade" id="deleteUserModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <form method="POST" id="deleteUserForm">
            @csrf @method('DELETE')
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title text-danger" style="font-family:'Lora',serif;">Delete User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body pt-0">
                    <p class="mb-0">Delete <strong id="deleteUserName"></strong>? This cannot be undone.</p>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Show/hide employment section based on role selection
document.getElementById('addRole').addEventListener('change', function () {
    const show = this.value === 'staff';
    const section = document.getElementById('employmentSection');
    section.style.display = show ? '' : 'none';
    // Toggle required on key fields
    ['employee_id','position','department','branch','hire_date'].forEach(name => {
        const el = section.querySelector(`[name="${name}"]`);
        if (el) el.required = show;
    });
});

document.getElementById('editUserModal').addEventListener('show.bs.modal', function (e) {
    const btn = e.relatedTarget;
    document.getElementById('editUserForm').action = `/users/${btn.dataset.id}`;
    document.getElementById('editName').value  = btn.dataset.name;
    document.getElementById('editEmail').value = btn.dataset.email;
    document.getElementById('editRole').value  = btn.dataset.role || 'staff';
});

document.getElementById('deleteUserModal').addEventListener('show.bs.modal', function (e) {
    const btn = e.relatedTarget;
    document.getElementById('deleteUserForm').action = `/users/${btn.dataset.id}`;
    document.getElementById('deleteUserName').textContent = btn.dataset.name;
});
</script>
@endsection
