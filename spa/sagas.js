import { all, call, delay, put, take, select, takeLatest } from 'redux-saga/effects';
import { actionTypes, generalContentLoadTrigger, generalContentLoadStarted, generalContentLoadSuccess, generalContentLoadFailure, generalContentLoadNotFound } from './actions';
import { makeSelectCurrentContentInstanceID, makeSelectContentLoading, makeSelectNextContentInstanceIDBeingLoaded } from 'selectors';
import { APICALLURL_GETFIRSTDATEDPOST, APICALLURL_GETGENERALCONTENT } from './constants';

import { isServer } from 'utils/detection';
import request from 'utils/request';

function* detetermineWhichApiEndpointToCallBasedOnPermalinkAnalysis(route) {
  // this is a specialized and opinioanted function that is used to guess, based on the route.slug what kind of thiing we anticipate we will be rendering.
  // remember it is the content authority that determines what kind oof thing we are dealing with here.
  console.log('detetermineWhichApiEndpointToCallBasedOnPermalinkAnalysis', route);
  const slugs = typeof route.slug !== 'undefined' ? route.slug : [];
  const permalink = slugs.join('/');
  // the name of the function sort of says it all.
  // this will return the requestURL and the typeOfThing that has been determined by anaylysis of the permalink
  // returns { requestURL, typeOfTHing }
  // and then typeOfThing will determine what components to use to render the results
  if (slugs.length === 0) {
    // then we have the home page.
    return { expectedContentType: 'home', requestURL: APICALLURL_GETGENERALCONTENT, permalink };
  } else if ([1,2,3].includes(slugs.length ) ) {
    console.log('this would be a special page, or an archive (a category or a tag)');// 
    // do we want to do the determiniation here? since we will have to do the same determiniation, on clinet side. better to abstract out into a reusable function. 
    // determine what kind of thing it is, based on the permalink
    // then call the api for the content for that route.
    // alternatively, we could just call the api endpoint, and let it determiine what kind of thing it is, and then respond accordingly.
    // we want to be able to make some opinionated guesses initially about what kind of thing it is iniitially. 
    // if there are special pages ... where there is a total custom layout, then we could do that here.
    // encodeURIComponent does not work on a path component in a url. %20 like characters break the routing in php and result in 404 not founds.
    // 
    
    if (slugs[0] === 'tag') {
      return { expectedContentType: 'tag', requestURL: `${APICALLURL_GETGENERALCONTENT}/${slugs.join('/')}`, permalink};
    }
    
    return { expectedContentType: 'category', requestURL: `${APICALLURL_GETGENERALCONTENT}/${slugs.join('/')}`, permalink};
    // 
  } else if ([5,6,7].includes(slugs.length)) {
    console.log('this looks like a single dated post');
    return { expectedContentType: 'post', requestURL: `${APICALLURL_GETGENERALCONTENT}/${slugs.join('/')}`, permalink };
  }
}

// function* loadAdditionalItemsIntoExistingContentStack () {

// }

export function* loadGeneralContentSaga(action) {
  console.log('loadGeneralContentSaga()', action);
  
  const route = action.route;
  // console.log('route', route);
  
  // const permalink = typeof action.route.slug === 'object' ? action.route.slug.join('/') : '';

  // what is the current content context?
  const currentContentID = yield select(makeSelectCurrentContentInstanceID());

  // we want to check to see if we are already loading anything first. 
  // selector to indicate if we are loading a thing, and what thing.

  // const permalinkID = route.asPath;// for the home page, there will be no asPath

  // console.log('permalink', permalink);

  const isLoading = yield select(makeSelectContentLoading());
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
  if (isServer) {
    // then just load the thing.
    console.log('');
    if (!!thing.requestURL) {
      console.log('thing.requestURL', thing.requestURL);
      yield put(generalContentLoadStarted(thing.permalink));// this is the key
      try {
        const caard = yield call(request, thing.requestURL);// caard with one d == content authority api response data. not the same as caardd with two Ds
        console.log('caard', caard);
        if (!!caard.notFound) {
          console.log('!!caard.notFound');
          yield put(generalContentLoadNotFound(caard));
        } else {
          yield put(generalContentLoadSuccess(caard));
        }
        

      } catch (e) {
        console.log('generalContentLoadFailure :(', e);
        yield put(generalContentLoadFailure(e));
      }
    }
  } else {

  }
}

export function* generalContentSaga() {
  console.log('loadGeneralContentSaga()');
  
  yield all([
    takeLatest(actionTypes.GENERAL_CONTENT_LOAD_TRIGGER, loadGeneralContentSaga),
  ]);
  

}



