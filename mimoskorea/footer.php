<footer id="colophon" class="site-footer full-width-section" role="contentinfo" aria-label="Rodapé do site">
    <div class="content-container">
        <?php
        $locations = get_nav_menu_locations();
        $footer_menu_id = isset($locations['footer']) ? (int)$locations['footer'] : 0;
        $footer_items = array();
        if ($footer_menu_id) {
            $footer_items = wp_get_nav_menu_items($footer_menu_id) ?: array();
        }

        $top_level_items = array_values(array_filter($footer_items, function ($item) {
            return (int)$item->menu_item_parent === 0;
        }));
        $count = count($top_level_items);
        $split = $count > 0 ? (int)ceil($count / 2) : 0;
        $col1 = $split > 0 ? array_slice($top_level_items, 0, $split) : array();
        $col2 = $split > 0 ? array_slice($top_level_items, $split) : array();
        ?>

        <div class="footer-top grid grid-cols-1 xl:grid-cols-12 gap-8 items-start">
            <div class="footer-menus xl:col-span-8 space-y-8" aria-label="Menus do rodapé">
                <div class="footer-menu-columns grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="footer-menu-column">
                        <ul class="footer-menu-list grid gap-2" role="list">
                            <?php foreach ($col1 as $item) : ?>
                                <li class="footer-menu-item">
                                    <a href="<?php echo esc_url($item->url); ?>" class="footer-menu-link" title="<?php echo esc_attr($item->title); ?>">
                                        <?php echo esc_html($item->title); ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <div class="footer-menu-column">
                        <ul class="footer-menu-list grid gap-2" role="list">
                            <?php foreach ($col2 as $item) : ?>
                                <li class="footer-menu-item">
                                    <a href="<?php echo esc_url($item->url); ?>" class="footer-menu-link" title="<?php echo esc_attr($item->title); ?>">
                                        <?php echo esc_html($item->title); ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>

                <div class="footer-social" aria-label="Redes sociais">
                    <ul class="footer-social-list flex items-center gap-3" role="list">
                        <li><a href="#" aria-label="Instagram" class="footer-social-link"><i class="ph ph-instagram-logo"></i></a></li>
                        <li><a href="#" aria-label="TikTok" class="footer-social-link"><i class="ph ph-tiktok-logo"></i></a></li>
                        <li><a href="#" aria-label="Pinterest" class="footer-social-link"><i class="ph ph-pinterest-logo"></i></a></li>
                        <li><a href="#" aria-label="Facebook" class="footer-social-link"><i class="ph ph-facebook-logo"></i></a></li>
                        <li><a href="#" aria-label="Medium" class="footer-social-link"><i class="ph ph-medium-logo"></i></a></li>
                        <li><a href="#" aria-label="YouTube" class="footer-social-link"><i class="ph ph-youtube-logo"></i></a></li>
                        <li><a href="#" aria-label="X (Twitter)" class="footer-social-link"><i class="ph ph-x-logo"></i></a></li>
                        <li><a href="#" aria-label="LinkedIn" class="footer-social-link"><i class="ph ph-linkedin-logo"></i></a></li>
                    </ul>
                </div>

                <div class="footer-payments flex flex-wrap items-center gap-2" aria-label="Opções de pagamento">
                    <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/payment-pix.svg'); ?>" alt="PIX" class="payment-icon h-8" />
                    <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/payment-visa.svg'); ?>" alt="Visa" class="payment-icon h-8" />
                    <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/payment-mastercard.svg'); ?>" alt="Mastercard" class="payment-icon h-8" />
                </div>
            </div>

            <div class="footer-side xl:col-span-4" aria-label="Newsletter e WhatsApp">
                <!-- <section class="footer-newsletter" aria-labelledby="newsletter-title">
                    <h2 id="newsletter-title" class="footer-heading">Ganhe 10% de desconto no seu próximo pedido</h2>
                    <p class="footer-text">Inscreva-se na nossa newsletter e embarque na sua jornada pessoal com o chá com um presente de boas-vindas da nossa parte.</p>
                    <form class="newsletter-form flex gap-2" action="#" method="post" aria-label="Newsletter">
                        <label for="newsletter-email" class="sr-only">E-mail</label>
                        <input type="email" id="newsletter-email" name="email" class="newsletter-input flex-1 border rounded px-3 py-2" placeholder="Seu e-mail" required />
                        <button type="submit" class="newsletter-button bg-primary text-white rounded px-4 py-2">Inscrever-se</button>
                    </form>
                    <p class="footer-note text-black/80 text-sm mt-3">Ao se inscrever, você concorda em receber atualizações empolgantes da nossa parte regularmente. Você pode cancelar a inscrição a qualquer momento. Encontre mais informações em nossa política de privacidade.</p>
                </section> -->

                <section class="footer-whatsapp" aria-labelledby="whatsapp-title">
                    <h2 id="whatsapp-title" class="footer-heading">Ofertas pelo WhatsApp</h2>
                    <p class="footer-text">Use o QRCode para se conectar com nosso WhatsApp e ganhe 10% de desconto no seu próximo pedido!</p>
                    <div class="whatsapp-qr flex items-center gap-4">
                        <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/whatsapp-qr-placeholder.svg'); ?>" alt="Código QR do WhatsApp" class="qr-image w-40 h-40 border border-black" />
                        <a href="#" class="whatsapp-link inline-flex items-center gap-2" aria-label="Abrir WhatsApp"><i class="ph ph-whatsapp-logo"></i> WhatsApp</a>
                    </div>
                </section>
            </div>
        </div>

        <!-- <div class="footer-bottom border-t border-black mt-8 pt-4"> -->
        <div class="footer-bottom border-t mt-8 pt-4">
            <p class="footer-copy">© Mimos Korea Design. Todos os direitos reservados.</p>
        </div>
    </div>
</footer>
</div><!-- #page -->

<?php wp_footer(); ?>

</body>

</html>
