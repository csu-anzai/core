///<reference path="../../../_buildfiles/jstests/definitions/kajona.d.ts" />
///<amd-module name="oauth2"/>

class Oauth2 {
  /**
   * @param {string} url
   */
  public static redirect(url: string) {
    location.href = url;
  }
}

export default Oauth2;
