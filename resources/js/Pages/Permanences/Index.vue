<script setup>
import Layout from '@/Layouts/LayoutAnnuaire.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

defineOptions({ layout: Layout });

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

const search = ref(props.search);
const startDate = ref(props.start_date || '');
const endDate = ref(props.end_date || '');
const sortOrder = ref(props.sort || 'desc');
const weekStatusFilter = ref(props.week_status || '');

function performSearch() {
    const params = { search: search.value };
    if (startDate.value) params.start_date = startDate.value;
    if (endDate.value) params.end_date = endDate.value;
    if (sortOrder.value) params.sort = sortOrder.value;
    if (weekStatusFilter.value) params.week_status = weekStatusFilter.value;
    
    router.get('/permanences', params, {
        preserveState: true,
        replace: true,
    });
}

function clearFilters() {
    search.value = '';
    startDate.value = '';
    endDate.value = '';
    sortOrder.value = 'desc';
    weekStatusFilter.value = '';
    router.get('/permanences', {}, {
        preserveState: true,
        replace: true,
    });
}

function deletePermanence(id) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette permanence ?')) {
        router.delete(`/permanences/${id}`);
    }
}

function deletePrecedantPermanences() {
    if (confirm('Êtes-vous sûr de vouloir supprimer toutes les permanences PRECEDANT ? Cette action est irréversible.')) {
        router.delete('/permanences/delete-precedant', {
            preserveState: true,
            onSuccess: () => {
                // Refresh the page after deletion
                router.get('/permanences', {}, {
                    preserveState: true,
                    replace: true,
                });
            }
        });
    }
}

function formatDate(dateString) {
  return new Intl.DateTimeFormat('fr-FR', {
    year: 'numeric', month: '2-digit', day: '2-digit'
  }).format(new Date(dateString));
}

function getNDappels(psName) {
  if (!psName) return [];
  const dest = props.destinations?.find(d => d.name === psName);
  if (!dest) return [];
  if (Array.isArray(dest.numeros) && dest.numeros.length > 0) {
    return dest.numeros.map(num => num.NDappel).filter(Boolean);
  }
  return dest.NDappel ? [dest.NDappel] : [];
}

function getMobileNDappels(psName) {
  if (!psName) return [];
  const dest = props.destinations?.find(d => d.name === psName);
  if (!dest) return [];
  if (Array.isArray(dest.numeros) && dest.numeros.length > 0) {
    // Only return NDappel where technologie.name === 'MOBILE'
    return dest.numeros
      .filter(num => num.technologie && num.technologie.name === 'MOBILE')
      .map(num => num.NDappel)
      .filter(Boolean);
  }
  return [];
}

// Export to Word functionality
function exportToWord() {
    // Get current date for the document number
    const currentDate = new Date();
    const currentYear = currentDate.getFullYear();
    
    // Function to format date with English digits
    function formatDateEnglish(dateString) {
        const date = new Date(dateString);
        const day = date.getDate().toString().padStart(2, '0');
        const month = (date.getMonth() + 1).toString().padStart(2, '0');
        const year = date.getFullYear();
        return `${day}/${month}/${year}`;
    }
    
    // Function to get Arabic month name
    function getArabicMonthName(date) {
        const months = [
            'يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو',
            'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'
        ];
        return months[date.getMonth()];
    }
    
    // Function to get phone number based on person name
    function getPhoneNumber(personName) {
        const phoneMap = {
            'خديمي محمـــد': '06-57-46-13-50',
            'عاشور عبد الرزاق': '06-99-50-91-27',
            'سكــوم الشيخ': '05-60-62-62-21',
            'ميموني بن عامر': '06-62-13-74-23',
            'ريغي أحمــــــــد': '07-74-17-71-57',
            'قطاف فوزي': '06-98-21-91-30',
            'جلولي محمد': '06-55-62-78-82'
        };
        return phoneMap[personName] || '';
    }
    
    // Get current date info for header
    const currentDay = currentDate.getDate();
    const currentMonth = currentDate.getMonth() + 1;
    const currentMonthName = getArabicMonthName(currentDate);
    const nextMonthDate = new Date(currentYear, currentMonth, currentDay);
    const nextMonthName = getArabicMonthName(nextMonthDate);
    
    // Filter permanences to only include future dates
    const allPermanences = props.permanences.data;

    // Generate table rows for all permanences
    const tableRows = allPermanences.map(p => {
        const startDate = formatDateEnglish(p.DSemaine);
        const endDate = formatDateEnglish(p.FSemaine);
        const phoneNumber = getPhoneNumber(p.PSemaine);
        
        return `
            <tr style='border:solid #000000;'>
                <td class='td1' style='border:solid #000000;'><h6>${p.PSemaine}</h6></td>
                <td style='border:solid #000000;'><h6>${startDate} من</h6><h6>${endDate} إلى</h6></td>
                <td style='border:solid #000000;'><h6 class='td2'>${phoneNumber}</h6></td>
                <td style='border:solid #000000;'><h6>${p.RSemaine}</h6></td>

            </tr>
        `;
    }).join('');

    const htmlContent = `
        <html dir="rtl">
        <head>
            <meta charset='utf-8'>
            <title>رزنامة المداومة</title>
            <style>
                body { font-family: Arial, sans-serif; }
                .table { position: relative; right: 1px; text-align: center; }
                table { text-align: center; width: 500px; }
                th { width: 20%; background: #aaaaaa; border: solid #000000; }
                th:last-child { width: 30%; }
                td { border: solid #000000; }
                h5 { margin: 0; padding: 5px; }
                h6 { margin: 0; padding: 3px; }
            </style>
        </head>
        <body>
            <div id="im">
                <h5 style="text-align: center; text-decoration: underline #000000;">الجمهورية الجزائرية الديمقراطية الشعبية</h5>
                <div style="width: 1200px; margin: auto; display: flex;">
                    <div style="display: flex; width: 20%;">
                        <h6>
                            <div style="display: flex;">
                                <div id="divim">البيض في: ${currentDay}/${currentMonth}/${currentYear}</div>
                            </div>
                        </h6>
                    </div>
                    <div style="width: 50%;"></div>
                    <div>
                        <h6 style="text-align: right; text-decoration: underline #000000;">ولايـــــــة البيـــــــض</h6>
                        <h6 style="text-align: right; text-decoration: underline #000000;">مديرية المواصلات السلكية واللاسلكية</h6>
                        <h6 style="text-align: right; text-decoration: underline #000000;">مصلحــــة الصيانــــة</h6>
                        <h6 style="text-align: right; text-decoration: underline #000000;" id="h2him">رقم:........../م.م.س.ل/${currentYear}</h6>
                    </div>
                </div>
                <h5 style="text-align: center;">رزنامة المداومة الخاصة بتقنيي مصلحة الصيانة</h5>
                <h6 id="h1im" style="text-align: center; direction: rtl;">شهر${currentMonthName}/${nextMonthName} ${currentYear}-</h6>

                <div class="table table-bordered" style="position: relative; right: 1px; text-align: center;">
                    <table style="text-align: center; width: 500px;">
                        <tr style='border:solid #000000;'>
                           
                            <th style="width:30%; background: #aaaaaa; border:solid #000000;">
                                <h5>اسم ولقب <br>المداوم</h5>
                            </th>
                            <th style="width:20%; background: #aaaaaa; border:solid #000000;">
                                <h5>التاريخ</h5>
                            </th>
                            <th style="width:20%; background: #aaaaaa; border:solid #000000;">
                                <h5>رقم الهاتف</h5>
                            </th>
                             <th style="width:20%; background: #aaaaaa; border:solid #000000;">
                                <h5>اسم ولقب<br>المرخص له بالغياب</h5>
                            </th>
                        </tr>
                        ${tableRows}
                    </table>
                </div>

                <div style="width: 1200px;">
                    <h6 style="text-align: right; text-decoration:underline;"> ملاحظات  : </h6>
                    <h6 style="text-align: right; text-decoration:underline;"> : فترات الحضور الضروري داخل المصلحة - I</h6>
                    <h6 style="text-align: right;"> أيام العمل : من 12 سا 00 إلى الساعة 14 سا و من 16سا30د إلى 18 سا00 - </h6>
                    <h6 style="text-align: right;">يوم السبت: من 09 سا 00 إلى الساعة 12 سا 00 - </h6>
                    <h6 style="text-align: right; text-decoration:underline;">: الاستعداد للتدخل - II</h6>
                    <h6 style="text-align: right;"> أيام العمل : بعد الساعة 18 سا 00 - </h6>
                    <h6 style="text-align: right;">يوم السبت : بعد الساعة 12 سا 00 - </h6>
                    <h6 style="text-align: right;">أيام الجمعة و الأعياد -</h6>
                    <h6 style="">ا لمــــــديــــــر</h6>
                </div>
            </div>
        </body>
        </html>
    `;

    const blob = new Blob([htmlContent], { type: 'application/msword' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = `رزنامة_المداومة_${currentDate.toISOString().split('T')[0]}.doc`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    URL.revokeObjectURL(url);
}
</script>

<template>
    <Head title="Permanences" />

    <div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h1 class="text-4xl font-bold text-gray-900 mb-2">Permanences</h1>
                        <p class="text-lg text-gray-600">Gestion des horaires de permanence</p>
                    </div>
                    <div class="flex gap-3">
                        <button
                            @click="exportToWord"
                            class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg flex items-center gap-3 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-1"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Exporter en Word
                        </button>
                        <button
                            @click="deletePrecedantPermanences"
                            class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg flex items-center gap-3 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-1"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Supprimer PRECEDANT
                        </button>
                        <Link 
                            :href="route('permanences.create')" 
                            class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-6 py-3 rounded-lg flex items-center gap-3 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-1"
                        >
                            <svg class="w-6 h-6" viewBox="0 0 24 24" stroke="currentColor" fill="none">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Nouvelle Permanence
                        </Link>
                    </div>
                </div>

                <!-- Search and Filter Bar -->
                <div class="bg-white rounded-xl shadow-md p-6 mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                        <div class="md:col-span-2">
                            <input
                                v-model="search"
                                @keyup.enter="performSearch"
                                type="text"
                                placeholder="Rechercher des permanences..."
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                            />
                        </div>
                        <div>
                            <input
                                v-model="startDate"
                                type="date"
                                placeholder="Date de début"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                            />
                        </div>
                        <div>
                            <input
                                v-model="endDate"
                                type="date"
                                placeholder="Date de fin"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                            />
                        </div>
                        <div>
                            <select
                                v-model="weekStatusFilter"
                                @change="performSearch"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                            >
                                <option value="">Tous les statuts</option>
                                <option value="MANTENANT">MANTENANT</option>
                                <option value="PROCHAIN">PROCHAIN</option>
                                <option value="PRECEDANT">PRECEDANT</option>
                            </select>
                        </div>
                        <div>
                            <button
                                @click="sortOrder = sortOrder === 'desc' ? 'asc' : 'desc'; performSearch()"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 bg-white hover:bg-gray-50 flex items-center justify-center gap-2"
                                :title="sortOrder === 'desc' ? 'Trier par date (plus récent en premier)' : 'Trier par date (plus ancien en premier)'"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                                </svg>
                                {{ sortOrder === 'desc' ? 'Plus récent' : 'Plus ancien' }}
                            </button>
                        </div>
                    </div>
                    <div class="flex gap-3 mt-4">
                        <button
                            @click="performSearch"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg transition-all duration-200 font-medium"
                        >
                            Rechercher
                        </button>
                        <button
                            @click="clearFilters"
                            class="bg-gray-500 hover:bg-gray-600 text-white px-8 py-3 rounded-lg transition-all duration-200 font-medium"
                        >
                            Effacer
                        </button>
                    </div>
                </div>

                <!-- Success Message -->
                <div v-if="$page.props.flash.success" class="mb-6 bg-green-100 border border-green-400 text-green-700 px-6 py-4 rounded-lg">
                    {{ $page.props.flash.success }}
                </div>
            </div>

            <!-- Table Container -->
            <div class="bg-white rounded-xl shadow-xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                            <tr>
                                <th class="px-8 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                    Période
                                </th>
                                <th class="px-8 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                    Statut
                                </th>
                                <th class="px-8 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                    PERMANENCE
                                </th>
                                <th class="px-8 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                    REPOS
                                </th>
                                <th class="px-8 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                    Numéros de Téléphone
                                </th>
                                <th class="px-8 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="p in permanences.data" :key="p.id" class="hover:bg-gray-50 transition-colors duration-200">
                                <td class="px-8 py-6 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        Du {{ formatDate(p.DSemaine) }} au {{ formatDate(p.FSemaine) }}
                                    </div>
                                </td>
                                <td class="px-8 py-6 whitespace-nowrap">
                                    <div v-if="p.week_status" 
                                         :class="`inline-flex items-center px-3 py-1 rounded-full text-xs font-medium border ${p.week_status_color}`">
                                        <span v-if="p.week_status === 'MANTENANT'" class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                                        <span v-else-if="p.week_status === 'PROCHAIN'" class="w-2 h-2 bg-blue-500 rounded-full mr-2"></span>
                                        <span v-else class="w-2 h-2 bg-gray-500 rounded-full mr-2"></span>
                                        {{ p.week_status }}
                                    </div>
                                </td>
                                <td class="px-8 py-6 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-blue-600">{{ p.PSemaine }}</div>
                                </td>
                                <td class="px-8 py-6 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-green-600">{{ p.RSemaine }}</div>
                                </td>
                                <td class="px-8 py-6">
                                    <div v-if="getMobileNDappels(p.PSemaine).length > 0" class="space-y-1">
                                        <div v-for="num in getMobileNDappels(p.PSemaine)" :key="num" 
                                             class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mr-2 mb-1">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>
                                            </svg>
                                            {{ num }}
                                        </div>
                                    </div>
                                    <div v-else class="text-sm text-gray-500 italic">
                                        Aucun numéro mobile
                                    </div>
                                </td>
                                <td class="px-8 py-6 whitespace-nowrap text-sm font-medium">
                                    <div class="flex gap-3">
                                        <Link
                                            :href="route('permanences.show', p.id)"
                                            class="text-blue-600 hover:text-blue-900 font-medium transition-colors duration-200"
                                        >
                                            Voir
                                        </Link>
                                        <Link
                                            :href="route('permanences.edit', p.id)"
                                            class="text-indigo-600 hover:text-indigo-900 font-medium transition-colors duration-200"
                                        >
                                            Modifier
                                        </Link>
                                        <button
                                            @click="deletePermanence(p.id)"
                                            class="text-red-600 hover:text-red-900 font-medium transition-colors duration-200"
                                        >
                                            Supprimer
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="permanences.data.length === 0">
                                <td colspan="6" class="px-8 py-12 text-center">
                                    <div class="text-gray-500">
                                        <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <h3 class="text-lg font-medium text-gray-900 mb-2">Aucune permanence trouvée</h3>
                                        <p class="text-gray-600 mb-4">Commencez par créer une nouvelle permanence.</p>
                                        <Link
                                            :href="route('permanences.create')"
                                            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700"
                                        >
                                            Nouvelle Permanence
                                        </Link>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="permanences.links.length > 3" class="bg-white px-6 py-4 border-t border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 flex justify-between sm:hidden">
                            <Link
                                v-if="permanences.prev_page_url"
                                :href="permanences.prev_page_url"
                                class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                            >
                                Précédent
                            </Link>
                            <Link
                                v-if="permanences.next_page_url"
                                :href="permanences.next_page_url"
                                class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                            >
                                Suivant
                            </Link>
                        </div>
                        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-gray-700">
                                    Affichage de
                                    <span class="font-medium">{{ permanences.from }}</span>
                                    à
                                    <span class="font-medium">{{ permanences.to }}</span>
                                    sur
                                    <span class="font-medium">{{ permanences.total }}</span>
                                    résultats
                                </p>
                            </div>
                            <div>
                                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                                    <Link
                                        v-for="(link, index) in permanences.links"
                                        :key="index"
                                        :href="link.url"
                                        v-html="link.label"
                                        class="relative inline-flex items-center px-4 py-2 border text-sm font-medium transition-colors duration-200"
                                        :class="[
                                            link.url === null
                                                ? 'bg-gray-100 text-gray-400 cursor-not-allowed'
                                                : link.active
                                                ? 'z-10 bg-blue-50 border-blue-500 text-blue-600'
                                                : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'
                                        ]"
                                    />
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
