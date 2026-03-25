// resources/js/Stores/lang.js
import { ref, computed } from 'vue'

export const lang = ref(localStorage.getItem('lang') || 'fr')

export function switchLang(l) {
    lang.value = l
    localStorage.setItem('lang', l)
}

export const isRTL = computed(() => lang.value === 'ar')

// Global helper to get the correct name
export function getName(obj) {
    if (!obj) return ''
    return lang.value === 'fr' ? obj.name_fr || obj.name || '' : obj.name || obj.name_fr || ''
}