@extends('layouts.master')

@section('title', 'Edit Home Section')

@section('content')

<style>

.section-card{
    border:none;
    border-radius:24px;
    overflow:hidden;
    background:#fff;
}

.section-header{
    padding:24px 30px;
    border-bottom:1px solid #f1f5f9;
}

.section-title{
    font-size:24px;
    font-weight:700;
    margin:0;
}

.form-control,
.form-select{
    border-radius:14px;
    min-height:48px;
}

.product-container{
    max-height:650px;
    overflow-y:auto;
}

.product-card{
    border-radius:18px;
    overflow:hidden;
    border:2px solid transparent;
    transition:0.3s;
    cursor:pointer;
    background:#fff;
}

.product-card:hover{
    transform:translateY(-3px);
    box-shadow:0 10px 20px rgba(0,0,0,0.08);
}

.product-checkbox:checked + .product-card{
    border-color:#2563eb;
    background:#eff6ff;
}

.product-image{
    width:100%;
    height:180px;
    object-fit:contain;
    background:#fff;
    padding:15px;
}

.product-title{
    font-size:14px;
    font-weight:600;
    min-height:45px;
}

.selected-badge{
    position:absolute;
    top:10px;
    right:10px;
    width:28px;
    height:28px;
    border-radius:50%;
    background:#22c55e;
    color:#fff;
    display:flex;
    align-items:center;
    justify-content:center;
    opacity:0;
}

.product-checkbox:checked + .product-card .selected-badge{
    opacity:1;
}

.selected-products{
    background:#f8fafc;
    border-radius:20px;
    padding:20px;
    min-height:120px;
}

.selected-product-item{
    display:flex;
    gap:10px;
    align-items:center;
    padding:10px;
    background:#fff;
    border-radius:12px;
    margin-bottom:10px;
}

.selected-product-item img{
    width:45px;
    height:45px;
    object-fit:contain;
}

.sticky-side{
    position:sticky;
    top:20px;
}

</style>

<div class="container-fluid py-4">

    <form
        action="{{ route('home-sections.update', $homeSection->id) }}"
        method="POST"
    >

        @csrf
        @method('PUT')

        <div class="row g-4">

            <!-- LEFT -->

            <div class="col-lg-8">

                <div class="card section-card shadow-sm">

                    <div class="section-header">

                        <h2 class="section-title">

                            Edit Homepage Section

                        </h2>

                    </div>

                    <div class="card-body p-4">

                        <div class="row">

                            <!-- TITLE -->

                            <div class="col-md-6 mb-4">

                                <label class="form-label">

                                    Section Title

                                </label>

                                <input
                                    type="text"
                                    name="title"
                                    class="form-control"
                                    value="{{ $homeSection->title }}"
                                    required
                                >

                            </div>


                            <!-- TYPE -->

                            <div class="col-md-6 mb-4">

                                <label class="form-label">

                                    Section Type

                                </label>

                                <select
                                    name="type"
                                    id="sectionType"
                                    class="form-select"
                                >

                                    <option
                                        value="manual"
                                        {{ $homeSection->type == 'manual' ? 'selected' : '' }}
                                    >
                                        Manual Products
                                    </option>

                                    <option
                                        value="category"
                                        {{ $homeSection->type == 'category' ? 'selected' : '' }}
                                    >
                                        Category Products
                                    </option>

                                    <option
                                        value="latest"
                                        {{ $homeSection->type == 'latest' ? 'selected' : '' }}
                                    >
                                        Latest Products
                                    </option>

                                </select>

                            </div>


                            <!-- CATEGORY -->

                            <div
                                class="col-md-6 mb-4"
                                id="categoryBlock"
                            >

                                <label class="form-label">

                                    Select Category

                                </label>

                                <select
                                    name="category_id"
                                    class="form-select"
                                >

                                    <option value="">
                                        Select Category
                                    </option>

                                    @foreach($categories as $category)

                                        <option
                                            value="{{ $category->id }}"
                                            {{ $homeSection->category_id == $category->id ? 'selected' : '' }}
                                        >

                                            {{ $category->name }}

                                        </option>

                                    @endforeach

                                </select>

                            </div>


                            <!-- SORT -->

                            <div class="col-md-6 mb-4">

                                <label class="form-label">

                                    Sort Order

                                </label>

                                <input
                                    type="number"
                                    name="sort_order"
                                    class="form-control"
                                    value="{{ $homeSection->sort_order }}"
                                >

                            </div>

                        </div>


                        <!-- PRODUCTS -->

                        <div id="productBlock">

                            <div class="mb-4">

                                <input
                                    type="text"
                                    id="productSearch"
                                    class="form-control"
                                    placeholder="Search Products..."
                                >

                            </div>

                            <div class="product-container">

                                <div class="row g-4">

                                    @foreach($items as $item)

                                        @php

                                            $image = $item->main_image;

                                            if(
                                                !empty($image)
                                                &&
                                                !filter_var($image, FILTER_VALIDATE_URL)
                                            ){

                                                $image = \Storage::disk('s3')->url($image);
                                            }

                                        @endphp

                                        <div
                                            class="col-md-4 product-wrapper"
                                            data-name="{{ strtolower($item->name) }}"
                                        >

                                            <label class="w-100">

                                                <input
                                                    type="checkbox"
                                                    name="products[]"
                                                    value="{{ $item->id }}"
                                                    class="product-checkbox d-none"
                                                    data-name="{{ $item->name }}"
                                                    data-image="{{ $image }}"
                                                    {{ $homeSection->items->contains($item->id) ? 'checked' : '' }}
                                                >

                                                <div class="product-card">

                                                    <div class="position-relative">

                                                        <img
                                                            src="{{ $image }}"
                                                            class="product-image"
                                                            onerror="this.src='{{ asset('images/default-medicine.png') }}'"
                                                        >

                                                        <div class="selected-badge">

                                                            <i class="fa fa-check"></i>

                                                        </div>

                                                    </div>

                                                    <div class="p-3">

                                                        <div class="product-title">

                                                            {{ $item->name }}

                                                        </div>

                                                    </div>

                                                </div>

                                            </label>

                                        </div>

                                    @endforeach

                                </div>

                            </div>

                        </div>


                        <!-- ACTIVE -->

                        <div class="form-check mt-4">

                            <input
                                type="checkbox"
                                name="is_active"
                                class="form-check-input"
                                value="1"
                                {{ $homeSection->is_active ? 'checked' : '' }}
                            >

                            <label class="form-check-label">

                                Active Section

                            </label>

                        </div>


                        <!-- BUTTON -->

                        <button
                            type="submit"
                            class="btn btn-primary px-5 mt-4"
                        >

                            Update Section

                        </button>

                    </div>

                </div>

            </div>


            <!-- RIGHT -->

            <div class="col-lg-4">

                <div class="sticky-side">

                    <div class="card section-card shadow-sm">

                        <div class="section-header">

                            <h5 class="mb-0">

                                Selected Products

                            </h5>

                        </div>

                        <div class="card-body">

                            <div
                                class="selected-products"
                                id="selectedProducts"
                            >

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </form>

</div>

@endsection


@section('scripts')

<script>

document.addEventListener('DOMContentLoaded', function(){

    const type = document.getElementById('sectionType');

    const categoryBlock = document.getElementById('categoryBlock');

    const productBlock = document.getElementById('productBlock');

    const search = document.getElementById('productSearch');

    const selectedProducts = document.getElementById('selectedProducts');

    const checkboxes = document.querySelectorAll('.product-checkbox');


    /*
    |--------------------------------------------------------------------------
    | TOGGLE
    |--------------------------------------------------------------------------
    */

    function toggleFields(){

        if(type.value === 'manual'){

            productBlock.style.display = 'block';

            categoryBlock.style.display = 'none';
        }

        else if(type.value === 'category'){

            productBlock.style.display = 'none';

            categoryBlock.style.display = 'block';
        }

        else{

            productBlock.style.display = 'none';

            categoryBlock.style.display = 'none';
        }
    }

    toggleFields();

    type.addEventListener('change', toggleFields);


    /*
    |--------------------------------------------------------------------------
    | SEARCH
    |--------------------------------------------------------------------------
    */

    search.addEventListener('keyup', function(){

        let value = this.value.toLowerCase();

        document.querySelectorAll('.product-wrapper')

            .forEach(function(card){

                let name = card.dataset.name;

                if(name.includes(value)){

                    card.style.display = 'block';
                }

                else{

                    card.style.display = 'none';
                }

            });

    });


    /*
    |--------------------------------------------------------------------------
    | SELECTED PRODUCTS
    |--------------------------------------------------------------------------
    */

    function renderSelectedProducts(){

        let html = '';

        let total = 0;

        checkboxes.forEach(function(box){

            if(box.checked){

                total++;

                html += `

                    <div class="selected-product-item">

                        <img src="${box.dataset.image}">

                        <div>

                            <div class="fw-semibold">

                                ${box.dataset.name}

                            </div>

                        </div>

                    </div>

                `;
            }

        });

        if(total === 0){

            html = `
                <div class="text-muted">
                    No Products Selected
                </div>
            `;
        }

        selectedProducts.innerHTML = html;
    }

    renderSelectedProducts();

    checkboxes.forEach(function(box){

        box.addEventListener(
            'change',
            renderSelectedProducts
        );

    });

});

</script>

@endsection