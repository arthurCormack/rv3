export const actionTypes = {
  FAILURE: 'FAILURE',
  INCREMENT: 'INCREMENT',
  DECREMENT: 'DECREMENT',
  RESET: 'RESET',
  LOAD_DATA: 'LOAD_DATA',
  LOAD_DATA_SUCCESS: 'LOAD_DATA_SUCCESS',
  START_CLOCK: 'START_CLOCK',
  TICK_CLOCK: 'TICK_CLOCK',
  HYDRATE: 'HYDRATE',


  PAGE_LOAD_TRIGGER: 'PAGE_LOAD_TRIGGER',
  PAGE_LOAD_STARTED: 'PAGE_LOAD_STARTED',
  PAGE_LOAD_SUCCESS: 'PAGE_LOAD_SUCCESS',
  PAGE_LOAD_FAILURE: 'PAGE_LOAD_FAILURE',

  GENERAL_CONTENT_LOAD_TRIGGER: 'GENERAL_CONTENT_LOAD_TRIGGER',
  GENERAL_CONTENT_LOAD_STARTED: 'GENERAL_CONTENT_LOAD_STARTED',
  GENERAL_CONTENT_LOAD_SUCCESS: 'GENERAL_CONTENT_LOAD_SUCCESS',
  GENERAL_CONTENT_LOAD_FAILURE: 'GENERAL_CONTENT_LOAD_FAILURE',

  POSTSTACK_LOADNEXTPOST_TRIGGER: 'POSTSTACK_LOADNEXTPOST_TRIGGER',
  POSTSTACK_LOADNEXTPOST_STARTED: 'POSTSTACK_LOADNEXTPOST_STARTED',
  POSTSTACK_LOADNEXTPOST_SUCCESS: 'POSTSTACK_LOADNEXTPOST_SUCCESS',
  POSTSTACK_LOADNEXTPOST_FAILURE: 'POSTSTACK_LOADNEXTPOST_FAILURE',
}

export function failure(error) {
  return {
    type: actionTypes.FAILURE,
    error,
  }
}

export function increment() {
  return { type: actionTypes.INCREMENT }
}

export function decrement() {
  return { type: actionTypes.DECREMENT }
}

export function reset() {
  return { type: actionTypes.RESET }
}

export function loadData() {
  return { type: actionTypes.LOAD_DATA }
}

export function loadDataSuccess(data) {
  return {
    type: actionTypes.LOAD_DATA_SUCCESS,
    data,
  }
}

export function startClock() {
  return { type: actionTypes.START_CLOCK }
}

export function tickClock(isServer) {
  return {
    type: actionTypes.TICK_CLOCK,
    light: !isServer,
    ts: Date.now(),
  }
}
