import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';

console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');

// Mobile menu toggle function
function toggleMobileMenu() {
    console.log('toggleMobileMenu called'); // Debug
    const burgerMenu = document.querySelector('.burger-menu');
    const mobileMenu = document.querySelector('.mobile-menu');
    
    console.log('burgerMenu:', burgerMenu); // Debug
    console.log('mobileMenu:', mobileMenu); // Debug
    
    if (burgerMenu && mobileMenu) {
        burgerMenu.classList.toggle('active');
        mobileMenu.classList.toggle('active');
        console.log('Classes toggled'); // Debug
    } else {
        console.log('Elements not found'); // Debug
    }
}

// Make function globally available
window.toggleMobileMenu = toggleMobileMenu;