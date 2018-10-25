//   (c) 2004-2006 by MulchProductions, www.mulchprod.de
//   (c) 2007-2016 by Kajona, www.kajona.de
//       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt

/**
 * @module oauth2
 */
define("oauth2", ["jquery"], function($) {
    return {
        /**
         * @param {string} url
         */
        redirect: function (url) {
            location.href = url;
        }
    };
});

