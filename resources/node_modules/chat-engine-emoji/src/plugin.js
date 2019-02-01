/**
 *
* @module chat-engine-emoji
* @description  Parses emoji in ```payload.data.text```.
*/

const emojis = require('./emoji.js');
const dotty = require("dotty");

// this is an example of middleware used in our test.js
// adds some text to message before it's sent and when it's received

/**
* @function
* @ceplugin
* @requires {@link ChatEngine}
* @param {Object} config The config object
* @param {String} [prop="data.text"] The payload to search for.
* @param {String} [event="message"] The ChatEngine event that will trigger emoji parsing.
* @param {Function} config.title The title of the desktop notification
* @param {Integer} [height=16] The height of the resulting emojiß images
* @param {String} [url] The web directory where emoji images are hosted. Filename (ex: /smile.png) will be added.
* @example
*
*
* const emoji = require('./src/plugin.js');
* chat = new OCF.Chat('emoji-chat');
* chat.plugin(emoji());
* chat.on('message', (payload) => {
*     // payload.data.text == '<img class="emoji" title=":pizza:" alt="pizza" src="http://www.webpagefx.com/tools/emoji-cheat-sheet/graphics/emojis/pizza.png" height="16" />';
* });
*
* chat.emit('message', {
*     text: 'I want :pizza:'
* });
*/
module.exports = (config = {}) => {

    // regular expression to find emoji strings
    const test = /:[a-z0-9_\-\+]+:/g;

    config.event = config.event || 'message';

    // where in the payload the text is
    config.prop = config.prop || 'data.text';

    config.height = config.height || 16;

    // where emoji images are hosted. filename (ex: /smile.png) will be added
    config.url = config.url || 'http://www.webpagefx.com/tools/emoji-cheat-sheet/graphics/emojis';

    // function to parse string for :smile: and other emoji
    const emoji = (someString, url = config.url, height = config.height) => someString.replace(test, (match) => {

        // use regex to find emoji and replace with html
        let result = match;

        // if text string is in list of emojis
        if (emojis.indexOf(match) !== -1) {

            // remove the : before and after
            let name = String(match).slice(1, -1);

            // return html image, using url and height supplied in
            // function
            result = '<img class="emoji" title=":' + name
                + ':" alt="' + name + '" src="' + url + '/'
                + encodeURIComponent(name) + '.png"'
                + (height ? (' height="' + height + '"') : '')
                + ' />';

        }

        return result;

    });

    /**
    * Turns```:smile:``` into ```<img src="/smile.png" />```
    * @listens message
    * @listens $history.message
    * @ceextends Chat
    */
    let parseEmoji = function(payload, next) {

        let message = dotty.get(payload, config.prop);

        // check if this sub property exists
        if(message.length) {

            // parse emoji
            let newPayload = emoji(message, config.url, config.height);
            dotty.put(payload, config.prop, newPayload);

        }

        // continue along middleware
        next(null, payload);

    };

    // these are new methods that will be added to the extended class
    class extension {

        /**
         * Renders emoji given ```:smile:``` as input.
         * @method render
         * @ceextends Chat
         * @param  {String} string The emoji text to turn into an icon
         * @param  {String} url Root url to look for emoji images
         * @param  {Int} height Height of the emoji icons
         * @returns {String} Returns the IMG HTML for this emoji
         */
        render(string, url, height) {
            return emoji(string, url, height);
        }

        /**
         * Finds partial string matches of emoji text by searching emoji db.
         * @method search
         * @ceextends Chat
         * @param  {Strings} query The partial text to search for
         * @returns {Array} An array of matching emoji strings. Call the render function to display these.
         */
        search(query) {

            var results = [];

            for(var i in emojis) {
                if(emojis[i].substring(0, query.length) == query) {
                    results.push(emojis[i]);
                }
            }

            return results;

        }
    }

    let result = {
        namespace: 'emoji',
        middleware: {
            on: {}
        },
        extends: {
            Chat: extension
        }
    }

    result.middleware.on[config.event] = parseEmoji;
    result.middleware.on['$history.' + config.event] = parseEmoji;

    // middleware tells the framework to use these functions when
    // messages are sent or received
    return result;
}
