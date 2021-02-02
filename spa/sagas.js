import { all, call, delay, put, take, select, takeLatest } from 'redux-saga/effects';
import { actionTypes, generalContentLoadTrigger, generalContentLoadStarted, generalContentLoadSuccess, generalContentLoadFailure } from './actions';
import { makeSelectCurrentContentInstanceID, makeSelectLoading, makeSelectNextContentInstanceIDBeingLoaded } from 'selectors';
import { APICALLURL_GETFIRSTDATEDPOST } from './constants';

import { isServer } from 'utils/detection';
import request from 'utils/request';

function* detetermineWhichApiEndpointToCallBasedOnPermalinkAnalysis(route) {
  console.log('detetermineWhichApiEndpointToCallBasedOnPermalinkAnalysis', route);

  const slugs = route.slug;
  
  // the name of the function sort of says it all.
  // this will return the requestURL and the typeOfThing that has been determined by anaylysis of the permalink
  // returns { requestURL, typeOfTHing }
  // and then typeOfThing will determine what components to use to render the results
  if ([1,2,3].includes(slugs.length ) ) {
    console.log('this would be a special page, or an archive (a category or a tag)');// 
    // do we want to do the determiniation here? since we will have to do the same determiniation, on clinet side. better to abstract out into a reusable function. 
    // determine what kind of thing it is, based on the permalink
    // then call the api for the content for that route.
    // alternatively, we could just call the api endpoint, and let it determiine what kind of thing it is, and then respond accordingly.
    // we want to be able to make some opinionated guesses initially about what kind of thing it is iniitially. 
    // if there are special pages ... where there is a total custom layout, then we could do that here.
    if (slugs[0] === 'tag') {
      return { expectedContentType: 'tag'}; 
    }
    return { expectedContentType: 'category'};
  } else if ([5,6,7].includes(slugs.length)) {
    console.log('this looks like a single dated post');
    return { expectedContentType: 'post', requestURL: APICALLURL_GETFIRSTDATEDPOST };
  }
}

function* loadAdditionalItemsIntoExistingContentStack () {

}

export function* loadGeneralContentSaga(action) {
  const route = action.route;
  console.log('loadGeneralContentSaga()', route);
  

  // what is the current content context?
  const currentContentID = yield select(makeSelectCurrentContentInstanceID());

  // we want to check to see if we are already loading anything first. 
  // selector to indicate if we are loading a thing, and what thing.

  const permalinkID = route.asPath;
  const isLoading = yield select(makeSelectLoading());
  const whatIsBeingLoaded = yield select(makeSelectNextContentInstanceIDBeingLoaded());
  const thing = yield call(detetermineWhichApiEndpointToCallBasedOnPermalinkAnalysis, route);// expect: { expectedContentType, requestURL }
  

  // if we are on a post page, and it is continuous, up until a maximum number of posts,
  // then we need to differetiate between establising a new content context and adding to an existing one

  // if we are in a stack, and the link we are going to is already in the stack, then we have to scroll to that thing.

  // switch (thing.type) {
  //   case 'category':
  //     //
  //     break;
  //   case 'tag':
  //     //
  //     break;
  //   case 'page':
  //     //
  //     break;
  //   case 'post':
  //     //
  //     break;
  // }
  if (isServer ) {
    // then just load the thing.
    console.log('');
    if (!!thing.requestURL) {
      yield put(generalContentLoadStarted(permalinkID));// this is the key
      try {
        const caard = yield call(request, thing.requestURL);// caard with one d == content authority api response data
        yield put(generalContentLoadSuccess(caard));

      } catch (e) {
        console.log('generalContentLoadFailure :(', e);
        yield put(generalContentLoadFailure(e));
      }
    }
  } else {

  }
}

function* postRootSaga() {
  console.log('rootSaga()');
  
  // yield all([
  //   takeLatest(actionTypes.GENERAL_CONTENT_LOAD_TRIGGER, loadGeneralContentSaga),
  // ]);
  

}



