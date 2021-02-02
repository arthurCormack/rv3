import { useEffect } from 'react';
import { useDispatch } from 'react-redux';
import { END } from 'redux-saga';
import { wrapper } from '../store';
import { loadData, startClock, tickClock } from '../actions';
import Page from '../components/page';

const Index = () => {
  const dispatch = useDispatch();

  useEffect(() => {
    dispatch(startClock());
  }, [dispatch]);

  return <Page title="Index Page" linkTo="/other" NavigateTo="Other Page" />
}


// so how could this work for EZ?
// how could we port what we have to the new thing? Get started with the basic outline, and a proof of concept, and then make a whole bunch of tickets and get Aja, Akhil, Manu to do the work of flushing out all the details
// what are the core functionalities that we will need to be concerned with in the new stack?

// we need to be worried about ... getting data out of the WP JSON API, primarily.
// we need to establish some basic consisitency for how this works, and what is returned

// conventions ... we need to be able to provide different http response codes [200, 301s, 404s, etc]
// we also need to respond with either a result+ads (a single result, for single posts) , or a resultSet+resultSetData+ads (when we have a bunch of posts in a set, like on an arcive page),
// we can't use getStaticProps, because we will not know how the routing should work beforehand.
// so we use getServerSideProps ... we still need to maintain the notion of the CAA (Content Authority API) and it's corresponding CAARDD (Content Authority API Response DeadDrop)
// and how will the mechanism work? will we use the saga, and run the sagas, twice? the first to trigger sagas that get the data, and the second to render stuff after the sagas have run?
// the double render is expensive. Maybe it might be better to have a loadData method on each container?  

// or ... 1 api endpoint for everything?
// and let the CAA figuree out what the thing is that is being rendered? 
// the problem then would bee that the SPA would have to have everything ready to go ... noo code-splitting would be possible
// but we really only have the same stuff ... archive page, and specialty pages
// getServerSideProps


// each page will have it's own getServerSideProps function! we don't have to have an oveerall one?
// we still need to inteerpret the response from the CAAR ( Content Authority API Response )

// so what would the routing look like for a single dated post then?
// /[inverse-category-tree (parent/child) ]/[yyyy]/[mm]/[dd]/[post_title]




export const getStaticProps = wrapper.getStaticProps(async ({ store }) => {
  store.dispatch(tickClock(false))

  if (!store.getState().placeholderData) {
    store.dispatch(loadData());// this is a trigger; a saga takes it and then loads data from an api endpoint using it.
    store.dispatch(END);
  }

  await store.sagaTask.toPromise();
})

export default Index
