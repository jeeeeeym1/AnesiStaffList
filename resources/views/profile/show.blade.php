@extends('layouts.app')

@section('title', 'My Profile — Anesi Staff System')
@section('page-title', 'My Profile')

@section('content')
<div class="row g-4">
    {{-- Profile Card --}}
    <div class="col-md-4">
        <div class="table-card p-4 text-center">
            <div class="position-relative d-inline-block mb-3">
                <div style="width:90px;height:90px;border-radius:50%;background:#1B3A2D;color:#fff;display:flex;align-items:center;justify-content:center;font-size:32px;font-weight:700;margin:0 auto;overflow:hidden;border:3px solid #DDD3C0;">
                    @if($user->avatar)
                        <img src="{{ asset('storage/'.$user->avatar) }}" style="width:100%;height:100%;object-fit:cover;" id="avatarPreview">
                    @else
                        <span id="avatarInitial">{{ strtoupper(substr($user->name,0,1)) }}</span>
                        <img src="" style="width:100%;height:100%;object-fit:cover;display:none;" id="avatarPreview">
                    @endif
                </div>
            </div>
            <h5 style="font-family:'Lora',serif;font-weight:700;">{{ $user->name }}</h5>
            <p class="text-muted mb-1" style="font-size:13px;">{{ $user->email }}</p>
            <span class="badge" style="background:#e8f0eb;color:#1B3A2D;">{{ ucfirst($user->role ?? 'staff') }}</span>

            <hr class="my-3">
            <div class="text-start" style="font-size:13px;">
                @if($user->phone)
                <div class="d-flex gap-2 mb-2"><i class="bi bi-telephone-fill text-muted"></i> {{ $user->phone }}</div>
                @endif
                @if($user->gender)
                <div class="d-flex gap-2 mb-2"><i class="bi bi-gender-ambiguous text-muted"></i> {{ ucfirst($user->gender) }}</div>
                @endif
                @if($user->address)
                <div class="d-flex gap-2 mb-2"><i class="bi bi-geo-alt-fill text-muted"></i> {{ $user->address }}</div>
                @endif
                <div class="d-flex gap-2 text-muted"><i class="bi bi-calendar3"></i> Joined {{ $user->created_at->format('M Y') }}</div>
            </div>
        </div>
    </div>

    {{-- Edit Form --}}
    <div class="col-md-8">
        <div class="table-card p-4">
            <div class="table-card-header px-0 pt-0 mb-3">
                <h6 style="font-family:'Lora',serif;"><i class="bi bi-pencil-square me-2"></i>Edit Profile</h6>
            </div>

            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label class="form-label fw-semibold">Profile Picture</label>
                    <input type="file" name="avatar" id="avatarInput" class="form-control" accept="image/*">
                    <div class="form-text">Max 2MB. JPG, PNG, GIF.</div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Full Name</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name', $user->name) }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Email</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email', $user->email) }}" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Phone</label>
                        <input type="text" name="phone" class="form-control"
                            value="{{ old('phone', $user->phone) }}" placeholder="+63 9XX XXX XXXX">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Gender</label>
                        <select name="gender" class="form-select">
                            <option value="">— Select —</option>
                            <option value="male"   {{ old('gender',$user->gender)==='male'   ? 'selected':'' }}>Male</option>
                            <option value="female" {{ old('gender',$user->gender)==='female' ? 'selected':'' }}>Female</option>
                            <option value="other"  {{ old('gender',$user->gender)==='other'  ? 'selected':'' }}>Other</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Address</label>
                        <input type="text" name="address" class="form-control"
                            value="{{ old('address', $user->address) }}" placeholder="Street, City, Province">
                    </div>
                </div>

                <hr class="my-3">
                <p class="fw-semibold mb-2" style="font-size:14px;">Change Password <small class="text-muted fw-normal">(leave blank to keep current)</small></p>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">New Password</label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                            placeholder="Min. 8 characters" minlength="8">
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Confirm Password</label>
                        <input type="password" name="password_confirmation" class="form-control" placeholder="Repeat password">
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-forest px-4">
                        <i class="bi bi-check-lg me-1"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.getElementById('avatarInput').addEventListener('change', function() {
    const file = this.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = e => {
        const preview = document.getElementById('avatarPreview');
        const initial = document.getElementById('avatarInitial');
        preview.src = e.target.result;
        preview.style.display = 'block';
        if (initial) initial.style.display = 'none';
    };
    reader.readAsDataURL(file);
});
</script>
@endsection
