@extends('layouts.app')

@section('title', 'Thông tin cá nhân - Cửa hàng rau củ quả sạch')

@section('content')
<div class="row">
    <div class="col-md-3">
        <div class="card mb-4">
            <div class="card-body text-center">
                <img src="{{ asset('images/avatars/default_avt.jpg') }}" alt="Avatar" class="rounded-circle img-fluid" style="width: 150px;">
                <h5 class="my-3">{{ Auth::user()->name }}</h5>
                <p class="text-muted mb-1">Khách hàng</p>
                <p class="text-muted mb-4">{{ Auth::user()->email }}</p>
                <div class="d-flex justify-content-center mb-2">
                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#changeAvatarModal">
                        Đổi ảnh đại diện
                    </button>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                        <a href="{{ url('/profile') }}" class="text-decoration-none text-dark w-100">
                            <i class="fas fa-user fa-fw me-3"></i>Thông tin cá nhân
                        </a>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                        <a href="{{ url('/orders') }}" class="text-decoration-none text-dark w-100">
                            <i class="fas fa-shopping-bag fa-fw me-3"></i>Đơn hàng của tôi
                        </a>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                        <a href="" class="text-decoration-none text-dark w-100">
                            <i class="fas fa-lock fa-fw me-3"></i>Đổi mật khẩu
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="col-md-9">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Thông tin cá nhân</h5>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Họ và tên</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', Auth::user()->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" value="{{ Auth::user()->email }}" readonly>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="phone" class="form-label">Số điện thoại</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', Auth::user()->phone) }}" required>
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="birthday" class="form-label">Ngày sinh</label>
                            <input type="date" class="form-control @error('birthday') is-invalid @enderror" id="birthday" name="birthday" value="{{ old('birthday', Auth::user()->date_of_birth) }}">
                            @error('birthday')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label d-block">Giới tính</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="gender" id="male" value="male" {{ old('gender', Auth::user()->gender) == 'male' ? 'checked' : '' }}>
                            <label class="form-check-label" for="male">Nam</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="gender" id="female" value="female" {{ old('gender', Auth::user()->gender) == 'female' ? 'checked' : '' }}>
                            <label class="form-check-label" for="female">Nữ</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="gender" id="other" value="other" {{ old('gender', Auth::user()->gender) == 'other' ? 'checked' : '' }}>
                            <label class="form-check-label" for="other">Khác</label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success">Cập nhật thông tin</button>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Địa chỉ mặc định</h5>
            </div>
            <div class="card-body">
                @if(isset($address))
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Họ tên:</strong> {{ $user->name }}</p>
                            <p><strong>Số điện thoại:</strong> {{ $user->phone }}</p>
                            <p><strong>Địa chỉ:</strong> {{ $user->address }}</p>
                        </div>
                        
                    </div>
        
                @else
                    <p>Bạn chưa có địa chỉ mặc định.</p>
                    <a href="" class="btn btn-primary">Thêm địa chỉ mới</a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
