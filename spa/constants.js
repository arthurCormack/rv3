// const someServerApiURL = typeof process.env.SERVER_API_URL === 'string' ? process.env.SERVER_API_URL : process.env.API_URL;
// export const BASE_URL = typeof window !== 'undefined' ? `${process.env.API_URL}` : `${someServerApiURL}`;
// export const API_ENPOINT_PREFIX = 'wp-json/zm-content/v1';


// export const API_URL = typeof window !== 'undefined' ? `${process.env.API_URL}/${API_ENPOINT_PREFIX}` : `${someServerApiURL}/${API_ENPOINT_PREFIX}`;

export const API_ENPOINT_PREFIX = 'wp-json/zm-content/v1';

export const API_URL = process.env.API_URL;// from .env variable
export const APICALLURL_GETFIRSTDATEDPOST = `${API_URL}/getfirstdatedpost`;// getfirstdatedpost
export const APICALLURL_GETNEXTDATEDPOST = `${API_URL}/getnextdatedpost`;// getfirstdatedpost