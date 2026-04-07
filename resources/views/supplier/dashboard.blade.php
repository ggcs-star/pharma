@extends('supplier.layouts.app')

@section('title', 'Dashboard')

@section('content')

<h2 class="mb-4">Dashboard</h2>

<div class="card p-4">
    <h4>Welcome, {{ auth('supplier')->user()->name }}</h4>
    <p>This is your supplier dashboard.</p>
</div>

@endsection