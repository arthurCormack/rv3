import { actionTypes } from './actions';
import { HYDRATE } from 'next-redux-wrapper';

const initialState = {
  count: 0,
  error: false,
  lastUpdate: 0,
  light: false,
  placeholderData: null,
  // there can only be one type of thing that, determing the current context of content, and that gets determined after a call is made to the CAA
  content: { loading: false, data: false, error: false, type: null, ads: null },// the type determines if there will be a stack inside data, or a single. types are from wp types. archive (category or tag) (single-chunk, or multi-chunk stack), page (single), post (stacked - for continuous scrolling)
  /* a contentStack? [
    {item of content}
    {item of content}
    ... ?
    // how about instances of a contentContext?

  ]*/
  // contentContextInstances!!!
  //previousContent: { loading: false, data: false, error: false, type: null, ads: null },
  // do we want to always only have 1 type of thing? or do we want to keep previous, so that we could do freakishly cool transition animations, like page-splig, or 3-D rotation?
  // we could put data into previousData, and then load newData, put it into data, and have 2 containers with content simultaneously.
  // 
  //page: { loading: false, data: false, error: false},
  content: {
    currentContentInstanceID: false,// this corresponds the the permalink of the content; it is used to determine the contentInstances
    contentInstances: [],// each time we click a link, we create a new contentInstance. A contentINstance could be a single post, a stack of posts, an archive page, etc.
    
  }
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
