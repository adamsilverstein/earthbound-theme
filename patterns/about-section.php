<?php
/**
 * Title: About Section
 * Slug: earthbound/about-section
 * Categories: earthbound
 * Keywords: about, bio, introduction
 * Description: An about section with image and text.
 *
 * @package Earthbound
 */

?>
<!-- wp:columns {"align":"wide","style":{"spacing":{"blockGap":{"left":"4rem"}}}} -->
<div class="wp-block-columns alignwide">

    <!-- wp:column {"width":"40%"} -->
    <div class="wp-block-column" style="flex-basis:40%">

        <!-- wp:image {"aspectRatio":"1","scale":"cover","sizeSlug":"large","className":"is-style-rounded","style":{"border":{"radius":"1.5rem"}}} -->
        <figure class="wp-block-image size-large has-custom-border is-style-rounded"><img alt="Profile photo" style="border-radius:1.5rem;aspect-ratio:1;object-fit:cover"/></figure>
        <!-- /wp:image -->

    </div>
    <!-- /wp:column -->

    <!-- wp:column {"width":"60%","verticalAlignment":"center"} -->
    <div class="wp-block-column is-vertically-aligned-center" style="flex-basis:60%">

        <!-- wp:heading -->
        <h2 class="wp-block-heading">About Me</h2>
        <!-- /wp:heading -->

        <!-- wp:paragraph {"textColor":"foreground","fontSize":"large"} -->
        <p class="has-foreground-color has-text-color has-large-font-size">I'm a developer passionate about building things for the open web. With experience in WordPress, JavaScript, and modern web technologies, I contribute to projects that help make the internet more accessible and user-friendly.</p>
        <!-- /wp:paragraph -->

        <!-- wp:paragraph {"textColor":"foreground-muted"} -->
        <p class="has-foreground-muted-color has-text-color">When I'm not coding, you can find me exploring the great outdoors, enjoying vintage vinyl, or tinkering with retro electronics. I believe in the power of open source and community-driven development.</p>
        <!-- /wp:paragraph -->

        <!-- wp:buttons {"style":{"spacing":{"margin":{"top":"2rem"}}}} -->
        <div class="wp-block-buttons" style="margin-top:2rem">
            <!-- wp:button {"className":"is-style-outline"} -->
            <div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="/contact">Get in Touch</a></div>
            <!-- /wp:button -->
        </div>
        <!-- /wp:buttons -->

    </div>
    <!-- /wp:column -->

</div>
<!-- /wp:columns -->
