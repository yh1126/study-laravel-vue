/**
 * クッキーの値を取得する
 * @param {String} searchKey 検索するキー
 * @returns {String} キーに対応する値
 */
export function getCookieValue(searchKey) {
  if (typeof searchKey === 'undefined') {
    return ''
  }

  let val = ''

  // document.cookieは下記のような形式でcookieを参照できる
  // name=12345;token=67890;key=abcde
  document.cookie.split(';').forEach(cookie => {
    console.log(cookie);
    const [key, value] = cookie.split('=')
    if (key === searchKey) {
      return val = value
    }
  })

  return val
}

export const OK = 200
export const CREATED = 201
export const INTERNAL_SERVER_ERROR = 500
