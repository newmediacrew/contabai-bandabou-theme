<?php
$itemUrl       = \Contabai\Controllers\ListingRewriteController::listing_url($item);
$itemPlace     = implode(' · ', array_filter([(string) ($item['area_name'] ?? ''), (string) ($item['region_name'] ?? '')]));
$itemGuests    = (int) ($item['max_guests'] ?? 0);
$itemBedrooms  = (int) ($item['bedrooms'] ?? 0);
$itemBathrooms = (int) ($item['bathrooms'] ?? 0);
$itemNights    = (int) ($item['min_nights'] ?? 0);
?>
<div class="bandabou-card">
    <div class="aspect-[4/3] bg-neutral-100"></div>
    <a href="<?php echo esc_url($itemUrl); ?>" class="bandabou-card-body">
        <span>
            <span class="bandabou-card-title contabai-seo-place"><?php echo esc_html($item['title'] ?? ''); ?></span>
            <span class="mt-1 block text-sm text-[#5A5348]"><?php echo esc_html($itemPlace); ?></span>
        </span>
        <span class="bandabou-card-facts">
            <span><?php echo \Contabai\Heroicon::outline('users', 'w-[17px] h-[17px]'); ?><?php echo esc_html(sprintf(_n('%d guest', '%d guests', $itemGuests, 'contabai'), $itemGuests)); ?></span>
            <span><?php echo \Contabai\Heroicon::outline('home', 'w-[17px] h-[17px]'); ?><?php echo esc_html(sprintf(_n('%d bedroom', '%d bedrooms', $itemBedrooms, 'contabai'), $itemBedrooms)); ?></span>
            <span><?php echo \Contabai\Heroicon::outline('home-modern', 'w-[17px] h-[17px]'); ?><?php echo esc_html(sprintf(_n('%d bathroom', '%d bathrooms', $itemBathrooms, 'contabai'), $itemBathrooms)); ?></span>
        </span>
        <span class="bandabou-card-foot">
            <span class="text-sm text-[#5A5348]"><?php echo esc_html__('from', 'contabai'); ?> <strong class="bandabou-card-price"><?php echo esc_html(($item['currency'] ?? 'EUR') . ' ' . number_format(((int) ($item['from_price_cents'] ?? 0)) / 100, 2, '.', '')); ?></strong> <?php echo esc_html__('/ night', 'contabai'); ?></span>
            <?php if ($itemNights > 0) : ?>
                <span class="whitespace-nowrap text-[13px] text-[#5A5348]"><?php echo esc_html(sprintf(__('min. %d nights', 'contabai-bandabou-theme'), $itemNights)); ?></span>
            <?php endif; ?>
        </span>
    </a>
</div>
