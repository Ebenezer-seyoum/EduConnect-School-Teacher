<?php
include 'Home/Homeheader.php';
?>

<!-- Hero Banner -->
<div class="slider-area">
    <div class="single-slider section-overly slider-height2 d-flex align-items-center" style="background-image: url('assets/img/hero/about.jpg');">
        <div class="container">
            <div class="row">
                <div class="col-xl-12">
                    <div class="hero-cap text-center">
                        <h2>About Edu-Connect</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Simple System Description -->
<section class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-10 text-center wow animate__animated animate__fadeInUp" data-wow-delay="0.2s">
            <h2>Connecting Schools and Teachers Seamlessly</h2>
            <p class="lead">Edu-Connect makes finding and connecting qualified teachers simple and efficient. Schools can post vacancies, teachers can create profiles and apply, and the platform’s admin ensures smooth, secure connections for successful placements.</p>
        </div>
    </div>
</section>

<!-- Benefits, Vision & Mission Section -->
<section class="container my-5">
    <div class="row text-center mb-4">
        <div class="col">
            <h2>Benefits, Vision & Mission</h2>
        </div>
    </div>
    <div class="row">
        <?php
        $infoCards = [
            ["icon"=>"flaticon-school", "title"=>"For Schools", "desc"=>"Access a wide pool of qualified teachers and find the right match quickly."],
            ["icon"=>"flaticon-teacher", "title"=>"For Teachers", "desc"=>"Showcase your profile, discover new opportunities, and apply easily."],
            ["icon"=>"flaticon-handshake", "title"=>"For Admins", "desc"=>"Manage requests, facilitate connections, and ensure smooth operations."],
            ["icon"=>"flaticon-eye", "title"=>"Vision", "desc"=>"Bridge schools and teachers globally, fostering growth and excellence."],
            ["icon"=>"flaticon-rocket", "title"=>"Mission", "desc"=>"Simplify recruitment and connect talented educators with schools efficiently."]
        ];
        $delay = 0.1;
        foreach($infoCards as $card){
            echo '<div class="col-lg-4 col-md-6 mb-4 wow animate__animated animate__fadeInUp" data-wow-delay="'.$delay.'s">
                    <div class="card h-100 shadow rounded p-4 text-center">
                        <div class="mb-3 display-4 text-primary">
                            <span class="'.$card['icon'].'"></span>
                        </div>
                        <h5>'.$card['title'].'</h5>
                        <p class="text-muted">'.$card['desc'].'</p>
                    </div>
                </div>';
            $delay += 0.2;
        }
        ?>
    </div>
</section>

<!-- How It Works Section -->
<section class="apply-process-area apply-bg py-5" style="background-image: url('Home/assets/img/gallery/how-applybg.png');">
    <div class="container">
        <div class="text-center text-white mb-5 wow animate__animated animate__fadeInDown">
            <h2>How It Works</h2>
            <p>Follow these simple steps to connect with the right schools and teachers</p>
        </div>

        <div class="row">
            <?php
            $steps = [
                ["icon"=>"flaticon-user", "title"=>"Create Account", "desc"=>"Teachers and schools sign up to start using Edu-Connect."],
                ["icon"=>"flaticon-curriculum-vitae", "title"=>"Create Profile", "desc"=>"Teachers create detailed profiles; schools provide vacancy info."],
                ["icon"=>"flaticon-notification", "title"=>"Post & Request", "desc"=>"Schools post vacancies and teachers apply. Requests go to admin."],
                ["icon"=>"flaticon-handshake", "title"=>"Admin Connect", "desc"=>"Admin reviews requests and facilitates secure introductions."],
                ["icon"=>"flaticon-success", "title"=>"Successful Connection", "desc"=>"Teachers and schools connect successfully for placements."]
            ];
            $delay = 0.1;
            foreach($steps as $step){
                echo '<div class="col-lg-4 col-md-6 mb-4 wow animate__animated animate__fadeInUp" data-wow-delay="'.$delay.'s">
                        <div class="single-process text-center p-4 shadow rounded bg-white h-100 transition hover-shadow">
                            <div class="process-ion mb-3 display-4 text-primary">
                                <span class="'.$step['icon'].'"></span>
                            </div>
                            <h5>'.$step['title'].'</h5>
                            <p class="text-muted">'.$step['desc'].'</p>
                        </div>
                    </div>';
                $delay += 0.2;
            }
            ?>
        </div>
    </div>
</section>

<!-- Custom Styles -->
<style>
.transition{
    transition: all 0.3s ease;
}
.transition:hover{
    transform: translateY(-5px);
    box-shadow: 0 15px 25px rgba(0,0,0,0.15);
}
.apply-process-area .single-process p,
.container .card p{
    font-size: 0.9rem;
}
@media (max-width: 768px){
    .apply-process-area .single-process h5,
    .container .card h5{
        font-size: 1rem;
    }
    .apply-process-area .single-process p,
    .container .card p{
        font-size: 0.85rem;
    }
}
</style>

<?php
include 'Home/Homefooter.php';
?>
