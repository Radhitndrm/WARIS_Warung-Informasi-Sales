/**
 * WARIS - Modul Kasir
 * 
 * Menangani seluruh interaksi halaman kasir:
 * - Filter & pencarian produk
 * - Manajemen keranjang (tambah, hapus, ubah qty)
 * - Input nominal pembayaran dengan format rupiah real-time
 * - Checkout via Tunai / QRIS (Midtrans Snap) / Utang
 * - Verifikasi pembayaran & tampil struk
 */

// ──────────────────────────────────────
// Utility
// ──────────────────────────────────────

/** Format angka ke string ribuan (id-ID) → 50000 → "50.000" */
function formatPrice(val) {
    return Number(val).toLocaleString('id-ID');
}

// ──────────────────────────────────────
// Filter Produk
// ──────────────────────────────────────

/** Filter produk berdasarkan kategori aktif dan keyword pencarian */
function filterProducts(products, activeCategory, search) {
    let result = products;
    if (activeCategory) {
        result = result.filter(p => p.category_id === activeCategory);
    }
    if (search.trim()) {
        const q = search.toLowerCase();
        result = result.filter(p => p.name.toLowerCase().includes(q));
    }
    return result;
}

// ──────────────────────────────────────
// Manajemen Keranjang
// ──────────────────────────────────────

/** Hitung total harga seluruh item di keranjang */
function calcCartTotal(cart) {
    return cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
}

/** Tambah produk ke keranjang. Jika sudah ada, tambah qty (maks = stok) */
function addToCart(cart, product) {
    const existing = cart.find(i => i.product_id === product.id);
    if (existing) {
        if (existing.quantity < product.stock) {
            existing.quantity++;
        }
    } else {
        if (product.stock > 0) {
            cart.push({
                product_id: product.id,
                name: product.name,
                price: product.price,
                stock: product.stock,
                quantity: 1,
            });
        }
    }
}

/** Hapus item dari keranjang berdasarkan index */
function removeFromCart(cart, index) {
    cart.splice(index, 1);
}

/** Ubah kuantitas item (+1 / -1). Hapus jika qty = 0. Batasi maks = stok */
function updateCartQuantity(cart, index, delta) {
    const item = cart[index];
    const newQty = item.quantity + delta;
    if (newQty <= 0) {
        removeFromCart(cart, index);
    } else if (newQty <= item.stock) {
        item.quantity = newQty;
    }
}

// ──────────────────────────────────────
// Input Nominal (Format Rupiah Real-time)
// ──────────────────────────────────────

/**
 * Handler event `@input` pada field nominal bayar.
 * Menghapus semua karakter non-digit, lalu memformat ulang tampilan
 * dan menyimpan nilai numerik asli ke amountPaid.
 */
function handleAmountInput(state, e) {
    let raw = e.target.value.replace(/[^\d]/g, '');
    if (raw === '' || raw === '0') {
        state.amountPaid = 0;
        state.amountPaidDisplay = '';
        return;
    }
    state.amountPaid = parseInt(raw, 10);
    state.amountPaidDisplay = Number(raw).toLocaleString('id-ID');
}

/**
 * Handler event `@keydown` pada field nominal bayar.
 * Hanya mengizinkan digit 0-9, backspace, delete, dan tombol navigasi.
 */
function handleAmountKeydown(e) {
    const allowed = ['Backspace', 'Delete', 'ArrowLeft', 'ArrowRight', 'Tab', 'Home', 'End'];
    if (allowed.includes(e.key)) return;
    if (e.key >= '0' && e.key <= '9') return;
    if (e.ctrlKey || e.metaKey) return;
    e.preventDefault();
}

// ──────────────────────────────────────
// Checkout & Pembayaran
// ──────────────────────────────────────

/** Validasi sebelum checkout: nominal cash cukup, data utang lengkap */
function validateCheckout(state) {
    if (state.paymentMethod === 'cash' && state.amountPaid < state.cartTotal) {
        alert('Jumlah bayar kurang dari total');
        return false;
    }
    if (state.paymentMethod === 'debt') {
        if (!state.customerName.trim()) {
            alert('Nama pelanggan harus diisi');
            return false;
        }
        if (!state.customerPhone.trim()) {
            alert('No. telepon harus diisi');
            return false;
        }
    }
    return true;
}

/** Kirim request checkout ke server, tangani response cash / debt / snap */
async function sendCheckout(state) {
    state.checkoutLoading = true;
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
    const checkoutUrl = document.querySelector('meta[name="checkout-url"]')?.content || '/kasir/checkout';
    const callbackUrl = document.querySelector('meta[name="payment-callback-url"]')?.content || '/kasir/payment-callback';
    try {
        const body = {
            items: state.cart.map(i => ({
                product_id: i.product_id,
                quantity: i.quantity,
            })),
            payment_method: state.paymentMethod,
            amount_paid: state.paymentMethod === 'cash' ? state.amountPaid : state.cartTotal,
        };
        if (state.paymentMethod === 'debt') {
            body.customer_name = state.customerName;
            body.customer_phone = state.customerPhone;
            if (state.dueDate) body.due_date = state.dueDate;
        }
        const res = await fetch(checkoutUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify(body),
        });
        const data = await res.json();
        if (data.success) {
            if (data.snap_token) {
                state.snapToken = data.snap_token;
                state.lastOrder = { id: data.order_id };
                state.checkoutLoading = false;
                state.processingPayment = true;
                openSnapPopup(state, callbackUrl, csrfToken);
            } else {
                state.lastOrder = data.order;
                state.showReceipt = true;
                state.cart.forEach(cartItem => {
                    const prod = state.products.find(p => p.id === cartItem.product_id);
                    if (prod) prod.stock -= cartItem.quantity;
                });
            }
        } else {
            alert(data.message || 'Gagal memproses transaksi');
        }
    } catch (e) {
        alert('Terjadi kesalahan koneksi');
    } finally {
        if (!state.snapToken) {
            state.checkoutLoading = false;
        }
    }
}

/** Buka popup Midtrans Snap dan daftarkan callback */
function openSnapPopup(state, callbackUrl, csrfToken) {
    window.snap.pay(state.snapToken, {
        onSuccess: () => verifyPayment(state, callbackUrl, csrfToken),
        onPending: () => {
            alert('Pembayaran masih diproses. Silakan cek status transaksi nanti.');
            resetCartState(state);
        },
        onError: (result) => {
            alert('Pembayaran gagal: ' + (result.status_message || 'Terjadi kesalahan'));
            resetCartState(state);
        },
        onClose: () => {
            if (state.processingPayment) {
                state.processingPayment = false;
                state.snapToken = null;
                state.checkoutLoading = false;
                state.lastOrder = null;
            }
        },
    });
}

/** Verifikasi status pembayaran QRIS ke server */
async function verifyPayment(state, callbackUrl, csrfToken) {
    if (!state.lastOrder && !state.snapToken) return;
    try {
        const orderId = state.lastOrder?.id;
        if (!orderId) return;
        const res = await fetch(callbackUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify({ order_id: orderId }),
        });
        const data = await res.json();
        if (data.success) {
            state.lastOrder = data.order;
            state.showReceipt = true;
            state.cart.forEach(cartItem => {
                const prod = state.products.find(p => p.id === cartItem.product_id);
                if (prod) prod.stock -= cartItem.quantity;
            });
        } else {
            alert(data.message || 'Pembayaran belum dikonfirmasi');
            resetCartState(state);
        }
    } catch (e) {
        alert('Gagal memverifikasi pembayaran');
        resetCartState(state);
    } finally {
        state.processingPayment = false;
        state.snapToken = null;
        state.checkoutLoading = false;
    }
}

/** Reset seluruh state ke kondisi awal (pesanan baru) */
function resetCartState(state) {
    state.cart = [];
    state.amountPaid = 0;
    state.amountPaidDisplay = '';
    state.customerName = '';
    state.customerPhone = '';
    state.dueDate = '';
    state.paymentMethod = 'cash';
    state.showReceipt = false;
    state.lastOrder = null;
    state.snapToken = null;
    state.processingPayment = false;
}

// ──────────────────────────────────────
// Aplikasi Utama (Alpine.js)
// ──────────────────────────────────────

/**
 * Inisialisasi state dan method untuk komponen Alpine.js halaman kasir.
 * Semua fungsi di atas dipanggil melalui method ini agar Alpine bisa mengaksesnya.
 */
window.kasirApp = function (serverProducts, serverCategories) {
    return {
        // Data produk & kategori dari server
        products: serverProducts || [],
        categories: serverCategories || [],
        activeCategory: null,
        search: '',

        // Keranjang
        cart: [],

        // Pembayaran
        paymentMethod: 'cash',
        amountPaid: 0,
        amountPaidDisplay: '',
        customerName: '',
        customerPhone: '',
        dueDate: '',

        // Status
        checkoutLoading: false,
        processingPayment: false,
        snapToken: null,
        showReceipt: false,
        lastOrder: null,

        // ── Computed ──

        get filteredProducts() {
            return filterProducts(this.products, this.activeCategory, this.search);
        },

        get cartTotal() {
            return calcCartTotal(this.cart);
        },

        get changeAmount() {
            return this.amountPaid - this.cartTotal;
        },

        // ── Utils ──

        formatPrice,

        // ── Cart actions ──

        addToCart(product) {
            addToCart(this.cart, product);
        },

        removeFromCart(index) {
            removeFromCart(this.cart, index);
            if (this.cart.length === 0) this.amountPaid = 0;
        },

        updateQuantity(index, delta) {
            updateCartQuantity(this.cart, index, delta);
        },

        // ── Input amount ──

        onAmountPaidInput(e) {
            handleAmountInput(this, e);
        },

        onAmountPaidKeydown(e) {
            handleAmountKeydown(e);
        },

        // ── Checkout flow ──

        async checkout() {
            if (this.cart.length === 0) return;
            if (!validateCheckout(this)) return;
            await sendCheckout(this);
        },
    };
};
