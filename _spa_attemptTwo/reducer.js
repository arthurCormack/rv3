import { actionTypes } from './actions'
import { HYDRATE } from 'next-redux-wrapper'
import { combineReducers } from 'redux';


function errorReducer (state = false, action) {
  switch (action.type) {
    case actionTypes.FAILURE:
      return {
          ...state,
          ...{ error: action.error },
        };
      
    default:
      return state;
  }
}

function countReducer(state = 0, action) {
  switch (action.type) {
    case actionTypes.INCREMENT:
      return {
        ...state,
        ...{ count: state.count + 1 },
      };
    case actionTypes.DECREMENT:
      return {
        ...state,
        ...{ count: state.count - 1 },
      };
    case actionTypes.RESET:
      return {
        ...state,
        ...{ count: state.count - 1 },
      };
    default:
      return state;
  }
}

function dataReducer(state = false, action) {
  switch (action.type) {
    case actionTypes.LOAD_DATA_SUCCESS:
      return {
        ...state,
        ...{ placeholderData: action.data },
      };
      
    default:
      return state;
  }

}


function lastUpdateReducer (state = 0, action) {
  switch (action.type) {
    case actionTypes.TICK_CLOCK:
      return {
        ...state,
        ...{ lastUpdate: action.ts },
      };
      
    default:
      return state;
  }
}

function lightReducer (state = false, action) {
  switch (action.type) {
    case actionTypes.TICK_CLOCK:
      return {
        ...state,
        ...{ light: !!action.light },
      };
      
    default:
      return state;
  }
}

function oldreducer (state, action) {
  switch (action.type) {
    case HYDRATE: {
      return { ...state, ...action.payload }
    }

    case actionTypes.FAILURE:
      return {
        ...state,
        ...{ error: action.error },
      }

    case actionTypes.INCREMENT:
      return {
        ...state,
        ...{ count: state.count + 1 },
      }

    case actionTypes.DECREMENT:
      return {
        ...state,
        ...{ count: state.count - 1 },
      }

    case actionTypes.RESET:
      return {
        ...state,
        ...{ count: initialState.count },
      }

    case actionTypes.LOAD_DATA_SUCCESS:
      return {
        ...state,
        ...{ placeholderData: action.data },
      }

    case actionTypes.TICK_CLOCK:
      return {
        ...state,
        ...{ lastUpdate: action.ts, light: !!action.light },
      }

    default:
      return state
  }
}
// function reducer(state, action) {
//   switch (action.type) {
//     case HYDRATE: {
//       return { ...state, ...action.payload }
//     }

//     case actionTypes.FAILURE:
//       return {
//         ...state,
//         ...{ error: action.error },
//       }

//     case actionTypes.INCREMENT:
//       return {
//         ...state,
//         ...{ count: state.count + 1 },
//       }

//     case actionTypes.DECREMENT:
//       return {
//         ...state,
//         ...{ count: state.count - 1 },
//       }

//     case actionTypes.RESET:
//       return {
//         ...state,
//         ...{ count: initialState.count },
//       }

//     case actionTypes.LOAD_DATA_SUCCESS:
//       return {
//         ...state,
//         ...{ placeholderData: action.data },
//       }

//     case actionTypes.TICK_CLOCK:
//       return {
//         ...state,
//         ...{ lastUpdate: action.ts, light: !!action.light },
//       }

//     default:
//       return state
//   }
// }



function contentsReducer (state = initialContentsState, action) {
  switch (action.type) {
    case actionTypes.GENERAL_CONTENT_LOAD_STARTED:
       return {
        ...state,
        ...{ loading: true, error: false },
      };

      break;
    case actionTypes.GENERAL_CONTENT_LOAD_SUCCESS:
      //
      const actualPermalinkOfContentFromCA = action.data.resultItem.permalink;
      let someContentInstances = state.contentInstances;
      someContentInstances[actualPermalinkOfContentFromCA] = action.data;
      return {
        ...state,
        ...{ loading: false, error: false, contentInstances: someContentInstances },
      };
      break;
    case actionTypes.GENERAL_CONTENT_LOAD_FAILURE:
      return {
        ...state,
        ...{ loading: false, error: action.error },
      };
      break;
    default:
      return state;
  }
}

export function reducer(state, action) {
  if (action.type === HYDRATE) {
    return { ...state, ...action.payload };
  }
  return combineReducers(
    { 
      // contents: contentsReducer,
      error: errorReducer,
      count: countReducer,
      data: dataReducer,
      light: lightReducer,
      ts: lastUpdateReducer
    }

  );

}

export const initialContentsState = {
  loading: false,
  nextContentInstanceIDBeingLoaded: false, 
  error: false, 
  transitionState: null, 
  currentContentInstanceID: false,// false, if there is no currentContentInstanceID, and then a string (item's permalink)
  contentInstances: {},// { loadStartTime, loadCompleteTime, ???}
};

export const initialState = {
  contents: initialContentsState,
  count: 0,
  error: false,
  lastUpdate: 0,
  light: false,
  placeholderData: null,
  // contents: initialContentsState,
}

export default reducer;
