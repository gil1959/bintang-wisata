@extends('layouts.admin')

@section('content')
<div class="container">

    <h3>Create Rent Car Package</h3>
    @if ($errors->any())
    <div class="alert alert-danger">
        <strong>Validation Error:</strong>
        <ul class="mt-2 mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>

    <hr>
@endif


    <form action="{{ route('admin.rent-car-packages.store') }}" method="POST" enctype="multipart/form-data">
        @include('admin.rentcar._form', ['buttonText' => 'Create Package'])
    </form>

</div>
@endsection
