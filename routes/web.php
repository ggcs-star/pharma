        <?php

        use Illuminate\Support\Facades\Route;
        use App\Http\Controllers\AuthController;

        /*
        |--------------------------------------------------------------------------
        | Controllers
        |--------------------------------------------------------------------------
        */
use App\Http\Controllers\Admin\OrderController;

        use App\Http\Controllers\Master\ManufacturerController;
        use App\Http\Controllers\Master\CategoryController;
        use App\Http\Controllers\Master\SupplierController;
        use App\Http\Controllers\Master\ItemController;
        use App\Http\Controllers\Master\CustomerController;
        use App\Http\Controllers\Master\DoctorController;
        use App\Http\Controllers\DashboardController;
        use App\Http\Controllers\Master\SubCategoryController;

        use App\Http\Controllers\Purchase\PurchaseController;
        use App\Http\Controllers\Sales\SalesController;
        use App\Http\Controllers\CustomerLedgerController;
        // use App\Http\Controllers\Report\ReportController;
        use App\Models\Batch;
use App\Http\Controllers\Sales\SalesReturnController;
        use App\Http\Controllers\Purchase\PurchaseOrderController;
use App\Http\Controllers\Purchase\PurchaseReturnController;
use Illuminate\Http\Request;
Route::get('/customer-ledger', [CustomerLedgerController::class, 'index'])->name('customer.ledger');

Route::get('/customer-payment', [CustomerLedgerController::class, 'createPayment'])->name('customer.payment.create');

Route::post('/customer-payment', [CustomerLedgerController::class, 'storePayment'])->name('customer.payment.store');
Route::post('/customers/ajax-store', [CustomerController::class, 'ajaxStore'])->name('customers.ajax-store');
Route::get('/purchase-return/items/{id}', [PurchaseReturnController::class, 'getPurchaseItemsByInvoice']);
Route::get('sales-return', [SalesReturnController::class, 'index'])
    ->name('sales_return.index');
    // Bill based create (GET)
Route::get('sales-return/create', [SalesReturnController::class, 'create'])
    ->name('sales.return.create');

// Store return (POST)
Route::get('sales-return/create', [SalesReturnController::class, 'create'])
    ->name('sales.return.create');

Route::post('sales-return/store', [SalesReturnController::class, 'store'])
    ->name('sales_return.store');

// Store Return
Route::post('sales-return/store', [SalesReturnController::class, 'store'])
    ->name('sales_return.store');
    Route::prefix('sales')->name('sales.')->group(function () {

    Route::get('/return', [SalesReturnController::class, 'index'])->name('return.index');

    Route::get('/return/create', [SalesReturnController::class, 'create'])->name('return.create');

    Route::post('/return/store', [SalesReturnController::class, 'store'])->name('return.store');

});Route::get('/return/{id}', [SalesReturnController::class, 'show'])
    ->name('sales.return.show');
// Route::get('/get-batch/{id}', [SalesController::class, 'getBatch']); // 🔥 THIS MISSING
// Route::get('/search-item', [SalesController::class, 'searchItem']);
// 🔥 GET BATCHES
Route::get('/get-batches/{item}', function ($itemId) {

    return Batch::where('item_id', $itemId)
        ->where('stock', '>', 0)
        ->get(['id', 'batch_code']);
});


// 🔥 GET BATCH DETAILS
Route::get('/get-batch-details/{batch}', function ($batchId) {

    $batch = Batch::with('purchaseItem.purchase.supplier')
        ->findOrFail($batchId);

    return response()->json([
        'stock' => $batch->stock,
        'rate' => $batch->purchaseItem->ptr,
        'supplier' => $batch->purchaseItem->purchase->supplier->name,
        'invoice' => $batch->purchaseItem->purchase->invoice_number,
        'purchase_id' => $batch->purchaseItem->purchase->id
    ]);
});
Route::middleware(['auth'])->group(function () {

    Route::prefix('purchase-return')->name('purchase-return.')->group(function () {

        Route::get('/', [PurchaseReturnController::class, 'index'])->name('index');

        Route::get('/create', [PurchaseReturnController::class, 'create'])->name('create');
Route::get('/get-purchase-item/{id}', [PurchaseReturnController::class, 'getPurchaseItem']);
        Route::post('/store', [PurchaseReturnController::class, 'store'])->name('store');

        Route::delete('/{return}', [PurchaseReturnController::class, 'destroy'])->name('delete');

    });

});
Route::resource('purchase-orders', PurchaseOrderController::class);
Route::get('/purchase/{purchase}/return', [PurchaseReturnController::class, 'create']);
Route::get('/purchase-orders/{id}/convert',
    [PurchaseOrderController::class, 'convert']
)->name('purchase-orders.convert');
use App\Models\Supplier;

Route::get('/get-supplier-by-code/{code}', function ($code) {

    $supplier = Supplier::where('supplier_code', $code)->first();

    if ($supplier) {
        return response()->json([
            'status' => true,
            'supplier' => $supplier
        ]);
    }

    return response()->json(['status' => false]);
});
Route::get('/api/item-details/{id}', function($id){

    $item = \App\Models\Item::with('packType')->find($id);

    if(!$item){
        return response()->json([]);
    }

    return response()->json([
        'pack_type' => $item->packType->name ?? '',
        'gst'       => $item->gst_percent,
        'unit'      => $item->unit,
        'hsn'       => $item->hsn_code
    ]);
});
Route::get('/api/get-item-full/{id}', function($id){

    $batch = \App\Models\Batch::where('item_id', $id)
        ->where('stock', '>', 0)
        ->whereDate('expiry_date', '>=', now())
        ->orderBy('expiry_date', 'asc')
        ->first();

    if(!$batch){
        return response()->json([]);
    }

    return response()->json([
            'batch_id'   => $batch->id, // 🔥 MOST IMPORTANT

        'batch'      => $batch->batch_code,
        'expiry'     => $batch->expiry_date,
        'mrp'        => $batch->mrp,
        'sale_price' => $batch->selling_price
    ]);
});

Route::get('/api/get-item-stock/{id}', function ($id) {

    $stock = Batch::where('item_id', $id)->sum('stock');

    $batch = Batch::where('item_id', $id)
        ->where('stock', '>', 0)
        ->orderBy('expiry_date', 'asc')
        ->first();

    return response()->json([
        'stock' => $stock,
        'selling_price' => $batch->selling_price ?? 0
    ]);

});

        /*
        |--------------------------------------------------------------------------
        | Public Routes
        |--------------------------------------------------------------------------
        */

        Route::get('/', function () {
            return view('landing');
        })->name('landing');

        /*
        |--------------------------------------------------------------------------
        | Auth Routes
        |--------------------------------------------------------------------------
        */

        Route::get('/login', [AuthController::class, 'showLoginPage'])->name('login');
        Route::post('/login', [AuthController::class, 'login']);

        Route::get('/register', [AuthController::class, 'showRegisterPage'])->name('register');
        Route::post('/register', [AuthController::class, 'register']);

        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

        Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
        Route::get('/reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->name('password.reset');
        Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');
        /*
        |--------------------------------------------------------------------------
        | Protected Routes
        |--------------------------------------------------------------------------
        */

        Route::middleware(['auth'])->group(function () {

            /*
            |--------------------------------------------------------------------------
            | Dashboard
            |--------------------------------------------------------------------------
            */



            /*
            |--------------------------------------------------------------------------
            | Master Routes
            |--------------------------------------------------------------------------
            */
            Route::prefix('master')->group(function () {

                Route::resource('manufacturers', ManufacturerController::class);
                Route::resource('categories', CategoryController::class);
                Route::resource('suppliers', SupplierController::class);
                // Route::resource('items', ItemController::class);
                Route::resource('customers', CustomerController::class);
                Route::resource('doctors', DoctorController::class);
Route::resource('sub-categories', SubCategoryController::class);

            });

            Route::prefix('master')->name('master.')->group(function () {

    // 👉 Import Page (GET)
    Route::get('items/import', function () {
        return view('master.items.import');
    })->name('items.import.page');

    // 👉 Import Submit (POST)
    Route::post('items/import', [ItemController::class, 'import'])->name('items.import');

    // 👉 Resource
    Route::resource('items', ItemController::class);
});

            /*
            |--------------------------------------------------------------------------
            | Purchase Routes
            |--------------------------------------------------------------------------
            */
            Route::prefix('purchase')->group(function () {

                Route::get('/', [PurchaseController::class,'index'])
                    ->name('purchase.index');

                Route::get('/create', [PurchaseController::class,'create'])
                    ->name('purchase.create');

                Route::post('/store', [PurchaseController::class,'store'])
                    ->name('purchase.store');
                        Route::get('/get-item/{id}', [PurchaseController::class,'getItem'])
        ->name('purchase.getItem'); 

                Route::get('/{purchase}', [PurchaseController::class,'show'])
                    ->name('purchase.show');

                Route::get('/{purchase}/edit', [PurchaseController::class,'edit'])
                    ->name('purchase.edit');

                Route::put('/{purchase}', [PurchaseController::class,'update'])
                    ->name('purchase.update');

                Route::delete('/{purchase}', [PurchaseController::class,'destroy'])
                    ->name('purchase.destroy');

            });

            Route::post('/ajax/manufacturer/store',[ManufacturerController::class,'storeAjax'])
->name('ajax.manufacturer.store');

Route::post('/ajax/category/store',[CategoryController::class,'storeAjax'])
->name('ajax.category.store');

Route::post('/ajax/subcategory/store',[SubCategoryController::class,'storeAjax'])
->name('ajax.subcategory.store');

            /*
            |--------------------------------------------------------------------------
            | Sales Routes
            |--------------------------------------------------------------------------
            */
           Route::prefix('sales')->name('sales.')->group(function () {
        Route::get('/', [SalesController::class, 'index'])->name('index');
        Route::get('/create', [SalesController::class, 'create'])->name('create');
        Route::post('/store', [SalesController::class, 'store'])->name('store');
        Route::get('/search-item', [SalesController::class, 'searchItem'])->name('search-item');
        Route::get('/get-batch/{item_id}', [SalesController::class, 'getBatch'])->name('get-batch');
        Route::get('/{id}', [SalesController::class, 'show'])->name('show');
        Route::get('/{id}/invoice', [SalesController::class, 'invoice'])->name('invoice');
        Route::delete('/{id}/cancel', [SalesController::class, 'cancel'])->name('cancel');
        Route::get('/customer/statement', [SalesController::class, 'customerStatement'])->name('customer.statement');
        
    });


            /*
            |--------------------------------------------------------------------------
            | Reports
            |--------------------------------------------------------------------------
            */
            // Route::prefix('reports')->group(function () {

            //     Route::get('/stock', [ReportController::class,'stock'])
            //         ->name('reports.stock');

            //     Route::get('/expiry', [ReportController::class,'expiry'])
            //         ->name('reports.expiry');

            // });
            Route::prefix('admin')->group(function () {
    Route::get('/orders', [OrderController::class, 'index'])->name('admin.orders.index');
    Route::get('/orders/{id}', [OrderController::class, 'show'])->name('admin.orders.show');
    Route::post('/admin/orders/{id}/status', [OrderController::class, 'updateStatus'])
    ->name('admin.orders.updateStatus');
});

        });