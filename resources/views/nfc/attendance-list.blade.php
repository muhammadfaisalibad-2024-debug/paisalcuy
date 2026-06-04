@extends('layouts.app')

@section('title', 'Daftar Absensi NFC')

@section('content')
<div class="container py-4">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Daftar Absensi NFC</h4>
            <div class="table-responsive">
                <table class="table table-sm table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Waktu</th>
                            <th>Serial</th>
                            <th>Owner</th>
                            <th>Status</th>
                            <th>Note</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rows as $row)
                            <tr>
                                <td>{{ $row->id }}</td>
                                <td>{{ $row->scanned_at }}</td>
                                <td>{{ $row->serial }}</td>
                                <td>{{ $row->nfcCard->owner_name ?? '-' }}</td>
                                <td>{{ $row->status }}</td>
                                <td>{{ $row->note }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $rows->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
