import { actionTypes } from './actions';
import { HYDRATE } from 'next-redux-wrapper';

export const initialState = {
  count: 0,
  error: false,
  lastUpdate: 0,
  light: false,
  placeholderData: null,
  // there can only be one type of thing that, determing the current context of content, and that gets determined after a call is made to the CAA
  contents: { 

    loading: false,
    nextContentInstanceIDBeingLoaded: false, 
    error: false, 
    transitionState: null, 
    
    
    currentContentInstanceID: false,// false, if there is no currentContentInstanceID, and then a string (item's permalink)
    contentInstances: {
      
    },
    // uh no. because what if the item is a continuous scrolling thing? then we have a stack of things.
    // so ... types are: single-post, 'post-stack' (for continuous scrolling), 'page', 'category', 'tag' (categories and tags are essentially the same), ... and other custom post types 
    // a keyed array (object) of objects, corresponding to the items, with keys, being the permalinks of the objects
    // { type: 'post', post_content: '..', permalink: '...', excerpt: '', description: '...', 'thumbnail'}// when active, we will have all this stuff, but when not active, we will 
     
     
    // ads: null // ads will be inside each of the contentInstances
  },// the type determines if there will be a stack inside data, or a single. types are from wp types. archive (category or tag) (single-chunk, or multi-chunk stack), page (single), post (stacked - for continuous scrolling)
  /* a contentStack? [
    {item of content}
    {item of content}
   

  ]*/
  // transitionState can be: 'empty', 'loadingSomethingNew', 'doneLoadingNewThingWaitingToTransition', 'transitioningFromOldThingToNewThing', 'doneTransition', 'restingOnCurrentThing'.
  // should we keep the previous thing? Certainly not if it was a stack. Now, we can just load it again, but bring it in on the left instead of the right.
  //

  // What are the new features that I want to introduce into the new stack?
  // some already existing features: like the gallery, but better
  // some better ad placements
  // sidekick ads, contests. Special promotional features for subscribing. New UI, animations. New creative treatments for interactive promotions.
  // 

  // contentContextInstances!!!
  //previousContent: { loading: false, data: false, error: false, type: null, ads: null },
  // do we want to always only have 1 type of thing? or do we want to keep previous, so that we could do freakishly cool transition animations, like page-splig, or 3-D rotation?
  // we could put data into previousData, and then load newData, put it into data, and have 2 containers with content simultaneously.
  // 
  //page: { loading: false, data: false, error: false},
  content: {
    currentContentInstanceID: false,// this corresponds the the permalink of the content; it is used to determine the contentInstances
    contentInstances: [],// each time we click a link, we create a new contentInstance. A contentINstance could be a single post, a stack of posts, an archive page, etc.
    // maybe I should abbreviate this stuff? ccid, and cis? what are the transitional states that content can be in?
    // on a thing, on a thing, and loading a new thing. finished loading the new thing and about to transition from old to new. transitioning from old to new. finished transition, then next becomes current
  },
  //post: { loading: false, data: false, error: false, stackType: null, single: false, stack: false},// a potentially a stack of posts (continuous scrolling) or just a single post
  ads: false,
};

function reducer(state = initialState, action) {
  switch (action.type) {
    case HYDRATE: {
      return { ...state, ...action.payload };
    }

    case actionTypes.FAILURE:
      return {
        ...state,
        ...{ error: action.error },
      };

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
        ...{ count: initialState.count },
      };

    case actionTypes.LOAD_DATA_SUCCESS:
      return {
        ...state,
        ...{ placeholderData: action.data },
      };

    case actionTypes.TICK_CLOCK:
      return {
        ...state,
        ...{ lastUpdate: action.ts, light: !!action.light },
      };
    
    
    
    default:
      return state;
  }
}

export default reducer;
