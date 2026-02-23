// ==================== KONFIGURASI APLIKASI ====================
const APP_CONFIG = {
    name: 'ScheApp',
    version: '2.0',
    storageKey: 'scheapp_schedules_v2',
    themeKey: 'scheapp_theme',
    exportPrefix: 'scheapp-backup'
};

// ==================== VARIABEL GLOBAL ====================
let schedules = [];
let currentFilter = 'all';
let currentSort = 'date-asc';
let currentView = 'grid';
let currentSearch = '';
let editScheduleId = null;

// ==================== QUOTE MOTIVASI ====================
const MOTIVATION_QUOTES = {
    general: [
        "Jadwalkan dengan bijak, capai dengan semangat! 🚀",
        "Setiap jadwal adalah langkah menuju tujuan.",
        "Produktivitas dimulai dengan perencanaan yang baik.",
        "Hari ini akan menjadi luar biasa! ✨",
        "Fokus pada yang penting, selesaikan yang perlu.",
        "Waktu adalah aset berharga, gunakan dengan bijak."
    ],
    belajar: [
        "Belajar satu hal baru setiap hari.",
        "Pengetahuan adalah investasi terbaik.",
        "Membaca adalah jendela dunia.",
        "Belajarlah sepanjang hayat.",
        "Setiap pelajaran membuatmu lebih baik."
    ],
    kerja: [
        "Kerja keras membuahkan hasil manis.",
        "Profesionalisme adalah kunci kesuksesan.",
        "Detail membuat perbedaan besar.",
        "Fokus pada solusi, bukan masalah.",
        "Kolaborasi menghasilkan karya terbaik."
    ],
    meeting: [
        "Meeting yang efektif = hasil yang maksimal.",
        "Dengarkan lebih banyak, bicara seperlunya.",
        "Setiap ide berharga dalam diskusi.",
        "Persiapan meeting = keberhasilan meeting.",
        "Waktu meeting adalah waktu produktif."
    ],
    santai: [
        "Istirahat yang cukup, produktivitas yang optimal.",
        "Me-time penting untuk kesehatan mental.",
        "Nikmati momen, hidup hanya sekali.",
        "Santai bukan malas, tapi recharge energi.",
        "Keseimbangan adalah kunci kebahagiaan."
    ],
    hari: {
        senin: [
            "Semangat awal minggu! 💪",
            "Monday motivation: Start strong!",
            "Minggu baru, kesempatan baru!",
            "Senin adalah kanvas kosong."
        ],
        jumat: [
            "Akhiri minggu dengan pencapaian! 🎯",
            "Friday feeling: Almost there!",
            "Selesaikan dengan baik, weekend menanti.",
            "Jumat berkah, kerja maksimal."
        ],
        weekend: [
            "Weekend yang produktif dan menyenangkan! ☕",
            "Waktu untuk recharge dan quality time.",
            "Akhir pekan = waktu untuk passion.",
            "Nikmati hari libur dengan bijak."
        ]
    }
};

// ==================== FUNGSI UTILITY ====================
function getTodayDate() {
    const today = new Date();
    const year = today.getFullYear();
    const month = String(today.getMonth() + 1).padStart(2, '0');
    const day = String(today.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

function formatDate(dateString) {
    const date = new Date(dateString);
    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    return date.toLocaleDateString('id-ID', options);
}

function formatShortDate(dateString) {
    const date = new Date(dateString);
    const options = { day: 'numeric', month: 'short' };
    return date.toLocaleDateString('id-ID', options);
}

function getDayName(dateString) {
    const date = new Date(dateString);
    const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    return days[date.getDay()];
}

function isToday(dateString) {
    return dateString === getTodayDate();
}

function isPastDate(dateString) {
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    const date = new Date(dateString);
    return date < today;
}

function generateId() {
    return Date.now().toString(36) + Math.random().toString(36).substr(2);
}

function getMotivationQuote(category, dateString) {
    const dayName = getDayName(dateString).toLowerCase();
    let quotes = [...MOTIVATION_QUOTES.general];
    
    // Tambahkan quotes berdasarkan kategori
    if (MOTIVATION_QUOTES[category.toLowerCase()]) {
        quotes = [...quotes, ...MOTIVATION_QUOTES[category.toLowerCase()]];
    }
    
    // Tambahkan quotes berdasarkan hari
    if (dayName === 'senin' && MOTIVATION_QUOTES.hari.senin) {
        quotes = [...quotes, ...MOTIVATION_QUOTES.hari.senin];
    } else if (dayName === 'jumat' && MOTIVATION_QUOTES.hari.jumat) {
        quotes = [...quotes, ...MOTIVATION_QUOTES.hari.jumat];
    } else if ((dayName === 'sabtu' || dayName === 'minggu') && MOTIVATION_QUOTES.hari.weekend) {
        quotes = [...quotes, ...MOTIVATION_QUOTES.hari.weekend];
    }
    
    return quotes[Math.floor(Math.random() * quotes.length)];
}

function getDailyQuote() {
    const today = new Date();
    const dayName = getDayName(today.toISOString().split('T')[0]).toLowerCase();
    
    if (dayName === 'senin') {
        return "Semangat Senin! Mari mulai minggu dengan energi positif! 🌅";
    } else if (dayName === 'jumat') {
        return "Hampir weekend! Selesaikan tugas dengan baik! 💼";
    } else if (dayName === 'sabtu' || dayName === 'minggu') {
        return "Selamat menikmati akhir pekan! Isi dengan kegiatan bermakna! ☕";
    } else {
        return "Hari yang penuh kesempatan! Manfaatkan dengan baik! ✨";
    }
}

// ==================== LOCALSTORAGE MANAGEMENT ====================
function saveToLocalStorage() {
    try {
        localStorage.setItem(APP_CONFIG.storageKey, JSON.stringify(schedules));
        console.log('✅ Data tersimpan ke localStorage');
        return true;
    } catch (error) {
        console.error('❌ Gagal menyimpan:', error);
        showToast('error', 'Gagal Menyimpan', 'Storage mungkin penuh');
        return false;
    }
}

function loadFromLocalStorage() {
    try {
        const savedData = localStorage.getItem(APP_CONFIG.storageKey);
        if (savedData) {
            schedules = JSON.parse(savedData);
            console.log(`📂 Loaded ${schedules.length} schedules`);
            return true;
        }
    } catch (error) {
        console.error('❌ Gagal memuat data:', error);
    }
    return false;
}

// ==================== TOAST NOTIFICATION ====================
function showToast(type, title, message) {
    const container = document.getElementById('toast-container');
    const toastId = 'toast-' + Date.now();
    
    const iconMap = {
        success: 'fas fa-check-circle',
        error: 'fas fa-exclamation-circle',
        warning: 'fas fa-exclamation-triangle',
        info: 'fas fa-info-circle'
    };
    
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.id = toastId;
    
    toast.innerHTML = `
        <i class="${iconMap[type] || 'fas fa-info-circle'}"></i>
        <div class="toast-content">
            <h4>${title}</h4>
            <p>${message}</p>
        </div>
        <button class="toast-close" onclick="removeToast('${toastId}')">
            <i class="fas fa-times"></i>
        </button>
    `;
    
    container.appendChild(toast);
    
    setTimeout(() => removeToast(toastId), 5000);
}

function removeToast(toastId) {
    const toast = document.getElementById(toastId);
    if (toast) {
        toast.style.animation = 'slideInRight 0.3s ease reverse';
        setTimeout(() => toast.remove(), 300);
    }
}

// ==================== FORM VALIDATION ====================
function validateScheduleForm(data) {
    const { userName, groupName, activityName, date } = data;
    
    if (!userName.trim()) return { valid: false, message: 'Nama harus diisi' };
    if (!groupName.trim()) return { valid: false, message: 'Nama grup harus diisi' };
    if (!activityName.trim()) return { valid: false, message: 'Nama kegiatan harus diisi' };
    if (!date) return { valid: false, message: 'Tanggal harus dipilih' };
    if (isPastDate(date)) return { valid: false, message: 'Tanggal tidak boleh sudah lewat' };
    
    return { valid: true, message: 'Validasi berhasil' };
}

function showFormMessage(type, message) {
    const element = document.getElementById('form-message');
    element.textContent = message;
    element.className = `form-message ${type}`;
    
    setTimeout(() => {
        element.style.opacity = '0';
        setTimeout(() => {
            element.className = 'form-message';
            element.style.opacity = '1';
        }, 300);
    }, 3000);
}

// ==================== SCHEDULE MANAGEMENT ====================
function addSchedule(scheduleData) {
    const newSchedule = {
        id: generateId(),
        ...scheduleData,
        createdAt: new Date().toISOString(),
        motivation: getMotivationQuote(scheduleData.category, scheduleData.date)
    };
    
    schedules.push(newSchedule);
    saveToLocalStorage();
    updateStatistics();
    renderAll();
    
    showToast('success', 'Berhasil!', 'Jadwal berhasil ditambahkan');
    showFormMessage('success', 'Jadwal ditambahkan!');
    
    return newSchedule.id;
}

function updateSchedule(id, updatedData) {
    const index = schedules.findIndex(s => s.id === id);
    if (index === -1) return false;
    
    schedules[index] = {
        ...schedules[index],
        ...updatedData,
        motivation: getMotivationQuote(updatedData.category, updatedData.date)
    };
    
    saveToLocalStorage();
    updateStatistics();
    renderAll();
    
    showToast('success', 'Berhasil!', 'Jadwal berhasil diperbarui');
    return true;
}

function deleteSchedule(id) {
    if (!confirm('Hapus jadwal ini?')) return false;
    
    const index = schedules.findIndex(s => s.id === id);
    if (index === -1) return false;
    
    schedules.splice(index, 1);
    saveToLocalStorage();
    updateStatistics();
    renderAll();
    
    showToast('success', 'Berhasil!', 'Jadwal berhasil dihapus');
    return true;
}

// ==================== FILTER & SORT ====================
function getFilteredSchedules() {
    let filtered = [...schedules];
    
    // Search filter
    if (currentSearch.trim()) {
        const searchTerm = currentSearch.toLowerCase();
        filtered = filtered.filter(s => 
            s.userName.toLowerCase().includes(searchTerm) ||
            s.groupName.toLowerCase().includes(searchTerm) ||
            s.activityName.toLowerCase().includes(searchTerm) ||
            s.category.toLowerCase().includes(searchTerm)
        );
    }
    
    // Category filter
    if (currentFilter !== 'all') {
        filtered = filtered.filter(s => s.category === currentFilter);
    }
    
    // Sort
    switch (currentSort) {
        case 'date-asc':
            filtered.sort((a, b) => new Date(a.date) - new Date(b.date));
            break;
        case 'date-desc':
            filtered.sort((a, b) => new Date(b.date) - new Date(a.date));
            break;
        case 'name-asc':
            filtered.sort((a, b) => a.activityName.localeCompare(b.activityName));
            break;
        case 'name-desc':
            filtered.sort((a, b) => b.activityName.localeCompare(a.activityName));
            break;
    }
    
    return filtered;
}

// ==================== RENDER FUNCTIONS ====================
function renderTodaySchedules() {
    const container = document.getElementById('today-schedules-container');
    const today = getTodayDate();
    const todaySchedules = schedules.filter(s => s.date === today);
    
    if (todaySchedules.length === 0) {
        container.innerHTML = `
            <div class="schedule-card today">
                <div class="activity-name">Tidak ada jadwal hari ini</div>
                <div class="motivation-text">Nikmati harimu! 😊</div>
            </div>
        `;
        return;
    }
    
    container.innerHTML = todaySchedules.map(schedule => `
        <div class="schedule-card today">
            <div class="card-header">
                <div class="user-info">
                    <div class="user-name">${schedule.userName}</div>
                    <div class="group-name">${schedule.groupName}</div>
                </div>
                <span class="category-badge ${schedule.category.toLowerCase()}">
                    ${schedule.category}
                </span>
            </div>
            <div class="activity-name">${schedule.activityName}</div>
            <div class="date-info">
                <i class="fas fa-clock"></i>
                <span>Hari ini • ${schedule.time || 'Sepanjang hari'}</span>
            </div>
            <div class="motivation-text">"${schedule.motivation}"</div>
            <div class="card-actions">
                <button class="btn-icon small edit-btn" data-id="${schedule.id}">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn-icon small delete-btn" data-id="${schedule.id}">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    `).join('');
    
    attachScheduleEventListeners();
}

function renderSchedules() {
    const filtered = getFilteredSchedules();
    const container = document.getElementById('schedules-container');
    const tableBody = document.getElementById('table-body');
    const emptyState = document.getElementById('empty-state');
    const countElement = document.getElementById('schedule-count');
    
    // Update count
    countElement.textContent = `${filtered.length} jadwal`;
    
    // Empty state
    if (filtered.length === 0) {
        container.innerHTML = '';
        tableBody.innerHTML = '';
        emptyState.classList.add('active');
        return;
    }
    
    emptyState.classList.remove('active');
    
    // Grid view
    container.innerHTML = filtered.map(schedule => {
        const isToday = schedule.date === getTodayDate();
        const categoryClass = schedule.category.toLowerCase();
        
        return `
            <div class="schedule-card ${isToday ? 'today' : ''}">
                <div class="card-header">
                    <div class="user-info">
                        <div class="user-name">${schedule.userName}</div>
                        <div class="group-name">${schedule.groupName}</div>
                    </div>
                    <span class="category-badge ${categoryClass}">
                        ${schedule.category}
                    </span>
                </div>
                <div class="activity-name">${schedule.activityName}</div>
                <div class="date-info">
                    <i class="fas fa-calendar"></i>
                    <span>${formatDate(schedule.date)}${schedule.time ? ` • ${schedule.time}` : ''}</span>
                </div>
                <div class="motivation-text">"${schedule.motivation}"</div>
                <div class="card-actions">
                    <button class="btn-icon small edit-btn" data-id="${schedule.id}">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn-icon small delete-btn" data-id="${schedule.id}">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `;
    }).join('');
    
    // Table view
    tableBody.innerHTML = filtered.map(schedule => {
        const categoryClass = schedule.category.toLowerCase();
        
        return `
            <tr>
                <td>${formatShortDate(schedule.date)}</td>
                <td>
                    <strong>${schedule.activityName}</strong><br>
                    <small>${schedule.userName}</small>
                </td>
                <td>${schedule.groupName}</td>
                <td>
                    <span class="category-badge ${categoryClass}">
                        ${schedule.category}
                    </span>
                </td>
                <td>
                    <button class="btn-icon small edit-btn" data-id="${schedule.id}">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn-icon small delete-btn" data-id="${schedule.id}">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
    }).join('');
    
    attachScheduleEventListeners();
}

function renderAll() {
    renderTodaySchedules();
    renderSchedules();
}

function attachScheduleEventListeners() {
    // Edit buttons
    document.querySelectorAll('.edit-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            openEditModal(btn.dataset.id);
        });
    });
    
    // Delete buttons
    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            deleteSchedule(btn.dataset.id);
        });
    });
}

// ==================== STATISTICS ====================
function updateStatistics() {
    const today = getTodayDate();
    
    // Total schedules
    document.getElementById('total-schedules').textContent = schedules.length;
    
    // Today's schedules
    const todayCount = schedules.filter(s => s.date === today).length;
    document.getElementById('today-schedules-count').textContent = todayCount;
    
    // Active groups
    const groups = [...new Set(schedules.map(s => s.groupName))];
    document.getElementById('active-groups').textContent = groups.length;
    
    // Today's motivation
    document.getElementById('today-motivation').textContent = getDailyQuote();
}

// ==================== MODAL FUNCTIONS ====================
function openEditModal(id) {
    const schedule = schedules.find(s => s.id === id);
    if (!schedule) return;
    
    editScheduleId = id;
    
    // Fill form
    document.getElementById('edit-user-name').value = schedule.userName;
    document.getElementById('edit-group-name').value = schedule.groupName;
    document.getElementById('edit-activity-name').value = schedule.activityName;
    document.getElementById('edit-category').value = schedule.category;
    document.getElementById('edit-date').value = schedule.date;
    document.getElementById('edit-time').value = schedule.time || '';
    
    // Show modal
    document.getElementById('edit-modal').classList.add('active');
}

function closeEditModal() {
    document.getElementById('edit-modal').classList.remove('active');
    editScheduleId = null;
}

// ==================== THEME MANAGEMENT ====================
function toggleTheme() {
    const current = document.documentElement.getAttribute('data-theme');
    const newTheme = current === 'dark' ? 'light' : 'dark';
    
    document.documentElement.setAttribute('data-theme', newTheme);
    localStorage.setItem(APP_CONFIG.themeKey, newTheme);
    
    // Update icon
    const icon = document.querySelector('#theme-toggle i');
    icon.className = newTheme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
    
    showToast('info', 'Tema Diubah', `Mode ${newTheme === 'dark' ? 'Gelap' : 'Terang'}`);
}

function loadTheme() {
    const saved = localStorage.getItem(APP_CONFIG.themeKey) || 'light';
    document.documentElement.setAttribute('data-theme', saved);
    
    const icon = document.querySelector('#theme-toggle i');
    icon.className = saved === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
}

// ==================== EXPORT/IMPORT ====================
function exportData() {
    const data = {
        app: APP_CONFIG.name,
        version: APP_CONFIG.version,
        exportDate: new Date().toISOString(),
        schedules: schedules
    };
    
    const json = JSON.stringify(data, null, 2);
    const blob = new Blob([json], { type: 'application/json' });
    const url = URL.createObjectURL(blob);
    
    const a = document.createElement('a');
    a.href = url;
    a.download = `${APP_CONFIG.exportPrefix}-${getTodayDate()}.json`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
    
    showToast('success', 'Berhasil!', 'Data berhasil diekspor');
}

function importData(event) {
    const file = event.target.files[0];
    if (!file) return;
    
    const reader = new FileReader();
    reader.onload = function(e) {
        try {
            const data = JSON.parse(e.target.result);
            
            if (!data.schedules || !Array.isArray(data.schedules)) {
                throw new Error('Format file tidak valid');
            }
            
            if (confirm(`Impor ${data.schedules.length} jadwal?`)) {
                schedules = data.schedules;
                saveToLocalStorage();
                updateStatistics();
                renderAll();
                
                showToast('success', 'Berhasil!', `${data.schedules.length} jadwal diimpor`);
            }
        } catch (error) {
            console.error('Import error:', error);
            showToast('error', 'Gagal!', 'File tidak valid');
        }
    };
    
    reader.readAsText(file);
    event.target.value = '';
}

// ==================== INITIALIZATION ====================
function initApp() {
    console.log(`🚀 ${APP_CONFIG.name} v${APP_CONFIG.version} starting...`);
    
    // Load data
    loadFromLocalStorage();
    loadTheme();
    
    // Set today's date
    const todayInput = document.getElementById('date');
    if (todayInput) {
        todayInput.value = getTodayDate();
        todayInput.min = getTodayDate();
    }
    
    // Set current date display
    const today = getTodayDate();
    document.getElementById('current-day').textContent = getDayName(today);
    document.getElementById('current-date').textContent = formatDate(today);
    
    // Set footer year
    document.getElementById('current-year').textContent = new Date().getFullYear();
    
    // Initial render
    updateStatistics();
    renderAll();
    
    // Daily motivation
    document.getElementById('daily-motivation').textContent = getDailyQuote();
    
    console.log(`✅ ${APP_CONFIG.name} ready!`);
}

// ==================== EVENT LISTENERS ====================
document.addEventListener('DOMContentLoaded', () => {
    // Initialize
    initApp();
    
    // Form submit
    document.getElementById('schedule-form').addEventListener('submit', (e) => {
        e.preventDefault();
        
        const formData = {
            userName: document.getElementById('user-name').value.trim(),
            groupName: document.getElementById('group-name').value.trim(),
            activityName: document.getElementById('activity-name').value.trim(),
            category: document.getElementById('category').value,
            date: document.getElementById('date').value,
            time: document.getElementById('time').value || null
        };
        
        const validation = validateScheduleForm(formData);
        if (!validation.valid) {
            showFormMessage('error', validation.message);
            return;
        }
        
        addSchedule(formData);
        e.target.reset();
        document.getElementById('date').value = getTodayDate();
    });
    
    // Clear form
    document.getElementById('clear-form').addEventListener('click', () => {
        document.getElementById('schedule-form').reset();
        document.getElementById('date').value = getTodayDate();
        showFormMessage('success', 'Form berhasil direset');
    });
    
    // Theme toggle
    document.getElementById('theme-toggle').addEventListener('click', toggleTheme);
    
    // New quote
    document.getElementById('new-quote').addEventListener('click', () => {
        document.getElementById('daily-motivation').textContent = getDailyQuote();
        showToast('info', 'Motivasi Baru', 'Quote diperbarui!');
    });
    
    // Search
    document.getElementById('search-input').addEventListener('input', (e) => {
        currentSearch = e.target.value;
        renderSchedules();
    });
    
    // Clear search
    document.getElementById('clear-search').addEventListener('click', () => {
        document.getElementById('search-input').value = '';
        currentSearch = '';
        renderSchedules();
    });
    
    // Filters
    document.getElementById('filter-category').addEventListener('change', (e) => {
        currentFilter = e.target.value;
        renderSchedules();
    });
    
    document.getElementById('filter-sort').addEventListener('change', (e) => {
        currentSort = e.target.value;
        renderSchedules();
    });
    
    // View toggle
    document.querySelectorAll('.view-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.view-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            currentView = this.dataset.view;
            document.querySelectorAll('.view-content').forEach(view => {
                view.classList.remove('active');
            });
            document.getElementById(`${currentView}-view`).classList.add('active');
        });
    });
    
    // Export
    document.getElementById('export-btn').addEventListener('click', exportData);
    
    // Print
    document.getElementById('print-btn').addEventListener('click', () => {
        window.print();
        showToast('info', 'Print', 'Mempersiapkan cetakan...');
    });
    
    // Import
    document.getElementById('import-btn').addEventListener('click', () => {
        document.getElementById('import-file').click();
    });
    
    document.getElementById('import-file').addEventListener('change', importData);
    
    // Add sample data
    document.getElementById('add-sample').addEventListener('click', () => {
        const samples = [
            {
                userName: 'Ahmad',
                groupName: 'Tim Developer',
                activityName: 'Code Review Session',
                category: 'Meeting',
                date: getTodayDate(),
                time: '10:00'
            },
            {
                userName: 'Siti',
                groupName: 'Study Group',
                activityName: 'Belajar JavaScript',
                category: 'Belajar',
                date: getTodayDate(),
                time: '14:00'
            },
            {
                userName: 'Budi',
                groupName: 'Project Team',
                activityName: 'Progress Presentation',
                category: 'Kerja',
                date: getTodayDate(),
                time: '16:00'
            }
        ];
        
        samples.forEach(sample => addSchedule(sample));
        showToast('success', 'Contoh Ditambahkan', '3 contoh jadwal berhasil ditambahkan');
    });
    
    // Help
    document.getElementById('help-btn').addEventListener('click', () => {
        alert(`${APP_CONFIG.name} v${APP_CONFIG.version}\n\n💡 Cara Penggunaan:\n1. Isi form untuk tambah jadwal\n2. Filter dan cari sesuai kebutuhan\n3. Klik edit/hapus untuk kelola jadwal\n4. Ekspor untuk backup data\n\n✨ Semua data tersimpan di browser Anda!`);
    });
    
    // Modal
    document.querySelectorAll('.close-modal').forEach(btn => {
        btn.addEventListener('click', closeEditModal);
    });
    
    // Save edit
    document.getElementById('save-edit').addEventListener('click', () => {
        if (!editScheduleId) return;
        
        const formData = {
            userName: document.getElementById('edit-user-name').value.trim(),
            groupName: document.getElementById('edit-group-name').value.trim(),
            activityName: document.getElementById('edit-activity-name').value.trim(),
            category: document.getElementById('edit-category').value,
            date: document.getElementById('edit-date').value,
            time: document.getElementById('edit-time').value || null
        };
        
        const validation = validateScheduleForm(formData);
        if (!validation.valid) {
            showToast('error', 'Gagal', validation.message);
            return;
        }
        
        if (updateSchedule(editScheduleId, formData)) {
            closeEditModal();
        }
    });
    
    // Welcome message
    setTimeout(() => {
        showToast('success', 'Selamat Datang!', `${APP_CONFIG.name} siap digunakan!`);
    }, 1000);
    
    // Auto-save
    setInterval(() => {
        if (schedules.length > 0) {
            saveToLocalStorage();
        }
    }, 30000);
});

// ==================== GLOBAL FUNCTIONS ====================
window.removeToast = removeToast;

// Auto-save on exit
window.addEventListener('beforeunload', saveToLocalStorage);