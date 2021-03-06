import { useMemo } from 'react'
import { createStore, applyMiddleware, compose } from 'redux'
import { composeWithDevTools } from 'redux-devtools-extension'
import { createInjectorsEnhancer, forceReducerReload } from 'redux-injectors';
// import thunkMiddleware from 'redux-thunk'
import createSagaMiddleware from 'redux-saga';
import { createWrapper } from 'next-redux-wrapper'
import rootSaga from './rootSaga';
// import reducers from './reducers'
import createReducer from './rootReducer';

let store

function initStore(initialState) {
  // console.log('initStore', initialState);


  let composeEnhancers = compose;
  const reduxSagaMonitorOptions = {};

  const sagaMiddleware = createSagaMiddleware(reduxSagaMonitorOptions);
  const { run: runSaga } = sagaMiddleware;
  const middlewarez = [sagaMiddleware];


  const enhancers = [
    applyMiddleware(...middlewarez),
    createInjectorsEnhancer({
      createReducer,
      runSaga,
    }),
  ];

  store = createStore(
    createReducer(),
    initialState,
    // composeWithDevTools(applyMiddleware(...middlewarez))
    composeWithDevTools(composeEnhancers(...enhancers)),
  );
  // Extensions
  store.runSaga = sagaMiddleware.run;

  store.sagaTask = sagaMiddleware.run(rootSaga);

  // store.injectedReducers = {}; // Reducer registry
  // store.injectedSagas = {}; // Saga registry
  return store;
  // return createStore(
  //   reducers,
  //   initialState,
  //   composeWithDevTools(applyMiddleware(...middlewarez))
  // )
}

export const initializeStore = (preloadedState) => {
  // console.log('initializeStore');
  let _store = store ?? initStore(preloadedState)

  // After navigating to a page with an initial Redux state, merge that state
  // with the current state in the store, and create a new store
  if (preloadedState && store) {
    _store = initStore({
      ...store.getState(),
      ...preloadedState,
    })
    // Reset the current store
    store = undefined
  }

  // For SSG and SSR always create a new store
  if (typeof window === 'undefined') return _store
  // Create the store once in the client
  if (!store) store = _store

  return _store
}

export function useStore(initialState) {
  const store = useMemo(() => initializeStore(initialState), [initialState])
  return store
}

export const wrapper = createWrapper(initializeStore, { debug: true })
