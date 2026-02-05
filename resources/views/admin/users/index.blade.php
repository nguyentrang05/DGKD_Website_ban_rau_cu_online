@extends('layouts.admin')

@section('title', 'Quản lý người dùng')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">Quản lý người dùng</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Người dùng</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Thêm người dùng
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Tổng người dùng
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalUsers ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Hoạt động
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalUsers ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Tạm khóa
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $blockedUsers ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-times fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Mới tháng này
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalUsers ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-plus fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- <!-- Search and Filter -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Tìm kiếm và lọc</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.users.index') }}">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="search" class="form-label">Tìm kiếm</label>
                        <input type="text" class="form-control" id="search" name="search"
                               value="{{ request('search') }}" placeholder="Tên, email, số điện thoại...">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="status" class="form-label">Trạng thái</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">Tất cả</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Hoạt động</option>
                            <option value="blocked" {{ request('status') == 'blocked' ? 'selected' : '' }}>Tạm khóa</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="role" class="form-label">Vai trò</label>
                        <select class="form-select" id="role" name="role">
                            <option value="">Tất cả</option>
                            <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="customer" {{ request('role') == 'customer' ? 'selected' : '' }}>Khách hàng</option>
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i>
                            </button>
                            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                                <i class="fas fa-redo"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div> --}}

    <!-- Users Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Danh sách người dùng</h6>
            {{-- <div class="dropdown">
                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-download me-1"></i>Xuất dữ liệu
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#"><i class="fas fa-file-excel me-2"></i>Excel</a></li>
                    <li><a class="dropdown-item" href="#"><i class="fas fa-file-pdf me-2"></i>PDF</a></li>
                </ul>
            </div> --}}
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="usersTable">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Ảnh đại diện</th>
                            <th>Họ tên</th>
                            <th>Email</th>
                            <th>Số điện thoại</th>
                            <th>Vai trò</th>
                            <th>Trạng thái</th>
                            <th>Ngày tạo</th>
                            <th width="120">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users ?? [] as $user)
                        <tr>
                            <td>{{ $user->id ?? '' }}</td>
                            <td>
                                @if(isset($user) && $user->avatar)
                                    <img src="{{ asset('storage/' . $user->avatar) }}"
                                         alt="Avatar" class="rounded-circle" width="40" height="40">
                                @else
                                    <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center"
                                         style="width: 40px; height: 40px;">
                                        <i class="fas fa-user text-white"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="fw-bold">{{ $user->name ?? 'N/A' }}</div>
                                @if(isset($user) && $user->last_login_at)
                                    <small class="text-muted">
                                        Đăng nhập: {{ $user->last_login_at->format('d/m/Y H:i') }}
                                    </small>
                                @endif
                            </td>
                            <td>{{ $user->email ?? 'N/A' }}</td>
                            <td>{{ $user->phone ?? 'Chưa cập nhật' }}</td>
                            <td>
                                @if(isset($user) && $user->role == 'admin')
                                    <span class="badge bg-danger">Admin</span>
                                @else
                                    <span class="badge bg-primary">Khách hàng</span>
                                @endif
                            </td>
                            <td>
                                @if(isset($user) && $user->status != 'active')
                                    <span class="badge bg-success">Hoạt động</span>
                                @else
                                    <span class="badge bg-warning">Tạm khóa</span>
                                @endif
                            </td>
                            <td>{{ isset($user) && $user->created_at ? $user->created_at->format('d/m/Y') : 'N/A' }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-sm btn-outline-danger"
                                            onclick="confirmDelete({{ $user->id ?? 0 }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center py-4">
                                <i class="fas fa-users fa-2x text-muted mb-2"></i>
                                <p class="text-muted mb-0">Không có người dùng nào</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if(isset($users) && method_exists($users, 'links'))
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted">
                        Hiển thị {{ $users->firstItem() ?? 0 }} đến {{ $users->lastItem() ?? 0 }}
                        trong tổng số {{ $users->total() ?? 0 }} kết quả
                    </div>
                    <div>
                        {{ $users->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Bulk Actions -->
    <div class="card shadow" id="bulkActions" style="display: none;">
        <div class="card-body">
            <div class="d-flex align-items-center gap-3">
                <span class="fw-bold">Đã chọn: <span id="selectedCount">0</span> người dùng</span>
                <button type="button" class="btn btn-warning btn-sm" onclick="bulkToggleStatus()">
                    <i class="fas fa-toggle-on me-1"></i>Thay đổi trạng thái
                </button>
                <button type="button" class="btn btn-danger btn-sm" onclick="bulkDelete()">
                    <i class="fas fa-trash me-1"></i>Xóa được chọn
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thêm người dùng mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addUserForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="addName" class="form-label">Họ tên <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="addName" name="name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="addEmail" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="addEmail" name="email" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="addPhone" class="form-label">Số điện thoại</label>
                            <input type="tel" class="form-control" id="addPhone" name="phone">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="addRole" class="form-label">Vai trò <span class="text-danger">*</span></label>
                            <select class="form-select" id="addRole" name="role" required>
                                <option value="customer">Khách hàng</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="addPassword" class="form-label">Mật khẩu <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="addPassword" name="password" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="addPasswordConfirm" class="form-label">Xác nhận mật khẩu <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="addPasswordConfirm" name="password_confirmation" required>
                        </div>
                        <div class="col-12 mb-3">
                            <label for="addAddress" class="form-label">Địa chỉ</label>
                            <textarea class="form-control" id="addAddress" name="address" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Thêm người dùng</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View User Modal -->
<div class="modal fade" id="viewUserModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Chi tiết người dùng</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="viewUserContent">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Chỉnh sửa người dùng</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editUserForm">
                <div class="modal-body" id="editUserContent">
                    <!-- Content will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Cập nhật</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}
.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}
.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}
.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}
.text-xs {
    font-size: 0.7rem;
}
.font-weight-bold {
    font-weight: 700 !important;
}
.text-gray-800 {
    color: #5a5c69 !important;
}
.text-gray-300 {
    color: #dddfeb !important;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Select all functionality
    const selectAllCheckbox = document.getElementById('selectAll');
    const userCheckboxes = document.querySelectorAll('.user-checkbox');
    const bulkActions = document.getElementById('bulkActions');
    const selectedCount = document.getElementById('selectedCount');

    selectAllCheckbox.addEventListener('change', function() {
        userCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateBulkActions();
    });

    userCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateBulkActions);
    });

    function updateBulkActions() {
        const checkedBoxes = document.querySelectorAll('.user-checkbox:checked');
        const count = checkedBoxes.length;

        selectedCount.textContent = count;
        bulkActions.style.display = count > 0 ? 'block' : 'none';

        selectAllCheckbox.checked = count === userCheckboxes.length;
        selectAllCheckbox.indeterminate = count > 0 && count < userCheckboxes.length;
    }
});

function viewUser(userId) {
    // Ajax call to load user details
    fetch(`/admin/users/${userId}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('viewUserContent').innerHTML = `
                <div class="row">
                    <div class="col-md-4 text-center mb-3">
                        <img src="${data.avatar || '/images/default-avatar.png'}"
                             class="rounded-circle mb-2" width="100" height="100">
                        <h5>${data.name}</h5>
                        <span class="badge bg-${data.role === 'admin' ? 'danger' : 'primary'}">${data.role === 'admin' ? 'Admin' : 'Khách hàng'}</span>
                    </div>
                    <div class="col-md-8">
                        <table class="table table-sm">
                            <tr><th>Email:</th><td>${data.email}</td></tr>
                            <tr><th>Số điện thoại:</th><td>${data.phone || 'Chưa cập nhật'}</td></tr>
                            <tr><th>Địa chỉ:</th><td>${data.address || 'Chưa cập nhật'}</td></tr>
                            <tr><th>Trạng thái:</th><td><span class="badge bg-${data.status === 'active' ? 'success' : 'warning'}">${data.status === 'active' ? 'Hoạt động' : 'Tạm khóa'}</span></td></tr>
                            <tr><th>Ngày tạo:</th><td>${new Date(data.created_at).toLocaleDateString('vi-VN')}</td></tr>
                            <tr><th>Lần đăng nhập cuối:</th><td>${data.last_login_at ? new Date(data.last_login_at).toLocaleDateString('vi-VN') : 'Chưa đăng nhập'}</td></tr>
                        </table>
                    </div>
                </div>
            `;
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi tải thông tin người dùng');
        });
}

function editUser(userId) {
    // Ajax call to load user data for editing
    fetch(`/admin/users/${userId}/edit`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('editUserContent').innerHTML = `
                <input type="hidden" name="id" value="${data.id}">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="editName" class="form-label">Họ tên <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="editName" name="name" value="${data.name}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="editEmail" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="editEmail" name="email" value="${data.email}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="editPhone" class="form-label">Số điện thoại</label>
                        <input type="tel" class="form-control" id="editPhone" name="phone" value="${data.phone || ''}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="editRole" class="form-label">Vai trò <span class="text-danger">*</span></label>
                        <select class="form-select" id="editRole" name="role" required>
                            <option value="customer" ${data.role === 'customer' ? 'selected' : ''}>Khách hàng</option>
                            <option value="admin" ${data.role === 'admin' ? 'selected' : ''}>Admin</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="editStatus" class="form-label">Trạng thái <span class="text-danger">*</span></label>
                        <select class="form-select" id="editStatus" name="status" required>
                            <option value="active" ${data.status === 'active' ? 'selected' : ''}>Hoạt động</option>
                            <option value="blocked" ${data.status === 'blocked' ? 'selected' : ''}>Tạm khóa</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="editPassword" class="form-label">Mật khẩu mới (để trống nếu không đổi)</label>
                        <input type="password" class="form-control" id="editPassword" name="password">
                    </div>
                    <div class="col-12 mb-3">
                        <label for="editAddress" class="form-label">Địa chỉ</label>
                        <textarea class="form-control" id="editAddress" name="address" rows="2">${data.address || ''}</textarea>
                    </div>
                </div>
            `;
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi tải thông tin người dùng');
        });
}

function confirmDelete(userId) {
    if (confirm('Bạn có chắc chắn muốn xóa người dùng này?')) {
        // Ajax call to delete user
        fetch(`/admin/users/${userId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Có lỗi xảy ra khi xóa người dùng');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi xóa người dùng');
        });
    }
}

function bulkToggleStatus() {
    const checkedBoxes = document.querySelectorAll('.user-checkbox:checked');
    const userIds = Array.from(checkedBoxes).map(cb => cb.value);

    if (confirm(`Bạn có chắc chắn muốn thay đổi trạng thái của ${userIds.length} người dùng?`)) {
        // Ajax call to toggle status
        fetch('/admin/users/bulk-toggle-status', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ user_ids: userIds })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Có lỗi xảy ra khi thay đổi trạng thái');
            }
        });
    }
}

function bulkDelete() {
    const checkedBoxes = document.querySelectorAll('.user-checkbox:checked');
    const userIds = Array.from(checkedBoxes).map(cb => cb.value);

    if (confirm(`Bạn có chắc chắn muốn xóa ${userIds.length} người dùng?`)) {
        // Ajax call to delete users
        fetch('/admin/users/bulk-delete', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ user_ids: userIds })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Có lỗi xảy ra khi xóa người dùng');
            }
        });
    }
}

// Form submissions
document.getElementById('addUserForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);

    fetch('/admin/users', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Có lỗi xảy ra khi thêm người dùng');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Có lỗi xảy ra khi thêm người dùng');
    });
});

document.getElementById('editUserForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const userId = formData.get('id');

    fetch(`/admin/users/${userId}`, {
        method: 'PUT',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Có lỗi xảy ra khi cập nhật người dùng');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Có lỗi xảy ra khi cập nhật người dùng');
    });
});
</script>
@endpush
