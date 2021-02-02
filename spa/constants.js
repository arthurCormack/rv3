// API_URL and SERVER_API_URL are defined in .env variables
export const API_ENPOINT_PREFIX = 'wp-json/zm-content/v1';

// during ssr, inside a container context, we can use the name of the service, and the docker network will resolve it. It will not know the actual machine's /etc/hosts 
export const API_URL = typeof window !== 'undefined' ? `${process.env.API_URL}/${API_ENPOINT_PREFIX}` : `${process.env.SERVER_API_URL}/${API_ENPOINT_PREFIX}`;// from .env variable
export const APICALLURL_GETFIRSTDATEDPOST = `${API_URL}/getfirstdatedpost`;// getfirstdatedpost
export const APICALLURL_GETNEXTDATEDPOST = `${API_URL}/getnextdatedpost`;// getfirstdatedpost