<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        <li class="nav-item nav-profile">
            <a href="#" class="nav-link">
                <div class="nav-profile-image">
                    <img src="{{ asset('assets/images/faces/face1.jpg') }}" alt="profile">
                    <span class="login-status online"></span>
                </div>
                <div class="nav-profile-text d-flex flex-column">
                    <span class="font-weight-bold mb-2">{{ Auth::user()->name }}</span>
                    <span class="text-secondary text-small">{{ Auth::user()->email }}</span>
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
        
        <li class="nav-item {{ Request::is('buku*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ url('/buku') }}">
                <span class="menu-title">Buku</span>
                <i class="mdi mdi-book-open-page-variant menu-icon"></i>
            </a>
        </li>

        <li class="nav-item {{ Request::is('barang*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ url('/barang') }}">
                <span class="menu-title">Data Barang</span>
                <i class="mdi mdi-tag-multiple menu-icon"></i>
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
    </ul>
</nav>
