@extends('layouts.admin')

@section('title', 'Cài Đặt Hệ Thống')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('js/admin-settings.js') }}"></script>
@endpush

@section('content')
    <div class="admin-content container-fluid">
        <div class="page-header mb-4">
            <h1>Cài Đặt Hệ Thống</h1>
            <p>Quản lý các thông tin cấu hình website</p>
        </div>

        {{-- Hiển thị thông báo thành công --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Form cập nhật cài đặt --}}
        <form id="settingsForm" action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row">
                @foreach ($settingsByGroup as $group => $settings)
                    <div class="col-md-12 mb-4">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-{{ getGroupIcon($group) }} me-2"></i>
                                    {{ getGroupLabel($group) }}
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    @foreach ($settings as $setting)
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">
                                                {{ $setting->label }}
                                                @if ($setting->description)
                                                    <small class="text-muted d-block">{{ $setting->description }}</small>
                                                @endif
                                            </label>

                                            {{-- Loại input theo type --}}
                                            @if ($setting->type === 'textarea')
                                                <textarea name="settings[{{ $setting->key }}]" class="form-control" rows="3">{{ $setting->value }}</textarea>
                                            @elseif($setting->type === 'boolean')
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="settings[{{ $setting->key }}]" id="{{ $setting->key }}"
                                                        value="1" {{ $setting->value == '1' ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="{{ $setting->key }}">Kích
                                                        hoạt</label>
                                                </div>
                                            @elseif($setting->type === 'image')
                                                <input type="file" name="settings[{{ $setting->key }}]"
                                                    class="form-control mb-2" accept="image/*"
                                                    onchange="previewSettingImage(event, '{{ $setting->key }}')">
                                                <div id="preview-{{ $setting->key }}" class="mt-2"
                                                    style="{{ $setting->value ? '' : 'display:none' }}">
                                                    <img src="{{ $setting->value ? asset('images/' . $setting->value) : '' }}"
                                                        alt="{{ $setting->label }}"
                                                        style="max-width:200px;max-height:100px;border:1px solid #ddd;border-radius:4px;padding:5px;">
                                                </div>
                                            @elseif($setting->type === 'number')
                                                <input type="number" name="settings[{{ $setting->key }}]"
                                                    class="form-control" value="{{ $setting->value }}" min="0"
                                                    step="1000">
                                            @else
                                                <input type="text" name="settings[{{ $setting->key }}]"
                                                    class="form-control" value="{{ $setting->value }}">
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Nút lưu --}}
            <div class="text-end mt-4">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-save me-2"></i> Lưu Tất Cả Cài Đặt
                </button>
            </div>
        </form>

        {{-- Khu vực dữ liệu & bảo mật --}}
        <div class="mt-5">
            <div class="card border-danger">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="fas fa-database me-2"></i>Dữ Liệu & Bảo Mật</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <button class="btn btn-outline-primary w-100" type="button" onclick="exportData()">
                                <i class="fas fa-download me-2"></i>Xuất Dữ Liệu
                            </button>
                        </div>
                        <div class="col-md-4">
                            <button class="btn btn-outline-info w-100" type="button" onclick="backupData()">
                                <i class="fas fa-save me-2"></i>Sao Lưu Dữ Liệu
                            </button>
                        </div>
                        <div class="col-md-4">
                            <button class="btn btn-outline-danger w-100" type="button" onclick="confirmClearData()">
                                <i class="fas fa-trash me-2"></i>Xóa Tất Cả Dữ Liệu
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
