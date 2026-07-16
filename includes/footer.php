<?php
/**
 * includes/footer.php
 * -------------------------------------------------------------
 * Layout footer global untuk bagian depan (User/Publik).
 * DESIGN.md aligned: clean white background, 12px/400w text,
 * subtle top border, no dark overlays.
 * -------------------------------------------------------------
 */
?>
    </div> <!-- End container my-4 -->

    <!-- Footer — Pinterest-style light footer -->
    <footer class="footer-custom mt-auto">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 text-md-start mb-2 mb-md-0">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <i class="ri-palette-fill" style="color: var(--vivid-purple);"></i>
                        <span style="font-weight: 700; color: var(--deep-violet);">Galeri Kreatif</span>
                    </div>
                    <p class="mb-0" style="font-size: 12px; font-weight: 400; color: var(--warm-gray);">
                        Galeri digital untuk komunitas kreatif — desainer grafis, pixel artist, dan tipografer.
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0" style="font-size: 12px; font-weight: 400; color: var(--warm-gray);">
                        &copy; <?= date('Y'); ?> Galeri Kreatif &mdash; 
                        <a href="<?= getBaseUrl(); ?>" style="color: var(--vivid-purple);">Beranda</a>
                        &nbsp;|&nbsp;
                        Platform Portofolio Kreatif
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS Bundle CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


