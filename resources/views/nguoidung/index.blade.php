{{-- resources/views/nguoidung/index.blade.php --}}
@extends('layouts.layoutmaster')

@push('styles')
<style>
    table{ border-collapse: collapse; width: 100%; }
    th, td{ text-align: left; padding: 10px 12px; border-bottom: 1px solid var(--line); vertical-align: middle; }
    th{ background-color: var(--navy); color: #F5F1E8; white-space: nowrap; }
    tr:nth-child(even){ background-color: var(--paper-2); }
    .badge{ display:inline-block; padding:3px 10px; border-radius:20px; font-size:11.5px; font-weight:600; }
    .badge-active{ background:#DFF3E3; color:#1E7B3C; }
    .badge-inactive{ background:#FBEAEA; color:#A33; }
    .badge-admin{ background:#16213E; color:#C9A227; }
    .badge-gv{ background:#E7EAF6; color:var(--navy-2); }
    .you{ font-size:11px; color:var(--ink-muted); margin-left:4px; }

    .table-toolbar{ display:flex; align-items:center; gap:10px; justify-content:space-between; margin-bottom:14px; flex-wrap:wrap; }
    .filter-form{ display:flex; gap:8px; flex-wrap:wrap; flex:1; }
    .filter-form input[type="text"], .filter-form select{
        padding:8px 12px; border:1px solid var(--line); border-radius:8px;
        background:#fff; color:var(--ink); font-size:13px; font-family:'Inter', sans-serif; outline:none; transition:border-color .15s;
    }
    .filter-form input[type="text"]:focus, .filter-form select:focus{ border-color:var(--gold); }
    .filter-form input[type="text"]{ min-width:220px; }
    .btn-filter{ padding:8px 16px; border:none; border-radius:8px; background:var(--navy); color:#F5F1E8; font-size:13px; font-weight:600; cursor:pointer; }
    .btn-filter:hover{ background:var(--navy-2); }
    .btn-reset{ padding:8px 14px; border:1px solid var(--line); border-radius:8px; background:#fff; color:var(--ink-muted); font-size:13px; text-decoration:none; display:inline-flex; align-items:center; }
    .btn-reset:hover{ border-color:var(--gold); color:var(--ink); }
    .btn-add{ display:inline-flex; align-items:center; gap:6px; background: var(--navy); color:#F5F1E8; padding:9px 18px; border-radius:8px; font-size:13.5px; font-weight:600; text-decoration:none; white-space:nowrap; }
    .btn-add:hover{ background: var(--navy-2); color:#F5F1E8; }

    th a.sort-link{ color:#F5F1E8; text-decoration:none; display:inline-flex; align-items:center; gap:4px; }
    th a.sort-link:hover{ color:var(--gold); }
    th a.sort-link .arrow{ font-size:11px; opacity:.6; }
    th a.sort-link.active-asc .arrow::after  { content:'▲'; opacity:1; color:var(--gold); }
    th a.sort-link.active-desc .arrow::after { content:'▼'; opacity:1; color:var(--gold); }
    th a.sort-link:not(.active-asc):not(.active-desc) .arrow::after { content:'⇅'; }

    .per-page-select{ padding:8px 12px; border:1px solid var(--line); border-radius:8px; background:#fff; color:var(--ink); font-size:13px; cursor:pointer; outline:none; }

    .action-group{ display:flex; gap:8px; }
    .btn-edit, .btn-delete{ display:inline-flex; align-items:center; justify-content:center; padding:6px 14px; border-radius:6px; font-size:12.5px; font-weight:600; text-decoration:none; border:1px solid transparent; cursor:pointer; }
    .btn-edit{ background:#FFF6E4; color:#8A6A0E; border-color:#E9D6A0; }
    .btn-edit:hover{ background:#FCEBC4; color:#8A6A0E; }
    .btn-delete{ background:#FBEAEA; color:#A33; border-color:#E8B4B4; }
    .btn-delete:hover{ background:#F6D6D6; }

    .table-footer{ display:flex; align-items:center; justify-content:space-between; margin-top:16px; flex-wrap:wrap; gap:10px; }
    .result-count{ font-size:12.5px; color:var(--ink-muted); }
</style>
@endpush

@section('content')
    <div class="page-title">Quản lý người dùng</div>
    <div class="page-sub">Tài khoản quản trị viên và giảng viên của hệ thống.</div>

    <div class="table-toolbar">
        <form class="filter-form" method="GET" action="{{ route('nguoidung.index') }}">
            <input type="hidden" name="per_page" value="{{ $perPage }}">
            <input type="hidden" name="sort_by"  value="{{ $sortBy }}">
            <input type="hidden" name="sort_dir" value="{{ $sortDir }}">

            <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm họ tên, email…">

            <select name="role">
                <option value="">Tất cả vai trò</option>
                @foreach ($roleOptions as $key => $label)
                    <option value="{{ $key }}" {{ request('role') === $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>

            <select name="trang_thai">
                <option value="">Tất cả trạng thái</option>
                <option value="1" {{ request('trang_thai') === '1' ? 'selected' : '' }}>Đang hoạt động</option>
                <option value="0" {{ request('trang_thai') === '0' ? 'selected' : '' }}>Đã khóa</option>
            </select>

            <button type="submit" class="btn-filter">Lọc</button>

            @if (request()->anyFilled(['search', 'role', 'trang_thai']))
                <a href="{{ route('nguoidung.index') }}" class="btn-reset">✕ Xóa lọc</a>
            @endif
        </form>

        <a href="{{ route('nguoidung.create') }}" class="btn-add">+ Thêm người dùng</a>
    </div>

    @php
        $sortUrl = function (string $col) use ($sortBy, $sortDir): string {
            $dir = ($sortBy === $col && $sortDir === 'asc') ? 'desc' : 'asc';
            return request()->fullUrlWithQuery(['sort_by' => $col, 'sort_dir' => $dir, 'page' => 1]);
        };
        $sortClass = fn (string $col): string => $sortBy !== $col ? '' : ($sortDir === 'asc' ? 'active-asc' : 'active-desc');
    @endphp

    <table>
        <tr>
            <th><a href="{{ $sortUrl('id') }}" class="sort-link {{ $sortClass('id') }}"># <span class="arrow"></span></a></th>
            <th><a href="{{ $sortUrl('name') }}" class="sort-link {{ $sortClass('name') }}">Họ tên <span class="arrow"></span></a></th>
            <th><a href="{{ $sortUrl('email') }}" class="sort-link {{ $sortClass('email') }}">Email <span class="arrow"></span></a></th>
            <th><a href="{{ $sortUrl('role') }}" class="sort-link {{ $sortClass('role') }}">Vai trò <span class="arrow"></span></a></th>
            <th><a href="{{ $sortUrl('trang_thai') }}" class="sort-link {{ $sortClass('trang_thai') }}">Trạng thái <span class="arrow"></span></a></th>
            <th><a href="{{ $sortUrl('created_at') }}" class="sort-link {{ $sortClass('created_at') }}">Ngày tạo <span class="arrow"></span></a></th>
            <th>Thao tác</th>
        </tr>

        @forelse ($users as $user)
        <tr>
            <td>{{ $user->id }}</td>
            <td>
                {{ $user->name }}
                @if ($user->id === auth()->id()) <span class="you">(bạn)</span> @endif
            </td>
            <td>{{ $user->email }}</td>
            <td>
                <span class="badge {{ $user->isAdmin() ? 'badge-admin' : 'badge-gv' }}">{{ $user->tenVaiTro() }}</span>
            </td>
            <td>
                @if ($user->trang_thai)
                    <span class="badge badge-active">Đang hoạt động</span>
                @else
                    <span class="badge badge-inactive">Đã khóa</span>
                @endif
            </td>
            <td>{{ $user->created_at?->format('d/m/Y') }}</td>
            <td>
                <div class="action-group">
                    <a href="{{ route('nguoidung.edit', $user->id) }}" class="btn-edit">Sửa</a>
                    @if ($user->id !== auth()->id())
                        <form action="{{ route('nguoidung.destroy', $user->id) }}" method="POST"
                              onsubmit="return confirm('Bạn có chắc muốn xóa người dùng {{ $user->name }} không?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-delete">Xóa</button>
                        </form>
                    @endif
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="7" style="text-align:center; color:var(--ink-muted); padding:24px;">
                Không tìm thấy người dùng nào.
            </td>
        </tr>
        @endforelse
    </table>

    <div class="table-footer">
        <span class="result-count">
            Hiển thị {{ $users->firstItem() ?? 0 }}–{{ $users->lastItem() ?? 0 }}
            / {{ $users->total() }} người dùng
        </span>

        <div style="display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
            <select class="per-page-select"
                onchange="
                    const url = new URL(window.location.href);
                    url.searchParams.set('per_page', this.value);
                    url.searchParams.delete('page');
                    window.location.href = url.toString();
                ">
                @foreach ([10, 25, 50] as $option)
                    <option value="{{ $option }}" {{ $perPage === $option ? 'selected' : '' }}>{{ $option }} / trang</option>
                @endforeach
            </select>

            {{ $users->links() }}
        </div>
    </div>
@endsection
