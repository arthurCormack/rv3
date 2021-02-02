import { actionTypes } from './actions';


export const initialContentsState = {
  loading: false,
  nextContentInstanceIDBeingLoaded: false, 
  error: false, 
  transitionState: null, 
  currentContentInstanceID: false,// false, if there is no currentContentInstanceID, and then a string (item's permalink)
  contentInstances: {},// { loadStartTime, loadCompleteTime, ???}
};


export function contentsReducer (state = initialContentsState, action) {
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
// contents: { currentContentInstanceID: false },