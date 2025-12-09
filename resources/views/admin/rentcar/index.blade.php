@extends('layouts.admin')

@section('content')
<div class="container">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold">Rent Car Packages</h3>

        <a href="{{ route('admin.rent-car-packages.create') }}" 
           class="btn btn-primary">
           + Add New Package
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success mb-3">
            {{ session('success') }}
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body table-responsive">
            <table class="table align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="width: 120px;">Thumbnail</th>
                        <th>Title</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Features</th>
                        <th style="width: 120px;">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($packages as $p)
                        <tr>
                            <td>
                                <img src="{{ asset('storage/' . $p->thumbnail_path) }}" 
                                     class="rounded" style="width: 80px; height: auto;">
                            </td>

                            <td class="fw-semibold">{{ $p->title }}</td>

                            <td>Rp {{ number_format($p->price_per_day) }}</td>

                            <td>
                                <span class="badge {{ $p->is_active ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $p->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>

                            <td>
                                @foreach($p->features as $f)
                                    <div>
                                        @if($f['available'])
                                            <span class="text-success fw-bold">✔</span>
                                        @else
                                            <span class="text-danger fw-bold">✘</span>
                                        @endif
                                        {{ $f['name'] }}
                                    </div>
                                @endforeach
                            </td>

                            <td>
                                <a href="{{ route('admin.rent-car-packages.edit', $p->id) }}" 
                                   class="btn btn-sm btn-warning mb-1">Edit</a>

                                <form action="{{ route('admin.rent-car-packages.destroy', $p->id) }}" 
                                      method="POST" onsubmit="return confirm('Delete this package?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger w-100">Delete</button>
                                </form>
                            </td>

                        </tr>
                    @endforeach
                </tbody>

            </table>
        </div>
    </div>

</div>
@endsection
