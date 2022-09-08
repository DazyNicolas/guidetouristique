/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.scss';

// start the Stimulus application
import './bootstrap';




//swiper slide guide
var swiper = new Swiper(".mySwiper", {
    slidesPerView: 3,
    spaceBetween: 25,
    centerSlide: 'true',
    grabCursor: 'true',
    loop: true,
    pagination: {
      el: ".swiper-pagination",
      clickable: true,
      dynamicBullets: true,
    },
    navigation: {
      nextEl: ".swiper-button-next",
      prevEl: ".swiper-button-prev",
    },

    breakpoints:{
        0:{
            slidesPerView: 1
        },
        800:{
            slidesPerView: 2
        },

        900:{
            slidesPerView: 2
        },
        1000:{
            slidesPerView: 3
        },
    }
  });

//swiper slide guide


// deb nav top animation

const header = document.querySelector("header");

  
window.addEventListener("scroll", ()=>{

    if (document.documentElement.scrollTop > 20){
        header.classList.add('sticky');
    }else{
        header.classList.remove('sticky'); 
    }
})

//fin nav top animation