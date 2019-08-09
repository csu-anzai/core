import axios from 'axios'
import qs from 'qs'
import { Service } from 'axios-middleware'
import WorkingIndicator from './WorkingIndicator'
const jwtDecode = require('jwt-decode')

/**
 * a wrapper class for axios used to configure axios globally and adds middleware for loading animation
 */
class GlobalAxiosConfig {
    private service : any
    constructor () {
        // global parameter serializer for axios : converts json data to url params
        axios.defaults.paramsSerializer = (params : any) => {
            return qs.stringify(params, { arrayFormat: 'brackets' })
        }
        // golbal axios's baseURL config
        axios.defaults.baseURL = KAJONA_WEBPATH
        // golbal axios's config : Access Token
        axios.defaults.headers.common = { 'Authorization': `bearer ${KAJONA_ACCESS_TOKEN}` }
        // Before each request, verify token
        axios.interceptors.request.use((config) => {
            const token = KAJONA_ACCESS_TOKEN
            let jwt = jwtDecode(token)
            if (!(token != null && Date.now() - (jwt.exp + 120000) > 0)) {
                config.headers.Authorization = `Bearer ${token}`
            } else {
                fetch('api.php/v1/authorization/refresh', { method: 'post',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ token: KAJONA_ACCESS_TOKEN }) }
                ).then(res => {
                    res.json().then(function (myJson) {
                        KAJONA_ACCESS_TOKEN = myJson.access_token
                        config.headers.Authorization = `Bearer ${myJson.access_token}`
                    })
                })
            }

            return config
        }, function (err) {
            return Promise.reject(err)
        })

        this.createMiddleware()
    }
    /**
     * use a middleware to be able to start/stop loader animation onRequest
     */
    private createMiddleware () : void {
        this.service = new Service(axios)
        this.service.register({
            onRequest (config : any) {
                WorkingIndicator.start()
                return config
            },
            onResponse (response : any) {
                WorkingIndicator.stop()
                return response
            }
        })
    }
}
export default GlobalAxiosConfig
