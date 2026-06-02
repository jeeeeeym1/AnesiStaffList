@extends('layouts.app')

@section('title', 'Staff Records — Anesi Staff System')
@section('page-title', 'Staff Records')

@section('content')
<div class="table-card">
    <div class="table-card-header">
        <h6><i class="bi bi-person-badge-fill me-2"></i>Staff Records</h6>
        <button class="btn btn-sm btn-forest" data-bs-toggle="modal" data-bs-target="#addStaffModal">
            <i class="bi bi-plus-lg me-1"></i> Add Staff
        </button>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead style="background:#f8f4ee;">
                <tr>
                    <th class="ps-3">Emp. ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Position</th>
                    <th>Department</th>
                    <th>Branch</th>
                    <th>Hire Date</th>
                    <th>Status</th>
                    <th class="text-end pe-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($records as $record)
                <tr>
                    <td class="ps-3"><code>{{ $record->employee_id }}</code></td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div style="width:30px;height:30px;border-radius:50%;background:#1B3A2D;color:#fff;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;flex-shrink:0;overflow:hidden;">
                                @if($record->user->avatar)
                                    <img src="{{ asset('storage/'.$record->user->avatar) }}" style="width:100%;height:100%;object-fit:cover;">
                                @else
                                    {{ strtoupper(substr($record->user->name,0,1)) }}
                                @endif
                            </div>
                            <span style="font-weight:500;">{{ $record->user->name }}</span>
                        </div>
                    </td>
                    <td style="font-size:13px;color:#8A7A65;">{{ $record->user->email }}</td>
                    <td style="font-size:13px;">{{ $record->position }}</td>
                    <td style="font-size:13px;">{{ $record->department }}</td>
                    <td style="font-size:13px;">{{ $record->branch }}</td>
                    <td style="font-size:13px;color:#8A7A65;">{{ \Carbon\Carbon::parse($record->hire_date)->format('M d, Y') }}</td>
                    <td>
                        @php
                            $colors = ['active'=>'#e6f4ea:#2e7d32','inactive'=>'#fce8e6:#c62828','on_leave'=>'#fff3e0:#e65100'];
                            [$bg,$fg] = explode(':', $colors[$record->status] ?? '#eee:#333');
                        @endphp
                        <span class="badge" style="background:{{ $bg }};color:{{ $fg }};">
                            {{ ucfirst(str_replace('_',' ',$record->status)) }}
                        </span>
                    </td>
                    <td class="text-end pe-3">
                        <button class="btn btn-sm btn-outline-secondary me-1"
                            data-bs-toggle="modal" data-bs-target="#editStaffModal"
                            data-id="{{ $record->id }}"
                            data-name="{{ $record->user->name }}"
                            data-email="{{ $record->user->email }}"
                            data-employee_id="{{ $record->employee_id }}"
                            data-position="{{ $record->position }}"
                            data-department="{{ $record->department }}"
                            data-branch="{{ $record->branch }}"
                            data-hire_date="{{ $record->hire_date }}"
                            data-salary="{{ $record->salary }}"
                            data-status="{{ $record->status }}"
                            data-notes="{{ $record->notes }}">
                            <i class="bi bi-pencil-fill"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger"
                            data-bs-toggle="modal" data-bs-target="#deleteStaffModal"
                            data-id="{{ $record->id }}"
                            data-name="{{ $record->user->name }}">
                            <i class="bi bi-trash-fill"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" class="text-center py-4 text-muted">No staff records found. Add your first one.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($records->hasPages())
    <div class="p-3">{{ $records->links() }}</div>
    @endif
</div>

{{-- ADD MODAL — creates user account + staff record --}}
<div class="modal fade" id="addStaffModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form method="POST" action="{{ route('staff.store') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header" style="background:#1B3A2D;">
                    <h5 class="modal-title text-white" style="font-family:'Lora',serif;">
                        <i class="bi bi-person-plus-fill me-2"></i>Add Staff
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    {{-- Account section --}}
                    <p class="fw-semibold mb-2" style="font-size:13px;color:#1B3A2D;border-bottom:1px solid #DDD3C0;padding-bottom:6px;">
                        <i class="bi bi-person-circle me-1"></i> Account Information
                    </p>
                    <div class="row g-3 mb-3">
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
                            <input type="text" class="form-control" value="Staff" disabled>
                        </div>
                    </div>

                    {{-- Record section --}}
                    <p class="fw-semibold mb-2" style="font-size:13px;color:#1B3A2D;border-bottom:1px solid #DDD3C0;padding-bottom:6px;">
                        <i class="bi bi-person-badge-fill me-1"></i> Employment Details
                    </p>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Employee ID</label>
                            <input type="text" name="employee_id" class="form-control" placeholder="EMP-001" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Position</label>
                            <input type="text" name="position" class="form-control" placeholder="Barista" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Department</label>
                            <input type="text" name="department" class="form-control" placeholder="Operations" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Branch</label>
                            <input type="text" name="branch" class="form-control" placeholder="Main Branch" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Hire Date</label>
                            <input type="date" name="hire_date" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Salary</label>
                            <input type="number" name="salary" class="form-control" placeholder="0.00" step="0.01" min="0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Status</label>
                            <select name="status" class="form-select" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="on_leave">On Leave</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Notes</label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="Optional notes..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-forest">
                        <i class="bi bi-person-check-fill me-1"></i> Create Staff Account
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- EDIT MODAL --}}
<div class="modal fade" id="editStaffModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form method="POST" id="editStaffForm">
            @csrf @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" style="font-family:'Lora',serif;">Edit Staff Record</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Full Name</label>
                            <input type="text" name="name" id="eName" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" name="email" id="eEmail" class="form-control">
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Employee ID</label>
                            <input type="text" name="employee_id" id="eEmpId" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Position</label>
                            <input type="text" name="position" id="ePosition" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Department</label>
                            <input type="text" name="department" id="eDept" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Branch</label>
                            <input type="text" name="branch" id="eBranch" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Hire Date</label>
                            <input type="date" name="hire_date" id="eHireDate" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Salary</label>
                            <input type="number" name="salary" id="eSalary" class="form-control" step="0.01" min="0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Status</label>
                            <select name="status" id="eStatus" class="form-select" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="on_leave">On Leave</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Notes</label>
                            <textarea name="notes" id="eNotes" class="form-control" rows="2"></textarea>
                        </div>
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

{{-- DELETE MODAL --}}
<div class="modal fade" id="deleteStaffModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <form method="POST" id="deleteStaffForm">
            @csrf @method('DELETE')
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title text-danger" style="font-family:'Lora',serif;">Delete Record</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body pt-0">
                    <p class="mb-0">Delete <strong id="deleteStaffName"></strong> and their user account? This cannot be undone.</p>
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
document.getElementById('editStaffModal').addEventListener('show.bs.modal', function(e) {
    const d = e.relatedTarget.dataset;
    document.getElementById('editStaffForm').action = `/staff/${d.id}`;
    document.getElementById('eName').value     = d.name;
    document.getElementById('eEmail').value    = d.email;
    document.getElementById('eEmpId').value    = d.employee_id;
    document.getElementById('ePosition').value = d.position;
    document.getElementById('eDept').value     = d.department;
    document.getElementById('eBranch').value   = d.branch;
    document.getElementById('eHireDate').value = d.hire_date;
    document.getElementById('eSalary').value   = d.salary;
    document.getElementById('eStatus').value   = d.status;
    document.getElementById('eNotes').value    = d.notes;
});
document.getElementById('deleteStaffModal').addEventListener('show.bs.modal', function(e) {
    const d = e.relatedTarget.dataset;
    document.getElementById('deleteStaffForm').action = `/staff/${d.id}`;
    document.getElementById('deleteStaffName').textContent = d.name;
});
</script>
@endsection
