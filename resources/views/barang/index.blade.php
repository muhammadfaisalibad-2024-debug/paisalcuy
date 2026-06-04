@extends('layouts.app')

@section('title', 'Data Barang')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
@endpush

@section('content')
<div class="row">
    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="card-title mb-0">Data Barang UMKM</h4>
                    <a href="{{ route('barang.create') }}" class="btn btn-primary btn-sm">
                        <i class="mdi mdi-plus"></i> Tambah Barang
                    </a>
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert">
                            <span>&times;</span>
                        </button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert">
                            <span>&times;</span>
                        </button>
                    </div>
                @endif

                {{-- Form cetak: disubmit oleh JS setelah modal dikonfirmasi --}}
                <form id="formCetak" action="{{ route('barang.cetak-pdf') }}" method="POST" target="_blank">
                    @csrf
                    <div id="hiddenIds"></div>
                    <input type="hidden" name="x" id="inputX">
                    <input type="hidden" name="y" id="inputY">
                </form>

                <div class="mb-2">
                    <button type="button" id="btnCetak" class="btn btn-warning btn-sm"
                        onclick="bukaModalCetak()">
                        <i class="mdi mdi-printer"></i> Cetak Tag Harga Terpilih (<span id="countSelected">0</span>)
                    </button>
                    <button type="button" id="btnPilihSemua" class="btn btn-outline-secondary btn-sm ml-1">
                        Pilih Semua
                    </button>
                    <button type="button" id="btnBatalSemua" class="btn btn-outline-secondary btn-sm ml-1">
                        Batal Semua
                    </button>
                </div>

                    <div class="table-responsive">
                        <table id="tblBarang" class="table table-bordered table-hover table-striped">
                            <thead class="thead-dark">
                                <tr>
                                    <th width="30"><input type="checkbox" id="checkAll"></th>
                                    <th>ID Barang</th>
                                    <th>Nama Barang</th>
                                    <th>Harga</th>
                                    <th>Satuan</th>
                                    <th>Kategori</th>
                                    <th>Stok</th>
                                    <th width="130">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($barangs as $barang)
                                <tr>
                                    <td class="text-center">
                                        <input type="checkbox" class="chk-barang" name="selected_ids[]"
                                            value="{{ $barang->id_barang }}">
                                    </td>
                                    <td><small class="text-muted">{{ $barang->id_barang }}</small></td>
                                    <td>{{ $barang->nama_barang }}</td>
                                    <td>{{ $barang->harga_rupiah }}</td>
                                    <td>{{ $barang->satuan }}</td>
                                    <td>{{ $barang->kategori ?? '-' }}</td>
                                    <td>{{ number_format($barang->stok) }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('barang.edit', $barang->id_barang) }}"
                                            class="btn btn-info btn-sm" title="Edit">
                                            <i class="mdi mdi-pencil"></i>
                                        </a>
                                        <form action="{{ route('barang.destroy', $barang->id_barang) }}"
                                            method="POST" class="d-inline"
                                            onsubmit="return confirm('Hapus barang ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                                <i class="mdi mdi-delete"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted">Belum ada data barang.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- Modal koordinat cetak --}}
<div class="modal fade" id="modalKoordinat" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title"><i class="mdi mdi-printer"></i> Posisi Awal Label</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small mb-3">Kertas TnJ No.108 — 5 kolom (X) × 8 baris (Y).<br>
                    Kosongkan slot sebelum posisi ini.</p>
                <div class="form-group">
                    <label class="font-weight-bold">Kolom (X) <small class="text-muted">1–5</small></label>
                    <input type="number" id="modalX" class="form-control" value="1" min="1" max="5">
                </div>
                <div class="form-group">
                    <label class="font-weight-bold">Baris (Y) <small class="text-muted">1–8</small></label>
                    <input type="number" id="modalY" class="form-control" value="1" min="1" max="8">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger btn-sm" onclick="submitCetak()">
                    <i class="mdi mdi-file-pdf"></i> Download PDF
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<script>
$(document).ready(function () {
    $('#tblBarang').DataTable({
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
        },
        columnDefs: [{ orderable: false, targets: [0, 7] }]
    });

    function updateCount() {
        var count = $('.chk-barang:checked').length;
        $('#countSelected').text(count);
        // Visual feedback: dim button saat belum ada yang dipilih
        if (count > 0) {
            $('#btnCetak').removeClass('btn-secondary').addClass('btn-warning');
        } else {
            $('#btnCetak').removeClass('btn-warning').addClass('btn-secondary');
        }
    }

    // Delegasi event — DataTables merender ulang DOM
    $(document).on('change', '.chk-barang', updateCount);

    $('#checkAll').on('change', function () {
        $('.chk-barang').prop('checked', this.checked);
        updateCount();
    });

    $('#btnPilihSemua').on('click', function () {
        $('.chk-barang').prop('checked', true);
        $('#checkAll').prop('checked', true);
        updateCount();
    });

    $('#btnBatalSemua').on('click', function () {
        $('.chk-barang, #checkAll').prop('checked', false);
        updateCount();
    });

    updateCount();
});

function bukaModalCetak() {
    var count = $('.chk-barang:checked').length;
    if (count === 0) {
        alert('Pilih minimal 1 barang terlebih dahulu!');
        return;
    }
    $('#modalX').val(1);
    $('#modalY').val(1);
    $('#modalKoordinat').modal('show');
}

function submitCetak() {
    var x = parseInt($('#modalX').val());
    var y = parseInt($('#modalY').val());
    var checked = $('.chk-barang:checked');

    if (isNaN(x) || x < 1 || x > 5 || isNaN(y) || y < 1 || y > 8) {
        alert('Kolom X harus 1–5 dan Baris Y harus 1–8!');
        return;
    }

    $('#inputX').val(x);
    $('#inputY').val(y);

    var container = $('#hiddenIds');
    container.empty();
    checked.each(function () {
        container.append('<input type="hidden" name="ids[]" value="' + $(this).val() + '">');
    });

    $('#modalKoordinat').modal('hide');
    // Tunggu modal selesai menutup baru submit
    $('#modalKoordinat').one('hidden.bs.modal', function () {
        $('#formCetak').submit();
    });
}
</script>
@endpush
