<?php

$chevronLeft  = \Contabai\Heroicon::solid('chevron-left', 'w-4 h-4');
$chevronRight = \Contabai\Heroicon::solid('chevron-right', 'w-4 h-4');
?>
<div class="bandabou-card group"<?php if (! empty($cardMap)): ?> x-on:mouseenter="hoveredId = listing.id" x-on:mouseleave="hoveredId = null"<?php endif; ?>>
    <div class="relative aspect-[4/3] overflow-hidden bg-neutral-100"
         x-data="{
            active: 0, startX: 0, animate: true,
            step(dir) {
                let count = slides(listing).length;
                if (count < 2 || (this.active === count && dir > 0)) return;
                if (dir > 0) { this.animate = true; this.active++; return; }
                if (this.active === 0) {
                    this.animate = false; this.active = count;
                    requestAnimationFrame(() => requestAnimationFrame(() => { this.animate = true; this.active = count - 1; }));
                    return;
                }
                this.animate = true; this.active--;
            },
            settle() {
                if (this.active === slides(listing).length) { this.animate = false; this.active = 0; }
            }
         }"
         x-on:touchstart.passive="startX = $event.touches[0].clientX"
         x-on:touchend="let dx = $event.changedTouches[0].clientX - startX; if (Math.abs(dx) >= 40) step(dx < 0 ? 1 : -1)">
        <div class="flex h-full w-full" x-bind:class="animate ? 'transition-transform duration-300 ease-out' : ''"
             x-on:transitionend.self="settle()"
             x-bind:style="'transform: translateX(-' + (active * 100) + '%)'">
            <template x-for="(photo, idx) in loopSlides(listing)" x-bind:key="idx">
                <img x-bind:src="photo.card" x-bind:alt="photo.alt" loading="lazy" draggable="false"
                     class="h-full w-full flex-none object-cover">
            </template>
        </div>

        <a x-bind:href="detailUrl(listing)" class="absolute inset-0 z-10" aria-label="<?php echo esc_attr__('View listing', 'contabai'); ?>"></a>

        <template x-if="slides(listing).length > 1">
            <div>
                <button type="button" x-on:click="step(-1)" aria-label="<?php echo esc_attr__('Previous photo', 'contabai'); ?>"
                        class="absolute left-2 top-1/2 z-20 flex h-7 w-7 -translate-y-1/2 items-center justify-center rounded-full bg-white/90 text-neutral-700 opacity-0 transition group-hover:opacity-100 hover:bg-white"><?php echo $chevronLeft; ?></button>
                <button type="button" x-on:click="step(1)" aria-label="<?php echo esc_attr__('Next photo', 'contabai'); ?>"
                        class="absolute right-2 top-1/2 z-20 flex h-7 w-7 -translate-y-1/2 items-center justify-center rounded-full bg-white/90 text-neutral-700 opacity-0 transition group-hover:opacity-100 hover:bg-white"><?php echo $chevronRight; ?></button>
            </div>
        </template>
    </div>

    <a x-bind:href="detailUrl(listing)" class="bandabou-card-body">
        <span>
            <span class="bandabou-card-title contabai-seo-place" x-text="listing.title"></span>
            <span class="mt-1 flex items-center gap-1 text-sm text-[#5A5348]">
                <span x-text="[areaName(listing), regionName(listing)].filter(Boolean).join(' · ')"></span>
                <span x-show="reviewCount(listing) > 0" class="ml-auto inline-flex items-center gap-1">
                    <span class="text-amber-400"><?php echo \Contabai\Heroicon::solid('star', 'w-4 h-4'); ?></span>
                    <span class="font-medium text-bandabou-ink" x-text="ratingLabel(listing)"></span>
                </span>
            </span>
        </span>
        <span class="bandabou-card-facts">
            <span><?php echo \Contabai\Heroicon::outline('users', 'w-[17px] h-[17px]'); ?><span x-text="guestsLabel(listing.max_guests || 0)"></span></span>
            <span><?php echo \Contabai\Heroicon::outline('home', 'w-[17px] h-[17px]'); ?><span x-text="detailsLabel(listing).split(' · ')[1]"></span></span>
            <span><?php echo \Contabai\Heroicon::outline('home-modern', 'w-[17px] h-[17px]'); ?><span x-text="detailsLabel(listing).split(' · ')[2]"></span></span>
        </span>
        <span class="bandabou-card-foot">
            <span class="text-sm text-[#5A5348]"><?php echo esc_html__('from', 'contabai'); ?> <strong class="bandabou-card-price" x-text="priceLabel(listing)"></strong> <?php echo esc_html__('/ night', 'contabai'); ?></span>
            <span x-show="listing.min_nights" class="whitespace-nowrap text-[13px] text-[#5A5348]" x-text="'<?php echo esc_js(__('min. %d nights', 'contabai-bandabou-theme')); ?>'.replace('%d', listing.min_nights)"></span>
        </span>
    </a>
</div>
