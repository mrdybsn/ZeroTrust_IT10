@extends('layouts.app')

@section('page-title', 'USER MANAGEMENT')

@section('content')
<div style="display:flex;justify-content:flex-end;margin-bottom:20px;">
    <button class="btn btn-primary" onclick="document.getElementById('addModal').classList.add('open')">+ ADD USER</button>
</div>

<div class="card">
    <div class="card-title">◉ REGISTERED AGENTS</div>
    <table class="data-table">
        <thead>
            <tr><th>#</th><th>Full Name</th><th>Username</th><th>Email</th><th>Role</th><th>Status</th><th>2FA</th><th>Actions</th></tr>
        </thead>
        <tbody>
        @foreach($users as $u)
            <tr>
                <td class="mono">{{ $u->id }}</td>
                <td>{{ $u->fullname }}</td>
                <td class="mono">{{ $u->username }}</td>
                <td class="mono" style="font-size:11px;">{{ $u->email ?? '—' }}</td>
                <td><span class="badge badge-{{ $u->role }}">{{ strtoupper($u->role) }}</span></td>
                <td><span class="badge badge-{{ $u->status }}">{{ strtoupper($u->status) }}</span></td>
                <td>{{ $u->two_factor_enabled ? 'ON' : 'OFF' }}</td>
                <td>
                    <button class="btn btn-warning btn-sm" onclick='openEdit(@json($u))'>EDIT</button>
                    @if($u->id !== auth()->id())
                    <form method="POST" action="{{ route('admin.users.destroy', $u) }}" style="display:inline;" onsubmit="return confirm('Delete this user?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">DEL</button>
                    </form>
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

<div class="modal" id="addModal">
    <div class="modal-box">
        <div class="card-title">ADD USER</div>
        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf
            <div class="field"><label class="field-label">FULL NAME</label><input name="fullname" required></div>
            <div class="field"><label class="field-label">USERNAME</label><input name="username" required maxlength="50"></div>
            <div class="field"><label class="field-label">EMAIL (for password reset)</label><input type="email" name="email"></div>
            <div class="field"><label class="field-label">PASSWORD</label><input type="password" name="password" id="addPassword" required minlength="8"><div class="strength-meter"><div class="bar" id="addPwBar"></div></div></div>
            <div class="field"><label class="field-label">ROLE</label><select name="role"><option value="player">Player</option><option value="admin">Admin</option></select></div>
            <div class="modal-actions">
                <button type="submit" class="btn btn-primary">CREATE</button>
                <button type="button" class="btn" onclick="document.getElementById('addModal').classList.remove('open')">CANCEL</button>
            </div>
        </form>
    </div>
</div>

<div class="modal" id="editModal">
    <div class="modal-box">
        <div class="card-title">EDIT USER</div>
        <form method="POST" id="editForm">
            @csrf @method('PUT')
            <input type="hidden" name="id" id="editId">
            <div class="field"><label class="field-label">FULL NAME</label><input name="fullname" id="editFullname" required></div>
            <div class="field"><label class="field-label">USERNAME</label><input name="username" id="editUsername" required></div>
            <div class="field"><label class="field-label">EMAIL</label><input type="email" name="email" id="editEmail"></div>
            <div class="field"><label class="field-label">NEW PASSWORD (leave blank to keep)</label><input type="password" name="password" minlength="8"></div>
            <div class="field"><label class="field-label">ROLE</label><select name="role" id="editRole"><option value="player">Player</option><option value="admin">Admin</option></select></div>
            <div class="field"><label class="field-label">STATUS</label><select name="status" id="editStatus"><option value="active">Active</option><option value="inactive">Inactive</option></select></div>
            <div class="field"><label><input type="checkbox" name="two_factor_enabled" value="1" id="edit2fa"> Enable 2FA (bonus)</label></div>
            <div class="modal-actions">
                <button type="submit" class="btn btn-primary">SAVE</button>
                <button type="button" class="btn" onclick="document.getElementById('editModal').classList.remove('open')">CANCEL</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openEdit(u) {
    document.getElementById('editForm').action = '{{ url('admin/users') }}/' + u.id;
    document.getElementById('editId').value = u.id;
    document.getElementById('editFullname').value = u.fullname;
    document.getElementById('editUsername').value = u.username;
    document.getElementById('editEmail').value = u.email || '';
    document.getElementById('editRole').value = u.role;
    document.getElementById('editStatus').value = u.status;
    document.getElementById('edit2fa').checked = !!u.two_factor_enabled;
    document.getElementById('editModal').classList.add('open');
}
document.getElementById('addPassword')?.addEventListener('input', function() {
    const v = this.value; const bar = document.getElementById('addPwBar');
    let s = 0; if (v.length>=8)s++; if(/[A-Z]/.test(v))s++; if(/[0-9]/.test(v))s++; if(/[^A-Za-z0-9]/.test(v))s++;
    bar.style.width = ['25%','50%','75%','100%'][s-1] || '10%';
    bar.style.background = ['#ff2d6b','#ffe700','#00ffe7','#0f0'][s-1] || '#456070';
});
</script>
@endpush
