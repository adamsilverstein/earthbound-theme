<?php
/**
 * Title: Project Grid
 * Slug: earthbound/project-grid
 * Categories: earthbound, earthbound-portfolio
 * Keywords: projects, portfolio, grid, work
 * Description: A grid of project cards showcasing featured work.
 *
 * @package Earthbound
 */

?>
<!-- wp:query {"queryId":10,"query":{"perPage":6,"pages":0,"offset":0,"postType":"eb_project","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false},"align":"wide"} -->
<div class="wp-block-query alignwide">

    <!-- wp:post-template {"layout":{"type":"grid","columnCount":3}} -->

        <!-- wp:group {"className":"project-card groovy-card","style":{"spacing":{"padding":{"top":"0","right":"0","bottom":"0","left":"0"}}},"backgroundColor":"background","layout":{"type":"constrained"}} -->
        <div class="wp-block-group project-card groovy-card has-background-background-color has-background" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0">

            <!-- wp:post-featured-image {"isLink":true,"aspectRatio":"16/10","style":{"border":{"radius":{"topLeft":"1.5rem","topRight":"1.5rem","bottomLeft":"0","bottomRight":"0"}}}} /-->

            <!-- wp:group {"style":{"spacing":{"padding":{"top":"1.5rem","right":"1.5rem","bottom":"1.5rem","left":"1.5rem"}}},"layout":{"type":"constrained"}} -->
            <div class="wp-block-group" style="padding-top:1.5rem;padding-right:1.5rem;padding-bottom:1.5rem;padding-left:1.5rem">

                <!-- wp:post-title {"isLink":true,"fontSize":"large"} /-->

                <!-- wp:post-excerpt {"excerptLength":15} /-->

                <!-- wp:group {"layout":{"type":"flex","flexWrap":"wrap","justifyContent":"left"},"style":{"spacing":{"blockGap":"0.5rem","margin":{"top":"1rem"}}}} -->
                <div class="wp-block-group" style="margin-top:1rem">
                    <!-- wp:read-more {"content":"View Project","className":"is-style-fill"} /-->
                </div>
                <!-- /wp:group -->

            </div>
            <!-- /wp:group -->

        </div>
        <!-- /wp:group -->

    <!-- /wp:post-template -->

    <!-- wp:query-no-results -->
        <!-- wp:paragraph {"align":"center","textColor":"foreground-muted"} -->
        <p class="has-text-align-center has-foreground-muted-color has-text-color">No projects found. Add some projects to get started!</p>
        <!-- /wp:paragraph -->
    <!-- /wp:query-no-results -->

</div>
<!-- /wp:query -->
