@extends('layouts.app')

@section('content')
<div class="container">

    <h3>Import Medicines CSV</h3>

    {{-- ✅ ERROR MESSAGE --}}
    @if(session('error'))
        <div class="alert alert-danger">
            ❌ {{ session('error') }}
        </div>
    @endif

    {{-- ✅ SUCCESS MESSAGE --}}
    @if(session('success'))
        <div class="alert alert-success">
            ✅ {{ session('success') }}
        </div>
    @endif

    {{-- ✅ VALIDATION ERROR --}}
    @if($errors->any())
        <div class="alert alert-danger">
            {{ $errors->first() }}
        </div>
    @endif

    <form action="{{ route('master.items.import') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
          <label>Select CSV / Excel File</label>
<input type="file" name="file" class="form-control" accept=".csv,.xlsx,.xls" required>
        </div>

        <button type="submit" class="btn btn-success">Upload & Import</button>

    </form>

</div>
@endsection