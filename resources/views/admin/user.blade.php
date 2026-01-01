@extends('layout-admin')

@section('content')
<div class="container-fluid py-4">

    {{-- HEADER --}}
    <div class="card mb-4">
        <div class="card-body d-flex justify-content-between align-items-center">
            <h5 class="mb-0">{{ $title ?? 'Manajemen User' }}</h5>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahUser">
                <i class="bx bx-plus"></i> Tambah User
            </button>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="userTable" class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th width="10%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $i => $usr)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td><strong>{{ $usr->name }}</strong></td>
                            <td>{{ $usr->email }}</td>
                            <td>
                                <span class="badge rounded-pill
                                    @if($usr->role=='admin') bg-primary
                                    @elseif($usr->role=='teacher') bg-success
                                    @else bg-info @endif">
                                    {{ ucfirst($usr->role) }}
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-danger"
                                        data-bs-toggle="modal"
                                        data-bs-target="#deleteUserModal"
                                        data-id="{{ $usr->id }}"
                                        data-name="{{ $usr->name }}">
                                    <i class="bx bx-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

{{-- ===============================
MODAL KONFIRMASI HAPUS (SATU SAJA)
=============================== --}}
<div class="modal fade" id="deleteUserModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" id="deleteUserForm" class="modal-content">
            @csrf
            @method('DELETE')

            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close btn-close-white"
                        data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <p>
                    Apakah Anda yakin ingin menghapus user
                    <strong id="deleteUserName"></strong>?
                </p>
                <p class="text-danger mb-0">
                    Tindakan ini tidak dapat dibatalkan.
                </p>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">
                    Batal
                </button>
                <button type="submit" class="btn btn-danger">
                    Ya, Hapus
                </button>
            </div>

        </form>
    </div>
</div>

{{-- ===============================
MODAL TAMBAH USER
=============================== --}}
<div class="modal fade" id="modalTambahUser" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form method="POST" action="{{ route('admin.users.store') }}" class="modal-content">
            @method('POST')
            @csrf

            <div class="modal-header">
                <h5 class="modal-title">Tambah User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Nama</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Role</label>
                        <select name="role" class="form-select role-select" required>
                            <option value="">-- Pilih Role --</option>
                            <option value="admin">Admin</option>
                            <option value="teacher">Teacher</option>
                            <option value="student">Student</option>
                        </select>
                    </div>
                </div>

                <div class="row g-3 role-student d-none">
                    <div class="col-md-6">
                        <label class="form-label">NIS</label>
                        <input type="text" name="nis" class="form-control">
                    </div>
                </div>

                <div class="row g-3 role-teacher d-none">
                    <div class="col-md-6">
                        <label class="form-label">NIP</label>
                        <input type="text" name="nip" class="form-control">
                    </div>
                </div>

                <div class="row g-3 role-shared d-none">
                    <div class="col-md-6">
                        <label class="form-label">Alamat</label>
                        <input type="text" name="address" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">No. HP</label>
                        <input type="text" name="phone" class="form-control">
                    </div>
                </div>

            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Batal</button>
                <button class="btn btn-primary" type="submit">Simpan</button>
            </div>

        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(function () {
    $('#userTable').DataTable({
        pageLength: 10,
        ordering: false
    });
});

/* ROLE HANDLER */
function toggleRoleForm(select) {
    const form = select.closest('form');
    form.querySelectorAll('.role-student,.role-teacher,.role-shared')
        .forEach(el => el.classList.add('d-none'));

    if (select.value === 'student') {
        form.querySelector('.role-student').classList.remove('d-none');
        form.querySelector('.role-shared').classList.remove('d-none');
    }

    if (select.value === 'teacher') {
        form.querySelector('.role-teacher').classList.remove('d-none');
        form.querySelector('.role-shared').classList.remove('d-none');
    }
}

document.addEventListener('change', e => {
    if (e.target.classList.contains('role-select')) {
        toggleRoleForm(e.target);
    }
});

/* DELETE MODAL */
document.getElementById('deleteUserModal')
.addEventListener('show.bs.modal', function (e) {
    const btn = e.relatedTarget;
    document.getElementById('deleteUserName').textContent =
        btn.getAttribute('data-name');

    document.getElementById('deleteUserForm').action =
        `/admin/users/${btn.getAttribute('data-id')}`;
});
</script>
@endpush
