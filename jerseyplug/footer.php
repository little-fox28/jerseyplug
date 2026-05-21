<?php
/**
 * Theme footer template.
 *
 * @package TailPress
 */
?>
        </main>

        <?php do_action('tailpress_content_end'); ?>
    </div>

    <?php do_action('tailpress_content_after'); ?>

    <?php get_template_part( 'components/layout/footer' ); ?>
</div>

<?php wp_footer(); ?>
</body>
</html>
