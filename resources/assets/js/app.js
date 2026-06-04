/**
 * JavaScript dependencies
 */

import axios from 'axios';

// Make axios available globally
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]')?.content;

// jQuery and Bootstrap
import $ from 'jquery';
window.$ = window.jQuery = $;

import 'bootstrap';
window.bootstrap = $.bootstrap;

// Marked for markdown parsing
import { marked } from 'marked';
window.marked = marked;

// Custom application logic
document.addEventListener('DOMContentLoaded', () => {
    // Sidebar toggle functionality
    const sidebarToggle = document.querySelector('.toggle-sidebar');
    const sidebarMenu = document.getElementById('sidebarMenu');
    
    if (sidebarToggle && sidebarMenu) {
        sidebarToggle.addEventListener('click', () => {
            sidebarMenu.classList.toggle('show');
        });
    }

    // Confirm dialogs for delete actions
    document.querySelectorAll('[data-confirm]').forEach(element => {
        element.addEventListener('click', (e) => {
            const message = element.getAttribute('data-confirm');
            if (!confirm(message)) {
                e.preventDefault();
            }
        });
    });
});