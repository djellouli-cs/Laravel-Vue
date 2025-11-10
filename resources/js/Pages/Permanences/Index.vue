<script setup>
import Layout from '@/Layouts/LayoutAnnuaire.vue';
import { Head, Link, router as inertiaRouter } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { ref } from 'vue';

defineOptions({ layout: Layout });

// Props
const props = defineProps({
    permanences: Object,
    search: String,
    destinations: Array,
    errors: Object,
    flash: Object,
    auth: Object,
    start_date: String,
    end_date: String,
    sort: String,
    week_status: String,
});

// Refs
const searchTerm = ref(props.search);
const startDate = ref(props.start_date || '');
const endDate = ref(props.end_date || '');
const sortOrder = ref(props.sort || 'desc');
const weekStatusFilter = ref(props.week_status || '');

// Search & Filters
function performSearch() {
    const params = { search: searchTerm.value };
    if (startDate.value) params.start_date = startDate.value;
    if (endDate.value) params.end_date = endDate.value;
    if (sortOrder.value) params.sort = sortOrder.value;
    if (weekStatusFilter.value) params.week_status = weekStatusFilter.value;

    inertiaRouter.get('/permanences', params, { preserveState: true, replace: true });
}

function clearFilters() {
    searchTerm.value = '';
    startDate.value = '';
    endDate.value = '';
    sortOrder.value = 'desc';
    weekStatusFilter.value = '';
    inertiaRouter.get('/permanences', {}, { preserveState: true, replace: true });
}

// Delete
function deletePermanence(id) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette permanence ?')) {
        inertiaRouter.delete(`/permanences/${id}`);
    }
}

function deletePrecedantPermanences() {
    if (confirm('Êtes-vous sûr de vouloir supprimer toutes les permanences PRECEDANT ?')) {
        inertiaRouter.delete('/permanences/delete-precedant', {
            preserveState: true,
            onSuccess: () => inertiaRouter.get('/permanences', {}, { preserveState: true, replace: true })
        });
    }
}

// Helpers
function formatDate(dateString) {
    return new Intl.DateTimeFormat('fr-FR', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit'
    }).format(new Date(dateString));
}

function getMobileNDappels(psName) {
    if (!psName) return [];
    const dest = props.destinations?.find(d => d.name === psName);
    if (!dest || !Array.isArray(dest.numeros)) return [];
    return dest.numeros
        .filter(num => num.technologie?.name === 'MOBILE')
        .map(num => num.NDappel)
        .filter(Boolean);
}

/** EXPORT WORD */
function exportToWord() {
    const currentDate = new Date();
    const currentYear = currentDate.getFullYear();

    const arabicMonths = [
        'يناير','فبراير','مارس','أبريل','مايو','يونيو',
        'يوليو','أغسطس','سبتمبر','أكتوبر','نوفمبر','ديسمبر'
    ];

    const monthNow = arabicMonths[currentDate.getMonth()];
    const nextMonthDate = new Date(currentYear, currentDate.getMonth() + 1, 1);
    const monthNext = arabicMonths[nextMonthDate.getMonth()];

    const todayFormatted = `${currentDate.getDate()}/${currentDate.getMonth() + 1}/${currentDate.getFullYear()}`;

    const rows = [...props.permanences.data].sort(
        (a, b) => new Date(a.DSemaine) - new Date(b.DSemaine)
    ).map(p => `
        <tr>
            <td>${p.PSemaine ?? ''}</td>
            <td>من ${formatDate(p.DSemaine)} <br/>إلى ${formatDate(p.FSemaine)} </td>
            <td>${getMobileNDappels(p.PSemaine).join(', ') || 'لا يوجد رقم'}</td>
            <td>${p.RSemaine ?? ''}</td>
        </tr>
    `).join('');

    const html = `
    <html dir="rtl">
    <head><meta charset="utf-8"></head>
    <body>

    <h5 style="text-align:center;text-decoration:underline;">الجمهورية الجزائرية الديمقراطية الشعبية</h5>

    <div style="width:1200px;margin:auto;display:flex;">
      <div style="width:20%;"><h6>البيض في : ${todayFormatted}</h6></div>
      <div style="width:50%;"></div>
      <div style="text-align:right;">
        <h6 style="text-decoration:underline;">ولايـــــــة البيـــــــض</h6>
        <h6 style="text-decoration:underline;">مديرية المواصلات السلكية واللاسلكية</h6>
        <h6 style="text-decoration:underline;">مصلحــــة الصيانــــة</h6>
        <h6 style="text-decoration:underline;">رقم:........../م.م.س.ل/</h6>
      </div>
    </div>

    <h5 style="text-align:center">رزنامة المداومة الخاصة بتقنيي مصلحة الصيانة</h5>
    <h6 style="text-align:center">شهر ${monthNow} / ${monthNext} ${currentYear}</h6>

    <table border="1" style="width:100%;border-collapse:collapse;text-align:center;margin-top:20px;">
      <tr>
        <th>اسم ولقب المداوم</th>
        <th>التاريخ</th>
        <th>رقم الهاتف</th>
        <th>اسم ولقب المرخص له بالغياب</th>
      </tr>
      ${rows}
    </table>

    <div style="width:1200px;margin-top:20px;text-align:right;">

        <h6 style="text-decoration:underline;">: ملاحظات</h6>
        <h6 style="text-decoration:underline;">: فترات الحضور الضروري داخل المصلحة - I</h6>
        <h6>أيام العمل : من 12 سا 00 إلى الساعة 14 سا و من 16سا30د  إلى 18 سا00</h6>
        <h6>يوم السبت : من 09 سا 00 إلى الساعة 12 سا 00</h6>

        <h6 style="text-decoration:underline;margin-top:10px;">: الاستعداد للتدخل - II</h6>
        <h6>أيام العمل : بعد الساعة 18 سا 00</h6>
        <h6>يوم السبت : بعد الساعة 12 سا 00</h6>
        <h6>أيام الجمعة و الأعياد</h6>

        <div style="margin-top:40px;text-align:left;">
            <h6>المدير</h6>
        </div>
    </div>

    </body>
    </html>
    `;

    const blob = new Blob([html], { type: "application/msword" });
    const url = URL.createObjectURL(blob);
    const a = document.createElement("a");
    a.href = url;
    a.download = `رزنامة_المداومة_${currentDate.toISOString().split('T')[0]}.doc`;
    a.click();
    URL.revokeObjectURL(url);
}
</script>

<template>
    <Head title="Permanences" />

    <div class="min-h-screen bg-gray-100 p-6 flex justify-center">
        <div class="w-full max-w-6xl bg-white shadow-xl rounded-2xl p-6">

            <!-- Header -->
            <h1 class="text-2xl font-bold text-gray-700 mb-6 text-center">
                Gestion des Permanences
            </h1>
<!-- Action Buttons -->
<div class="flex flex-wrap justify-between gap-3 mb-5">
    <Link 
        :href="route('permanences.create')" 
        class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-5 py-2 rounded-xl shadow transition">
        ➕ Ajouter Permanence
    </Link>
    <button @click="exportToWord"
        class="bg-green-600 hover:bg-green-700 text-white font-medium px-5 py-2 rounded-xl shadow transition">
        📄 Export Word
    </button>
    <button @click="deletePrecedantPermanences"
        class="bg-red-600 hover:bg-red-700 text-white font-medium px-5 py-2 rounded-xl shadow transition">
        🗑 Supprimer PRECEDANT
    </button>
</div>

            <!-- Filters -->
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-3 mb-6 bg-gray-50 p-4 rounded-xl shadow-sm">
                <input v-model="searchTerm" placeholder="🔍 Rechercher..."
                    class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none text-sm" />
                <input type="date" v-model="startDate"
                    class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none text-sm" />
                <input type="date" v-model="endDate"
                    class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none text-sm" />
                <select v-model="weekStatusFilter"
                    class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none text-sm">
                    <option value="">Tous</option>
                    <option value="MANTENANT">MANTENANT</option>
                    <option value="PROCHAIN">PROCHAIN</option>
                    <option value="PRECEDANT">PRECEDANT</option>
                </select>
                <button @click="performSearch"
                    class="bg-blue-600 hover:bg-blue-700 text-white rounded-lg px-4 py-2 font-medium transition">Search</button>
                <button @click="clearFilters"
                    class="bg-gray-500 hover:bg-gray-600 text-white rounded-lg px-4 py-2 font-medium transition">Clear</button>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto rounded-xl shadow">
                <table class="w-full text-sm text-left border-collapse">
                    <thead class="bg-gray-200 text-gray-700">
                        <tr>
                            <th class="py-3 px-4 text-center font-semibold">Période</th>
                            <th class="py-3 px-4 text-center font-semibold">Statut</th>
                            <th class="py-3 px-4 text-center font-semibold">Permanence</th>
                            <th class="py-3 px-4 text-center font-semibold">Repos</th>
                            <th class="py-3 px-4 text-center font-semibold">Numéros</th>
                            <th class="py-3 px-4 text-center font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="p in permanences.data" :key="p.id"
                            class="border-b hover:bg-gray-50 transition">
                            <td class="py-3 px-4 text-center">{{ formatDate(p.DSemaine) }} → {{ formatDate(p.FSemaine) }}</td>
                            <td class="py-3 px-4 text-center">
                                <span class="px-2 py-1 rounded text-xs font-semibold"
                                    :class="{
                                        'bg-green-100 text-green-700': p.week_status === 'MANTENANT',
                                        'bg-blue-100 text-blue-700': p.week_status === 'PROCHAIN',
                                        'bg-gray-200 text-gray-700': p.week_status === 'PRECEDANT'
                                    }">
                                    {{ p.week_status }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center">{{ p.PSemaine }}</td>
                            <td class="py-3 px-4 text-center">{{ p.RSemaine }}</td>
                            <td class="py-3 px-4 text-center">{{ getMobileNDappels(p.PSemaine).join(', ') || '—' }}</td>
                            <td class="py-3 px-4 text-center">
                                <Link :href="route('permanences.show', p.id)" class="text-blue-600 mx-1 hover:underline font-medium">Voir</Link>
                                <Link :href="route('permanences.edit', p.id)" class="text-green-600 mx-1 hover:underline font-medium">Edit</Link>
                                <button @click="deletePermanence(p.id)" class="text-red-600 mx-1 hover:underline font-medium">X</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</template>
