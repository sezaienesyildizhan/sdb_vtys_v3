<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teknolojiler</title>
    <?php require('inc/links.php'); ?>
    <style>
        .pop:hover {
            border-top-color: var(--teal) !important;
            transform: scale(1.03);
            transition: all 0.3s ease;
        }
    </style>
</head>
<body>
    <?php require('inc/header.php'); ?>
    
    <div class="my-5 px-4">
        <h2 class="fw-bold h-font text-center">
            Kullandığımız Teknolojiler
        </h2>

        <div class="h-line bg-dark"> </div>

        <p class="text-center mt-3">
            Lorem ipsum dolor sit, amet consectetur adipisicing elit. Explicabo quia voluptas veritatis velit modi atque, nulla ut, vitae officiis fuga dolore commodi cum? Quo, labore? Modi voluptas recusandae neque ea.
            Aliquid quasi consequatur voluptate placeat odio iure, aut molestias veritatis est quas saepe maiores commodi? Accusantium sit aspernatur rem, aut sequi cumque quos dolorem libero non doloribus consequuntur obcaecati debitis!
            Adipisci distinctio ea vel ipsam maiores quam deleniti iste. Enim, provident possimus. Ratione iste cum repellendus porro deleniti aliquid libero ea voluptatum magnam, provident dolorum temporibus asperiores, tempore aliquam odit!
        </p>

        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-5 px-4">
                    <div class="bg-white rounded shadow p-4 border-top border-4 border-dark pop">
                        <div class="d-flex align-items-center mb-2">
                            <img width="40px" src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/unity/unity-original.svg" />
                            <h5 class="m-0 ms-3">Unity</h5>
                        </div>
                        <p>
                            Lorem ipsum dolor sit amet consectetur adipisicing elit. Ab quisquam omnis sunt, voluptatem minima tempora accusantium  repudiandae odit quos vero alias earum incidunt, 
                            corporis doloribus.
                            Dolorem quibusdam eligendi veniam quaerat et a rerum, voluptates officiis,
                        </p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-5 px-4">
                    <div class="bg-white rounded shadow p-4 border-top border-4 border-dark pop">
                        <div class="d-flex align-items-center mb-2">
                            <img width="40px" src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/unity/unity-original.svg" />
                            <h5 class="m-0 ms-3">Unity</h5>
                        </div>
                        <p>
                            Lorem ipsum dolor sit amet consectetur adipisicing elit. Ab quisquam omnis sunt, voluptatem minima tempora accusantium  repudiandae odit quos vero alias earum incidunt, 
                            corporis doloribus.
                            Dolorem quibusdam eligendi veniam quaerat et a rerum, voluptates officiis,
                        </p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-5 px-4">
                    <div class="bg-white rounded shadow p-4 border-top border-4 border-dark pop">
                        <div class="d-flex align-items-center mb-2">
                            <img width="40px" src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/unity/unity-original.svg" />
                            <h5 class="m-0 ms-3">Unity</h5>
                        </div>
                        <p>
                            Lorem ipsum dolor sit amet consectetur adipisicing elit. Ab quisquam omnis sunt, voluptatem minima tempora accusantium  repudiandae odit quos vero alias earum incidunt, 
                            corporis doloribus.
                            Dolorem quibusdam eligendi veniam quaerat et a rerum, voluptates officiis,
                        </p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-5 px-4">
                    <div class="bg-white rounded shadow p-4 border-top border-4 border-dark pop">
                        <div class="d-flex align-items-center mb-2">
                            <img width="40px" src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/unity/unity-original.svg" />
                            <h5 class="m-0 ms-3">Unity</h5>
                        </div>
                        <p>
                            Lorem ipsum dolor sit amet consectetur adipisicing elit. Ab quisquam omnis sunt, voluptatem minima tempora accusantium  repudiandae odit quos vero alias earum incidunt, 
                            corporis doloribus.
                            Dolorem quibusdam eligendi veniam quaerat et a rerum, voluptates officiis,
                        </p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-5 px-4">
                    <div class="bg-white rounded shadow p-4 border-top border-4 border-dark pop">
                        <div class="d-flex align-items-center mb-2">
                            <img width="40px" src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/unity/unity-original.svg" />
                            <h5 class="m-0 ms-3">Unity</h5>
                        </div>
                        <p>
                            Lorem ipsum dolor sit amet consectetur adipisicing elit. Ab quisquam omnis sunt, voluptatem minima tempora accusantium  repudiandae odit quos vero alias earum incidunt, 
                            corporis doloribus.
                            Dolorem quibusdam eligendi veniam quaerat et a rerum, voluptates officiis,
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php require('inc/footer.php'); ?>


    <!-- js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <!-- swiper -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script>
        var swiper = new Swiper(".swiper-container", {
        spaceBetween: 30,
        effect: "fade",
        loop: true,
        autoplay: {
            delay: 3500,
            disableOnInteraction: false,
        }
        });
        var swiper = new Swiper(".swiper-testimonials", {
            effect: "coverflow",
            grabCursor: true,
            centeredSlides: true,
            slidesPerView: "auto",
            coverflowEffect: {
                rotate: 50,
                stretch: 0,
                depth: 100,
                modifier: 1,
                slideShadows: true,
            },
            pagination: {
                el: ".swiper-pagination",
            },
        });
    </script>
</body>
</html>