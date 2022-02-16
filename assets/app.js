/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';

// start the Stimulus application
import './bootstrap';

const btn = document.querySelector('.mobile-menu-button')
const sidebar = document.querySelector('.sidebar')

btn.addEventListener('click', e => {
    e.stopPropagation()
    sidebar.classList.toggle('-translate-x-full')
})

document.addEventListener('click', () => {
    sidebar.classList.add('-translate-x-full')
})
