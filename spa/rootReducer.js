import { combineReducers } from '@reduxjs/toolkit';
import { actionTypes } from './actions';
import { contentsReducer, initialContentsState } from './reducers';// all content loaded from the general content load lives here.


// COUNTER REDUCER
const counterReducer = (state = 0, { type }) => {
  switch (type) {
    case actionTypes.INCREMENT:
      return state + 1
    case actionTypes.DECREMENT:
      return state - 1
    case actionTypes.RESET:
      return 0
    default:
      return state
  }
}

// INITIAL TIMER STATE
const initialTimerState = {
  lastUpdate: 0,
  light: false,
}

// TIMER REDUCER
const timerReducer = (state = initialTimerState, { type, payload }) => {
  switch (type) {
    case actionTypes.TICK:
      return {
        lastUpdate: payload.ts,
        light: !!payload.light,
      }
    default:
      return state
  }
}



export const initialState = {
  counter: 0,
  timer: initialTimerState,
  contents: initialContentsState,
};

export const rootReducer = combineReducers({
  counter: counterReducer,
  timer: timerReducer,
});

// what reducers / state do we want to have be present all the time, and what things do we want to have 'injected' dynamically?
// the general 'content' slice, is so intrinsic to this particular application - all of it's stuff
export default function createReducer(injectedReducers) {
  return combineReducers({
    global: rootReducer,
    contents: contentsReducer,
    ...injectedReducers,
  });
}