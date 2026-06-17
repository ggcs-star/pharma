@extends('layouts.master')

@section('title', 'Create Home Section')

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
    color:#0f172a;
}

.form-label{
    font-weight:600;
    color:#334155;
    margin-bottom:10px;
}

.form-control,
.form-select{
    border-radius:14px;
    min-height:48px;
    border:1px solid #dbeafe;
    box-shadow:none !important;
}

.form-control:focus,
.form-select:focus{
    border-color:#3b82f6;
}

.search-box{
    position:sticky;
    top:0;
    z-index:10;
    background:#fff;
    padding-bottom:15px;
}

.product-container{
    max-height:650px;
    overflow-y:auto;
    overflow-x:hidden;
    padding-right:5px;
}

.product-container::-webkit-scrollbar{
    width:6px;
}

.product-container::-webkit-scrollbar-thumb{
    background:#cbd5e1;
    border-radius:20px;
}

.product-card{
    border-radius:20px;
    overflow:hidden;
    border:2px solid transparent;
    transition:0.3s;
    cursor:pointer;
    height:100%;
    background:#fff;
}

.product-card:hover{
    transform:translateY(-4px);
    box-shadow:0 10px 25px rgba(0,0,0,0.08);
}

.product-checkbox:checked + .product-card{
    border-color:#2563eb;
    background:#eff6ff;
}

.product-image{
    width:100%;
    height:180px;
    object-fit:contain;
    padding:15px;
    background:#fff;
}

.product-title{
    font-size:14px;
    font-weight:600;
    line-height:1.5;
    min-height:42px;
    color:#0f172a;
}

.selected-badge{
    position:absolute;
    top:12px;
    right:12px;
    width:30px;
    height:30px;
    border-radius:50%;
    background:#22c55e;
    color:#fff;
    display:flex;
    align-items:center;
    justify-content:center;
    opacity:0;
    transition:0.3s;
    font-size:13px;
}

.product-checkbox:checked + .product-card .selected-badge{
    opacity:1;
}

.selected-products{
    background:#f8fafc;
    border-radius:20px;
    padding:20px;
    min-height:80px;
}

.selected-product-item{
    display:flex;
    align-items:center;
    gap:10px;
    background:#fff;
    border-radius:12px;
    padding:8px 12px;
    margin-bottom:10px;
    border:1px solid #e2e8f0;
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

.empty-state{
    text-align:center;
    color:#94a3b8;
    padding:30px 10px;
}

</style>


<div class="container-fluid py-4">

    <form
        action="{{ route('home-sections.store') }}"
        method="POST"
    >

        @csrf

        <div class="row g-4">

            <!-- LEFT -->

            <div class="col-lg-8">

                <div class="card section-card shadow-sm">

                    <div class="section-header">

                        <h2 class="section-title">

                            Create Homepage Section

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
                                    placeholder="Enter Section Title"
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

                                    <option value="manual">

                                        Manual Products

                                    </option>

                                    <option value="category">

                                        Category Products

                                    </option>

                                    <option value="latest">

                                        Latest Products

                                    </option>

                                </select>

                            </div>


                            <!-- CATEGORY -->

                            <div
                                class="col-md-6 mb-4"
                                id="categoryBlock"
                                style="display:none;"
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

                                        <option value="{{ $category->id }}">

                                            {{ $category->name }}

                                        </option>

                                    @endforeach

                                </select>

                            </div>


                            <!-- SORT ORDER -->

                            <div class="col-md-6 mb-4">

                                <label class="form-label">

                                    Sort Order

                                </label>

                                <input
                                    type="number"
                                    name="sort_order"
                                    class="form-control"
                                    value="0"
                                >

                            </div>

                        </div>


                        <!-- PRODUCTS -->

                        <div
                            id="productBlock"
                        >

                            <div class="search-box">

                                <label class="form-label">

                                    Search & Select Products

                                </label>

                                <input
                                    type="text"
                                    id="productSearch"
                                    class="form-control"
                                    placeholder="Search Medicine Name..."
                                >

                            </div>


                            <div class="product-container mt-4">

                                <div class="row g-4" id="productGrid">

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
                                checked
                            >

                            <label class="form-check-label fw-semibold">

                                Active Section

                            </label>

                        </div>


                        <!-- BUTTON -->

                        <button
                            type="submit"
                            class="btn btn-primary px-5 py-2 rounded-3 mt-4"
                        >

                            Create Section

                        </button>

                    </div>

                </div>

            </div>


            <!-- RIGHT -->

            <div class="col-lg-4">

                <div class="sticky-side">

                    <div class="card section-card shadow-sm">

                        <div class="section-header">

                            <h5 class="mb-0 fw-bold">

                                Selected Products

                            </h5>

                        </div>

                        <div class="card-body">

                            <div
                                class="selected-products"
                                id="selectedProducts"
                            >

                                <div class="empty-state">

                                    No Products Selected

                                </div>

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



    /*
    |--------------------------------------------------------------------------
    | TOGGLE FIELDS
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
    | SEARCH PRODUCTS
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

    const checkboxes = document.querySelectorAll('.product-checkbox');

    function renderSelectedProducts(){

        let html = '';

        let selected = 0;

        checkboxes.forEach(function(box){

            if(box.checked){

                selected++;

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

        if(selected === 0){

            html = `

                <div class="empty-state">

                    No Products Selected

                </div>

            `;
        }

        selectedProducts.innerHTML = html;
    }

    checkboxes.forEach(function(box){

        box.addEventListener(
            'change',
            renderSelectedProducts
        );

    });

});

</script>

@endsection