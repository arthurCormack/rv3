import { all, call, delay, put, take, select, takeLatest } from 'redux-saga/effects';
import { actionTypes, generalContentLoadTrigger, generalContentLoadStarted, generalContentLoadSuccess, generalContentLoadFailure } from './actions';
import { makeSelectCurrentContentInstanceID, makeSelectLoading, makeSelectNextContentInstanceIDBeingLoaded } from 'selectors';
import { APICALLURL_GETFIRSTDATEDPOST } from './constants';

import { isServer } from 'utils/detection';
import request from 'utils/request';
// function* runClockSaga() {
//   yield take(actionTypes.START_CLOCK)
//   while (true) {
//     yield put(tickClock(false))
//     yield delay(1000)
//   }
// }

// function* loadDataSaga() {
//   try {
//     const res = yield fetch('https://jsonplaceholder.typicode.com/users')
//     const data = yield res.json()
//     yield put(loadDataSuccess(data))
//   } catch (err) {
//     yield put(failure(err))
//   }
// }

function getPostSlugElements(whichPermalink) {
  const slugArray = whichPermalink.split('/');
  // // console.log('slugArray==');
  // // console.log(slugArray);
  let categorySlug = slugArray[1];// the first / in the url path (at the beginning) makes the 0th item be empty. category slug comes after that.
  let postSlug = slugArray[5];
  let yearSlug = slugArray[2];
  let monthSlug = slugArray[3];
  let daySlug = slugArray[4];
  if (slugArray.length == 8) {
    categorySlug = slugArray[2];// the first / in the url path (at the beginning) makes the 0th item be empty. category slug comes after that.
    postSlug = slugArray[6];
    yearSlug = slugArray[3];
    monthSlug = slugArray[4];
    daySlug = slugArray[5];
  } else if (slugArray.length == 9) {
    categorySlug = slugArray[3];// the first / in the url path (at the beginning) makes the 0th item be empty. category slug comes after that.
    postSlug = slugArray[7];
    yearSlug = slugArray[4];
    monthSlug = slugArray[5];
    daySlug = slugArray[6];
  }
  //let requestURL = `${APICALLURL_GETDATEDPOST}/${categorySlug}/${yearSlug}/${monthSlug}/${daySlug}/${postSlug}?fullpermalink=${whichNextPostPermalink}`;
  return ({categorySlug, postSlug, yearSlug, monthSlug, daySlug});// return one object which can later be easily deconstructed
}

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
    // It is a POST!
    console.log('this looks like a single dated post');
    // we might have 1, 2 or 3 categories - we really only care about the last one when calling the API
    // really, the category shouldn't even be used - the date is enough to avoid namespace collisions when querying contnet
    let categorySlug = slugs[0];// the first / in the url path (at the beginning) makes the 0th item be empty. category slug comes after that.
    let postSlug = slugs[4];
    let yearSlug = slugs[1];
    let monthSlug = slugs[2];
    let daySlug = slugs[3];
    if (slugs.length === 6) {
      postSlug = slugs[5];
      yearSlug = slugs[2];
      monthSlug = slugs[3];
      daySlug = slugs[4];
    } else if (slugs.length === 7) {
      postSlug = slugs[6];
      yearSlug = slugs[3];
      monthSlug = slugs[4];
      daySlug = slugs[5];
    }
    const requestURL = `${APICALLURL_GETFIRSTDATEDPOST}/${categorySlug}/${yearSlug}/${monthSlug}/${daySlug}/${postSlug}`;
    console.log('requestURL', requestURL);
    return { expectedContentType: 'post', requestURL};// primaryCategory/yyyy/mm/dd/lastslug
  }
}

function* loadAdditionalItemsIntoExistingContentStack () {

}

function* loadGeneralContentSaga(action) {
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
    
    if (!!thing.requestURL) {
      yield put(generalContentLoadStarted(permalinkID));// this is the key
      // const requestURL = 
      console.log('making a call');
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

function* rootSaga() {
  console.log('rootSaga()');
  
  yield all([
    takeLatest(actionTypes.GENERAL_CONTENT_LOAD_TRIGGER, loadGeneralContentSaga),
  ]);
  // if (isServer) {
  //   // yield all([
  //   //   takeLatest(actionTypes.GENERAL_CONTENT_LOAD_TRIGGER, loadGeneralContentSaga),
  //   // ]);
  //   // if it is the server, then we have to trigger it ourselves here? because for some reason, the getServerSideProps dispatches the action, before we have a chance to set up the listener for the trigger
  //   // so we have to trigger it here.
  //   // yield put(generalContentLoadTrigger());
  // } else {
  //   yield all([
  //     // call(runClockSaga),
  //     // takeLatest(actionTypes.LOAD_DATA, loadDataSaga),
  //     takeLatest(actionTypes.GENERAL_CONTENT_LOAD_TRIGGER, loadGeneralContentSaga),
  //     takeLatest(actionTypes.POSTSTACK_LOADNEXTPOST_TRIGGER, loadNextPostIntoStack)
  //   ]);
  // }

}



export default rootSaga
