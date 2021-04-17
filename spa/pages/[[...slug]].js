import { useEffect } from 'react';
import { useSelector, useDispatch } from 'react-redux';

import { useRouter } from 'next/router';

import { initializeStore } from '../store';
// import { useInjectReducer, useInjectSaga } from 'redux-injectors';
import { contentsReducer as reducer} from '../reducers';
// import { generalContentSaga as saga} from "../sagas";

const key = 'content';

import { END } from 'redux-saga';
import { wrapper } from '../store';

import { 
  pageLoadTrigger, pageLoadStarted, pageLoadSuccess, pageLoadFailure, simpleTest,
  generalContentLoadTrigger, generalContentLoadStarted, generalContentLoadSuccess, generalContentLoadFailure 
} from '../actions';

import { createStructuredSelector } from 'reselect';
import { makeSelectCurrentContentInstanceID, makeSelectContentLoading, makeSelectNextContentInstanceIDBeingLoaded } from 'selectors';
// // generalContentLoadTrigger

import ursula_akbar from 'images/ursula_akbar.jpg';// there does not appear to be an image loader built in to next.
// import Page from '../components/page';



import Head from 'next/head';
// import Image from 'next/image';
import Img from 'react-optimized-image';

// AllRoutes. One single Route that catches everything. The big drawback, is that route based code-splitting is affected: all code required for any route in this catch-all needs to be bundled
// The advantage, is that we can handle all of the routes, and use our own logic here to determine what to do.

// now ... we can look at the route, and match it withe known patterns that we anticipate, based upon our criteria, such as:
// specific slugs
// the number of slugs
// ??? a combination of these things.
// we could also have special things for things like the book club.

// so what would an experiment to determine the feasibility of using this for our stack look like?
// we need to make a call to the CAA, and then either send a 200 response, or a 301 or a 404, depending upon what has been returned from the API

// one tricky problem that I want us to solve the heights of images ... and cululative layout shiift: https://web.dev/cls/
// but what to do about the images that are appeearing in the content of wp posts?
// we can do something about the featured images, specifying heights and so on.
// but what about the images within the content? That is much trickier.
// maybe there iis a thing that could be done on wp-side? to make the image beforee load complete occupy the space it will occupy after load complete
// maybe we can specify the exact height in the <img /> tag of the post_content?

// another thing we could do to mitigate CLS is measuring the heights of tings in the stack, and remembering them ( remeber to reset on resize),
// and making the containers maintain that previously calculated height.

const stateSelector = createStructuredSelector({
  currentContentInstanceID: makeSelectCurrentContentInstanceID(),
  loading: makeSelectContentLoading(), 
  nextContentInstanceIDBeingLoaded: makeSelectNextContentInstanceIDBeingLoaded()
});

const AllRoutes = () => {
  const dispatch = useDispatch();

  const router = useRouter();
  
  console.log('router', router);
  const routeSlugs = router.query.slug;

  // const content = useSelector((state) => state.content);
  const { currentContentInstanceID, loading, nextContentInstanceIDBeingLoaded } = useSelector(stateSelector);
  console.log('AllRoutes()');
  // useInjectReducer({ key, reducer });
  // useInjectSaga({ key, saga });

  useEffect(() => {
    // if we don't have the data that we are supposed to have.
    // so we need to check our current props. also with useSelector.
    if (!currentContentInstanceID) {
      console.log('useEffect, !currentContentInstanceID');
      dispatch(generalContentLoadTrigger(router.query));
    }
  }, [dispatch]);
  
  

  // console.log('routeSlugs');
  // console.log(routeSlugs)
  // we will useSelector, to know what kind of thing we are dealing with during the render. It will alread have been determined.
  // on server, we will have the data, and render with te data. On Client, the initial determination happens before load, but final determination happens after data is receved from Content Authority Api - 

  return (
    <div>
      <Head>
        <title>AllRoutes title 🤔</title>
        <meta charSet="utf-8" />
        <meta name="viewport" content="initial-scale=1.0, width=device-width" />
      </Head>
      <Img src={ursula_akbar} alt="Ursula and Akbar, together at last" />
      {/* <Img src={ursula_akbar} alt="Ursula and Akbar, together at last" type="thumbnail" /> */}
      
      {/* <Img
        src={ursula_akbar}
        alt="Picture of the author"
        width={500}
        height={500}
      /> */}

      This is the special AllRoutes
ddevup

    </div>
  );
}

// export function detetermineWhichApiEndpointToCallBasedOnPermalinkAnalysis(slugs) {
//   console.log('detetermineWhichApiEndpointToCallBasedOnPermalinkAnalysis', slugs);
//   // the name of the function sort of says it all.
//   // this will return the requestURL and the typeOfThing that has been determined by anaylysis of the permalink
//   // returns { requestURL, typeOfTHing }
//   // and then typeOfThing will determine what components to use to render the results
//   if (slugs.length === 0)  {

//   } else if ([1,2,3].includes(slugs.length ) ) {
//     console.log('this would be a special page, or an archive (a category or a tag)');// 
//     // do we want to do the determiniation here? since we will have to do the same determiniation, on clinet side. better to abstract out into a reusable function. 
//     // determine what kind of thing it is, based on the permalink
//     // then call the api for the content for that route.
//     // alternatively, we could just call the api endpoint, and let it determiine what kind of thing it is, and then respond accordingly.
//     // we want to be able to make some opinionated guesses initially about what kind of thing it is iniitially. 
//     // if there are special pages ... where there is a total custom layout, then we could do that here.
//     if (slugs[0] === 'tag') {
//       return { expectedContentType: 'tag'}; 
//     }
//     return { expectedContentType: 'category'};
//   } else if ([5,6,7].includes(slugs.length)) {
//     console.log('this looks like a single dated post');
//     return { expectedContentType: 'post'};
//   }
// }


// export const getServerSideProps = wrapper.getServerSideProps(async ({ store, req, res, query }) => {
  export const getServerSideProps = async ({req, res, query }) => {
  // the wrapper gives is thee state! without it, we aren't getting the state passed in!
  // export const getServerSideProps = async (stuff) => {
  
  // const store = useStore();
  // console.log('store:', store);
  const store = initializeStore();
  console.log('getServerSideProps', store);
  // return false;
  // store.dispatch(tickClock(false))
  // but ... do we have access to the router in getServerSideProps?
  // store = stuff.store;

  // console.log('query', query);

  // can we determine what to do attempt to do here, based solely on the number of items in query.slug array?
  // 1, 2 or 3 items is an archive
  // 5, 6 or 7 items is a single dated post
  // tag/[tagname] won't be in here, because we will have to make a seperate page for it, with 

  // so the place where the initial logic of what to do, as well as the trigger that starts off whatever has to be done, starts here.
  // on the server. but on the client, it geets triggered by the useEffect.
  // but maybe there should be a function that can be used by either.
  // we need to also have child sagas, for the different scenarios, where we have potentially multiple other things to load in.
  // 


  // const slugs = query.slug;
  
  // const determination = detetermineWhichApiEndpointToCallBasedOnPermalinkAnalysis(slugs);
  // what data is needed to construct the requestURL?
  // we have arbitrary endpoints to call. So we have a function that looks at the type, and then based on that type
  // calls an endpoint, with the params needed, such as slugs. 
  // what else might be required? maybe the whole query, instead of just the slugs?
  // it might need query params as well as slugs
  // for that matter, we could simply pass along the query to the generalContentLoadTrigger, and let the saga take care of everything.
  // that does seem to make the most sense, imo
  
  // console.log('determination', determination);
  // loading data on the client side can be accomplished via diispatching a trigger action inside useEffect.
  // can we use the redux-injectors here?
  // console.log('store.getState()', store.getState());
  // console.log('store.getState()()', store.getState();
  // it seems that thhis is happening, before the store has had a chance to be created.
  if (!store.getState().contents || !store.getState().contents.currentContentInstanceID) {// this will be false initially
    //
    console.log('just b4 generalContentLoadTrigger ...');
    store.dispatch(generalContentLoadTrigger(query));// this is a trigger; a saga takes it and then loads data from an api endpoint using it.
    // store.dispatch(simpleTest());
    store.dispatch(END);
  }

  await store.sagaTask.toPromise();
  // what happens if we get a notFound? we check to see if we also got a redirectLocation, and if so, then we redirect to there.
  // console.log('after the await');
  // console.log('store.getState()()', store.getState()());
  const contents = store.getState().contents;
  console.log('contents', contents);
  if (!!contents.notFound && !!contents.redirectLocation) {
    console.log('trying to redirect...');
    res.setHeader('Location', contents.redirectLocation);
    res.statusCode = 302;
    res.end();
  }

  return { props: { initialReduxState: store.getState() } }
};

export default AllRoutes
