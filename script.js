
/* ===== SIMPLE ANIMATION START UP ==== */
window.addEventListener('DOMContentLoaded', () => {
    const container = document.querySelector('.container');
    if (container) {
        // Small timeout to ensure the browser is ready
        setTimeout(() => {
            container.classList.add('show');
        }, 100);
    }
});

/* ===== SYSTEM TIME ===== */

document.addEventListener("DOMContentLoaded", function() {

    function updateTimeAndGreeting() {
        const now = new Date();
        
        // moved the clock form the html to the js //
        const clockElement = document.getElementById("clock");
        if (clockElement) {
            clockElement.innerHTML = now.toLocaleString('en-US', {
                hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true,
                month: 'short', day: '2-digit', year: 'numeric'
            });
        }

        //  GREETING //
        const hour = now.getHours();
        let currentGreeting = "Good Evening 🌙"; 
        
        if (hour < 12) {
            currentGreeting = "Good Morning 🌅";
        } else if (hour < 18) {
            currentGreeting = "Good Afternoon ☀️";
        }
        
        const greetingElement = document.getElementById("greeting");
        if (greetingElement) {
            greetingElement.innerText = "Welcome, " + currentGreeting + "";
        }
    }

    // Run both the clock and the greeting every 1 second //
    setInterval(updateTimeAndGreeting, 1000); 

    // Start it immediately //
    updateTimeAndGreeting();
});

// smooth scroll of back to top button //
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        document.querySelector(this.getAttribute('href')).scrollIntoView({
            behavior: 'smooth'
        });
    });
});


// submit button // 

const form = document.querySelector('form');
const submitBtn = document.querySelector('.btn-submit');

if(form) {
    form.addEventListener('submit', () => {
        submitBtn.innerHTML = "Processing Entry... ⏳";
        submitBtn.style.opacity = "0.7";
        submitBtn.style.pointerEvents = "none"; // Disables double-clicking
    });
}

// export confirmation on toast notifications // 

document.querySelector('.btn-export').addEventListener('click', function(){
    // 1. Create the toast element
    const toast = document.createElement('div');
    
    // Use your existing ID for the blue/gold styling
    toast.id = "toast"; 
    toast.className = "toast-show"; 
    toast.innerHTML = "📥 Preparing CSV Export...";
    
    // 2. Add to page
    document.body.appendChild(toast);

    // 3. Remove after 3 seconds
    setTimeout(() => {
        toast.className = "toast-hide";
        
        // Wait for animation to finish before removing from DOM
        setTimeout(() => {
            toast.remove();
        }, 400); 
    }, 2500);
});