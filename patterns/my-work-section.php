<?php
/**
 * Title: My Work Section
 * Slug: earthbound/my-work-section
 * Categories: earthbound, earthbound-portfolio
 * Keywords: work, portfolio, projects
 * Description: A complete work section with projects, GitHub, and Trac feeds.
 *
 * @package Earthbound
 */

?>
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"4rem","bottom":"4rem"}}},"backgroundColor":"background","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull has-background-background-color has-background" style="padding-top:4rem;padding-bottom:4rem">

    <!-- wp:heading {"textAlign":"center","align":"wide"} -->
    <h2 class="wp-block-heading alignwide has-text-align-center">My Work</h2>
    <!-- /wp:heading -->

    <!-- wp:paragraph {"align":"center","textColor":"foreground-muted","style":{"spacing":{"margin":{"bottom":"3rem"}}}} -->
    <p class="has-text-align-center has-foreground-muted-color has-text-color" style="margin-bottom:3rem">A collection of my projects, contributions, and open source work.</p>
    <!-- /wp:paragraph -->

    <!-- wp:group {"className":"work-section work-section--projects","align":"wide","style":{"spacing":{"margin":{"bottom":"4rem"}}}} -->
    <div class="wp-block-group alignwide work-section work-section--projects" style="margin-bottom:4rem">

        <!-- wp:heading {"level":3} -->
        <h3 class="wp-block-heading">Featured Projects</h3>
        <!-- /wp:heading -->

        <!-- wp:pattern {"slug":"earthbound/project-grid"} /-->

    </div>
    <!-- /wp:group -->

    <!-- wp:columns {"align":"wide","style":{"spacing":{"blockGap":{"left":"3rem"}}}} -->
    <div class="wp-block-columns alignwide">

        <!-- wp:column -->
        <div class="wp-block-column">

            <!-- wp:group {"className":"work-section work-section--github"} -->
            <div class="wp-block-group work-section work-section--github">

                <!-- wp:heading {"level":3} -->
                <h3 class="wp-block-heading">GitHub Activity</h3>
                <!-- /wp:heading -->

                <!-- wp:earthbound/github-feed {"perPage":5} /-->

            </div>
            <!-- /wp:group -->

        </div>
        <!-- /wp:column -->

        <!-- wp:column -->
        <div class="wp-block-column">

            <!-- wp:group {"className":"work-section work-section--trac"} -->
            <div class="wp-block-group work-section work-section--trac">

                <!-- wp:heading {"level":3} -->
                <h3 class="wp-block-heading">WordPress Core</h3>
                <!-- /wp:heading -->

                <!-- wp:earthbound/trac-feed {"perPage":5} /-->

            </div>
            <!-- /wp:group -->

        </div>
        <!-- /wp:column -->

    </div>
    <!-- /wp:columns -->

</div>
<!-- /wp:group -->
