document.addEventListener('DOMContentLoaded', () => {

    // AOS
    AOS.init({
        duration:1000,
        once:true
    });

    // Navbar Scroll
    window.addEventListener('scroll', () => {

        const navbar = document.querySelector('.navbar-custom');

        if(window.scrollY > 50){
            navbar.classList.add('scrolled');
        }else{
            navbar.classList.remove('scrolled');
        }
    });

    // Smooth Scroll
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {

        anchor.addEventListener('click', function(e){

            e.preventDefault();

            const target = document.querySelector(
                this.getAttribute('href')
            );

            if(target){

                target.scrollIntoView({
                    behavior:'smooth'
                });
            }
        });
    });

    // Service Button Loading Effect
    document.querySelectorAll('.service-btn').forEach(button => {

        button.addEventListener('click', function(){

            const serviceCard =
                this.closest('.service-card');

            if(serviceCard){

                const serviceName =
                    serviceCard.querySelector('.service-title').textContent;

                console.log('Service selected:', serviceName);
            }

            const originalText = this.innerHTML;

            this.innerHTML =
                '<i class="fas fa-spinner fa-spin me-2"></i>Loading...';

            this.style.pointerEvents = 'none';

            setTimeout(() => {

                this.innerHTML = originalText;

                this.style.pointerEvents = 'auto';

            }, 1500);
        });
    });

});