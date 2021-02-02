// import App from 'next/app';
import { Provider } from "react-redux";
import withRedux from "next-redux-wrapper";
import { makeStore, wrapper } from '../store';


function App({ Component, pageProps }) {
  return <Component {...pageProps} />
}

export default wrapper.withRedux(App)
