export const actionTypes = { 
  TICK: 'TICK', 
  INCREMENT: 'INCREMENT', 
  DECREMENT: 'DECREMENT', 
  RESET: 'RESET', 
  SIMPLE_TEST: 'SIMPLE_TEST'
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
