import { useEffect } from 'react';
import { useSelector, useDispatch } from 'react-redux'

import { useRouter } from 'next/router';
import { END } from 'redux-saga';
import { wrapper } from '../store';
import { pageLoadTrigger, pageLoadStarted, pageLoadSuccess, pageLoadFailure } from '../actions';
import Page from '../components/page';


import Head from 'next/head'

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
  // const dispatch = useDispatch();

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
      This is the AllRoutes
    </div>
  );
}




export const getServerSideProps = wrapper.getStaticProps(async ({ store, req, res, query }) => {
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
  const slugs = query.slug;
  if ([1,2,3].includes(slugs.length ) ) {
    console.log('this would be an archive');
  } else if ([5,6,7].includes(slugs.length)) {
    console.log('this looks like a single dated post');
  }

  // loading data on the client side can be accomplished via diispatching a trigger action inside useEffect.

  if (!store.getState().page.data) {
    store.dispatch(pageLoadTrigger());// this is a trigger; a saga takes it and then loads data from an api endpoint using it.
    store.dispatch(END);
  }

  await store.sagaTask.toPromise();
});

export default AllRoutes
