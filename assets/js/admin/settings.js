"use strict";

(function() {
    if ('loading' === document.readyState) {
        // The DOM has not yet been loaded.
        document.addEventListener('DOMContentLoaded', initSettings);
    } else {
        // The DOM has already been loaded.
        initSettings();
    }

    // Initiate settings when the DOM loads.
    function initSettings() {
        let nothingFoundLabel = document.getElementById('wwp-clear-cache-404');
        let successLabel = document.getElementById('wwp-clear-cache-updated');
        let successError = document.getElementById('wwp-clear-cache-error');
        const clearCacheButton = document.getElementById('wwp-clear-cache-button');

        // Clear cookie set by the plugin.
        const clearCache = function() {
            let isUpdated = false;
            let cookies = document.cookie.split(';');

            // reset labels
            successError.setAttribute('aria-hidden', true);
            successLabel.setAttribute('aria-hidden', true);
            nothingFoundLabel.setAttribute('aria-hidden', true);

            if (!cookies.length) {
                successError.setAttribute('aria-hidden', false);
                return;
            }

            // Find a cookie saved by the plugin.
            cookies.forEach(
                function(item, index) {
                    if (!item) {
                        return;
                    }

                    let cookie = decodeURIComponent(item);
                    const isFound = cookie.match(/welcomewp-message-/g);

                    // Remove a cookie saved by the plugin.
                    if (isFound) {
                        let name = cookie.split('=');
                        name = name[0].trim();

                        document.cookie = name + '=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';

                        isUpdated = true;
                    }
                }
            );

            // Display success label.
            if (isUpdated) {
                successLabel.setAttribute('aria-hidden', false);
                return;
            }

            // Display nothing found label.
            nothingFoundLabel.setAttribute('aria-hidden', false);
        }

        clearCacheButton.addEventListener('click', clearCache, false);
    }
})();