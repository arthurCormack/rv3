export const actionTypes = { 
  TICK: 'TICK', 
  INCREMENT: 'INCREMENT', 
  DECREMENT: 'DECREMENT', 
  RESET: 'RESET', 
  SIMPLE_TEST: 'SIMPLE_TEST',

  PAGE_LOAD_TRIGGER: 'PAGE_LOAD_TRIGGER',
  PAGE_LOAD_STARTED: 'PAGE_LOAD_STARTED',
  PAGE_LOAD_SUCCESS: 'PAGE_LOAD_SUCCESS',
  PAGE_LOAD_FAILURE: 'PAGE_LOAD_FAILURE',

  GENERAL_CONTENT_LOAD_TRIGGER: 'GENERAL_CONTENT_LOAD_TRIGGER',
  GENERAL_CONTENT_LOAD_STARTED: 'GENERAL_CONTENT_LOAD_STARTED',
  GENERAL_CONTENT_LOAD_SUCCESS: 'GENERAL_CONTENT_LOAD_SUCCESS',
  GENERAL_CONTENT_LOAD_FAILURE: 'GENERAL_CONTENT_LOAD_FAILURE',
  GENERAL_CONTENT_LOAD_NOTFOUND: 'GENERAL_CONTENT_LOAD_NOTFOUND',

  POSTSTACK_LOADNEXTPOST_TRIGGER: 'POSTSTACK_LOADNEXTPOST_TRIGGER',
  POSTSTACK_LOADNEXTPOST_STARTED: 'POSTSTACK_LOADNEXTPOST_STARTED',
  POSTSTACK_LOADNEXTPOST_SUCCESS: 'POSTSTACK_LOADNEXTPOST_SUCCESS',
  POSTSTACK_LOADNEXTPOST_FAILURE: 'POSTSTACK_LOADNEXTPOST_FAILURE',

}

// INITIALIZES CLOCK ON SERVER
export const serverRenderClock = () => (dispatch) =>
  dispatch({
    type: types.TICK,
    payload: { light: false, ts: Date.now() },
  })

// INITIALIZES CLOCK ON CLIENT
export const startClock = () => (dispatch) =>
  setInterval(() => {
    dispatch({ type: types.TICK, payload: { light: true, ts: Date.now() } })
  }, 1000)

// INCREMENT COUNTER BY 1
export const incrementCount = () => ({ type: actionTypes.INCREMENT })

// DECREMENT COUNTER BY 1
export const decrementCount = () => ({ type: actionTypes.DECREMENT })

// RESET COUNTER
export const resetCount = () => ({ type: actionTypes.RESET })



export function simpleTest() {
  return { type: actionTypes.SIMPLE_TEST }
}



export function pageLoadTrigger() {
  return {
    type: actionTypes.PAGE_LOAD_TRIGGER,
  }
}
export function pageLoadStarted() {
  return {
    type: actionTypes.PAGE_LOAD_STARTED,
  }
}
export function pageLoadSuccess(data) {
  return {
    type: actionTypes.PAGE_LOAD_SUCCESS,
    data,
  }
}
export function pageLoadFailure(error) {
  return {
    type: actionTypes.PAGE_LOAD_FAILURE,
    error,
  }
}

// generalContentLoadTrigger, generalContentLoadStarted, generalContentLoadSuccess, generalContentLoadFailure
// generalContentLoadTrigger
export function generalContentLoadTrigger(route) {
  console.log('inside the generalContentLoadTrigger action ...');
  return {
    type: actionTypes.GENERAL_CONTENT_LOAD_TRIGGER,
    route
  }
}
export function generalContentLoadStarted(route) {
  return {
    type: actionTypes.GENERAL_CONTENT_LOAD_STARTED,
    route,
  }
}
export function generalContentLoadSuccess(data) {
  return {
    type: actionTypes.GENERAL_CONTENT_LOAD_SUCCESS,
    data,
  }
}
export function generalContentLoadFailure(error) {
  return {
    type: actionTypes.GENERAL_CONTENT_LOAD_FAILURE,
    error,
  }
}

export function generalContentLoadNotFound(caard) {
  return {
    type: actionTypes.GENERAL_CONTENT_LOAD_NOTFOUND,
    caard,
  }
}