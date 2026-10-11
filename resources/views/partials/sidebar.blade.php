{{-- resources/views/partials/sidebar.blade.php --}}
@push('styles')
<style>
    aside{
        grid-area:sidebar; background:var(--navy); padding:22px 14px;
        display:flex; flex-direction:column; gap:26px; overflow-y:auto;
    }
    .nav-group-label{
        font-size:10.5px; letter-spacing:.14em; text-transform:uppercase;
        color:#7A85AB; padding:0 12px; margin-bottom:9px; font-weight:600;
    }
    .nav-list{ list-style:none; display:flex; flex-direction:column; gap:3px; }
    .nav-list a{
        display:flex; align-items:center; gap:12px; padding:10px 12px; border-radius:8px;
        text-decoration:none; color:#B9C0DC; font-size:13.5px; font-weight:500;
        transition:background .15s ease, color .15s ease;
    }
    .nav-list a:hover{ background:var(--navy-2); color:#F5F1E8; }
    .nav-list a.active{ background:var(--gold-dim); color:var(--gold); box-shadow:inset 3px 0 0 var(--gold); }
    .nav-list a .icon{ width:17px; height:17px; flex-shrink:0; display:flex; align-items:center; justify-content:center; }
    .nav-list a .count{ margin-left:auto; font-family:'JetBrains Mono', monospace; font-size:10.5px; color:#7A85AB; }
    .nav-list a.active .count{ color:var(--gold); }
    .sidebar-divider{
        margin-top:auto; padding:14px 12px; border-top:1px solid var(--navy-border);
        font-size:11px; color:#7A85AB; line-height:1.5;
    }

    @media (max-width: 860px){
        aside{ display:none; }
    }
</style>
@endpush

<aside>
    {{-- Menu lấy từ bảng menus (vị trí Sidebar), quản lý tại trang /menu --}}
    @foreach ($sidebarNhoms as $nhom)
        <nav>
            <div class="nav-group-label">{{ $nhom['nhom'] }}</div>
            <ul class="nav-list">
                @foreach ($nhom['items'] as $menu)
                    <li><a href="{{ url($menu->url) }}" @class(['active' => $menu->dangChon()])>
                        <span class="icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">{!! $menu->iconSvg() !!}</svg>
                        </span>
                        {{ $menu->ten }}
                    </a></li>
                @endforeach
            </ul>
        </nav>
    @endforeach

    <div class="sidebar-divider">
        Học kỳ 1 · Năm học 2026–2027
    </div>
</aside>
