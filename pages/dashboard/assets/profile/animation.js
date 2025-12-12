import anime from 'https://cdn.jsdelivr.net/npm/animejs@3.2.1/lib/anime.es.js';

// Custom splitText implementation for Anime.js V3
function splitText(selector) {
    const el = document.querySelector(selector);
    if (!el) return { chars: [] };

    const text = el.textContent;
    el.innerHTML = '';

    const chars = [];
    for (let char of text) {
        const span = document.createElement('span');
        span.textContent = char;
        // Preserve spaces
        if (char === ' ') {
            span.innerHTML = '&nbsp;';
        }
        span.style.display = 'inline-block';
        el.appendChild(span);
        chars.push(span);
    }

    return { chars };
}

// Ensure the element exists before animating
const element = document.querySelector('.animate-text');

if (element) {
    const { chars } = splitText('.animate-text');

    anime({
        targets: chars,
        translateY: ['-1.5em', 0],
        rotate: [-360, 0],
        delay: anime.stagger(50),
        easing: 'easeOutBounce', // Matching the V4 'outBounce' roughly
        duration: 1000,
        loop: true,
        loopDelay: 1000
    });
}
