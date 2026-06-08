<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        <li class="nav-item nav-profile">
            <a href="#" class="nav-link">
                <div class="nav-profile-image">
                    <img src="{{ asset('assets/images/faces/face1.jpg') }}" alt="profile">
                    <span class="login-status online"></span>
                </div>
                <div class="nav-profile-text d-flex flex-column">
                    <span class="font-weight-bold mb-2">{{ optional(Auth::user())->name ?? 'Guest' }}</span>
                    <span class="text-secondary text-small">{{ optional(Auth::user())->email ?? '' }}</span>
                </div>
                <i class="mdi mdi-bookmark-check text-success nav-profile-badge"></i>
            </a>
        </li>
        
        <li class="nav-item {{ Request::is('/') || Request::is('home') ? 'active' : '' }}">
            <a class="nav-link" href="{{ url('/') }}">
                <span class="menu-title">Dashboard</span>
                <i class="mdi mdi-home menu-icon"></i>
            </a>
        </li>
        
        <li class="nav-item {{ Request::is('kategori*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ url('/kategori') }}">
                <span class="menu-title">Kategori</span>
                <i class="mdi mdi-folder menu-icon"></i>
            </a>
        </li>
        
        <li class="nav-item {{ Request::is('buku') ? 'active' : '' }}">
            <a class="nav-link" href="{{ url('/buku') }}">
                <span class="menu-title">Buku</span>
                <i class="mdi mdi-book-open-page-variant menu-icon"></i>
            </a>
        </li>

        <li class="nav-item {{ Request::is('buku-datatables') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('buku.datatables') }}">
                <span class="menu-title">Buku (DataTables)</span>
                <i class="mdi mdi-table-search menu-icon"></i>
            </a>
        </li>

        <li class="nav-item {{ Request::is('kategori/select-demo') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('kategori.select-demo') }}">
                <span class="menu-title">Demo Select</span>
                <i class="mdi mdi-format-list-bulleted menu-icon"></i>
            </a>
        </li>

        <li class="nav-item {{ Request::is('barang*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ url('/barang') }}">
                <span class="menu-title">Data Barang</span>
                <i class="mdi mdi-tag-multiple menu-icon"></i>
            </a>
        </li>

        <li class="nav-item {{ Request::is('barang/kasir') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('barang.kasir') }}">
                <span class="menu-title">Kasir POS</span>
                <i class="mdi mdi-cash-register menu-icon"></i>
            </a>
        </li>

        <li class="nav-item nav-category">
            <span class="nav-item-head">Mini Kantin</span>
        </li>

        <li class="nav-item {{ Request::is('kantin/customer') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('kantin.customer') }}" target="_blank">
                <span class="menu-title">Customer Order</span>
                <i class="mdi mdi-cart menu-icon"></i>
            </a>
        </li>

        <li class="nav-item {{ Request::is('kantin/vendor/menu') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('kantin.vendor.menu') }}">
                <span class="menu-title">Vendor - Master Menu</span>
                <i class="mdi mdi-food menu-icon"></i>
            </a>
        </li>

        <li class="nav-item {{ Request::is('kantin/vendor/pesanan-lunas') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('kantin.vendor.pesanan-lunas') }}">
                <span class="menu-title">Vendor - Pesanan Lunas</span>
                <i class="mdi mdi-cash-check menu-icon"></i>
            </a>
        </li>

        <li class="nav-item nav-category">
            <span class="nav-item-head">Dokumen PDF</span>
        </li>

        <li class="nav-item {{ Request::is('pdf/sertifikat*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('pdf.sertifikat') }}" target="_blank">
                <span class="menu-title">Sertifikat</span>
                <i class="mdi mdi-certificate menu-icon"></i>
            </a>
        </li>

        <li class="nav-item {{ Request::is('pdf/undangan*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('pdf.undangan') }}" target="_blank">
                <span class="menu-title">Undangan</span>
                <i class="mdi mdi-email-open menu-icon"></i>
            </a>
        </li>
        <li class="nav-item nav-category">
                <span class="nav-item-head">NFC</span>
        </li>

        <li class="nav-item {{ Request::is('nfc/scanner') ? 'active' : '' }}">
            <a class="nav-link" href="{{ url('/nfc/scanner') }}">
                <span class="menu-title">NFC Scanner</span>
                <i class="mdi mdi-cellphone-nfc menu-icon"></i>
            </a>
        </li>

        <li class="nav-item {{ Request::is('nfc/attendance*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('nfc.attendance.list') }}">
            <span class="menu-title">Absensi NFC</span>
            <i class="mdi mdi-clipboard-check menu-icon"></i>
        </a>
         </li>
        
        <li class="nav-item nav-category">
        <span class="nav-item-head">Geolocation</span>
     </li>

    <li class="nav-item {{ Request::is('kunjungan-toko*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('kunjungan.index') }}">
            <span class="menu-title">Kunjungan Toko</span>
            <i class="mdi mdi-map-marker-radius menu-icon"></i>
        </a>
    </li>

    <li class="nav-item {{ Request::is('toko*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('toko.index') }}">
            <span class="menu-title">Data Toko</span>
            <i class="mdi mdi-store menu-icon"></i>
        </a>
    </li>
    <li class="nav-item nav-category">
    <span class="nav-item-head">Server Sent Events</span>
    </li>

    <li class="nav-item">
        <a class="nav-link" href="/guest" target="_blank">
            <span class="menu-title">Guest Antrian</span>
            <i class="mdi mdi-account-plus menu-icon"></i>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link" href="/admin-antrian" target="_blank">
            <span class="menu-title">Admin Antrian</span>
            <i class="mdi mdi-account-tie menu-icon"></i>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link" href="/papan" target="_blank">
            <span class="menu-title">Papan Antrian</span>
            <i class="mdi mdi-monitor-dashboard menu-icon"></i>
        </a>
    </li>
    </ul>
</nav>
