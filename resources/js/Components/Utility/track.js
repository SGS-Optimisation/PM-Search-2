import { ref } from 'vue'
import {usePage}     from "@inertiajs/vue3";

export function trackPage(title, url = null) {
    if (!window.matomo_settings.matomo_tracking_enabled)
        return;

    url = url || window.location.href;

    window._paq.push(['setCustomUrl', url ]);
    window._paq.push(['setDocumentTitle', title]);
    window._paq.push(['trackPageView', title]);
}
export function trackSearch(title, url = null, search_data = null, report = null) {
    if (!window.matomo_settings.matomo_tracking_enabled)
        return;

    url = url || window.location.href;

    console.log('tracking ' + title);

    return;

    window._paq.push(['setCustomUrl', url ]);
    window._paq.push(['setDocumentTitle', title]);
    window._paq.push([
        'trackSiteSearch',
        this.searchName, (this.search_data ? this.search_data.type : 'image'),
        this.report.length
    ]);
}
