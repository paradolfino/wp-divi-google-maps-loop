<?php
/*
Template Name: Map Page Template
*/

get_header();

$is_page_builder_used = et_pb_is_pagebuilder_used( get_the_ID() ); ?>


<?php if ( ! $is_page_builder_used ) : ?>

	<div class="container">
		<div id="content-area" class="clearfix">
			<div id="left-area">

<?php endif; ?>

			<?php while ( have_posts() ) : the_post(); ?>

				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

				<?php if ( ! $is_page_builder_used ) : ?>

					<h1 class="main_title"><?php the_title(); ?></h1>
				<?php
					$thumb = '';

					$width = (int) apply_filters( 'et_pb_index_blog_image_width', 1080 );

					$height = (int) apply_filters( 'et_pb_index_blog_image_height', 675 );
					$classtext = 'et_featured_image';
					$titletext = get_the_title();
					$thumbnail = get_thumbnail( $width, $height, $classtext, $titletext, $titletext, false, 'Blogimage' );
					$thumb = $thumbnail["thumb"];

					if ( 'on' === et_get_option( 'divi_page_thumbnails', 'false' ) && '' !== $thumb )
						print_thumbnail( $thumb, $thumbnail["use_timthumb"], $titletext, $width, $height );
				?>

				<?php endif; ?>


					<div class="entry-content">

					<?php
                        the_content();
                        

						if ( ! $is_page_builder_used )
							wp_link_pages( array( 'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'Divi' ), 'after' => '</div>' ) );
					?>
                    

					</div> <!-- .entry-content -->




				<?php
					if ( ! $is_page_builder_used && comments_open() && 'on' === et_get_option( 'divi_show_pagescomments', 'false' ) ) comments_template( '', true );
				?>

				</article> <!-- .et_pb_post -->

			<?php endwhile; ?>

<?php if ( !$is_page_builder_used ) : ?>
           
            

			</div> <!-- #left-area -->

			<?php get_sidebar(); ?>
		
		</div> <!-- #content-area -->
	</div> <!-- .container -->

<?php endif; ?>



<div id="main-content">

<!-- <div id="main-content-title" style="text-align: center; padding: 20px 0 20px 0;">
    <h1>Welcome to virtual Jewish Dallas</h1>
    <p>Explore the various sites and... </p>
</div> -->
<!-- grab data -->
        <!--The div element for the map -->
        <div id="map-container">
            <?php
                $args = array(
                    'numberposts'	=> -1,
                    'post_type'		=> 'page',
                    'meta_query'	=> array(
                        'relation'		=> 'AND',
                        array(
                            'key'		=> 'lat',
                            'value'		=> '',
                            'compare'	=> '!='
                        ),
                        array(
                            'key'		=> 'long',
                            'value'		=> '',
                            'compare'	=> '!='
                        )
                    )
                );
                 $query = new WP_Query($args);
                 $pages = $query->posts;
                 $stack = array();

                 // grab custom field lat and long data from post
                 foreach ($pages as &$value) {
                    $id = $value->ID;
                    $url = get_the_post_thumbnail_url($id, 'full');
                    $root = content_url();
                    $title_str = get_the_title($id);
                    $title = (strlen($title_str) > 30) ? substr($title_str,0,30).'...' : $title_str;
                    $link = get_the_permalink($id);
                    $chopper = "[@]";

                    array_push($stack, $id. $chopper . get_post_meta($id, 'lat', TRUE) .$chopper. get_post_meta($id, 'long', TRUE) . $chopper . $url . $chopper . $title . $chopper . $link . $chopper . $root);
                }
                
            ?>

            
            <span id="query" style="display: none"><?php echo json_encode($stack)?></span>
            <div id="map" style="height: 500px; width: 100%; margin: 0 auto;"></div>
            <script>
            var pages = JSON.parse(document.getElementById('query').innerHTML);
            //console.log(pages);

            

            // Initialize and add the map
            function initMap() {
                var activeInfoWindow;
                var urlApiStub = "https://www.google.com/maps/search/?api=1&query=";
                var mapOptions = {
                    zoom: 12, 
                    center: {lat: 32.7841232, lng: -96.7884417},
                    mapTypeControl: false,
                    streetViewControl: false
                };
                // The map, centered at downtown Dallas
                var map = new google.maps.Map(
                    document.getElementById('map'), mapOptions);
                // For each
                
                //console.log(pages.length);
                for (var j = 0; j < pages.length; j++) {

                    var elements = pages[j].split("[@]");
                    //thumbnail
                    var contentUrl = elements[6];
                    //console.log(contentUrl);
                    var parsedUrl = elements[3].split("/");
                    //console.log(parsedUrl);
                    var uploadsIndex = parsedUrl.indexOf("uploads");
                    var imgUrl = contentUrl + "/" + parsedUrl.splice(uploadsIndex).join("\/");

                    //title
                    var title = elements[4];
                    var encodedTitle = encodeURI(title);
                    var mapsUrl = urlApiStub + elements[1] + "," + elements[2];
                    //console.log(urlApiStub + encodedTitle);
                    
                    
                    //console.log(imgUrl);

                    //permalink

                    var parsedLink = elements[5];




                    var marker = new google.maps.Marker({
                        position: {lat: parseFloat(elements[1]), lng: parseFloat(elements[2])}, 
                        map: map,
                    });

                    var contentString = "<div id='marker" + j + "-info' class='marker' style='height: auto; width: 150px; overflow: hidden; white-space: no-wrap; margin-left: 20px;text-align: center;'>"+
                    "<img src='"+imgUrl+"' style='height:auto;width:95%; max-height: 115px; margin: 5% auto;'/><h5><a href='"+parsedLink+"'>"+title+"</a></h5>"+
                    "<a href='"+mapsUrl+"' target='_blank'>View in Maps</a></div>";
                    var infowindow = new google.maps.InfoWindow({});

                    google.maps.event.addListener(marker,'click', (function(marker,content,infowindow){ 
                        return function() {
                            if (activeInfoWindow) { activeInfoWindow.close();}
                            infowindow.setContent(content);
                            infowindow.open(map,marker);
                            activeInfoWindow = infowindow;
                        };
                    })(marker,contentString,infowindow)); 

                    
                }

                
                
            }

            
                </script>
                <!--Load the API from the specified URL
                * The async attribute allows the browser to render the page while the API loads
                * The key parameter will contain your own API key (which is not needed for this tutorial)
                * The callback parameter executes the initMap() function
                -->
                <script async defer
                src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAmQo9u2ln5BsslUlHNr6_vNNxeffHGACE&callback=initMap">
                </script>


        </div> <!-- end of map container -->




</div> <!-- #main-content -->

<?php

get_footer(); ?>

