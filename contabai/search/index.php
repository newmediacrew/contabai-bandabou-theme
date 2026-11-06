<?php

$preset = $preset ?? [];
$preset['country'] = ($preset['country'] ?? '') ?: 'cw';

$compact = ! empty($compact);

$durations = [
    ['nights' => 2, 'label' => __('Weekend', 'contabai')],
    ['nights' => 5, 'label' => __('Midweek', 'contabai')],
    ['nights' => 7, 'label' => __('1 week', 'contabai')],
    ['nights' => 14, 'label' => __('2 weeks', 'contabai')],
];

?>
<div x-data="contabaiSearch('<?php echo esc_js($resultsUrl); ?>', <?php echo esc_attr(wp_json_encode((object) array_filter($preset))); ?>)" x-on:keydown.escape="open = ''; sheet = false" x-on:click.outside="open = ''" x-on:contabai-search-open.window="if (open !== 'when') { toggle('when'); }" class="relative w-full">

    <div class="bandabou-search<?php echo $compact ? ' is-compact' : ''; ?>">
        <button type="button" x-on:click="toggle('when')" class="bandabou-search-field" x-bind:class="open === 'when' ? 'is-open' : ''">
            <span class="bandabou-search-label"><?php esc_html_e('Arrival', 'contabai-bandabou-theme'); ?></span>
            <span class="bandabou-search-value" x-bind:class="fromDate ? '' : 'is-empty'" x-text="fromDate ? fmt(fromDate) : '<?php echo esc_js(__('Pick a date', 'contabai-bandabou-theme')); ?>'"></span>
        </button>
        <button type="button" x-on:click="toggle('when')" class="bandabou-search-field" x-bind:class="open === 'when' ? 'is-open' : ''">
            <span class="bandabou-search-label"><?php esc_html_e('Departure', 'contabai-bandabou-theme'); ?></span>
            <span class="bandabou-search-value" x-bind:class="tillDate ? '' : 'is-empty'" x-text="tillDate ? fmt(tillDate) : '<?php echo esc_js(__('Pick a date', 'contabai-bandabou-theme')); ?>'"></span>
        </button>
        <button type="button" x-on:click="toggle('who')" class="bandabou-search-field" x-bind:class="open === 'who' ? 'is-open' : ''">
            <span class="bandabou-search-label"><?php esc_html_e('Guests', 'contabai-bandabou-theme'); ?></span>
            <span class="bandabou-search-value" x-text="whoLabel()"></span>
        </button>
        <button type="button" x-on:click="submit()" class="bandabou-search-submit"><?php esc_html_e('Check availability', 'contabai-bandabou-theme'); ?></button>
    </div>

    <div x-show="open || sheet" x-cloak
         x-effect="document.body.style.overflow = ((open || sheet) && window.matchMedia('(max-width: 639px)').matches) ? 'hidden' : ''"
         x-transition:enter="transition ease-out duration-120" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
         class="contabai-search-panel">

        <div class="contabai-search-sheet-head mb-4 items-center justify-between border-b border-neutral-200 pb-3">
            <span class="text-base font-semibold text-neutral-900"><?php echo esc_html__('Find your place', 'contabai'); ?></span>
            <button type="button" x-on:click="open = ''; sheet = false" aria-label="<?php echo esc_attr__('Close', 'contabai'); ?>"
                    class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-neutral-300 text-neutral-600 transition hover:bg-neutral-50">
                <?php echo \Contabai\Heroicon::outline('x-mark', 'w-5 h-5'); ?>
            </button>
        </div>

        <button type="button" x-on:click="toggle('when')"
                class="contabai-search-card mb-3 w-full items-center justify-between gap-3 rounded-xl border px-4 py-3 text-left transition"
                x-bind:class="open === 'when' ? 'border-[color:var(--theme-color,#EE6A3C)]' : 'border-neutral-200 hover:bg-neutral-50'">
            <span class="min-w-0">
                <span class="block text-sm font-semibold text-neutral-900"><?php echo esc_html__('When?', 'contabai'); ?></span>
                <span class="block truncate text-sm" x-bind:class="fromDate ? 'text-neutral-800' : 'text-neutral-400'"
                      x-text="whenLabel() || '<?php echo esc_js(__('Select dates', 'contabai')); ?>'"></span>
            </span>
            <span class="shrink-0 text-neutral-400 transition" x-bind:class="open === 'when' ? 'rotate-180' : ''"><?php echo \Contabai\Heroicon::outline('chevron-down', 'w-4 h-4'); ?></span>
        </button>

        <div x-show="open === 'when'" class="mb-3">
            <div class="mb-3 flex flex-wrap gap-2">
                <?php foreach ($durations as $d): ?>
                    <button type="button" x-on:click="setNights(<?php echo (int) $d['nights']; ?>)"
                            class="rounded-full border px-3 py-1.5 text-sm transition"
                            x-bind:class="nights === <?php echo (int) $d['nights']; ?> ? 'border-[color:var(--theme-color,#EE6A3C)] bg-[color:var(--theme-color,#EE6A3C)]/10 font-semibold text-neutral-900' : 'border-neutral-300 text-neutral-600 hover:bg-neutral-50'">
                        <?php echo esc_html($d['label']); ?> <span class="text-neutral-400"><?php echo esc_html('· ' . $d['nights']); ?></span>
                    </button>
                <?php endforeach; ?>
                <button type="button" x-on:click="setNights(0)"
                        class="rounded-full border px-3 py-1.5 text-sm transition"
                        x-bind:class="nights === 0 ? 'border-[color:var(--theme-color,#EE6A3C)] bg-[color:var(--theme-color,#EE6A3C)]/10 font-semibold text-neutral-900' : 'border-neutral-300 text-neutral-600 hover:bg-neutral-50'">
                    <?php echo esc_html__('Exact dates', 'contabai'); ?>
                </button>
            </div>

            <p class="mb-2 text-xs text-neutral-500" x-show="nights" x-cloak
               x-text="'<?php echo esc_js(__('Pick an arrival date — departure is set automatically.', 'contabai')); ?>'"></p>

            <div x-ref="cal"></div>

            <div x-show="counting || count !== null" x-cloak class="mt-3 flex items-center gap-3 border-t border-neutral-200 pt-3 text-sm">
                <span x-show="counting" x-cloak class="text-neutral-400"><?php echo esc_html__('Counting…', 'contabai'); ?></span>
                <span x-show="!counting && count !== null" x-cloak class="font-medium text-neutral-900"
                      x-text="countLabel()"></span>
            </div>
        </div>

        <button type="button" x-on:click="toggle('who')"
                class="contabai-search-card mb-3 w-full items-center justify-between gap-3 rounded-xl border px-4 py-3 text-left transition"
                x-bind:class="open === 'who' ? 'border-[color:var(--theme-color,#EE6A3C)]' : 'border-neutral-200 hover:bg-neutral-50'">
            <span class="min-w-0">
                <span class="block text-sm font-semibold text-neutral-900"><?php echo esc_html__('How many?', 'contabai'); ?></span>
                <span class="block truncate text-sm" x-bind:class="guests() > 0 ? 'text-neutral-800' : 'text-neutral-400'"
                      x-text="whoLabel() || '<?php echo esc_js(__('Number of people', 'contabai')); ?>'"></span>
            </span>
            <span class="shrink-0 text-neutral-400 transition" x-bind:class="open === 'who' ? 'rotate-180' : ''"><?php echo \Contabai\Heroicon::outline('chevron-down', 'w-4 h-4'); ?></span>
        </button>

        <div x-show="open === 'who'">
            <div class="flex items-center justify-between gap-4 py-2">
                <span>
                    <span class="block text-sm text-neutral-900"><?php echo esc_html__('Adults', 'contabai'); ?></span>
                </span>
                <span class="flex items-center gap-3">
                    <button type="button" x-on:click="adults = Math.max(1, adults - 1)" aria-label="<?php echo esc_attr__('Fewer', 'contabai'); ?>"
                            class="inline-flex h-8 w-8 items-center justify-center rounded-full border border-neutral-300 text-neutral-600 transition hover:bg-neutral-50"><?php echo \Contabai\Heroicon::outline('minus', 'w-4 h-4'); ?></button>
                    <span class="w-5 text-center text-sm font-medium" x-text="adults"></span>
                    <button type="button" x-on:click="adults = adults + 1" aria-label="<?php echo esc_attr__('More', 'contabai'); ?>"
                            class="inline-flex h-8 w-8 items-center justify-center rounded-full border border-neutral-300 text-neutral-600 transition hover:bg-neutral-50"><?php echo \Contabai\Heroicon::outline('plus', 'w-4 h-4'); ?></button>
                </span>
            </div>
            <div class="flex items-center justify-between gap-4 border-t border-neutral-200 py-2">
                <span>
                    <span class="block text-sm text-neutral-900"><?php echo esc_html__('Children', 'contabai'); ?></span>
                </span>
                <span class="flex items-center gap-3">
                    <button type="button" x-on:click="children = Math.max(0, children - 1)" aria-label="<?php echo esc_attr__('Fewer', 'contabai'); ?>"
                            class="inline-flex h-8 w-8 items-center justify-center rounded-full border border-neutral-300 text-neutral-600 transition hover:bg-neutral-50"><?php echo \Contabai\Heroicon::outline('minus', 'w-4 h-4'); ?></button>
                    <span class="w-5 text-center text-sm font-medium" x-text="children"></span>
                    <button type="button" x-on:click="children = children + 1" aria-label="<?php echo esc_attr__('More', 'contabai'); ?>"
                            class="inline-flex h-8 w-8 items-center justify-center rounded-full border border-neutral-300 text-neutral-600 transition hover:bg-neutral-50"><?php echo \Contabai\Heroicon::outline('plus', 'w-4 h-4'); ?></button>
                </span>
            </div>
            <p class="mt-2 border-t border-neutral-200 pt-2 text-xs text-neutral-500"><?php echo esc_html__('We only show places with room for everyone.', 'contabai'); ?></p>
        </div>

        <div class="contabai-search-sheet-foot sticky bottom-0 -mx-4 mt-2 flex-col items-stretch gap-2 border-t border-neutral-200 bg-white px-4 pb-2 pt-3">
            <button type="button" x-on:click="submit()" x-bind:disabled="! hasWhere()"
                    class="contabai-search-submit contabai-accent-bg text-sm font-semibold text-white transition hover:opacity-90 disabled:pointer-events-none disabled:opacity-40">
                <?php echo \Contabai\Heroicon::outline('magnifying-glass', 'w-5 h-5'); ?>
                <?php echo esc_html__('Search', 'contabai'); ?>
            </button>
            <button type="button" x-on:click="clearAll()" class="w-full rounded-lg border border-neutral-300 bg-white px-4 py-2.5 text-sm font-semibold text-neutral-800 transition hover:bg-neutral-50"><?php echo esc_html__('Clear all', 'contabai'); ?></button>
        </div>

    </div>
</div>

<?php if ($printScript): ?>
<script>
document.addEventListener('alpine:init', function () {
    Alpine.data('contabaiSearch', function (resultsUrl, preset) {
        return {
            open: '',
            sheet: false,
            listingsEndpoint: '<?php echo esc_js($listingsEndpoint); ?>',
            resultsUrl: resultsUrl,
            preset: preset || {},
            locale: '<?php echo esc_js(\Contabai\Helper::currentLang()); ?>',
            today: '<?php echo esc_js(gmdate('Y-m-d')); ?>',

            q: '',
            country: '',
            region: '',
            area: '',

            nights: 7,
            fromDate: '',
            tillDate: '',
            calendar: null,
            cols: 0,
            syncing: false,

            adults: 2,
            children: 0,

            count: null,
            counting: false,
            countTimer: null,

            clearAll: function () {
                this.fromDate = ''; this.tillDate = ''; this.nights = 7; this.count = null;
                this.adults = 2; this.children = 0;
                if (this.calendar) { this.syncing = true; try { this.calendar.selectedDates = []; this.calendar.update({ dates: true }); } finally { let self = this; setTimeout(function () { self.syncing = false; }, 0); } }
                this.open = '';
            },

            guests: function () {
                return this.adults + this.children;
            },

            hasWhere: function () {
                return !! (this.country || this.region || this.area);
            },

            whoLabel: function () {
                let guestCount = this.guests();
                if (! guestCount) { return ''; }
                return guestCount + ' ' + (guestCount === 1
                    ? '<?php echo esc_js(__('guest', 'contabai')); ?>'
                    : '<?php echo esc_js(__('guests', 'contabai')); ?>');
            },

            fmt: function (iso) {
                if (! iso) { return ''; }
                try { return new Date(iso + 'T00:00:00').toLocaleDateString(this.locale, { day: 'numeric', month: 'short' }); }
                catch (e) { return iso; }
            },

            whenLabel: function () {
                if (! this.fromDate) { return ''; }
                if (! this.tillDate) { return this.fmt(this.fromDate); }
                return this.fmt(this.fromDate) + ' → ' + this.fmt(this.tillDate);
            },

            countLabel: function () {
                let count = this.count;
                return count + ' ' + (count === 1
                    ? '<?php echo esc_js(__('property', 'contabai')); ?>'
                    : '<?php echo esc_js(__('properties', 'contabai')); ?>');
            },

            addDays: function (iso, days) {
                let parts = iso.split('-');
                let date = new Date(Number(parts[0]), Number(parts[1]) - 1, Number(parts[2]));
                date.setDate(date.getDate() + days);
                let month = String(date.getMonth() + 1).padStart(2, '0');
                let day = String(date.getDate()).padStart(2, '0');
                return date.getFullYear() + '-' + month + '-' + day;
            },

            setNights: function (n) {
                this.nights = n;
                if (n && this.fromDate) {
                    this.tillDate = this.addDays(this.fromDate, n);
                    this.syncCalendar();
                    this.queueCount();
                }
            },

            monthsForWidth: function () {
                return window.innerWidth >= 768 ? 2 : 1;
            },

            options: function (n) {
                let self = this;
                let calendarOptions = {
                    type: n === 1 ? 'default' : 'multiple',
                    displayMonthsCount: n,
                    monthsToSwitch: 1,
                    displayDatesOutside: false,
                    disableDatesPast: true,
                    dateToday: self.today,
                    selectionDatesMode: 'multiple-ranged',
                    locale: self.locale,
                    selectedTheme: 'light',
                    onClickDate: function (cal) { self.handleSelect(cal); }
                };
                // v3.4+ requires the months extension for multi-month display; register it always (extensions are constructor-only, so a later 1→2 month resize can't add it).
                if (window.VanillaCalendarMonths) { calendarOptions.extensions = [window.VanillaCalendarMonths]; }
                return calendarOptions;
            },

            handleSelect: function (cal) {
                if (this.syncing) { return; }
                let dates = (cal.context && cal.context.selectedDates) || [];
                if (! dates.length) { this.fromDate = ''; this.tillDate = ''; this.count = null; return; }

                if (this.nights) {
                    this.fromDate = dates[0];
                    this.tillDate = this.addDays(this.fromDate, this.nights);
                    this.syncCalendar();
                } else if (dates.length >= 2) {
                    this.fromDate = dates[0];
                    this.tillDate = dates[dates.length - 1];
                } else {
                    this.fromDate = dates[0];
                    this.tillDate = '';
                }
                this.queueCount();
            },

            syncCalendar: function () {
                if (! this.calendar || ! this.fromDate || ! this.tillDate) { return; }
                let days = [];
                let cursor = this.fromDate;
                let guard = 0;
                while (cursor <= this.tillDate && guard < 400) { days.push(cursor); cursor = this.addDays(cursor, 1); guard++; }
                let self = this;
                let fromParts = this.fromDate.split('-');
                self.syncing = true;
                setTimeout(function () {
                    try {
                        self.calendar.selectedDates = days;
                        self.calendar.selectedYear = Number(fromParts[0]);
                        self.calendar.selectedMonth = Number(fromParts[1]) - 1;
                        self.calendar.update({ dates: true, month: true, year: true });
                    }
                    finally { setTimeout(function () { self.syncing = false; }, 0); }
                }, 0);
            },

            queueCount: function () {
                let self = this;
                if (! self.fromDate || ! self.tillDate) { self.count = null; return; }
                clearTimeout(self.countTimer);
                self.counting = true;
                self.countTimer = setTimeout(function () { self.fetchCount(); }, 350);
            },

            fetchCount: function () {
                let self = this;
                let params = self.params();
                params.set('per_page', '1');
                fetch(self.listingsEndpoint + '?' + params.toString(), { credentials: 'same-origin', headers: { 'Accept': 'application/json' } })
                    .then(function (r) { return r.json(); })
                    .then(function (res) {
                        self.count = (res && res.data && typeof res.data.total === 'number') ? res.data.total : null;
                        self.counting = false;
                    })
                    .catch(function () { self.counting = false; self.count = null; });
            },

            params: function (withState) {
                let searchParams = new URLSearchParams();
                if (this.country) { searchParams.set('country', this.country); }
                if (this.region) { searchParams.set('region', this.region); }
                if (this.area) { searchParams.set('area', this.area); }
                if (this.fromDate && this.tillDate) {
                    searchParams.set('from_date', this.fromDate);
                    searchParams.set('till_date', this.tillDate);
                }
                if (this.guests() > 0) { searchParams.set('guests', this.guests()); }
                if (withState) {
                    if (this.nights) { searchParams.set('nights', this.nights); }
                    searchParams.set('adults', this.adults);
                    if (this.children) { searchParams.set('children', this.children); }
                }
                return searchParams;
            },

            init: function () {
                let queryParams = new URLSearchParams(window.location.search);
                let num = function (key, fallback) {
                    let value = parseInt(queryParams.get(key), 10);
                    return isNaN(value) ? fallback : value;
                };

                let fromUrl = queryParams.get('country') || queryParams.get('region') || queryParams.get('area');
                this.country = (fromUrl ? queryParams.get('country') : this.preset.country) || '';
                this.region = (fromUrl ? queryParams.get('region') : this.preset.region) || '';
                this.area = (fromUrl ? queryParams.get('area') : '') || '';

                let from = queryParams.get('from_date') || '';
                let till = queryParams.get('till_date') || '';
                if (from && till) { this.fromDate = from; this.tillDate = till; }

                this.nights = num('nights', this.nights);
                this.children = Math.max(0, num('children', 0));
                if (queryParams.get('adults')) {
                    this.adults = Math.max(1, num('adults', 2));
                } else if (queryParams.get('guests')) {
                    this.adults = Math.max(1, num('guests', 2) - this.children);
                }
            },

            toggle: function (key) {
                this.open = this.open === key ? '' : key;
                if (this.open === 'when') { this.mountCalendar(); }
            },

            mountCalendar: function (tries) {
                let self = this;
                let left = typeof tries === 'number' ? tries : 40;
                if (self.calendar) { return; }
                if (! window.VanillaCalendar || ! self.$refs.cal) {
                    if (left > 0) { setTimeout(function () { self.mountCalendar(left - 1); }, 50); }
                    return;
                }
                self.cols = self.monthsForWidth();
                self.calendar = new window.VanillaCalendar(self.$refs.cal, self.options(self.cols));
                self.calendar.init();
                window.addEventListener('resize', function () {
                    let months = self.monthsForWidth();
                    if (months !== self.cols && self.calendar) { self.cols = months; self.calendar.set(self.options(months)); }
                });
            },

            submit: function () {
                if (! this.hasWhere()) { return; }
                let queryString = this.params(true).toString();
                let sep = this.resultsUrl.indexOf('?') >= 0 ? '&' : '?';
                window.location = this.resultsUrl + (queryString ? sep + queryString : '');
            }
        };
    });
});
</script>
<?php endif; ?>
