export const actionTypes = {
  FAILURE: 'FAILURE',
  INCREMENT: 'INCREMENT',
  DECREMENT: 'DECREMENT',
  RESET: 'RESET',
  LOAD_DATA: 'LOAD_DATA',
  LOAD_DATA_SUCCESS: 'LOAD_DATA_SUCCESS',
  START_CLOCK: 'START_CLOCK',
  TICK_CLOCK: 'TICK_CLOCK',

  
  PAGE_LOAD_TRIGGER: 'PAGE_LOAD_TRIGGER',
  PAGE_LOAD_STARTED: 'PAGE_LOAD_STARTED',
  PAGE_LOAD_SUCCESS: 'PAGE_LOAD_SUCCESS',
  PAGE_LOAD_FAILURE: 'PAGE_LOAD_FAILURE',
  HYDRATE: 'HYDRATE',
};

// 
// [THING]_LOAD_TRIGGER, [THING]_LOAD_ERROR, [THING]_LOAD_SUCCESS, [THING]_APIURL
// Possible THings include: SinglePost, SinglePostStack, SinglePage (pages never stack), ArchiveStack, 
// 

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

export function failure(error) {
  return {
    type: actionTypes.FAILURE,
    error,
  }
};

export function increment() {
  return { type: actionTypes.INCREMENT }
};

export function decrement() {
  return { type: actionTypes.DECREMENT }
};

export function reset() {
  return { type: actionTypes.RESET }
};

export function loadData() {
  return { type: actionTypes.LOAD_DATA }
};

export function loadDataSuccess(data) {
  return {
    type: actionTypes.LOAD_DATA_SUCCESS,
    data,
  };
};

export function startClock() {
  return { type: actionTypes.START_CLOCK };
};

export function tickClock(isServer) {
  return {
    type: actionTypes.TICK_CLOCK,
    light: !isServer,
    ts: Date.now(),
  };
};
