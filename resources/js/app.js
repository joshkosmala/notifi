import './bootstrap';
import * as bootstrap from 'bootstrap';
import intlTelInput from 'intl-tel-input';
import 'intl-tel-input/build/css/intlTelInput.css';
import flatpickr from 'flatpickr';
import 'flatpickr/dist/flatpickr.min.css';

const MAPBOX_TOKEN = 'pk.eyJ1Ijoibm90aWZpbnoiLCJhIjoiY21rbXZwYWdrMGppZDNlcHlkczcxMXFqeSJ9.qrbOQi87M89FgLk_kcyuzg';

// Dark mode theme management
function getPreferredTheme() {
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme) {
        return savedTheme;
    }
    return 'auto';
}

function getActualTheme(theme) {
    if (theme === 'auto') {
        return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    }
    return theme;
}

function applyTheme(theme) {
    const actualTheme = getActualTheme(theme);
    document.documentElement.setAttribute('data-bs-theme', actualTheme);
    updateThemeUI(theme);
}

function setTheme(theme) {
    if (theme === 'auto') {
        localStorage.removeItem('theme');
    } else {
        localStorage.setItem('theme', theme);
    }
    applyTheme(theme);
}

function updateThemeUI(theme) {
    // Update dropdown icon based on current theme
    const dropdownIcon = document.querySelector('#themeDropdown i');
    if (dropdownIcon) {
        dropdownIcon.className = 'bi';
        if (theme === 'light') {
            dropdownIcon.classList.add('bi-sun');
        } else if (theme === 'dark') {
            dropdownIcon.classList.add('bi-moon-stars');
        } else {
            dropdownIcon.classList.add('bi-circle-half');
        }
    }
    
    // Update checkmarks
    document.querySelectorAll('.theme-check').forEach(check => {
        check.style.display = check.dataset.theme === theme ? 'inline' : 'none';
    });
}

// Initialize theme immediately (before DOMContentLoaded to prevent flash)
applyTheme(getPreferredTheme());

// Listen for system theme changes (affects auto mode)
window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
    const savedTheme = localStorage.getItem('theme');
    if (!savedTheme) {
        applyTheme('auto');
    }
});

// Make setTheme available globally for the dropdown buttons
window.setTheme = setTheme;

// Update UI once DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    updateThemeUI(getPreferredTheme());
});

// Initialize international phone input on any element with data-phone-input
document.addEventListener('DOMContentLoaded', function() {
    // Auto-dismiss Bootstrap alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
            bsAlert.close();
        }, 5000);
    });

    const phoneInputs = document.querySelectorAll('[data-phone-input]');
    
    phoneInputs.forEach(function(input) {
        const iti = intlTelInput(input, {
            initialCountry: 'nz',
            countryOrder: ['nz', 'au', 'us'],
            separateDialCode: true,
            utilsScript: 'https://cdn.jsdelivr.net/npm/intl-tel-input@23.0.0/build/js/utils.js',
        });

        // Store the full international number in a hidden field on form submit
        const form = input.closest('form');
        if (form) {
            form.addEventListener('submit', function() {
                input.value = iti.getNumber();
            });
        }
    });

    // Initialize Mapbox address autocomplete on any element with data-address-input
    const addressInputs = document.querySelectorAll('[data-address-input]');
    
    addressInputs.forEach(function(input) {
        let dropdown = null;
        let debounceTimer = null;

        // Create dropdown element
        function createDropdown() {
            dropdown = document.createElement('div');
            dropdown.className = 'address-autocomplete-dropdown';
            dropdown.style.cssText = 'position:absolute;z-index:1000;background:#fff;border:1px solid #dee2e6;border-radius:0.375rem;box-shadow:0 0.5rem 1rem rgba(0,0,0,0.15);max-height:300px;overflow-y:auto;display:none;width:100%;';
            input.parentNode.style.position = 'relative';
            input.parentNode.appendChild(dropdown);
        }

        createDropdown();

        // Fetch suggestions from Mapbox
        async function fetchSuggestions(query) {
            if (query.length < 3) {
                dropdown.style.display = 'none';
                return;
            }

            const url = `https://api.mapbox.com/search/searchbox/v1/suggest?q=${encodeURIComponent(query)}&access_token=${MAPBOX_TOKEN}&session_token=${crypto.randomUUID()}&country=nz,au,us,gb&types=address&limit=5`;
            
            try {
                const response = await fetch(url);
                const data = await response.json();
                showSuggestions(data.suggestions || []);
            } catch (error) {
                console.error('Address lookup failed:', error);
            }
        }

        // Show suggestions in dropdown
        function showSuggestions(suggestions) {
            dropdown.innerHTML = '';
            
            if (suggestions.length === 0) {
                dropdown.style.display = 'none';
                return;
            }

            suggestions.forEach(suggestion => {
                const item = document.createElement('div');
                item.className = 'address-autocomplete-item';
                item.style.cssText = 'padding:10px 12px;cursor:pointer;border-bottom:1px solid #f0f0f0;';
                item.innerHTML = `<strong>${suggestion.name}</strong><br><small class="text-muted">${suggestion.place_formatted || ''}</small>`;
                
                item.addEventListener('mouseenter', () => item.style.background = '#f8f9fa');
                item.addEventListener('mouseleave', () => item.style.background = '#fff');
                
                item.addEventListener('click', async () => {
                    // Retrieve full address details
                    const retrieveUrl = `https://api.mapbox.com/search/searchbox/v1/retrieve/${suggestion.mapbox_id}?access_token=${MAPBOX_TOKEN}&session_token=${crypto.randomUUID()}`;
                    try {
                        const response = await fetch(retrieveUrl);
                        const data = await response.json();
                        const feature = data.features?.[0];
                        if (feature) {
                            input.value = feature.properties.full_address || suggestion.name;
                            
                            // Store coordinates in hidden fields if they exist
                            const form = input.closest('form');
                            if (form && feature.geometry?.coordinates) {
                                const latInput = form.querySelector('[name$="_latitude"], [name="latitude"]');
                                const lngInput = form.querySelector('[name$="_longitude"], [name="longitude"]');
                                if (latInput) latInput.value = feature.geometry.coordinates[1];
                                if (lngInput) lngInput.value = feature.geometry.coordinates[0];
                            }
                        } else {
                            input.value = suggestion.name + (suggestion.place_formatted ? ', ' + suggestion.place_formatted : '');
                        }
                    } catch {
                        input.value = suggestion.name + (suggestion.place_formatted ? ', ' + suggestion.place_formatted : '');
                    }
                    dropdown.style.display = 'none';
                });
                
                dropdown.appendChild(item);
            });

            dropdown.style.display = 'block';
        }

        // Event listeners
        input.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => fetchSuggestions(this.value), 300);
        });

        input.addEventListener('blur', function() {
            setTimeout(() => dropdown.style.display = 'none', 200);
        });

        input.addEventListener('focus', function() {
            if (this.value.length >= 3) {
                fetchSuggestions(this.value);
            }
        });
    });

    // Initialize Flatpickr datetime picker on any element with data-datetime-input
    const datetimeInputs = document.querySelectorAll('[data-datetime-input]');
    
    datetimeInputs.forEach(function(input) {
        flatpickr(input, {
            enableTime: true,
            dateFormat: 'Y-m-d H:i',
            altInput: true,
            altFormat: 'D j M Y, h:i K',
            minDate: 'today',
            time_24hr: false,
            minuteIncrement: 15,
            disableMobile: true,
        });
    });
});