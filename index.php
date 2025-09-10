<?php
include 'Home/Homeheader.php';
?>
<main role="main">
    <!-- Hero Section: full background image -->
    <section class="hero-landing d-flex align-items-center justify-content-end">
        <div class="container">
            <div class="row">
                <!-- Text/Search column -->
                <div class="col-12 col-lg-6 text-lg-end text-center hero-text">
                    <h1 class="display-5 fw-bold mb-3">Connecting Schools with Qualified Teachers</h1>
                    <p class="lead mb-4">Fast, secure, and easy hiring for education.</p>

                    <!-- Search Bar -->
                    <form class="search-group d-inline-flex" action="search.php" method="get" role="search" aria-label="Find teachers">
                        <div class="input-group input-group-lg hero-input rounded-pill overflow-hidden">
                            <input type="text" name="query" class="form-control border-0" placeholder="Search teachers by subject or location" aria-label="Search teachers">
                            <button type="submit" class="btn btn-hero-search"><i class="fa-solid fa-magnifying-glass me-2" aria-hidden="true"></i>Search</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <style>
        /* Hero layout with full responsive background image */
        .hero-landing {
            position: relative;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            /* Modern, responsive spacing with header offset */
            --header-height: 72px; /* adjust to your header height */
            padding-top: calc(var(--header-height) + clamp(64px, 12vh, 160px));
            padding-right: clamp(16px, 6vw, 64px);
            padding-bottom: clamp(24px, 8vh, 96px);
            padding-left: clamp(12px, 3vw, 32px);
            background: url('Home/assets/img/teacher.png') center top/cover no-repeat;
            background-attachment: scroll;
        }

        /* Remove any overlay */
        .hero-landing::before {
            display: none !important;
        }

        /* Text column alignment */
        .hero-text {
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            height: 100%;
        }

        @media (min-width: 992px) {
            .hero-text {
                text-align: right;
                padding-right: 2rem;
            }
        }

        @media (max-width: 991.98px) {
            .hero-text {
                text-align: center;
                padding-right: 0;
            }
        }

        /* Text styling */
        .hero-landing h1,
        .hero-landing p {
            color: #ffffff;
            text-shadow: 0 2px 6px rgba(0,0,0,0.5);
        }

        /* Search button styling */
        .btn-hero-search {
            background-color: #0d1b45;
            color: #fff;
            transition: background-color 0.3s;
        }

        .btn-hero-search:hover {
            background-color: #0f2a6b;
            color: #fff;
        }

        /* Search input styling */
        .hero-input .form-control {
            border: none;
        }
    </style>
</main>

    <!-- School Vacancies Preview -->
    <section class="container my-5">
        <div class="d-flex justify-content-between align-items-end mb-3">
            <div>
                <h2 class="fw-bold gradient-text mb-1">School Vacancies</h2>
                <div class="text-muted">Latest openings from schools</div>
            </div>
            <a href="school-vacancy.php" class="btn btn-outline-primary">View all</a>
        </div>
        <div class="row g-4">
            <?php if (empty($homeVacancies)) { ?>
                <div class="col-12">
                    <div class="alert alert-info">No vacancies yet.</div>
                </div>
                <?php } else {
                foreach ($homeVacancies as $v) { ?>
                    <div class="col-md-4">
                        <div class="card h-100 border-0 shadow-sm hover-raise rounded-4">
                            <div class="card-body">
                                <h5 class="mb-1"><?php echo htmlspecialchars($v['title']); ?></h5>
                                <div class="small text-muted mb-2"><i class="fa-solid fa-location-dot"></i> <?php echo htmlspecialchars($v['location']); ?> • <strong>ETB</strong> <?php echo (int)$v['salary']; ?> • <?php echo (int)($v['experience'] ?? 0); ?> yrs</div>
                                <span class="badge bg-info"><?php echo htmlspecialchars($v['employment_type']); ?></span>
                            </div>
                            <div class="card-footer bg-transparent border-0 pt-0">
                                <a class="btn btn-sm btn-primary" href="school-vacancy.php">Details</a>
                            </div>
                        </div>
                    </div>
            <?php }
            } ?>
        </div>
    </section>

    <!-- Find Teachers Preview -->
    <section class="container my-5">
        <div class="d-flex justify-content-between align-items-end mb-3">
            <div>
                <h2 class="fw-bold gradient-text mb-1">Find Teachers</h2>
                <div class="text-muted">Explore teacher profiles</div>
            </div>
            <a href="find-teachers.php" class="btn btn-outline-primary">View all</a>
        </div>
        <div class="row g-4">
            <?php if (empty($homeTeachers)) { ?>
                <div class="col-12">
                    <div class="alert alert-info">No teachers available.</div>
                </div>
                <?php } else {
                $i = 0;
                foreach ($homeTeachers as $t) {
                    if ($i++ >= 6) break;
                    $name = htmlspecialchars($t['full_name'] ?? '');
                    $pic = !empty($t['profile_picture']) ? htmlspecialchars($t['profile_picture']) : 'admin/assets/images/no.png'; ?>
                    <div class="col-6 col-md-4 col-lg-2">
                        <div class="card border-0 shadow-sm rounded-4 hover-raise h-100 text-center">
                            <img src="<?php echo $pic; ?>" alt="<?php echo $name; ?>" class="img-fluid rounded-top" style="height:140px; object-fit:cover;">
                            <div class="card-body p-2">
                                <div class="small fw-semibold text-truncate" title="<?php echo $name; ?>"><?php echo $name; ?></div>
                            </div>
                        </div>
                    </div>
            <?php }
            } ?>
        </div>
    </section>

    <!-- Contact CTA -->
    <section class="container my-5">
        <div class="glass-card p-4 rounded-4 d-flex flex-column flex-md-row align-items-center justify-content-between">
            <div class="mb-3 mb-md-0">
                <h3 class="fw-bold gradient-text mb-1">Have questions?</h3>
                <div class="text-muted">We’d love to help. Get in touch with our team.</div>
            </div>
            <a href="contact.php" class="btn btn-primary">Contact Us</a>
        </div>
    </section>

    <!-- Auth CTAs -->
    <section class="container my-5">
        <div class="row g-3">
            <div class="col-md-6">
                <div class="glass-card p-4 rounded-4 d-flex align-items-center justify-content-between hover-raise">
                    <div>
                        <h5 class="mb-1">New here?</h5>
                        <div class="text-muted">Create an account to get started.</div>
                    </div>
                    <a href="register.php" class="btn btn-outline-primary">Register</a>
                </div>
            </div>
            <div class="col-md-6">
                <div class="glass-card p-4 rounded-4 d-flex align-items-center justify-content-between hover-raise">
                    <div>
                        <h5 class="mb-1">Already have an account?</h5>
                        <div class="text-muted">Log in to continue.</div>
                    </div>
                    <a href="login.php" class="btn btn-primary">Login</a>
                </div>
            </div>
        </div>
    </section>

</main>
<?php
include 'Home/Homefooter.php';
?>
<style>
    .hero-landing {
        min-height: 55vh;
        position: relative;
        margin-top: 20px;
        /* start below header links */
    }

    .hero-landing::before {
        content: none !important;
        /* ensure no overlay */
    }

    .hero-landing>.container {
        position: relative;
        z-index: 2
    }

    .glass-card {
        background: rgba(255, 255, 255, 0.14);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.25);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.18);
    }

    .gradient-text {
        background: linear-gradient(90deg, #6a11cb, #2575fc);
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
        color: transparent
    }

    .hover-raise {
        transition: transform .25s ease, box-shadow .25s ease
    }

    .hover-raise:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, .12) !important
    }

    /* Hero input + quick links */
    .hero-input {
        border-radius: 50px;
        overflow: hidden;
        background: #fff;
        border: 1px solid rgba(0, 0, 0, 0.08);
    }

    .hero-input .form-control:focus {
        box-shadow: none;
    }

    .quick-links .btn-quick {
        background: rgba(255, 255, 255, 0.9);
        border: 1px solid rgba(0, 0, 0, 0.08);
        color: #0a2540;
        border-radius: 999px;
        padding: 6px 14px;
        font-weight: 600;
        transition: all .2s ease;
    }

    .quick-links .btn-quick:hover {
        background: #0a2540;
        border-color: #0a2540;
        color: #fff;
        transform: translateY(-1px);
        box-shadow: 0 8px 16px rgba(10, 37, 64, 0.25);
    }

    /* Optional: use a background image with color overlay and fallback color */
    .hero-landing.bg-image {
        background-color: #0d1b45;
        /* fallback color behind the image */
        background-image: linear-gradient(135deg, rgba(13, 27, 69, 0.55), rgba(13, 27, 69, 0.35)), url('Home/assets/img/teacher.png');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
    }

    /* Hide inline image when using background image mode to avoid duplication */
    .hero-landing.bg-image .hero-graphic {
        display: none;
    }
</style>