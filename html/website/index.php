<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Custom error handling
function handle_error($errno, $errstr, $errfile, $errline) {
    error_log("Error [$errno]: $errstr in $errfile on line $errline");
    if (ini_get('display_errors')) {
        echo "<p style='color: red;'>An error occurred. Please try again later.</p>";
    }
}
set_error_handler("handle_error");

// Security headers to prevent vulnerabilities
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: SAMEORIGIN");
header("Referrer-Policy: no-referrer");
header("Strict-Transport-Security: max-age=31536000; includeSubDomains; preload");
header("Permissions-Policy: accelerometer=(), camera=(), geolocation=(), microphone=(), interest-cohort=()");
?>

<!doctype html>
<html lang="en-GB">
    <head>

<!-- Script from Tutorial 1 - base_page.html -->
    <script id="f5_cspm">(function(){var f5_cspm={f5_p:'GACDIHHOEAJENGIBDHMAJIGCFEMKMJBJOJHFOFJFDJIALGNHPLAEIPLGENIKCLEEDPGBGPNPAAIJBKLBDALAJKMBAAAPNBIMIGOMHJFKMFKIMHCLALOJKEJEJKOMFDOO',setCharAt:function(str,index,chr){if(index>str.length-1)return str;return str.substr(0,index)+chr+str.substr(index+1);},get_byte:function(str,i){var s=(i/16)|0;i=(i&15);s=s*32;return((str.charCodeAt(i+16+s)-65)<<4)|(str.charCodeAt(i+s)-65);},set_byte:function(str,i,b){var s=(i/16)|0;i=(i&15);s=s*32;str=f5_cspm.setCharAt(str,(i+16+s),String.fromCharCode((b>>4)+65));str=f5_cspm.setCharAt(str,(i+s),String.fromCharCode((b&15)+65));return str;},set_latency:function(str,latency){latency=latency&0xffff;str=f5_cspm.set_byte(str,40,(latency>>8));str=f5_cspm.set_byte(str,41,(latency&0xff));str=f5_cspm.set_byte(str,35,2);return str;},wait_perf_data:function(){try{var wp=window.performance.timing;if(wp.loadEventEnd>0){var res=wp.loadEventEnd-wp.navigationStart;if(res<60001){var cookie_val=f5_cspm.set_latency(f5_cspm.f5_p,res);window.document.cookie='f5avr0139236398aaaaaaaaaaaaaaaa_cspm_='+encodeURIComponent(cookie_val)+';path=/;'+'';}
        return;}}
        catch(err){return;}
        setTimeout(f5_cspm.wait_perf_data,100);return;},go:function(){var chunk=window.document.cookie.split(/\s*;\s*/);for(var i=0;i<chunk.length;++i){var pair=chunk[i].split(/\s*=\s*/);if(pair[0]=='f5_cspm'&&pair[1]=='1234')
        {var d=new Date();d.setTime(d.getTime()-1000);window.document.cookie='f5_cspm=;expires='+d.toUTCString()+';path=/;'+';';setTimeout(f5_cspm.wait_perf_data,100);}}}}
        f5_cspm.go();}());
    </script>
<!-- Script from Tutorial 1 - base_page.html -->

    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width" />
    <title>Lancaster's Restaurant</title>
    <meta http-equiv="Content-Security-Policy" content="default-src 'self'; img-src 'self' data: https://*; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; font-src 'self'; connect-src 'self';">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<?php include('navbar.php'); ?>
<main role="main">
    <section id="home" aria-labelledby="home-heading">
        <img src="images/logos/Lancaster's-logos_white_cropped.png" alt="Lancaster's Restaurant Logo" class="logo">
        <section class="featured-section">
            <img src="compressed images/food-6-compressed.jpeg" alt="Home Page Food Image">
            <div class="text-container">
                <p class="tagline">Discover Conscious Gastronomy in the heart of St James's</p>
                <div class="view-menu-button">
                <button onclick="location.href=decodeURIComponent('booktable.php')" aria-label="Book a table">Book a Table</button>
                </div>
            </div>
        </section>
    </section>
    <section id="about" aria-labelledby="about-heading">
        <div class="text">
            <div>
                <h1 id="home-heading">About Us</h1>
                <div class="address-times">
                    <div>
                        <h4>Opening Times</h4>
                        <ul>
                            <li>Mon – Fri: 07:30 am – 11 pm</li>
                            <li>Sat: 9am - 11pm</li>
                            <li>Sun: 11:30 am – 10 pm</li>
                        </ul>
                    </div>
                    <div>
                        <h4>Address</h4>
                        <address>
                            52 Haymarket<br>
                            London<br>
                            SW1Y 4RP
                        </address>
                    </div>
                </div>
                <h3>Our Story</h3>
                <p>Lancaster’s was founded by chef Ana Lancaster and Sommelier Robert Lancaster in May 2005. The essence of this combination makes up much of Lancaster's DNA, where conscious culinary creativity meets hospitality experience and passion for wine. What followed from their meeting were a series of sold-out residencies to establish Lancaster’s as one of the most exciting restaurant concepts on the UK restaurant scene. Lancaster’s permanent home in St James’s market was established in November 2010 and has since attracted a string of awards including both the Marie Claire and GQ ‘sustainable restaurant of the year’ and the Caterer award for ‘best new restaurant’.</p>
                <p>Keep yourself updated by following us on Instagram. For collaborations please contact <a href="mailto:marketing@lancasters.com">marketing@lancasters.com</a>. For business opportunities please contact Robert Lancaster at <a href="mailto:office@lancasters.com">office@lancasters.com</a>.</p>

                <h3>Info</h3>
                <p>We are happy to accommodate dietary requirements. Please just make a note in your reservation or let us know upon arrival. Lancaster’s is on ground level, with an accessible bathroom situated on the same floor. Please note that pets are not permitted inside the restaurant, except for registered service animals.</p>
            </div>

            <div>
                <img src="images/people/ana-cooking.jpeg" alt="Ana Lancaster Cooking" />
                <img src="images/people/robert-bottle.jpeg" alt="Robert Lancaster with Wine Bottle" />
            </div>
        </div>
    

    <section id="reviews" aria-labelledby="reviews-heading">
        <div class="press-quotes">
            <h3>What the Press Says</h3>
            <p><strong>"Well-balanced dishes which are packed with flavour"</strong> - Michelin Guide</p>
            <p><strong>"Lancaster’s is generous and indulgent, relaxed and innovative, and in short, it’s everything you want from eating out."</strong> - Squaremeal</p>
            <p><strong>"Style and substance in equal - and environmentally conscious – measure"</strong> - CONDÉ NAST TRAVELLER</p>
            <p><strong>"Sustainable kitchen offers charm, finesse and an enlightened drinks list"</strong> - Fay Maschler</p>
            <h3>What Our Customers Say</h3>
        </div>
        <div class="review-container">
            <div class="review-card">
                <p><strong>Elizabeth S - 5/5</strong></p>
                <blockquote>
                    <p><em>Yum!</em></p>
                    <p class="review-text">Had a great dinner here during our trip to London from the states. Highly recommend the mushroom parfait, ribs, and strawberry custard cream. The mussels and burger were also very good. Server was attentive and had great knowledge of the menu.</p>
                    <p class="review-date">Written 24 August 2024</p>
                </blockquote>
            </div>
    
            <div class="review-card">
                <p><strong>BJM - 5/5</strong></p>
                <blockquote>
                    <p><em>Stunning food</em></p>
                    <p class="review-text">Booked to dine here on a Friday night having been a fan of the owners YouTube account for a while now. The restaurant was busy but we got seated straight away and were given water by the waiter. The food was genuinely one of the best meals I've had all year.</p>
                    <p class="review-date">Written 20 August 2024</p>
                </blockquote>
            </div>
    
            <div class="review-card">
                <p><strong>Christopher A - 5/5</strong></p>
                <blockquote>
                    <p><em>Excellent food and great atmosphere</em></p>
                    <p class="review-text">Exceptional food combined with wonderful service - the mushroom parfait and the beef ribs were sensational. Can’t wait to return!</p>
                    <p class="review-date">Written 19 August 2024</p>
                </blockquote>
            </div>
    
            <div class="review-card">
                <p><strong>Mandy A - 5/5</strong></p>
                <blockquote>
                    <p><em>Fantastic vibe, service, staff and food.</em></p>
                    <p class="review-text">My brother and I have been following Lancaster’s on social media and finally decided to visit. Friendly staff, great vibe, amazing service and food.</p>
                    <p class="review-date">Written 17 August 2024</p>
                </blockquote>
            </div>
    
            <div class="review-card">
                <p><strong>Tom C - 5/5</strong></p>
                <blockquote>
                    <p><em>Great brunch</em></p>
                    <p class="review-text">Just had brunch - food was delicious, service was 5-star. This restaurant is performing at every level, we just love coming back.</p>
                    <p class="review-date">Written 17 August 2024</p>
                </blockquote>
            </div>
        </div>
    </section>
    

    <section id="gallery" aria-labelledby="gallery-heading">
    <h1 id="home-heading">Gallery</h1>
        <div class="gallery-container">
            <img src="images/food/food-1.jpeg" alt="Gallery food-1" loading="lazy">
            <img src="images/food/food-2.jpeg" alt="Gallery food-2" loading="lazy">
            <img src="images/food/food-3.jpeg" alt="Gallery food-3" loading="lazy">
            <img src="images/food/food-4.jpeg" alt="Gallery food-4" loading="lazy">
            <img src="images/food/food-5.jpeg" alt="Gallery food-5" loading="lazy">
            <img src="images/food/food-7.jpeg" alt="Gallery food-7" loading="lazy">
            <img src="images/food/food-service-1.jpeg" alt="Gallery food-service-1" loading="lazy">
            <img src="images/food/food-service-2.jpeg" alt="Gallery food-service-2" loading="lazy">
            <img src="images/people/ana-cooking.jpeg" alt="Gallery ana-cooking" loading="lazy">
            <img src="images/people/ana-plating.jpeg" alt="Gallery ana-plating" loading="lazy">
            <img src="images/people/robert-bottle.jpeg" alt="Gallery robert-bottle" loading="lazy">
            <img src="images/people/robert-check.jpeg" alt="Gallery robert-check" loading="lazy">
            <img src="images/people/robert-glasses.jpeg" alt="Gallery robert-glasses" loading="lazy">
            <img src="images/people/robert-pour.jpeg" alt="Gallery robert-pour" loading="lazy">
            <img src="images/rerestaurant/restaurant-1.jpeg" alt="Gallery restaurant-1" loading="lazy">
            <img src="images/rerestaurant/restaurant-2.jpeg" alt="Gallery restaurant-2" loading="lazy">
            <img src="images/rerestaurant/restaurant-3.jpeg" alt="Gallery restaurant-3" loading="lazy">
            <img src="images/rerestaurant/restaurant-4.jpeg" alt="Gallery restaurant-4" loading="lazy">
        </div>
    </article>
    </main>
    <?php
        echo $twig->render('footer.twig');
    ?> 
</body>
</html>
