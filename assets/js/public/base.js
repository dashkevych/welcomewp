"use strict";

(function() {
    if ("loading" === document.readyState) {
        // The DOM has not yet been loaded.
        document.addEventListener("DOMContentLoaded", initMessage);
    } else {
        // The DOM has already been loaded.
        initMessage();
    }

    // Initiate the message when the DOM loads.
    function initMessage() {
        const message = document.getElementById("welcomewp-message");

        if (!message) {
            return;
        }

        if (isHidden(message)) {
            hideMessage(message);

            return;
        }

        showMessage(message);
    }

    // check if message visible.
    function isHidden(message) {
        const id = getCookieId(message);

        return getCookie(id);
    }

    // return message cookie id.
    function getCookieId(message) {
        const messageId = message.getAttribute("data-message-id");
        const cookieId = message.id + "-" + messageId;

        return cookieId;
    }

    // hide message.
    function hideMessage(message) {
        if (!message) {
            return;
        }

        // make sure message has id.
        const messageId = message.getAttribute("data-message-id");

        if (!messageId) {
            return;
        }

        // Hide the wideget.
        message.setAttribute("aria-hidden", true);

        const cookieId = getCookieId(message);

        // Remember seen message when needed.
        if (getCookie(cookieId)) {
            return;
        }

        const cookieExpiration = getCookieExpiration(message);

        setCookie(cookieId, "hide", cookieExpiration);
    }

    // Show message.
    function showMessage(message) {
        let closeButtons = message.querySelectorAll(
            ".welcomewp-message__close-button"
        );

        // Make sure close button exists.
        if (!closeButtons.length) {
            return;
        }

        // Close the message.
        const closeMessage = function() {
            hideMessage(message);
        };

        // Add class to the main menu item if a dropdown menu would go off the screen.
        for (let i = 0; i < closeButtons.length; i++) {
            closeButtons[i].addEventListener("click", closeMessage, false);
        }
    }

    // Store a cookie.
    function setCookie(cookieName, cookieValue, cookieExpiration) {
        // Make sure settings for expiration are present.
        if (!cookieExpiration) {
            return;
        }

        // Get current time.
        let expiryDate = new Date();

        // Set expiration date.
        switch (cookieExpiration["measurement"]) {
            case "minutes":
                expiryDate.setTime(
                    expiryDate.getTime() + cookieExpiration["interval"] * 60 * 1000
                );
                break;
            case "hours":
                expiryDate.setTime(
                    expiryDate.getTime() + cookieExpiration["interval"] * 3600 * 1000
                );
                break;
            case "days":
                expiryDate.setDate(expiryDate.getDate() + cookieExpiration["interval"]);
                break;
            case "months":
                expiryDate.setMonth(
                    expiryDate.getMonth() + cookieExpiration["interval"]
                );
                break;
        }

        let expires = "expires=" + expiryDate.toUTCString();

        document.cookie =
            cookieName + "=" + cookieValue + ";" + expires + ";path=/";
    }

    /**
     * Get the value of a cookie by its name.
     *
     * @param {string} cookieName - The name of the cookie.
     * @returns {string|undefined} - Returns the value of the cookie or undefined if not found.
     */
    function getCookie(cookieName) {
        // Check if cookieName is provided and is a non-empty string.
        if (typeof cookieName !== "string" || cookieName.trim() === "") {
            return undefined;
        }

        // Escape any characters that might conflict with regex syntax.
        const escapedCookieName = cookieName.replace(
            /([\.$?*|{}\(\)\[\]\\\/\+^])/g,
            "\\$1"
        );

        // Try to match the cookie in the document's cookie string.
        const matches = document.cookie.match(
            new RegExp("(?:^|; )" + escapedCookieName + "=([^;]*)")
        );

        // If matches found, decode and return the value. Otherwise, return undefined.
        return matches ? decodeURIComponent(matches[1]) : undefined;
    }

    // get cookie expiration date.
    function getCookieExpiration(message) {
        let settings = {
            interval: 5,
            measurement: "minutes",
        };

        const expireTimeString = message.getAttribute("data-message-expire-time");
        const expireTimeArray = expireTimeString.split("|");

        if (expireTimeArray[0] !== undefined) {
            settings["interval"] = parseInt(expireTimeArray[0]);
        }

        if (expireTimeArray[1] !== undefined) {
            const measurement = ["minutes", "hours", "days", "months"];

            if (measurement.includes(expireTimeArray[1])) {
                settings["measurement"] = expireTimeArray[1];
            }
        }

        return settings;
    }
})();