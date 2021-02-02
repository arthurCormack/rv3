import { Provider } from 'react-redux'
// import { useStore } from '../store/configureStore'

import { useStore } from '../store'
export default function App({ Component, pageProps }) {
  const store = useStore(pageProps.initialReduxState);// this might be undefiined to begin with!
  console.log('_app App(), store:', store);
  return (
    <Provider store={store}>
      <Component {...pageProps} />
    </Provider>
  )
}
