import './bootstrap';

document.addEventListener('DOMContentLoaded', () => {
    const heroSection = document.querySelector('.hero-observe');
    if (!heroSection) {
        return;
    }

    const shirt = heroSection.querySelector('.hero-shirt');
    const print = heroSection.querySelector('.hero-print');

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    shirt?.classList.remove('opacity-10');
                    shirt?.classList.add('opacity-100');
                    print?.classList.remove('opacity-0');
                    print?.classList.add('opacity-100');
                }
            });
        },
        { threshold: 0.35 }
    );

    observer.observe(heroSection);
});
