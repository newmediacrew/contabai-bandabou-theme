<?php

use BandabouTheme\Heroicon;

$contactPhone   = (string) get_option('bandabou_contact_phone', '+5999 699 6011');
$contactEmail   = (string) get_option('bandabou_contact_email', 'rental@bandaboucuracao.com');
$contactAddress = implode(', ', array_filter([(string) get_option('bandabou_contact_address', 'Parallelweg J-8'), (string) get_option('bandabou_contact_city', 'Julianadorp, Curaçao')]));
$contactWhatsapp = (string) get_option('bandabou_cta_url', 'https://wa.me/59996996011');
$instagramUrl   = trim((string) get_option('bandabou_social_instagram', 'https://www.instagram.com/bandaboucuracao'));
$instagramName  = $instagramUrl !== '' ? '@' . basename((string) wp_parse_url($instagramUrl, PHP_URL_PATH)) : '';
?>
<section id="contact" class="scroll-mt-24 pb-[72px] pt-16 lg:pb-[104px] lg:pt-24">
    <div class="bandabou-container grid grid-cols-1 items-start gap-12 lg:grid-cols-2 lg:gap-16">
        <div>
            <p class="bandabou-eyebrow"><?php esc_html_e('Contact', 'contabai-bandabou-theme'); ?></p>
            <h2 class="bandabou-section-title mt-2.5"><?php esc_html_e('Questions? We are happy to help', 'contabai-bandabou-theme'); ?></h2>
            <p class="mt-4 text-[17px] leading-[1.6] text-[color:var(--content-text-color,#3A4A52)]"><?php esc_html_e('Send us a message or WhatsApp us directly. We usually reply the same day.', 'contabai-bandabou-theme'); ?></p>
            <ul class="mt-8 flex list-none flex-col gap-[18px] p-0 text-[17px]">
                <?php if ($contactPhone !== '') : ?>
                    <li class="flex items-center gap-4"><span class="bandabou-contact-icon"><?php echo Heroicon::render('whatsapp', 'brands', 'w-[22px] h-[22px]'); ?></span><a href="<?php echo esc_url($contactWhatsapp); ?>" class="font-semibold text-bandabou-ink no-underline"><?php echo esc_html($contactPhone); ?></a></li>
                <?php endif; ?>
                <?php if ($contactEmail !== '') : ?>
                    <li class="flex items-center gap-4"><span class="bandabou-contact-icon"><?php echo Heroicon::outline('envelope', 'w-[22px] h-[22px]'); ?></span><a href="mailto:<?php echo esc_attr($contactEmail); ?>" class="font-semibold text-bandabou-ink no-underline"><?php echo esc_html($contactEmail); ?></a></li>
                <?php endif; ?>
                <?php if ($instagramName !== '') : ?>
                    <li class="flex items-center gap-4"><span class="bandabou-contact-icon"><?php echo Heroicon::render('instagram', 'brands', 'w-[22px] h-[22px]'); ?></span><a href="<?php echo esc_url($instagramUrl); ?>" target="_blank" rel="noopener noreferrer" class="font-semibold text-bandabou-ink no-underline"><?php echo esc_html($instagramName); ?></a></li>
                <?php endif; ?>
                <?php if ($contactAddress !== '') : ?>
                    <li class="flex items-center gap-4"><span class="bandabou-contact-icon"><?php echo Heroicon::outline('map-pin', 'w-[22px] h-[22px]'); ?></span><span class="font-semibold"><?php echo esc_html($contactAddress); ?></span></li>
                <?php endif; ?>
            </ul>
        </div>

        <form id="bandabou-contact-form" aria-label="<?php esc_attr_e('Contact form', 'contabai-bandabou-theme'); ?>" class="bandabou-contact-form relative flex flex-col gap-[18px]">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <label class="bandabou-contact-label"><?php esc_html_e('Name', 'contabai-bandabou-theme'); ?><input type="text" name="fullname" required autocomplete="name" placeholder="<?php esc_attr_e('Your name', 'contabai-bandabou-theme'); ?>" class="bandabou-contact-input"></label>
                <label class="bandabou-contact-label"><?php esc_html_e('Email', 'contabai-bandabou-theme'); ?><input type="email" name="email" required autocomplete="email" placeholder="<?php esc_attr_e('you@example.com', 'contabai-bandabou-theme'); ?>" class="bandabou-contact-input"></label>
            </div>
            <label class="bandabou-contact-label"><?php esc_html_e('Which villa and period?', 'contabai-bandabou-theme'); ?><input type="text" name="stay" placeholder="<?php esc_attr_e('E.g. Villa Cuchita, 12 to 19 January', 'contabai-bandabou-theme'); ?>" class="bandabou-contact-input"></label>
            <label class="bandabou-contact-label"><?php esc_html_e('Message', 'contabai-bandabou-theme'); ?><textarea name="message" rows="5" required placeholder="<?php esc_attr_e('Tell us what you are looking for', 'contabai-bandabou-theme'); ?>" class="bandabou-contact-input resize-y"></textarea></label>
            <div aria-hidden="true" class="absolute -left-[9999px] h-px w-px overflow-hidden">
                <label>Website<input type="text" name="website" tabindex="-1" autocomplete="off"></label>
            </div>
            <button type="submit" class="bandabou-cta min-h-14 self-start !rounded-[14px] border-0 px-[30px] text-base"><?php esc_html_e('Send message', 'contabai-bandabou-theme'); ?></button>
        </form>
    </div>
</section>
<script>
(function () {
    const form = document.getElementById('bandabou-contact-form');
    if (! form) { return; }

    const labels = {
        subject: <?php echo wp_json_encode(__('Question via the website', 'contabai-bandabou-theme')); ?>,
        name: <?php echo wp_json_encode(__('Name', 'contabai-bandabou-theme')); ?>,
        email: <?php echo wp_json_encode(__('Email', 'contabai-bandabou-theme')); ?>,
        stay: <?php echo wp_json_encode(__('Villa and period', 'contabai-bandabou-theme')); ?>
    };

    const readyAt = Date.now();
    let hasInteracted = false;
    ['input', 'focusin'].forEach(function (eventName) {
        form.addEventListener(eventName, function () { hasInteracted = true; }, { once: true });
    });

    form.addEventListener('submit', function (event) {
        event.preventDefault();

        if (form.website && form.website.value !== '') { return; }
        if (! hasInteracted || Date.now() - readyAt < 1500) { return; }

        const name = form.fullname.value.trim();
        const stay = form.stay.value.trim();
        const subject = labels.subject + ': ' + (stay || name);
        const body = form.message.value.trim() + '\n\n' + labels.name + ': ' + name + '\n' + labels.email + ': ' + form.email.value.trim() + '\n' + labels.stay + ': ' + (stay || '—');
        const recipient = atob(<?php echo wp_json_encode(base64_encode($contactEmail)); ?>);
        window.location.href = 'mailto:' + recipient + '?subject=' + encodeURIComponent(subject) + '&body=' + encodeURIComponent(body);
    });
})();
</script>
