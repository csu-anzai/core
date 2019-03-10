class Oauth2 {
  /**
   * @param {string} url
   */
  public static redirect(url: string) {
    location.href = url;
  }
}

export default Oauth2;
