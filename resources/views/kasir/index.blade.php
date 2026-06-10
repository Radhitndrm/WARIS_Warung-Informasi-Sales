@extends('layouts.app')

@section('title', 'Kasir')

@push('head')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://{{ config('services.midtrans.is_production') ? 'app' : 'app.sandbox' }}.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.client_key') }}"></script>
<style>
    .kasir-layout {
        display: flex;
        gap: 1.5rem;
        height: calc(100vh - 6rem);
    }
    .product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
        gap: 0.75rem;
        align-content: start;
    }
    .cart-sidebar {
        width: 22rem;
        flex-shrink: 0;
        display: flex;
        flex-direction: column;
        background: #F4F2DE;
        border: 1px solid #8C8A75;
        border-radius: 1rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06);
        overflow: hidden;
    }
    .cart-items {
        flex: 1;
        overflow-y: auto;
        min-height: 0;
    }
    .category-pill {
        padding: 0.375rem 0.875rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.15s ease;
        white-space: nowrap;
        border: 1px solid transparent;
    }
    .category-pill.active {
        background: #8A9B6A;
        color: white;
        border-color: #8A9B6A;
    }
    .category-pill:not(.active) {
        background: white;
        color: #6B7A52;
        border-color: #C8C4A0;
    }
    .category-pill:not(.active):hover {
        background: #E6E4CE;
    }
    .product-card {
        background: white;
        border: 1px solid #E5E3C8;
        border-radius: 0.875rem;
        padding: 1rem;
        cursor: pointer;
        transition: all 0.15s ease;
        position: relative;
    }
    .product-card:hover {
        border-color: #8A9B6A;
        box-shadow: 0 2px 8px rgba(138,155,106,0.15);
        transform: translateY(-1px);
    }
    .product-card .badge-stok {
        position: absolute;
        top: 0.5rem;
        right: 0.5rem;
        font-size: 0.625rem;
        font-weight: 700;
        padding: 0.125rem 0.5rem;
        border-radius: 9999px;
        background: #F7CDCD;
        color: #991b1b;
    }
    .cart-item-qty-btn {
        width: 1.5rem;
        height: 1.5rem;
        border-radius: 9999px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: 700;
        border: 1px solid #C8C4A0;
        background: white;
        color: #394766;
        cursor: pointer;
        transition: all 0.1s;
    }
    .cart-item-qty-btn:hover {
        background: #E6E4CE;
    }
    .receipt-overlay {
        position: fixed;
        inset: 0;
        z-index: 100;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1rem;
        background: rgba(0,0,0,0.4);
    }
    .receipt-card {
        background: #F4F2DE;
        border: 1px solid #8C8A75;
        border-radius: 1rem;
        box-shadow: 0 8px 32px rgba(0,0,0,0.15);
        width: 100%;
        max-width: 420px;
        max-height: 90vh;
        overflow-y: auto;
        padding: 1.5rem;
    }
    @media (max-width: 1024px) {
        .kasir-layout { flex-direction: column; height: auto; }
        .cart-sidebar { width: 100%; max-height: 50vh; }
        .product-grid { grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); }
    }
    @media (max-width: 640px) {
        .product-grid { grid-template-columns: repeat(2, 1fr); gap: 0.5rem; }
        .product-card { padding: 0.75rem; }
        .product-card .product-img { height: 3.5rem; }
        .kasir-layout { gap: 0.75rem; }
    }
</style>
@endpush

@section('content')

<div x-data="kasirApp()" class="kasir-layout">

    {{-- Left: Products --}}
    <div class="flex-1 flex flex-col min-w-0 space-y-4">

        {{-- Search --}}
        <div class="flex items-center gap-3">
            <div class="relative flex-1">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" x-model="search" @input="filterProducts"
                    class="w-full pl-9 pr-4 py-2.5 bg-white border border-[#C8C4A0] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-sidebar/40 focus:border-sidebar placeholder:text-gray-400"
                    placeholder="Cari produk...">
            </div>
        </div>

        {{-- Category Filter --}}
        <div class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none">
            <button @click="activeCategory = null" :class="activeCategory === null ? 'active' : ''" class="category-pill">
                <i class="fa-solid fa-th-large mr-1.5"></i>Semua
            </button>
            <template x-for="cat in categories" :key="cat.id">
                <button @click="activeCategory = cat.id" :class="activeCategory === cat.id ? 'active' : ''" class="category-pill">
                    <i class="fa-solid fa-tag mr-1.5"></i><span x-text="cat.name"></span>
                </button>
            </template>
        </div>

        {{-- Product Grid --}}
        <div class="flex-1 overflow-y-auto min-h-0">
            <template x-if="filteredProducts.length === 0">
                <div class="flex flex-col items-center justify-center py-16 text-gray-400">
                    <i class="fa-solid fa-box-open text-5xl mb-4 text-gray-300"></i>
                    <p class="font-semibold text-gray-500">Produk tidak ditemukan</p>
                    <p class="text-sm mt-1">Coba kata kunci lain</p>
                </div>
            </template>
            <div class="product-grid">
                <template x-for="product in filteredProducts" :key="product.id">
                    <div @click="addToCart(product)" class="product-card">
                        <template x-if="product.image">
                            <img :src="'/storage/' + product.image" :alt="product.name"
                                class="w-full h-24 object-cover rounded-lg mb-2.5 bg-[#E6E4CE]">
                        </template>
                        <template x-if="!product.image">
                            <div class="w-full h-24 bg-[#E6E4CE] rounded-lg flex items-center justify-center text-gray-500 mb-2.5">
                                <i class="fa-solid fa-box text-3xl text-gray-400"></i>
                            </div>
                        </template>
                        <p class="text-sm font-semibold text-gray-800 leading-tight truncate" x-text="product.name"></p>
                        <p class="text-xs text-gray-500 mt-0.5" x-text="'Rp ' + formatPrice(product.price)"></p>
                        <div class="flex items-center justify-between mt-1.5">
                            <span class="text-[11px] font-medium text-gray-400" x-text="'Stok: ' + product.stock"></span>
                            <span x-show="product.stock <= 5" class="badge-stok">
                                <i class="fa-solid fa-triangle-exclamation mr-0.5"></i><span x-text="product.stock"></span>
                            </span>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    {{-- Right: Cart --}}
    <div class="cart-sidebar">
        <div class="px-4 py-3.5 border-b border-[#8C8A75]/30 flex items-center justify-between">
            <h3 class="font-bold text-gray-800 text-sm flex items-center gap-2">
                <i class="fa-solid fa-cart-shopping text-sidebar"></i>
                Pesanan
            </h3>
            <span class="bg-sidebar text-white text-xs font-bold px-2.5 py-1 rounded-full" x-text="cart.length"></span>
        </div>

        <div class="cart-items p-4 space-y-2.5">
            <template x-if="cart.length === 0">
                <div class="flex flex-col items-center justify-center py-12 text-gray-400">
                    <i class="fa-solid fa-basket-shopping text-4xl mb-3 text-gray-300"></i>
                    <p class="text-sm font-semibold text-gray-500">Belum ada item</p>
                    <p class="text-xs mt-1 text-center">Klik produk untuk menambah pesanan</p>
                </div>
            </template>
            <template x-for="(item, index) in cart" :key="item.product_id">
                <div class="bg-white border border-[#E5E3C8] rounded-xl p-3 flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-[#E6E4CE] flex items-center justify-center text-gray-500 shrink-0">
                        <i class="fa-solid fa-box text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-800 truncate" x-text="item.name"></p>
                        <p class="text-xs text-gray-500" x-text="'Rp ' + formatPrice(item.price) + ' x ' + item.quantity"></p>
                        <p class="text-xs font-bold text-sidebar" x-text="'Rp ' + formatPrice(item.price * item.quantity)"></p>
                    </div>
                    <div class="flex items-center gap-1.5 shrink-0">
                        <button @click="updateQuantity(index, -1)" class="cart-item-qty-btn"><i class="fa-solid fa-minus fa-xs"></i></button>
                        <span class="text-sm font-bold text-gray-800 w-6 text-center" x-text="item.quantity"></span>
                        <button @click="updateQuantity(index, 1)" class="cart-item-qty-btn"><i class="fa-solid fa-plus fa-xs"></i></button>
                        <button @click="removeFromCart(index)" class="ml-1 w-6 h-6 rounded-full flex items-center justify-center text-gray-400 hover:text-red-500 hover:bg-red-50 transition-colors">
                            <i class="fa-solid fa-trash-can fa-xs"></i>
                        </button>
                    </div>
                </div>
            </template>
        </div>

        {{-- Total & Checkout --}}
        <div class="border-t border-[#8C8A75]/30 p-4 space-y-3">
            <div class="flex justify-between items-center">
                <span class="text-sm font-semibold text-gray-600">Total</span>
                <span class="text-xl font-bold text-gray-800" x-text="'Rp ' + formatPrice(cartTotal)"></span>
            </div>

            {{-- Payment Method --}}
            <div class="flex gap-2">
                <button @click="paymentMethod = 'cash'" :class="paymentMethod === 'cash' ? 'bg-sidebar text-white border-sidebar' : 'bg-white text-gray-600 border-[#C8C4A0]'"
                    class="flex-1 py-2.5 rounded-xl text-sm font-semibold border transition-colors">
                    <i class="fa-solid fa-money-bill-wave mr-1.5"></i>Tunai
                </button>
                <button @click="paymentMethod = 'qris'" :class="paymentMethod === 'qris' ? 'bg-sidebar text-white border-sidebar' : 'bg-white text-gray-600 border-[#C8C4A0]'"
                    class="flex-1 py-2.5 rounded-xl text-sm font-semibold border transition-colors">
                    <i class="fa-solid fa-qrcode mr-1.5"></i>QRIS
                </button>
                <button @click="paymentMethod = 'debt'" :class="paymentMethod === 'debt' ? 'bg-amber-600 text-white border-amber-600' : 'bg-white text-gray-600 border-[#C8C4A0]'"
                    class="flex-1 py-2.5 rounded-xl text-sm font-semibold border transition-colors">
                    <i class="fa-solid fa-file-invoice-dollar mr-1.5"></i>Utang
                </button>
            </div>

            {{-- Amount Paid (cash only) --}}
            <div x-show="paymentMethod === 'cash'" x-cloak>
                <input type="number" x-model="amountPaid" @input.debounce="calcChange"
                    class="w-full px-4 py-2.5 bg-white border border-[#C8C4A0] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-sidebar/40 focus:border-sidebar placeholder:text-gray-400"
                    placeholder="Jumlah bayar...">
                <div x-show="amountPaid > 0 && changeAmount >= 0" class="flex justify-between mt-1.5 text-sm">
                    <span class="text-gray-500">Kembali</span>
                    <span class="font-bold" :class="changeAmount >= 0 ? 'text-green-700' : 'text-red-600'" x-text="'Rp ' + formatPrice(changeAmount)"></span>
                </div>
            </div>

            {{-- Debt Customer Info --}}
            <div x-show="paymentMethod === 'debt'" x-cloak class="space-y-2">
                <input type="text" x-model="customerName"
                    class="w-full px-4 py-2.5 bg-white border border-[#C8C4A0] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/40 focus:border-amber-500 placeholder:text-gray-400"
                    placeholder="Nama pelanggan...">
                <input type="text" x-model="customerPhone"
                    class="w-full px-4 py-2.5 bg-white border border-[#C8C4A0] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/40 focus:border-amber-500 placeholder:text-gray-400"
                    placeholder="No. telepon...">
                <div>
                    <label class="text-xs text-gray-500 mb-1 block">Jatuh Tempo (default 30 hari)</label>
                    <input type="date" x-model="dueDate"
                        class="w-full px-4 py-2.5 bg-white border border-[#C8C4A0] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/40 focus:border-amber-500 placeholder:text-gray-400">
                </div>
            </div>

            <button @click="checkout" :disabled="cart.length === 0 || checkoutLoading"
                :class="cart.length === 0 ? 'bg-gray-300 cursor-not-allowed' : 'bg-sidebar hover:bg-sidebar-hover cursor-pointer'"
                class="w-full py-3 rounded-xl text-sm font-bold text-white transition-colors flex items-center justify-center gap-2">
                <i x-show="!checkoutLoading" class="fa-solid fa-check"></i>
                <i x-show="checkoutLoading" class="fa-solid fa-spinner fa-spin"></i>
                <span x-text="checkoutLoading ? 'Memproses...' : 'Bayar'"></span>
            </button>
        </div>
    </div>

    {{-- Receipt Modal --}}
    <template x-if="showReceipt">
        <div class="receipt-overlay" @click.self="showReceipt = false">
            <div class="receipt-card">
                <div class="text-center mb-5">
                    <div class="w-14 h-14 mx-auto mb-3 rounded-full flex items-center justify-center"
                        :class="lastOrder?.payment_method === 'debt' ? 'bg-amber-100' : 'bg-[#C1F2D0]'">
                        <i class="fa-solid text-2xl"
                            :class="lastOrder?.payment_method === 'debt' ? 'fa-file-invoice-dollar text-amber-600' : 'fa-check text-green-600'"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800" x-text="lastOrder?.payment_method === 'debt' ? 'Transaksi Utang Tercatat' : 'Pembayaran Berhasil'"></h3>
                    <p class="text-xs text-gray-500 mt-0.5" x-text="lastOrder?.invoice_no"></p>
                    <template x-if="lastOrder?.payment_method === 'debt'">
                        <div class="mt-2 text-xs text-gray-600">
                            <p>Pelanggan: <span class="font-semibold" x-text="lastOrder?.customer_name"></span></p>
                            <p>Telp: <span class="font-semibold" x-text="lastOrder?.customer_phone"></span></p>
                            <p x-show="lastOrder?.due_date">Jatuh tempo: <span class="font-semibold text-amber-700" x-text="lastOrder?.due_date"></span></p>
                        </div>
                    </template>
                </div>

                <div class="border-t border-b border-[#8C8A75]/30 py-3 space-y-2 mb-4 text-sm">
                    <template x-for="item in lastOrder?.items" :key="item.product">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600" x-text="item.quantity + 'x ' + item.product"></span>
                            <span class="font-semibold text-gray-800" x-text="'Rp ' + formatPrice(item.subtotal)"></span>
                        </div>
                    </template>
                </div>

                <div class="space-y-1.5 text-sm mb-5">
                    <div class="flex justify-between font-bold">
                        <span class="text-gray-800">Total</span>
                        <span class="text-gray-800" x-text="'Rp ' + formatPrice(lastOrder?.total)"></span>
                    </div>
                    <div class="flex justify-between text-gray-500">
                        <span>Metode</span>
                        <span class="font-semibold capitalize" x-text="lastOrder?.payment_method"></span>
                    </div>
                    <div x-show="lastOrder?.payment_method === 'cash'" class="flex justify-between text-gray-500">
                        <span>Kembali</span>
                        <span class="font-semibold text-green-700" x-text="'Rp ' + formatPrice(lastOrder?.change_amount)"></span>
                    </div>
                    <div class="flex justify-between text-xs text-gray-400">
                        <span>Waktu</span>
                        <span x-text="lastOrder?.created_at"></span>
                    </div>
                </div>

                <button @click="resetCart"
                    class="w-full py-2.5 bg-sidebar text-white rounded-xl text-sm font-bold hover:bg-sidebar-hover transition-colors">
                    <i class="fa-solid fa-plus mr-1.5"></i>Pesanan Baru
                </button>
            </div>
        </div>
    </template>

</div>

@endsection

@push('scripts')
<script>
function kasirApp() {
    return {
        products: @json($products),
        categories: @json($categories),
        activeCategory: null,
        search: '',
        cart: [],
        paymentMethod: 'cash',
        amountPaid: 0,
        customerName: '',
        customerPhone: '',
        dueDate: '',
        checkoutLoading: false,
        processingPayment: false,
        snapToken: null,
        showReceipt: false,
        lastOrder: null,

        get filteredProducts() {
            let result = this.products;
            if (this.activeCategory) {
                result = result.filter(p => p.category_id === this.activeCategory);
            }
            if (this.search.trim()) {
                const q = this.search.toLowerCase();
                result = result.filter(p => p.name.toLowerCase().includes(q));
            }
            return result;
        },

        get cartTotal() {
            return this.cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        },

        get changeAmount() {
            return this.amountPaid - this.cartTotal;
        },

        formatPrice(val) {
            return Number(val).toLocaleString('id-ID');
        },

        addToCart(product) {
            const existing = this.cart.find(i => i.product_id === product.id);
            if (existing) {
                if (existing.quantity < product.stock) {
                    existing.quantity++;
                }
            } else {
                if (product.stock > 0) {
                    this.cart.push({
                        product_id: product.id,
                        name: product.name,
                        price: product.price,
                        stock: product.stock,
                        quantity: 1,
                    });
                }
            }
        },

        removeFromCart(index) {
            this.cart.splice(index, 1);
            if (this.cart.length === 0) this.amountPaid = 0;
        },

        updateQuantity(index, delta) {
            const item = this.cart[index];
            const newQty = item.quantity + delta;
            if (newQty <= 0) {
                this.removeFromCart(index);
            } else if (newQty <= item.stock) {
                item.quantity = newQty;
            }
        },

        calcChange() {
            // reactive getter, but trigger reactivity
        },

        async checkout() {
            if (this.cart.length === 0) return;
            if (this.paymentMethod === 'cash' && this.amountPaid < this.cartTotal) {
                alert('Jumlah bayar kurang dari total');
                return;
            }
            if (this.paymentMethod === 'debt') {
                if (!this.customerName.trim()) {
                    alert('Nama pelanggan harus diisi');
                    return;
                }
                if (!this.customerPhone.trim()) {
                    alert('No. telepon harus diisi');
                    return;
                }
            }
            this.checkoutLoading = true;
            try {
                const body = {
                    items: this.cart.map(i => ({
                        product_id: i.product_id,
                        quantity: i.quantity,
                    })),
                    payment_method: this.paymentMethod,
                    amount_paid: this.paymentMethod === 'cash' ? this.amountPaid : this.cartTotal,
                };
                if (this.paymentMethod === 'debt') {
                    body.customer_name = this.customerName;
                    body.customer_phone = this.customerPhone;
                    if (this.dueDate) body.due_date = this.dueDate;
                }
                const res = await fetch('{{ route("kasir.checkout") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify(body),
                });
                const data = await res.json();
                if (data.success) {
                    if (data.snap_token) {
                        this.snapToken = data.snap_token;
                        this.lastOrder = { id: data.order_id };
                        this.checkoutLoading = false;
                        this.processingPayment = true;
                        this.openSnap();
                    } else {
                        this.lastOrder = data.order;
                        this.showReceipt = true;
                        this.cart.forEach(cartItem => {
                            const prod = this.products.find(p => p.id === cartItem.product_id);
                            if (prod) prod.stock -= cartItem.quantity;
                        });
                    }
                } else {
                    alert(data.message || 'Gagal memproses transaksi');
                }
            } catch (e) {
                alert('Terjadi kesalahan koneksi');
            } finally {
                if (!this.snapToken) {
                    this.checkoutLoading = false;
                }
            }
        },

        openSnap() {
            const snapToken = this.snapToken;
            const self = this;
            window.snap.pay(snapToken, {
                onSuccess: function(result) {
                    self.verifyPayment();
                },
                onPending: function(result) {
                    alert('Pembayaran masih diproses. Silakan cek status transaksi nanti.');
                    self.resetCart();
                },
                onError: function(result) {
                    alert('Pembayaran gagal: ' + (result.status_message || 'Terjadi kesalahan'));
                    self.resetCart();
                },
                onClose: function() {
                    if (self.processingPayment) {
                        self.processingPayment = false;
                        self.snapToken = null;
                        self.checkoutLoading = false;
                        self.lastOrder = null;
                    }
                },
            });
        },

        async verifyPayment() {
            if (!this.lastOrder && !this.snapToken) return;
            try {
                const orderId = this.lastOrder?.id;
                if (!orderId) return;
                const res = await fetch('{{ route("kasir.payment-callback") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({ order_id: orderId }),
                });
                const data = await res.json();
                if (data.success) {
                    this.lastOrder = data.order;
                    this.showReceipt = true;
                    this.cart.forEach(cartItem => {
                        const prod = this.products.find(p => p.id === cartItem.product_id);
                        if (prod) prod.stock -= cartItem.quantity;
                    });
                } else {
                    alert(data.message || 'Pembayaran belum dikonfirmasi');
                    this.resetCart();
                }
            } catch (e) {
                alert('Gagal memverifikasi pembayaran');
                this.resetCart();
            } finally {
                this.processingPayment = false;
                this.snapToken = null;
                this.checkoutLoading = false;
            }
        },

        resetCart() {
            this.cart = [];
            this.amountPaid = 0;
            this.customerName = '';
            this.customerPhone = '';
            this.dueDate = '';
            this.paymentMethod = 'cash';
            this.showReceipt = false;
            this.lastOrder = null;
            this.snapToken = null;
            this.processingPayment = false;
        },

    };
}
</script>
@endpush
