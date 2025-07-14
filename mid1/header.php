<?php
/**
 * The template for displaying the header
 *
 * This is the template that displays all of the <head> section, opens the <body> tag and adds the site's header.
 *
 * @package HelloElementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
    $viewport_content = apply_filters( 'hello_elementor_viewport_content', 'width=device-width, initial-scale=1' );
    $enable_skip_link = apply_filters( 'hello_elementor_enable_skip_link', true );
    $skip_link_url = apply_filters( 'hello_elementor_skip_link_url', '#content' );
    ?>
    <!doctype html>
    <html <?php language_attributes(); ?>>
    <head>
    	<meta charset="<?php bloginfo( 'charset' ); ?>">
    	<meta name="viewport" content="<?php echo esc_attr( $viewport_content ); ?>">
    	<link rel="profile" href="https://gmpg.org/xfn/11">
    	<?php wp_head(); ?>
    </head>
    <body <?php body_class(); ?>>
    
    <!--
    <link rel="preload" href="https://mid1.tamland.ir/css/a5cd3cf.css?ver=cd035" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://mid1.tamland.ir/css/a5cd3cf.css?ver=cd035"></noscript>
    -->
    
    <script>
    (function () {
      const isMobile = /Android|iPhone|iPad|iPod/i.test(navigator.userAgent);
      const delay = isMobile ? 300 : 1500;
    
      function lazyLoadImagesAndBackgrounds() {
        document.querySelectorAll('.swiper-slide-bg').forEach(el => {
          const bg = el.dataset.bg || el.getAttribute('data-bg') || el.dataset.src;
          if (bg) el.style.backgroundImage = `url("${bg}")`;
        });
    
        document.querySelectorAll('img[data-src]').forEach(img => {
          if (!img.src || img.src.includes('data:image')) img.src = img.dataset.src;
          if (img.dataset.srcset) img.srcset = img.dataset.srcset;
        });
    
        document.querySelectorAll('img.wmu-preview-img[data-src]').forEach(img => {
          if (!img.src || img.src.includes('data:image')) img.src = img.dataset.src;
          if (img.dataset.srcset) img.srcset = img.dataset.srcset;
        });
      }
    
      function loadDesktopScripts() {
        const gtm = document.createElement('script');
        gtm.src = 'https://www.googletagmanager.com/gtag/js?id=G-FRY93YKGD1';
        gtm.async = true;
        document.head.appendChild(gtm);
    
        const goftino = document.createElement('script');
        goftino.src = 'https://www.goftino.com/widget/kkkIbA';
        goftino.defer = true;
        document.body.appendChild(goftino);
    
        const clarity = document.createElement('script');
        clarity.src = 'https://www.clarity.ms/tag/G-GYYC1J9D7L';
        clarity.defer = true;
        document.body.appendChild(clarity);
      }
    
      function loadMobileScripts() {
        const dialog = document.createElement('script');
        dialog.src = 'https://mid1.tamland.ir/js/dialog/dialog.min.js';
        dialog.defer = true;
        document.body.appendChild(dialog);
    
        const slides = document.createElement('script');
        slides.src = 'https://mid1.tamland.ir/js/slides.c002964.bundle.min.js';
        slides.defer = true;
        document.body.appendChild(slides);
      }
    
      window.addEventListener('load', () => {
        setTimeout(() => {
          if (isMobile) {
            loadMobileScripts();
          } else {
            loadDesktopScripts();
          }
          lazyLoadImagesAndBackgrounds();
        }, delay);
      });
    
    
      if (isMobile) {
        window.addEventListener('DOMContentLoaded', lazyLoadImagesAndBackgrounds);
      }
    })();
    </script>
    
    
    <style>
    .swiper-slide-bg {
      transition: background-image 0.5s ease-in-out;
    }
    </style>
    
    
    <!--<div id="wptime-plugin-preloader"></div>-->
    <?php wp_body_open(); ?>
    
    <?php if ( $enable_skip_link ) { ?>
    <a class="skip-link screen-reader-text" href="<?php echo esc_url( $skip_link_url ); ?>"><?php echo esc_html__( 'Skip to content', 'hello-elementor' ); ?></a>
    <?php } ?>
    
    <?php
    if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'header' ) ) {
    	if ( hello_elementor_display_header_footer() ) {
    		if ( did_action( 'elementor/loaded' ) && hello_header_footer_experiment_active() ) {
    			get_template_part( 'template-parts/dynamic-header' );
    		} else {
    			get_template_part( 'template-parts/header' );
    		}
    	}
    }