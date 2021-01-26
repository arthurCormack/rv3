import { useEffect } from 'react';
import { useSelector, useDispatch } from 'react-redux';

import { useRouter } from 'next/router';
import { END } from 'redux-saga';
import { wrapper } from '../store';
import { pageLoadTrigger, pageLoadStarted, pageLoadSuccess, pageLoadFailure, generalContentLoadTrigger, generalContentLoadStarted, generalContentLoadSuccess, generalContentLoadFailure } from '../actions';
 
// generalContentLoadTrigger

import ursula_akbar from 'images/ursula_akbar.jpg';// there does not appear to be an image loader built in to next.
import Page from '../components/page';


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

const AllRoutes = () => {
  const dispatch = useDispatch();

  // useEffect(() => {
  //   dispatch(startClock());
  // }, [dispatch]);
  const content = useSelector((state) => state.content);

  useEffect(() => {
    // if we don't have the data that we are supposed to have.
    // so we need to check our current props. also with useSelector.
  }, [dispatch]);
  // return <Page title="Index Page" linkTo="/other" NavigateTo="Other Page" />
  const router = useRouter();
  
  const routeSlugs = router.query.slug;

  // console.log('routeSlugs');
  // console.log(routeSlugs)

  return (
    <div>
      <Head>
        <title>AllRoutes title 🤔</title>
        <meta charSet="utf-8" />
        <meta name="viewport" content="initial-scale=1.0, width=device-width" />
      </Head>
      <Img src={ursula_akbar} alt="Ursula and Akbar, together at last" />
      <Img src={ursula_akbar} alt="Ursula and Akbar, together at last" type="thumbnail" />
      
      {/* <Img
        src={ursula_akbar}
        alt="Picture of the author"
        width={500}
        height={500}
      /> */}
      This is the AllRoutes
ddevup
    </div>
  );
}

export function detetermineWhichApiEndpointToCallBasedOnPermalinkAnalysis(permalink) {
  // the name of the function sort of says it all.
  // this will return the requestURL and the typeOfThing that has been determined by anaylysis of the permalink
  // returns { requestURL, typeOfTHing }
  // and then typeOfThing will determine what components to use to render the results
}


export const getServerSideProps = wrapper.getServerSideProps(async ({ store, req, res, query }) => {
  // store.dispatch(tickClock(false))
  // but ... do we have access to the router in getServerSideProps?
  // store = stuff.store;
  console.log('getServerSideProps()');
  // console.log(stuff);
  console.log('query', query);
  // can we determine what to do attempt to do here, based solely on the number of items in query.slug array?
  // 1, 2 or 3 items is an archive
  // 5, 6 or 7 items is a single dated post
  // tag/[tagname] won't be in here, because we will have to make a seperate page for it, with 

  // so the place where the initial logic of what to do, as well as the trigger that starts off whatever has to be done, starts here.
  // on the server. but on the client, it geets triggered by the useEffect.
  // but maybe there should be a function that can be used by either.
  // we need to also have child sagas, for the different scenarios, where we have potentially multiple other things to load in.
  // 


  const slugs = query.slug;
  if ([1,2,3].includes(slugs.length ) ) {
    console.log('this would be a special page, or an archive (a category or a tag)');// 
    // do we want to do the determiniation here? since we will have to do the same determiniation, on clinet side. better to abstract out into a reusable function. 
    // determine what kind of thing it is, based on the permalink
    // then call the api for the content for that route.
    // alternatively, we could just call the api endpoint, and let it determiine what kind of thing it is, and then respond accordingly.
    // we want to be able to make some opinionated guesses initially about what kind of thing it is iniitially. 
    // if there are special pages ... where there is a total custom layout, then we could do that here.

  } else if ([5,6,7].includes(slugs.length)) {
    console.log('this looks like a single dated post');
  }

  // loading data on the client side can be accomplished via diispatching a trigger action inside useEffect.
  // can we use the redux-injectors here?
  // 
  if (!store.getState().contents.currentContentInstanceID) {// this will be false initially
    store.dispatch(generalContentLoadTrigger());// this is a trigger; a saga takes it and then loads data from an api endpoint using it.
    store.dispatch(END);
  }

  await store.sagaTask.toPromise();
});

export default AllRoutes
