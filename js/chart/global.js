
$(function() {
    $('a[href*=\\#]:not([href=\\#])').on('click', function() {
        var target = $(this.hash);
        target = target.length ? target : $('[name=' + this.hash.substr(1) +']');
        if (target.length) {
            $('html,body').animate({
                scrollTop: target.offset().top
            }, 1000);
            return false;
        }
    });
});


$(document).ready(function(){

    $('.knowledge').owlCarousel({
        loop:true,
        margin:30,
        nav:true,
        smartSpeed: 2000,
        responsive:{
            0:{
                items:1
            },
            600:{
                items:2
            },
            1000:{
                items:3
            }
        }
    })
	
	$('.event_slider').owlCarousel({
        loop:true,
        margin:30,
        autoplay:true,
        autoplayHoverPause:true,        
        nav:true,
        dots: false,
        smartSpeed: 2000,
        responsive:{
            0:{
                items:1
            },
            600:{
                items:2
            },
            1000:{
                items:3
            }
        }
    })
});


// Check the saved dark mode state on page load
document.addEventListener("DOMContentLoaded", function() {
  const darkModeSwitch = document.getElementById('dark-mode-switch');
  const darkModeState = localStorage.getItem('dark-theme');

  if (darkModeState === 'enabled') {
    document.body.classList.add('dark-theme');
    darkModeSwitch.checked = true;
  }

  darkModeSwitch.addEventListener('change', function() {
    if (darkModeSwitch.checked) {
      document.body.classList.add('dark-theme');
      localStorage.setItem('dark-theme', 'enabled');
    } else {
      document.body.classList.remove('dark-theme');
      localStorage.setItem('dark-theme', 'disabled');
    }
  });
});


        // $('#slick1').slick({
        //     rows: 3,
        //     dots: false,
        //     arrows: true,
        //     infinite: false,
        //     speed: 300,
        //     slidesToShow: 3,
        //     slidesToScroll: 3
        // });


        $(document).ready(function () {
            $('.fa-bars').click(function () {
                $('.left_menu').addClass('show_menu');
            });
            $('.fa_times').click(function () {
                $('.left_menu').removeClass('show_menu');
            });
        });

//         $('.tabs').click(function () {
//             $('div.tabContent').hide();
//             $('div.' + this.src).show();
//             return false;
//         }
//         );

//         const tabs = document.querySelectorAll(".operations__tab");
//         const content = document.querySelectorAll(".operations__content");
//         const container = document.querySelector(".operations__tab-container")
//         container.addEventListener('click', function (e) {
//             const clicked = e.target.closest('.operations__tab');
//             if (!clicked) return;

//             tabs.forEach(t => t.classList.remove('operations__tab--active'));
//             content.forEach(c => c.classList.remove('operations__content--active'))
//             clicked.classList.add('operations__tab--active');
//             document.querySelector(`.operations__content--${clicked.dataset.tab}`)
//                 .classList.add('operations__content--active');
//         });




//         function openNav() {
//             document.getElementById("mySidepanel").style.width = "100%";
//         }

//         function closeNav() {
//             document.getElementById("mySidepanel").style.width = "0";
//         }



//         // Get the button
//         let mybutton = document.getElementById("myBtn");

//         // When the user scrolls down 20px from the top of the document, show the button
//         window.onscroll = function() {scrollFunction()};

//         function scrollFunction() {
//         if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
//             mybutton.style.display = "block";
//         } else {
//             mybutton.style.display = "none";
//         }
//         }

//         // When the user clicks on the button, scroll to the top of the document
//         function topFunction() {
//         document.body.scrollTop = 0;
//         document.documentElement.scrollTop = 0;
//         }

//         //font-size
//         var increment = document.getElementById('up'),
//     decrement = document.getElementById('down'),
//     fsize     = document.getElementById('test'),
//     step      = 2;

// fsize.style.fontSize = '30px';

// increment.onclick = function(){
//   fsize.style.fontSize =  parseInt(fsize.style.fontSize) + step + 'px'; 
// };

// decrement.onclick = function(){
//   fsize.style.fontSize =  parseInt(fsize.style.fontSize) - step + 'px'; 
// };


// var slideIndex = 1;
// showSlides(slideIndex);

// // Next/previous controls
// function plusSlides(n) {
//   showSlides(slideIndex += n);
// }

// // Thumbnail image controls
// function currentSlide(n) {
//   showSlides(slideIndex = n);
// }

// function showSlides(n) {
//   var i;
//   var slides = document.getElementsByClassName("mySlides");
//   var dots = document.getElementsByClassName("demo");
//   var captionText = document.getElementById("caption");
//   if (n > slides.length) {slideIndex = 1}
//   if (n < 1) {slideIndex = slides.length}
//   for (i = 0; i < slides.length; i++) {
//     slides[i].style.display = "none";
//   }
//   for (i = 0; i < dots.length; i++) {
//     dots[i].className = dots[i].className.replace(" active", "");
//   }
//   slides[slideIndex-1].style.display = "block";
//   dots[slideIndex-1].className += " active";
//   captionText.innerHTML = dots[slideIndex-1].alt;
// }

//daynight
// function myFunction() {
//    var element = document.body;
//    element.classList.toggle("dark-theme");
// }

$(document).ready(function(){
  $('.toggle').click(function(){
    $('.sidebar-contact').toggleClass('active')
    $('.toggle').toggleClass('active')
  })
})

     


