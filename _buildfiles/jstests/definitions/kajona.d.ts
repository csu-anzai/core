///<reference path="../node_modules/@types/jquery/index.d.ts" />
///<reference path="../node_modules/@types/ckeditor/index.d.ts" />
///<reference path="../node_modules/@types/toastr/index.d.ts" />
///<reference path="../node_modules/@types/qtip2/index.d.ts" />
///<reference path="../node_modules/@types/requirejs/index.d.ts" />
///<reference path="../node_modules/@types/bootstrap/index.d.ts" />
///<reference path="../node_modules/@types/jqueryui/index.d.ts" />

declare module 'jquery' {
    export = jQuery;
}

interface Admin {
    folderview: any;
    lang: any;
    forms: any;
}

interface Kajona {
    util: any;
    admin: any;
}

declare var KAJONA_WEBPATH: string;
declare var KAJONA_DEBUG: number;
declare var KAJONA_LANGUAGE: string;
declare var KAJONA_BROWSER_CACHEBUSTER: number;
declare var KAJONA_PHARMAP: Array<string>;

declare var routie: any;

/** @deprecated */
declare var KAJONA: Kajona;

/** @deprecated */
declare var jsDialog_0: any;
/** @deprecated */
declare var jsDialog_1: any;
/** @deprecated */
declare var jsDialog_2: any;
/** @deprecated */
declare var jsDialog_3: any;
