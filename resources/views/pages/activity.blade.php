@extends('layouts.master')

@section('page', 'catatan aktivitas')

@section('title', 'Catatan Aktivitas')

@section('content')
    <style>
        /* Mobile: 0 - 575.98px */
        @media (max-width: 575.98px) {
            .container,
            .card-body,
            table,
            table th,
            table td,
            .pagination {
                font-size: 0.5rem;
            }

            table.table td,
            table.table th {
                padding: 0.35rem 0.4rem;
                vertical-align: middle;
            }

            .pagination li a,
            .pagination li span {
                padding: 0.2rem 0.4rem;
                font-size: 0.75rem;
            }

            .table-responsive-sm {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                margin-left: -8px;
                margin-right: -8px;
                padding-left: 8px;
                padding-right: 8px;
            }

            .pagination-outer {
                width: 100%;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            .pagination-container {
                min-width: max-content;
                display: flex;
                justify-content: center;
            }

            .pagination {
                flex-wrap: nowrap;
                font-size: 0.8rem;
            }

            .pagination .page-item {
                margin: 2px;
            }

            .pagination .page-link {
                padding: 0.4rem 0.7rem;
                white-space: nowrap;
            }

            .modal-dialog {
                max-width: 70%; /* modal lebih ramping dari lebar layar */
                margin: 1.5rem auto;
            }

            .modal-content {
                max-height: 90vh;
                overflow-y: auto;
                border-radius: 0.75rem;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            }

            .modal-header {
                background-color: #29B6A5; /* warna hijau toska */
                color: white;
            }

            .modal-title {
                font-size: 1rem;
                font-weight: 600;
            }

            .close {
                font-size: 1.25rem;
                color: white;
            }

            .modal-body label {
                font-size: 0.8rem;
                margin-bottom: 0.25rem;
            }

            .modal-body select.form-control {
                font-size: 0.75rem;
                padding: 0.25rem 0.5rem;
                height: calc(1.5em + 0.75rem + 2px);
            }

            .modal-body .form-group {
                margin-bottom: 0.75rem;
            }

            body.modal-open {
                overflow: hidden;
            }
        }

        /* Tablet: 576px - 991.98px */
        @media (min-width: 576px) and (max-width: 991.98px) {
            .pagination-outer {
                width: 100%;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            .pagination-container {
                min-width: max-content;
                display: flex;
                justify-content: center;
            }

            .pagination {
                flex-wrap: nowrap;
                font-size: 0.85rem;
            }

            .pagination .page-link {
                padding: 0.4rem 0.7rem;
                white-space: nowrap;
            }

            .pagination .page-item {
                margin: 2px;
            }
        }

        /* Desktop: 992px ke atas */
        @media (min-width: 992px) {
            .pagination-outer {
                overflow-x: hidden;
            }

            .pagination-container {
                min-width: unset;
                justify-content: center;
                width: 100%;
            }

            .pagination {
                flex-wrap: wrap;
                font-size: 1rem;
            }

            .pagination .page-link {
                padding: 0.5rem 0.9rem;
            }
        }
    </style>

    <div class="container">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive-sm">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th scope="row">No</th>
                                <th>Aktivitas</th>
                                <th>Pengguna</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($logs as $log)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $log->description }}</td>
                                    <td>{{ strtoupper($log->causer?->getRoleNames()->first() ?? '-') }}@if ($log->causer?->hasRole('rw')) {{ $log->causer?->rwDetail->name ?? '' }} @elseif ($log->causer?->hasRole('rt')) {{ $log->causer?->rtDetail->name ?? '' }} @endif
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($log->updated_at)->timezone('Asia/Jakarta')->translatedFormat('d F Y H:i:s') }}</td>        
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4">Data Tidak Ada</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($logs->hasPages())
                    <div class="pagination-outer mt-3">
                        <div class="pagination-container">
                            {!! $logs->links('pagination::bootstrap-4') !!}
                        </div>
                    </div>
                @endif
                {{-- @if ($logs->hasPages())
                    <div class="pagination-container mt-3">
                        <div class="pagination-wrapper">
                            {!! $logs->links('pagination::bootstrap-4') !!}
                        </div>
                    </div>
                @endif --}}
            </div>
        </div>
    </div>
@endsection