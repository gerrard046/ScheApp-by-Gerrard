@extends('layouts.app')

@section('content')
<style>
/* ===== WALLET PAGE STYLES ===== */
.wallet-page { padding: 28px 24px; max-width: 1280px; margin: 0 auto; }

/* Hero Balance Card */
.balance-hero {
    background: linear-gradient(135deg, #1E88E5 0%, #1565C0 40%, #6366f1 100%);
    border-radius: 28px;
    padding: 36px 40px;
    position: relative;
    overflow: hidden;
    color: white;
    box-shadow: 0 20px 60px rgba(30, 136, 229, 0.35);
    margin-bottom: 28px;
}
.balance-hero::before {
    content: '';
    position: absolute; top: -40%; right: -10%; width: 400px; height: 400px;
    background: rgba(255,255,255,0.06); border-radius: 50%;
}
.balance-hero::after {
    content: '';
    position: absolute; bottom: -60%; left: -5%; width: 350px; height: 350px;
    background: rgba(255,255,255,0.04); border-radius: 50%;
}
.balance-label { font-size: 13px; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; opacity: 0.75; margin-bottom: 8px; }
.balance-amount { font-size: 52px; font-weight: 900; letter-spacing: -2px; line-height: 1; margin-bottom: 24px; }
.balance-amount span { font-size: 28px; font-weight: 600; opacity: 0.8; vertical-align: top; margin-top: 8px; display: inline-block; }
.balance-stats { display: flex; gap: 32px; }
.balance-stat { display: flex; flex-direction: column; gap: 4px; }
.balance-stat-label { font-size: 11px; font-weight: 700; opacity: 0.65; text-transform: uppercase; letter-spacing: 1px; }
.balance-stat-value { font-size: 18px; font-weight: 800; }
.balance-stat-value.income-val { color: #86efac; }
.balance-stat-value.expense-val { color: #fca5a5; }
.balance-actions { position: absolute; top: 36px; right: 40px; display: flex; gap: 12px; z-index: 1; }
.hero-btn {
    padding: 10px 20px; border-radius: 14px; font-weight: 800; font-size: 13px;
    border: none; cursor: pointer; transition: all 0.25s ease; display: flex; align-items: center; gap: 6px;
    font-family: inherit;
}
.hero-btn-primary { background: rgba(255,255,255,0.22); color: white; backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.3); }
.hero-btn-primary:hover { background: rgba(255,255,255,0.32); transform: translateY(-2px); }
.hero-btn-success { background: rgba(134, 239, 172, 0.25); color: #86efac; border: 1px solid rgba(134,239,172,0.4); }
.hero-btn-success:hover { background: rgba(134, 239, 172, 0.35); transform: translateY(-2px); }
.hero-btn-danger { background: rgba(252, 165, 165, 0.25); color: #fca5a5; border: 1px solid rgba(252,165,165,0.4); }
.hero-btn-danger:hover { background: rgba(252, 165, 165, 0.35); transform: translateY(-2px); }

/* Grid Layout */
.wallet-grid { display: grid; grid-template-columns: 1fr 360px; gap: 24px; }
.wallet-main { display: flex; flex-direction: column; gap: 24px; }
.wallet-sidebar { display: flex; flex-direction: column; gap: 24px; }

/* Cards */
.w-card {
    background: var(--card-bg); border: 1px solid var(--border-color);
    border-radius: 22px; padding: 24px;
    box-shadow: var(--vibrant-shadow);
}
.w-card-title {
    font-size: 15px; font-weight: 800; color: var(--text-main);
    margin: 0 0 20px 0; display: flex; align-items: center; gap: 8px;
}
.w-card-title .title-badge {
    background: var(--soft-bg); border-radius: 8px; padding: 3px 10px;
    font-size: 11px; color: var(--text-muted); font-weight: 700; margin-left: auto;
}

/* Transaction List */
.txn-item {
    display: flex; align-items: center; gap: 14px; padding: 14px 0;
    border-bottom: 1px solid var(--border-color); transition: all 0.2s ease;
    cursor: default;
}
.txn-item:last-child { border-bottom: none; padding-bottom: 0; }
.txn-item:hover { transform: translateX(4px); }
.txn-icon {
    width: 44px; height: 44px; border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    font-size: 20px; flex-shrink: 0;
}
.txn-icon.income-icon { background: rgba(16, 185, 129, 0.1); }
.txn-icon.expense-icon { background: rgba(239, 68, 68, 0.1); }
.txn-info { flex: 1; min-width: 0; }
.txn-desc { font-size: 13px; font-weight: 700; color: var(--text-main); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.txn-meta { font-size: 11px; color: var(--text-muted); font-weight: 600; margin-top: 3px; }
.txn-cat-badge {
    display: inline-block; background: var(--soft-bg); border-radius: 6px;
    padding: 2px 8px; font-size: 10px; font-weight: 700; color: var(--text-muted);
}
.txn-amount { font-size: 15px; font-weight: 900; flex-shrink: 0; }
.txn-amount.income { color: #10B981; }
.txn-amount.expense { color: #EF4444; }
.txn-delete {
    background: none; border: none; color: var(--text-muted);
    cursor: pointer; padding: 6px; border-radius: 8px;
    transition: all 0.2s; font-size: 14px; opacity: 0; flex-shrink: 0;
}
.txn-item:hover .txn-delete { opacity: 1; }
.txn-delete:hover { background: rgba(239,68,68,0.1); color: #EF4444; }

/* Empty State */
.empty-txn { text-align: center; padding: 48px 20px; color: var(--text-muted); }
.empty-txn .empty-icon { font-size: 52px; margin-bottom: 12px; }
.empty-txn p { font-size: 14px; font-weight: 600; }

/* Modal Overlay */
.modal-overlay {
    display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
    background: rgba(15, 23, 42, 0.65); backdrop-filter: blur(8px);
    z-index: 99999; align-items: center; justify-content: center; padding: 20px;
}
.modal-overlay.active { display: flex; }
.modal-box {
    background: var(--card-bg); border-radius: 28px; padding: 32px;
    width: 100%; max-width: 480px; border: 1px solid var(--border-color);
    box-shadow: 0 30px 80px rgba(0,0,0,0.2);
    animation: modalSlide 0.35s cubic-bezier(0.16, 1, 0.3, 1);
}
@keyframes modalSlide {
    0% { opacity: 0; transform: scale(0.92) translateY(20px); }
    100% { opacity: 1; transform: scale(1) translateY(0); }
}
.modal-title { font-size: 20px; font-weight: 900; margin: 0 0 24px 0; color: var(--text-main); display: flex; align-items: center; gap: 10px; }
.modal-close { margin-left: auto; background: var(--soft-bg); border: none; width: 32px; height: 32px; border-radius: 10px; cursor: pointer; font-size: 16px; transition: 0.2s; }
.modal-close:hover { background: rgba(239,68,68,0.1); }
.form-group { margin-bottom: 16px; }
.form-label { font-size: 12px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 8px; display: block; }
.form-input {
    width: 100%; padding: 13px 16px; border-radius: 14px;
    border: 1.5px solid var(--border-color); background: var(--soft-bg);
    font-size: 14px; font-weight: 600; color: var(--text-main);
    transition: all 0.25s ease; outline: none; font-family: inherit;
}
.form-input:focus { border-color: var(--primary); box-shadow: 0 0 0 4px rgba(30, 136, 229, 0.1); }
.form-input::placeholder { color: var(--text-muted); font-weight: 500; }
.amount-input-wrap { position: relative; }
.amount-prefix { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); font-weight: 800; color: var(--text-muted); font-size: 14px; pointer-events: none; }
.amount-input-wrap .form-input { padding-left: 48px; }
.btn-submit {
    width: 100%; padding: 14px; border-radius: 14px; border: none;
    font-weight: 900; font-size: 15px; cursor: pointer; transition: all 0.25s ease;
    display: flex; align-items: center; justify-content: center; gap: 8px;
    font-family: inherit; margin-top: 8px;
}
.btn-topup    { background: linear-gradient(135deg, #1E88E5, #1565C0); color: white; box-shadow: 0 4px 16px rgba(30,136,229,0.3); }
.btn-income   { background: linear-gradient(135deg, #10B981, #059669); color: white; box-shadow: 0 4px 16px rgba(16,185,129,0.3); }
.btn-expense  { background: linear-gradient(135deg, #EF4444, #DC2626); color: white; box-shadow: 0 4px 16px rgba(239,68,68,0.3); }
.btn-submit:hover { transform: translateY(-2px); filter: brightness(1.05); }
.btn-submit:active { transform: translateY(0); }

/* Category Quick Buttons */
.cat-pills { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 4px; }
.cat-pill {
    background: var(--soft-bg); border: 1.5px solid var(--border-color);
    border-radius: 10px; padding: 6px 12px; font-size: 12px; font-weight: 700;
    color: var(--text-muted); cursor: pointer; transition: all 0.2s ease; user-select: none;
}
.cat-pill:hover, .cat-pill.active {
    border-color: var(--primary); color: var(--primary); background: rgba(30,136,229,0.06);
}

/* Summary Stats in Sidebar */
.stat-bar { margin-bottom: 16px; }
.stat-bar-header { display: flex; justify-content: space-between; font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 6px; }
.stat-bar-track { background: var(--soft-bg); border-radius: 99px; height: 8px; overflow: hidden; }
.stat-bar-fill { height: 100%; border-radius: 99px; transition: width 1s cubic-bezier(0.16, 1, 0.3, 1); }

/* Filter tabs */
.filter-tabs { display: flex; gap: 8px; margin-bottom: 20px; }
.filter-tab {
    padding: 7px 16px; border-radius: 10px; font-size: 12px; font-weight: 800;
    background: var(--soft-bg); border: 1.5px solid var(--border-color);
    color: var(--text-muted); cursor: pointer; transition: all 0.2s ease; font-family: inherit;
}
.filter-tab.active { background: var(--primary); border-color: var(--primary); color: white; }
.filter-tab:hover:not(.active) { border-color: var(--primary); color: var(--primary); }

/* Alert */
.alert-flash {
    padding: 14px 20px; border-radius: 16px; font-weight: 700; font-size: 14px;
    margin-bottom: 24px; display: flex; align-items: center; gap: 10px;
    animation: slideDown 0.5s cubic-bezier(0.16, 1, 0.3, 1);
}
@keyframes slideDown { 0% { opacity: 0; transform: translateY(-14px); } 100% { opacity: 1; transform: translateY(0); } }
.alert-success { background: rgba(16,185,129,0.1); border: 1.5px solid rgba(16,185,129,0.3); color: #059669; }
.alert-error   { background: rgba(239,68,68,0.1); border: 1.5px solid rgba(239,68,68,0.3); color: #DC2626; }
.dark .alert-success { color: #6ee7b7; }
.dark .alert-error   { color: #fca5a5; }

/* Responsive */
@media (max-width: 900px) {
    .wallet-grid { grid-template-columns: 1fr; }
    .balance-actions { position: static; margin-top: 24px; flex-wrap: wrap; }
    .balance-hero { padding: 28px 24px; }
    .balance-amount { font-size: 38px; }
    .balance-stats { gap: 20px; }
    .wallet-page { padding: 20px 16px; }
}
</style>

<div class="wallet-page">

    {{-- Flash Messages --}}
    @if(session('success'))
    <div class="alert-flash alert-success">
        <span style="font-size:18px;">✅</span> {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="alert-flash alert-error">
        <span style="font-size:18px;">❌</span> {{ session('error') }}
    </div>
    @endif

    {{-- Hero Balance Card --}}
    <div class="balance-hero">
        <div class="balance-label">💼 Dompet Digital</div>
        <div class="balance-amount">
            <span>Rp</span>{{ number_format($wallet->balance, 0, ',', '.') }}
        </div>
        <div class="balance-stats">
            <div class="balance-stat">
                <span class="balance-stat-label">📈 Total Masuk</span>
                <span class="balance-stat-value income-val">+Rp {{ number_format($totalIncome, 0, ',', '.') }}</span>
            </div>
            <div class="balance-stat">
                <span class="balance-stat-label">📉 Total Keluar</span>
                <span class="balance-stat-value expense-val">-Rp {{ number_format($totalExpense, 0, ',', '.') }}</span>
            </div>
        </div>
        <div class="balance-actions">
            <button class="hero-btn hero-btn-primary" onclick="openModal('topupModal')">💰 Top Up Saldo</button>
            <button class="hero-btn hero-btn-success" onclick="openModal('incomeModal')">➕ Uang Masuk</button>
            <button class="hero-btn hero-btn-danger" onclick="openModal('expenseModal')">➖ Uang Keluar</button>
        </div>
    </div>

    <div class="wallet-grid">
        {{-- Main: Transaction List --}}
        <div class="wallet-main">
            <div class="w-card">
                <div style="display:flex; align-items:center; margin-bottom:20px; gap:12px; flex-wrap:wrap;">
                    <h2 class="w-card-title" style="margin:0;">📋 Riwayat Transaksi</h2>
                    <div class="filter-tabs" style="margin-bottom:0; margin-left:auto;">
                        <button class="filter-tab active" id="tab-all" onclick="filterTxn('all', this)">Semua</button>
                        <button class="filter-tab" id="tab-income" onclick="filterTxn('income', this)">Masuk</button>
                        <button class="filter-tab" id="tab-expense" onclick="filterTxn('expense', this)">Keluar</button>
                    </div>
                </div>

                <div id="txn-list">
                    @forelse($transactions as $txn)
                    <div class="txn-item txn-{{ $txn->type }}" data-type="{{ $txn->type }}">
                        <div class="txn-icon {{ $txn->type }}-icon">
                            @if($txn->type === 'income') 💚 @else 🔴 @endif
                        </div>
                        <div class="txn-info">
                            <div class="txn-desc">{{ $txn->description }}</div>
                            <div class="txn-meta">
                                <span class="txn-cat-badge">{{ $txn->category ?? '-' }}</span>
                                &nbsp;{{ $txn->created_at->format('d M Y, H:i') }}
                            </div>
                        </div>
                        <div class="txn-amount {{ $txn->type }}">
                            {{ $txn->type === 'income' ? '+' : '-' }}Rp {{ number_format($txn->amount, 0, ',', '.') }}
                        </div>
                        <form action="/wallet/{{ $txn->id }}" method="POST" style="margin:0;" onsubmit="return confirm('Hapus transaksi ini dan sesuaikan saldo?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="txn-delete" title="Hapus">🗑️</button>
                        </form>
                    </div>
                    @empty
                    <div class="empty-txn" id="empty-state">
                        <div class="empty-icon">💳</div>
                        <p>Belum ada transaksi</p>
                        <p style="font-size:12px; margin-top:6px; opacity:0.6;">Tambah saldo atau catat transaksimu sekarang!</p>
                    </div>
                    @endforelse
                </div>

                <div id="empty-filtered" style="display:none;" class="empty-txn">
                    <div class="empty-icon">🔍</div>
                    <p>Tidak ada transaksi yang sesuai filter</p>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="wallet-sidebar">

            {{-- Quick Actions --}}
            <div class="w-card">
                <h3 class="w-card-title">⚡ Aksi Cepat</h3>
                <div style="display:flex; flex-direction:column; gap:10px;">
                    <button class="btn-submit btn-topup" onclick="openModal('topupModal')" style="width:100%;">
                        💰 Tambah Saldo / Top Up
                    </button>
                    <button class="btn-submit btn-income" onclick="openModal('incomeModal')" style="width:100%;">
                        ➕ Catat Uang Masuk
                    </button>
                    <button class="btn-submit btn-expense" onclick="openModal('expenseModal')" style="width:100%;">
                        ➖ Catat Uang Keluar
                    </button>
                </div>
            </div>

            {{-- Ringkasan Pengeluaran --}}
            @if($expenseByCategory->count() > 0)
            <div class="w-card">
                <h3 class="w-card-title">
                    📊 Pengeluaran per Kategori
                    <span class="title-badge">{{ $expenseByCategory->count() }} kategori</span>
                </h3>
                @php $maxExpense = $expenseByCategory->max('total'); @endphp
                @foreach($expenseByCategory->take(6) as $cat)
                <div class="stat-bar">
                    <div class="stat-bar-header">
                        <span>{{ $cat->category ?? 'Lainnya' }}</span>
                        <span style="color:var(--danger);">Rp {{ number_format($cat->total, 0, ',', '.') }}</span>
                    </div>
                    <div class="stat-bar-track">
                        <div class="stat-bar-fill"
                             style="width: {{ $maxExpense > 0 ? round(($cat->total / $maxExpense) * 100) : 0 }}%;
                                    background: linear-gradient(90deg, #EF4444, #f97316);">
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            {{-- Summary Card --}}
            <div class="w-card">
                <h3 class="w-card-title">📈 Ringkasan</h3>
                <div style="display:flex; flex-direction:column; gap:14px;">
                    <div style="display:flex; justify-content:space-between; align-items:center; padding:12px 16px; background:rgba(16,185,129,0.07); border-radius:14px; border:1px solid rgba(16,185,129,0.15);">
                        <span style="font-size:13px; font-weight:700; color:var(--text-muted);">✅ Total Transaksi</span>
                        <span style="font-weight:900; font-size:16px; color:var(--text-main);">{{ $transactions->count() }}</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; align-items:center; padding:12px 16px; background:rgba(16,185,129,0.07); border-radius:14px; border:1px solid rgba(16,185,129,0.15);">
                        <span style="font-size:13px; font-weight:700; color:var(--text-muted);">📈 Uang Masuk</span>
                        <span style="font-weight:900; font-size:16px; color:#10B981;">Rp {{ number_format($totalIncome, 0, ',', '.') }}</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; align-items:center; padding:12px 16px; background:rgba(239,68,68,0.07); border-radius:14px; border:1px solid rgba(239,68,68,0.15);">
                        <span style="font-size:13px; font-weight:700; color:var(--text-muted);">📉 Uang Keluar</span>
                        <span style="font-weight:900; font-size:16px; color:#EF4444;">Rp {{ number_format($totalExpense, 0, ',', '.') }}</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; align-items:center; padding:12px 16px; background:rgba(30,136,229,0.07); border-radius:14px; border:1px solid rgba(30,136,229,0.15);">
                        <span style="font-size:13px; font-weight:700; color:var(--text-muted);">💰 Saldo Saat Ini</span>
                        <span style="font-weight:900; font-size:16px; color:var(--primary);">Rp {{ number_format($wallet->balance, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- ===== MODALS ===== --}}

{{-- Top Up Modal --}}
<div class="modal-overlay" id="topupModal">
    <div class="modal-box">
        <h3 class="modal-title">
            💰 Top Up Saldo
            <button class="modal-close" onclick="closeModal('topupModal')">✕</button>
        </h3>
        <form action="/wallet/topup" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Jumlah Top Up</label>
                <div class="amount-input-wrap">
                    <span class="amount-prefix">Rp</span>
                    <input type="number" name="amount" class="form-input" placeholder="50.000" min="1000" required id="topup-amount">
                </div>
                <div class="cat-pills" style="margin-top:10px;">
                    <div class="cat-pill" onclick="setAmount('topup-amount', 50000)">50rb</div>
                    <div class="cat-pill" onclick="setAmount('topup-amount', 100000)">100rb</div>
                    <div class="cat-pill" onclick="setAmount('topup-amount', 200000)">200rb</div>
                    <div class="cat-pill" onclick="setAmount('topup-amount', 500000)">500rb</div>
                    <div class="cat-pill" onclick="setAmount('topup-amount', 1000000)">1jt</div>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Keterangan</label>
                <input type="text" name="description" class="form-input" placeholder="Misal: Transfer dari rekening BCA..." required>
            </div>
            <button type="submit" class="btn-submit btn-topup">💰 Tambahkan Saldo</button>
        </form>
    </div>
</div>

{{-- Income Modal --}}
<div class="modal-overlay" id="incomeModal">
    <div class="modal-box">
        <h3 class="modal-title" style="color:#10B981;">
            ➕ Catat Uang Masuk
            <button class="modal-close" onclick="closeModal('incomeModal')">✕</button>
        </h3>
        <form action="/wallet/income" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Jumlah Uang Masuk</label>
                <div class="amount-input-wrap">
                    <span class="amount-prefix">Rp</span>
                    <input type="number" name="amount" class="form-input" placeholder="0" min="1" required id="income-amount">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Keterangan</label>
                <input type="text" name="description" class="form-input" placeholder="Misal: Gaji bulan Maret..." required>
            </div>
            <div class="form-group">
                <label class="form-label">Kategori</label>
                <input type="text" name="category" class="form-input" id="income-cat" placeholder="Pilih atau ketik kategori..." required>
                <div class="cat-pills" style="margin-top:10px;">
                    @foreach(['Gaji','Freelance','Bisnis','Hadiah','Investasi','Bonus','Lainnya'] as $cat)
                    <div class="cat-pill" onclick="setCategory('income-cat', '{{ $cat }}', this)">{{ $cat }}</div>
                    @endforeach
                </div>
            </div>
            <button type="submit" class="btn-submit btn-income">✅ Simpan Uang Masuk</button>
        </form>
    </div>
</div>

{{-- Expense Modal --}}
<div class="modal-overlay" id="expenseModal">
    <div class="modal-box">
        <h3 class="modal-title" style="color:#EF4444;">
            ➖ Catat Uang Keluar
            <button class="modal-close" onclick="closeModal('expenseModal')">✕</button>
        </h3>
        <p style="font-size:13px; color:var(--text-muted); margin:-12px 0 20px; font-weight:600;">
            Saldo saat ini: <strong style="color:var(--primary);">Rp {{ number_format($wallet->balance, 0, ',', '.') }}</strong>
        </p>
        <form action="/wallet/expense" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Jumlah Pengeluaran</label>
                <div class="amount-input-wrap">
                    <span class="amount-prefix">Rp</span>
                    <input type="number" name="amount" class="form-input" placeholder="0" min="1" max="{{ $wallet->balance }}" required id="expense-amount">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Keterangan</label>
                <input type="text" name="description" class="form-input" placeholder="Misal: Makan siang di warteg..." required>
            </div>
            <div class="form-group">
                <label class="form-label">Kategori</label>
                <input type="text" name="category" class="form-input" id="expense-cat" placeholder="Pilih atau ketik kategori..." required>
                <div class="cat-pills" style="margin-top:10px;">
                    @foreach(['Makan','Transport','Belanja','Tagihan','Hiburan','Kesehatan','Pendidikan','Lainnya'] as $cat)
                    <div class="cat-pill" onclick="setCategory('expense-cat', '{{ $cat }}', this)">{{ $cat }}</div>
                    @endforeach
                </div>
            </div>
            <button type="submit" class="btn-submit btn-expense">📝 Simpan Pengeluaran</button>
        </form>
    </div>
</div>

<script>
// Modal Controls
function openModal(id) {
    document.getElementById(id).classList.add('active');
    document.body.style.overflow = 'hidden';
}
function closeModal(id) {
    document.getElementById(id).classList.remove('active');
    document.body.style.overflow = '';
}
// Close modal on backdrop click
document.querySelectorAll('.modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', function(e) {
        if (e.target === this) closeModal(this.id);
    });
});
// ESC key
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        document.querySelectorAll('.modal-overlay.active').forEach(m => closeModal(m.id));
    }
});

// Quick Amount
function setAmount(inputId, amount) {
    document.getElementById(inputId).value = amount;
    document.querySelectorAll(`#${inputId}`).forEach(() => {});
}

// Quick Category
function setCategory(inputId, cat, el) {
    document.getElementById(inputId).value = cat;
    // Toggle active pill
    el.closest('.cat-pills').querySelectorAll('.cat-pill').forEach(p => p.classList.remove('active'));
    el.classList.add('active');
}

// Filter Transactions
function filterTxn(type, btn) {
    const items = document.querySelectorAll('.txn-item');
    const emptyFiltered = document.getElementById('empty-filtered');
    let visible = 0;

    items.forEach(item => {
        if (type === 'all' || item.dataset.type === type) {
            item.style.display = 'flex';
            visible++;
        } else {
            item.style.display = 'none';
        }
    });

    emptyFiltered.style.display = visible === 0 && items.length > 0 ? 'block' : 'none';

    document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
    btn.classList.add('active');
}

// Auto hide flash after 5s
setTimeout(() => {
    document.querySelectorAll('.alert-flash').forEach(el => {
        el.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
        el.style.opacity = '0';
        el.style.transform = 'translateY(-10px)';
        setTimeout(() => el.remove(), 500);
    });
}, 5000);

// Animate stat bars on load
window.addEventListener('load', () => {
    document.querySelectorAll('.stat-bar-fill').forEach(bar => {
        const w = bar.style.width;
        bar.style.width = '0';
        setTimeout(() => bar.style.width = w, 100);
    });
});
</script>
@endsection
