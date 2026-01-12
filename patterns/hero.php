<?php
/**
 * Title: Hero Section
 * Slug: earthbound/hero
 * Categories: earthbound, earthbound-hero
 * Keywords: hero, banner, intro
 * Description: A retro 70s-inspired hero section with heading and call-to-action.
 *
 * @package Earthbound
 */

?>
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"6rem","bottom":"6rem"}}},"backgroundColor":"background-alt","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull has-background-alt-background-color has-background" style="padding-top:6rem;padding-bottom:6rem">

    <!-- wp:group {"align":"wide","layout":{"type":"constrained","contentSize":"800px"}} -->
    <div class="wp-block-group alignwide">

        <!-- wp:heading {"textAlign":"center","level":1,"fontSize":"hero"} -->
        <h1 class="wp-block-heading has-text-align-center has-hero-font-size">Welcome to the Groovy Side</h1>
        <!-- /wp:heading -->

        <!-- wp:paragraph {"align":"center","style":{"spacing":{"margin":{"top":"1.5rem","bottom":"2rem"}}},"textColor":"foreground-muted","fontSize":"large"} -->
        <p class="has-text-align-center has-foreground-muted-color has-text-color has-large-font-size" style="margin-top:1.5rem;margin-bottom:2rem">I'm a developer, designer, and open source enthusiast. I build things for the web and contribute to projects that make the internet a better place.</p>
        <!-- /wp:paragraph -->

        <!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"},"style":{"spacing":{"blockGap":"1rem"}}} -->
        <div class="wp-block-buttons">
            <!-- wp:button {"className":"is-style-chunky"} -->
            <div class="wp-block-button is-style-chunky"><a class="wp-block-button__link wp-element-button" href="/my-work">See My Work</a></div>
            <!-- /wp:button -->

            <!-- wp:button {"className":"is-style-outline"} -->
            <div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="/about">About Me</a></div>
            <!-- /wp:button -->
        </div>
        <!-- /wp:buttons -->

    </div>
    <!-- /wp:group -->

</div>
<!-- /wp:group -->
