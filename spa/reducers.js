import { actionTypes } from './actions';


export const initialContentsState = {
  loading: false,
  nextContentInstanceIDBeingLoaded: false, 
  error: false, 
  transitionState: null, 
  currentContentInstanceID: false,// false, if there is no currentContentInstanceID, and then a string (item's permalink)
  contentInstances: {},// { loadStartTime, loadCompleteTime, ???}
};


// when we trigger a new GENERAL_CONTENT_LOAD_STARTED, we are indicating that a new thing will be the current thing, and the current current thing, will sooon be the previous thing
// so we are in a loadingANewThing transitional state, as soon as GENERAL_CONTENT_LOAD_STARTED gets fired ...
// now is loadingANEw thine a piece of global state, or is it specific to the thing being loaded ...
// we should be indicating what the new thing specifically being loaded is, so that we can use it to avoid collisions / race conditions.
// if a user clicked 2 links, one right after  the other, then we would expect the former to override the latter
// so ... when we first call GENERAL_CONTENT_LOAD_STARTED, we need to know precisely what we are loading.
// also, we need to distinguish between loading a route's content, and loading a widget's content.
// they are different. 'loading' by itself and 'error' by itself are not specific ... we need to know what those pertain to
export function contentsReducer (state = initialContentsState, action) {
  switch (action.type) {
    // GENERAL_CONTENT_ pertains to content that loads for a specific permalink
    case actionTypes.GENERAL_CONTENT_LOAD_STARTED:
       return {
        ...state,
         ...{ loading: true, error: false, routeBeingLoaded: action.route },// by storing permalinkOfRouteBeingLoaded, wwe can check that the content received in GENERAL_CONTENT_LOAD_SUCCESS matches what it was supposed to be
      };

      break;
    case actionTypes.GENERAL_CONTENT_LOAD_SUCCESS:
      console.log('GENERAL_CONTENT_LOAD_SUCCESS', action.data);

      // the structure of thee state that we put content into ... an associative array of contentInstances ...
      // is an essential concept. this allows for us to do things like transitionns back and forward. Swiping, like on TikTok through content.
      // what to do if the result is not found?!
      // if(action.data.notFound) {
      //   //
      //   return {
      //     ...state,
      //     ...{ loading: false, error: false, notFound: true, redirectionLocation: action.data.redirectionLocation },
      //   }
      //   break;
      // } 
      const actualPermalinkOfContentFromCA = action.data.resultItem.permalink;// expected data shape: { resultItem: { permalink: ~ }, ads, nextItem } ... this always needs to be the case. The API must respond with that stuff always.
      let someContentInstances = state.contentInstances;
      someContentInstances[actualPermalinkOfContentFromCA] = action.data;// uses the permalink of the item as the key in the array. We know that this will be unique for each item of content.
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
    case actionTypes.GENERAL_CONTENT_LOAD_NOTFOUND:
      //
      console.log('GENERAL_CONTENT_LOAD_NOTFOUND');
      console.log(action);
      return {
        ...state,
        ...{ loading: false, error: false, notFound: true, redirectLocation: action.caard.redirectLocation },
      }
      break;
    default:
      return state;
  }
}
// contents: { currentContentInstanceID: false },