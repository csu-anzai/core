///<reference path="../node_modules/@types/jquery/index.d.ts" />
///<reference path="../node_modules/@types/ckeditor/index.d.ts" />
///<reference path="../node_modules/@types/toastr/index.d.ts" />
///<reference path="../node_modules/@types/qtip2/index.d.ts" />
///<reference path="../node_modules/@types/requirejs/index.d.ts" />

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
declare var KAJONA: Kajona;
declare var execScript: any;
