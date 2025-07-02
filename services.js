      function toggleMenu() {
        const menu = document.getElementById("mobile-menu");
        menu.classList.toggle("hidden");
      }
      
      // Header scroll effect
      window.addEventListener('scroll', function() {
        const header = document.getElementById('main-header');
        if (window.scrollY > 50) {
          header.classList.add('scrolled');
        } else {
          header.classList.remove('scrolled');
        }
      });
      
      // Scroll reveal animation
      function scrollReveal() {
        const reveals = document.querySelectorAll('.reveal');
        
        reveals.forEach(reveal => {
          const revealTop = reveal.getBoundingClientRect().top;
          const revealPoint = 150;
          
          if (revealTop < window.innerHeight - revealPoint) {
            reveal.classList.add('active');
          }
        });
      }
      
      window.addEventListener('scroll', scrollReveal);
      
      document.addEventListener("DOMContentLoaded", () => {
        // Initialize all sliders
        const engineeringSlider = new Glider(document.getElementById('engineering-slider'), {
          slidesToShow: 1,
          slidesToScroll: 1,
          draggable: true,
          dots: '#engineering-dots',
          arrows: {
            prev: '.glider-prev',
            next: '.glider-next'
          },
          responsive: [
            {
              breakpoint: 768,
              settings: {
                slidesToShow: 2,
                slidesToScroll: 1
              }
            },
            {
              breakpoint: 1024,
              settings: {
                slidesToShow: 3,
                slidesToScroll: 1
              }
            }
          ]
        });
        
        const dataSlider = new Glider(document.getElementById('data-slider'), {
          slidesToShow: 1,
          slidesToScroll: 1,
          draggable: true,
          dots: '#data-dots',
          arrows: {
            prev: '.glider-prev',
            next: '.glider-next'
          },
          responsive: [
            {
              breakpoint: 768,
              settings: {
                slidesToShow: 2,
                slidesToScroll: 1
              }
            },
            {
              breakpoint: 1024,
              settings: {
                slidesToShow: 3,
                slidesToScroll: 1
              }
            }
          ]
        });
        
        const securitySlider = new Glider(document.getElementById('security-slider'), {
          slidesToShow: 1,
          slidesToScroll: 1,
          draggable: true,
          dots: '#security-dots',
          arrows: {
            prev: '.glider-prev',
            next: '.glider-next'
          },
          responsive: [
            {
              breakpoint: 768,
              settings: {
                slidesToShow: 2,
                slidesToScroll: 1
              }
            },
            {
              breakpoint: 1024,
              settings: {
                slidesToShow: 3,
                slidesToScroll: 1
              }
            }
          ]
        });
        
        // Auto-rotate sliders
        setInterval(() => {
          engineeringSlider.scrollItem('next');
        }, 5000);
        
        setInterval(() => {
          dataSlider.scrollItem('next');
        }, 5600);
        
        setInterval(() => {
          securitySlider.scrollItem('next');
        }, 6000);
        
        // Close mobile menu when clicking links
        document.querySelectorAll('#mobile-menu a').forEach(link => {
          link.addEventListener('click', () => {
            const menu = document.getElementById("mobile-menu");
            if (!menu.classList.contains('hidden')) {
              menu.classList.add('hidden');
            }
          });
        });
        
        // Initial scroll reveal check
        scrollReveal();
      });