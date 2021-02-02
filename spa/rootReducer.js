import { combineReducers } from '@reduxjs/toolkit';
import { actionTypes } from './actions';

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

export const rootReducer = combineReducers({
  counter: counterReducer,
  timer: timerReducer,
});
export default function createReducer(injectedReducers) {
  return combineReducers({
    global: rootReducer,
    ...injectedReducers,
  });
}