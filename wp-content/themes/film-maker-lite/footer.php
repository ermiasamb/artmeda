<?php
/**
 * The template for displaying the footer
 *
 * @package Film Maker Lite
 * @subpackage film_maker_lite
 */

?>
		</div>
		<footer id="footer" class="site-footer" role="contentinfo">
			<?php
				get_template_part( 'template-parts/footer/footer', 'widgets' );

				get_template_part( 'template-parts/footer/site', 'info' );
			?>
				<?php if( get_theme_mod( 'film_maker_lite_return_to_header',true) != '' || get_theme_mod( 'film_maker_lite_return_to_header_mob',false) != '') { ?>
				<div class="return-to-header">
					<a href="javascript:" id="return-to-top"><i class="fas fa-arrow-up"></i></a>
				</div>
			<?php }?>
		</footer>
	</div>
</div>
<?php wp_footer(); ?>

</body>
</html>
